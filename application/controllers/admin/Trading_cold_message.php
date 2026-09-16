<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Trading_cold_message extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('leads_model');

        // Check if the user has permission to view cold_wp_messages since we share the permission
        if (!staff_can('view', 'cold_wp_messages')) {
            access_denied('Trading Cold Message');
        }

        // Automatic DB Migration for logging messages
        $db_prefix = db_prefix();
        if (!$this->db->table_exists($db_prefix . 'trading_cold_messages')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `{$db_prefix}trading_cold_messages` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(255) DEFAULT NULL,
                `phone_number` VARCHAR(30) NOT NULL,
                `message_text` TEXT,
                `media_paths` TEXT,
                `sent_by` VARCHAR(100) NOT NULL,
                `sent_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `status` VARCHAR(20) DEFAULT 'Sent'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        }
    }

    public function index()
    {
        $data['title'] = 'Trading Cold Message';
        $this->load->view('admin/trading_cold_message/index', $data);
    }

    public function upload_media()
    {
        if (!staff_can('create', 'cold_wp_messages')) {
            echo json_encode(['success' => false, 'message' => 'Access denied.']);
            return;
        }

        $path = FCPATH . 'uploads/trading_cold_messages/';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $uploaded_files = [];
        $errors = [];

        if (isset($_FILES['media']) && !empty($_FILES['media']['name'][0])) {
            $file_count = count($_FILES['media']['name']);

            for ($i = 0; $i < $file_count; $i++) {
                $ext = strtolower(pathinfo($_FILES['media']['name'][$i], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'mov', 'avi'];
                
                if (in_array($ext, $allowed_extensions)) {
                    $new_filename = uniqid() . '.' . $ext;
                    $target_file = $path . $new_filename;
                    
                    if (move_uploaded_file($_FILES['media']['tmp_name'][$i], $target_file)) {
                        $uploaded_files[] = 'uploads/trading_cold_messages/' . $new_filename;
                    } else {
                        $errors[] = 'Failed to upload ' . $_FILES['media']['name'][$i];
                    }
                } else {
                    $errors[] = 'Invalid file type for ' . $_FILES['media']['name'][$i];
                }
            }
        }

        if (count($uploaded_files) > 0) {
            echo json_encode(['success' => true, 'files' => $uploaded_files, 'errors' => $errors]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No files uploaded.', 'errors' => $errors]);
        }
    }

    public function send_message()
    {
        if (!staff_can('create', 'cold_wp_messages')) {
            echo json_encode(['success' => false, 'message' => 'Access denied.']);
            return;
        }

        $phone_number = $this->input->post('phone_number');
        $name = $this->input->post('name');
        $message_text = $this->input->post('message_text');
        $media_paths = $this->input->post('media_paths'); // Array of file paths

        if (empty($phone_number)) {
             echo json_encode(['success' => false, 'message' => 'Phone number is required.']);
             return;
        }

        // Clean phone number
        $clean_phone = preg_replace('/[^0-9]/', '', $phone_number);
        if (strlen($clean_phone) === 10) {
            $clean_phone = '91' . $clean_phone;
        }

        $url = 'https://2fa.tehub.in/api/whatsapp.php';
        $api_key = get_option('whatsapp_api_key') ?: 'b0b306dc4bf090c19f85c584906a967c';

        // Prepare Text payload
        $payload = [
            'to' => $clean_phone,
            'type' => 'general'
        ];

        // Replace any {name} placeholder in the message text
        if (!empty($name)) {
            $message_text = str_ireplace('{name}', $name, $message_text);
        }
        $payload['message'] = $message_text;

        $api_responses = [];
        $has_error = false;
        $error_message = '';

        // If no media, just send the text
        if (empty($media_paths) || !is_array($media_paths) || count($media_paths) === 0) {
            $api_responses[] = $this->_send_api_request($url, $api_key, $payload);
        } else {
            // We have media. Send the first media with the text message.
            foreach ($media_paths as $index => $path) {
                $current_payload = $payload;
                
                if ($index > 0) {
                    // For subsequent files, maybe omit the text message to avoid sending it multiple times
                    unset($current_payload['message']);
                }

                if (file_exists(FCPATH . $path)) {
                    $file_data = file_get_contents(FCPATH . $path);
                    $base64_file = base64_encode($file_data);
                    
                    $current_payload['image'] = $base64_file;
                    $current_payload['media'] = $base64_file;
                    $current_payload['file'] = $base64_file;
                    $current_payload['pdf'] = $base64_file;
                    $current_payload['filename'] = pathinfo($path, PATHINFO_BASENAME);
                    
                    $mime = mime_content_type(FCPATH . $path);
                    $data_uri = 'data:' . $mime . ';base64,' . $base64_file;
                    
                    $current_payload['image_uri'] = $data_uri;
                    $current_payload['media_uri'] = $data_uri;
                    $current_payload['image_url'] = base_url($path);
                    $current_payload['media_url'] = base_url($path);
                    $current_payload['file_url'] = base_url($path);
                }
                
                $api_responses[] = $this->_send_api_request($url, $api_key, $current_payload);
            }
        }

        // Check if any request failed
        foreach ($api_responses as $res) {
            if (!$res['success']) {
                $has_error = true;
                $error_message = $res['message'];
                break;
            }
        }

        if ($has_error) {
            echo json_encode([
                'success' => false,
                'message' => 'WhatsApp API Error: ' . $error_message
            ]);
            return;
        }

        // Log the message
        $changed_by = get_staff_full_name(get_staff_user_id());
        $this->db->insert(db_prefix() . 'trading_cold_messages', [
            'name' => $name,
            'phone_number' => $phone_number,
            'message_text' => $message_text,
            'media_paths' => !empty($media_paths) ? json_encode($media_paths) : null,
            'sent_by' => $changed_by,
            'status' => 'Sent'
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Message sent successfully.'
        ]);
    }

    private function _send_api_request($url, $api_key, $payload)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Allow more time for large files

        $api_response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $res_decoded = json_decode($api_response, true);
        $success = false;
        $message = 'Failed to connect to API.';

        if ($http_code === 200 && isset($res_decoded['success']) && $res_decoded['success'] === true) {
            $success = true;
            $message = 'Sent';
        } else {
            if (isset($res_decoded['error'])) {
                $message = $res_decoded['error'];
            } elseif (isset($res_decoded['message'])) {
                $message = $res_decoded['message'];
            }
        }

        return ['success' => $success, 'message' => $message];
    }
}
