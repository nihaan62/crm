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
                $rawHeaders = str_getcsv($lines[0]);
                $headerMap  = [];
                
                // Define unwanted technical columns
                $unwanted = ['id', 'ad_id', 'adset_id', 'campaign_id', 'form_id'];

                foreach ($rawHeaders as $idx => $h) {
                    $hTrim = trim($h);
                    // Skip created_time and technical ID columns
                    if (strtolower($hTrim) === 'created_time' || in_array(strtolower($hTrim), $unwanted)) {
                        continue;
                    }
                    if ($hTrim !== '') {
                        $headerMap[$idx] = $hTrim;
                        $headers[]       = $hTrim;
                    }
                }

                for ($i = 1; $i < count($lines); $i++) {
                    if (trim($lines[$i]) === '') {
                        continue;
                    }
                    $rowValues = str_getcsv($lines[$i]);
                    if (empty(array_filter($rowValues))) {
                        continue;
                    }

                    // Skip the row if it contains keys (e.g., "id" or "created_time" as data)
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
}
