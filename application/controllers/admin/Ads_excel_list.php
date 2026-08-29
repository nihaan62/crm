<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ads_excel_list extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (!is_admin()) {
            access_denied('Ads Excel List');
        }

        $sheet_url = get_option('excel_sheet_url');
        if (empty($sheet_url)) {
            $sheet_url = 'https://docs.google.com/spreadsheets/d/17hEUmsz8Q8Q32KDKO7qi0uTdhAXIDz7vRvPmkMS7Yv8/edit?usp=sharing';
        }

        $lead_count = (int) get_option('excel_lead_count');
        if ($lead_count <= 0) {
            $lead_count = 30;
        }

        // Convert Google Sheet URL to CSV export link
        $csv_url = $sheet_url;
        if (preg_match('/spreadsheets\/d\/([a-zA-Z0-9-_]+)/', $sheet_url, $matches)) {
            $csv_url = "https://docs.google.com/spreadsheets/d/" . $matches[1] . "/export?format=csv";
        }

        // Fetch CSV contents
        $csvContent = '';
        $fetch_error = '';

        $opts = [
            "http" => [
                "method" => "GET",
                "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n",
                "timeout" => 15
            ],
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ]
        ];
        $context = stream_context_create($opts);

        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $csv_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if (!ini_get('open_basedir')) {
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            }
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
            $csvContent = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_err = curl_error($ch);
            curl_close($ch);

            if (($httpCode == 301 || $httpCode == 302) && ini_get('open_basedir')) {
                $csvContent = @file_get_contents($csv_url, false, $context);
            }
            
            if (empty($csvContent)) {
                $fetch_error = "cURL error: " . $curl_err . " (HTTP Status " . $httpCode . ")";
            }
        }

        if (empty($csvContent)) {
            $csvContent = @file_get_contents($csv_url, false, $context);
            if (empty($csvContent)) {
                $fetch_error = "Failed to fetch Google Sheet data. Please check your sheet URL and internet connection.";
            } else {
                $fetch_error = "";
            }
        }

        // Check if returned content is HTML (meaning the Google Sheet is private and redirected to Google Login)
        if (!empty($csvContent) && (strpos($csvContent, '<!DOCTYPE html>') !== false || strpos($csvContent, '<html') !== false)) {
            $fetch_error = "The Google Sheet is private. Please share it as 'Anyone with the link can view' so the CRM can read it.";
            $csvContent = '';
        }

        $headers    = [];
        $data_rows  = [];
        $total_sheet_rows = 0;

        if (!empty($csvContent)) {
            $lines = explode("\n", str_replace("\r", "", $csvContent));
            if (count($lines) > 0) {
                // Find the header row by looking for 'created_time' or 'form_name'
                $headerLineIndex = 0;
                foreach ($lines as $idx => $line) {
                    $rowValues = str_getcsv($line);
                    if (in_array('created_time', $rowValues) || in_array('form_name', $rowValues)) {
                        $headerLineIndex = $idx;
                        break;
                    }
                }

                $rawHeaders = str_getcsv($lines[$headerLineIndex]);
                $headerMap  = [];
                
                // Define unwanted technical and metadata columns
                $unwanted = [
                    'id', 'ad id', 'adset id', 'campaign id', 'form id', 
                    'ad name', 'adset name', 'campaign name', 'form name', 
                    'is organic', 'organic', 'lead status', 'note', 'cold wp send', 
                    'cold wp send status', 'created time'
                ];

                foreach ($rawHeaders as $idx => $h) {
                    $hTrim = trim($h);
                    $hLower = str_replace('_', ' ', strtolower($hTrim));
                    
                    // Skip unwanted columns if they match or contain unwanted tags
                    $shouldSkip = false;
                    foreach ($unwanted as $u) {
                        if ($hLower === $u || strpos($hLower, $u) !== false) {
                            $shouldSkip = true;
                            break;
                        }
                    }
                    if ($shouldSkip) {
                        continue;
                    }
                    if ($hTrim !== '') {
                        $headerMap[$idx] = $hTrim;
                        $headers[]       = $hTrim;
                    }
                }

                // Retrieve DB leads to match by phone number
                $db_leads = $this->db->select('id, name, phonenumber, click_1, click_2, click_1_time, click_2_time')->get(db_prefix() . 'leads')->result_array();
                $leads_by_phone = [];
                foreach ($db_leads as $dl) {
                    $clean = preg_replace('/[^0-9]/', '', $dl['phonenumber']);
                    if (strlen($clean) >= 10) {
                        $key = substr($clean, -10);
                        $leads_by_phone[$key] = $dl;
                    }
                }

                for ($i = 0; $i < count($lines); $i++) {
                    if ($i === $headerLineIndex) {
                        continue;
                    }
                    if (trim($lines[$i]) === '') {
                        continue;
                    }
                    $rowValues = str_getcsv($lines[$i]);
                    if (empty(array_filter($rowValues))) {
                        continue;
                    }

                    // Skip the row if it contains key strings
                    if (isset($rowValues[0]) && (trim($rowValues[0]) === 'id' || trim($rowValues[0]) === 'created_time')) {
                        continue;
                    }
                    if (isset($rowValues[1]) && trim($rowValues[1]) === 'created_time') {
                        continue;
                    }

                    $total_sheet_rows++;

                    if (count($data_rows) < $lead_count) {
                        $row = [];
                        foreach ($headerMap as $idx => $hName) {
                            $row[$hName] = isset($rowValues[$idx]) ? trim($rowValues[$idx]) : '';
                        }

                        // Try to find matching phone in CRM
                        $row['db_lead'] = null;
                        
                        // Find the phone field in the rowValues using the raw header index
                        $phoneIdx = -1;
                        foreach ($rawHeaders as $idx => $h) {
                            if (strpos(strtolower(trim($h)), 'phone') !== false) {
                                $phoneIdx = $idx;
                                break;
                            }
                        }

                        if ($phoneIdx !== -1 && isset($rowValues[$phoneIdx])) {
                            $pClean = preg_replace('/[^0-9]/', '', $rowValues[$phoneIdx]);
                            if (strlen($pClean) >= 10) {
                                $pKey = substr($pClean, -10);
                                if (isset($leads_by_phone[$pKey])) {
                                    $row['db_lead'] = $leads_by_phone[$pKey];
                                }
                            }
                        }

                        $data_rows[] = $row;
                    }
                }
            }
        }

        $data['title']            = 'Ads Excel List';
        $data['sheet_url']        = $sheet_url;
        $data['lead_count']       = $lead_count;
        $data['headers']          = $headers;
        $data['rows']             = $data_rows;
        $data['total_sheet_rows'] = $total_sheet_rows;
        $data['fetch_error']      = $fetch_error;

        $this->load->view('admin/ads_excel_list/index', $data);
    }

    public function import_lead_ajax()
    {
        if (!$this->input->is_ajax_request()) {
            ajax_access_denied();
        }

        $name = $this->input->post('name');
        $phone = $this->input->post('phone');
        $email = $this->input->post('email');
        
        // Clean phone number format for lead creation
        $phoneClean = preg_replace('/[^0-9+]/', '', $phone);

        // Format extra questionnaire fields into description
        $description = '';
        foreach ($this->input->post() as $key => $val) {
            if (in_array($key, ['name', 'phone', 'email', 'db_lead'])) {
                continue;
            }
            // Skip Perfex CRM global properties like CSRF token
            if ($key === $this->security->get_csrf_token_name()) {
                continue;
            }
            if (!empty($val) && is_string($val)) {
                $cleanKey = ucwords(str_replace(['_', '?', '.'], [' ', '', ''], $key));
                $description .= $cleanKey . ': ' . $val . "\n";
            }
        }

        $lead_data = [
            'name'        => $name ?: 'Excel Lead',
            'phonenumber' => $phoneClean,
            'email'       => $email,
            'description' => trim($description),
            'source'      => 1, // default fallback source ID
            'status'      => 1, // default status ID (usually customer/created)
            'assigned'    => get_staff_user_id() ?: 1,
            'dateadded'   => date('Y-m-d H:i:s')
        ];

        // Find or create 'Ads Excel List' source
        $this->db->where('name', 'Ads Excel List');
        $source = $this->db->get(db_prefix() . 'leads_sources')->row();
        if ($source) {
            $lead_data['source'] = $source->id;
        } else {
            // Check 'Ads WhatsApp'
            $this->db->where('name', 'Ads WhatsApp');
            $source2 = $this->db->get(db_prefix() . 'leads_sources')->row();
            if ($source2) {
                $lead_data['source'] = $source2->id;
            }
        }

        $success = $this->db->insert(db_prefix() . 'leads', $lead_data);
        $insert_id = $this->db->insert_id();

        if ($success && $insert_id) {
            echo json_encode([
                'success' => true,
                'lead_id' => $insert_id,
                'message' => 'Lead successfully imported to CRM.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to import lead. Please try again.'
            ]);
        }
        exit;
    }
}
