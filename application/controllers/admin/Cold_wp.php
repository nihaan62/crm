<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cold_wp extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('leads_model');

        // Automatic DB Migration checks
        $db_prefix = db_prefix();
        if (!$this->db->table_exists($db_prefix . 'cold_wp_messages')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `{$db_prefix}cold_wp_messages` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `lead_id` INT NOT NULL,
                `phone_number` VARCHAR(30) NOT NULL,
                `message_text` TEXT NOT NULL,
                `image_path` VARCHAR(255) DEFAULT NULL,
                `sent_by` VARCHAR(100) NOT NULL,
                `sent_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `status` VARCHAR(20) DEFAULT 'Sent'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        }

        if (!$this->db->table_exists($db_prefix . 'cold_wp_templates')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `{$db_prefix}cold_wp_templates` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(255) NOT NULL,
                `message_text` TEXT NOT NULL,
                `image_path` VARCHAR(255) DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        }

        if (!$this->db->field_exists('batch_name', $db_prefix . 'leads')) {
            $this->db->query("ALTER TABLE `{$db_prefix}leads` ADD COLUMN `batch_name` VARCHAR(191) DEFAULT NULL");
        }
    }

    public function index()
    {
        if (!staff_can('view', 'cold_wp_messages')) {
            access_denied('Cold WP Messages');
        }

        $lead_ids_str = $this->input->get('ids');
        $leads = [];

        if (!empty($lead_ids_str)) {
            $ids = explode(',', $lead_ids_str);
            $ids = array_filter(array_map('intval', $ids));
            
            if (count($ids) > 0) {
                $this->db->where_in('id', $ids);
                $leads = $this->db->get(db_prefix() . 'leads')->result_array();
            }
        } else {
            // Load all active leads (not lost, not junk) by default
            $this->db->where('lost', 0);
            $this->db->where('junk', 0);
            $this->db->order_by('name', 'asc');
            $leads = $this->db->get(db_prefix() . 'leads')->result_array();
        }

        $templates = $this->db->order_by('title', 'asc')->get(db_prefix() . 'cold_wp_templates')->result_array();

        $data = [
            'leads' => $leads,
            'templates' => $templates,
            'title' => 'Send Cold WhatsApp Messages'
        ];

        $this->load->view('admin/cold_wp/send', $data);
    }

    public function log_send()
    {
        if (!staff_can('create', 'cold_wp_messages')) {
            echo json_encode(['success' => false, 'message' => 'Access denied.']);
            return;
        }

        $lead_id = $this->input->post('lead_id');
        $phone_number = $this->input->post('phone_number');
        $message_text = $this->input->post('message_text');

        $image_path = null;

        if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
            $path = FCPATH . 'uploads/cold_wp_images/';
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $new_filename = uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $path . $new_filename)) {
                    $image_path = 'uploads/cold_wp_images/' . $new_filename;
                }
            }
        } else {
            $image_path = $this->input->post('image_path');
        }

        $changed_by = get_staff_full_name(get_staff_user_id());

        $insert_data = [
            'lead_id' => $lead_id,
            'phone_number' => $phone_number,
            'message_text' => $message_text,
            'image_path' => $image_path,
            'sent_by' => $changed_by,
            'status' => 'Sent'
        ];

        $this->db->insert(db_prefix() . 'cold_wp_messages', $insert_data);

        echo json_encode([
            'success' => true,
            'message' => 'Message send logged successfully.',
            'image_path' => $image_path
        ]);
    }

    public function logs()
    {
        try {
            if (!staff_can('view', 'cold_wp_messages')) {
                access_denied('Cold WP Messages');
            }

            $this->db->select(db_prefix() . 'cold_wp_messages.*, ' . db_prefix() . 'leads.name as lead_name');
            $this->db->from(db_prefix() . 'cold_wp_messages');
            $this->db->join(db_prefix() . 'leads', db_prefix() . 'leads.id = ' . db_prefix() . 'cold_wp_messages.lead_id', 'left');
            $this->db->order_by(db_prefix() . 'cold_wp_messages.sent_at', 'desc');
            $logs = $this->db->get()->result_array();

            $data = [
                'logs' => $logs,
                'title' => 'WhatsApp Cold Message Logs'
            ];

            $this->load->view('admin/cold_wp/logs', $data);
        } catch (Throwable $e) {
            show_error('Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
        }
    }

    public function delete_log($id)
    {
        if (!staff_can('delete', 'cold_wp_messages')) {
            access_denied('Cold WP Messages');
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'cold_wp_messages');

        set_alert('success', 'Log record deleted successfully.');
        redirect(admin_url('cold_wp/logs'));
    }

    public function save_template()
    {
        if (!staff_can('create', 'cold_wp_messages')) {
            echo json_encode(['success' => false, 'message' => 'Access denied.']);
            return;
        }

        $title = $this->input->post('title');
        $message_text = $this->input->post('message_text');
        $image_path = null;

        if (empty($title)) {
            echo json_encode(['success' => false, 'message' => 'Template Title is required.']);
            return;
        }

        if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
            $path = FCPATH . 'uploads/cold_wp_images/';
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $new_filename = uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $path . $new_filename)) {
                    $image_path = 'uploads/cold_wp_images/' . $new_filename;
                }
            }
        }

        $insert_data = [
            'title' => $title,
            'message_text' => $message_text,
            'image_path' => $image_path
        ];

        $this->db->insert(db_prefix() . 'cold_wp_templates', $insert_data);
        $insert_id = $this->db->insert_id();

        echo json_encode([
            'success' => true,
            'message' => 'Template saved successfully.',
            'template' => [
                'id' => $insert_id,
                'title' => $title,
                'message_text' => $message_text,
                'image_path' => $image_path ? base_url($image_path) : null,
                'raw_image_path' => $image_path
            ]
        ]);
    }

    public function delete_template($id)
    {
        if (!staff_can('delete', 'cold_wp_messages')) {
            echo json_encode(['success' => false, 'message' => 'Access denied.']);
            return;
        }

        $this->db->where('id', (int)$id);
        $this->db->delete(db_prefix() . 'cold_wp_templates');

        echo json_encode(['success' => true, 'message' => 'Template deleted successfully.']);
    }
}
