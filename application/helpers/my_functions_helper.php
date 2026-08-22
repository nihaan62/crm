<?php

defined('BASEPATH') or exit('No direct script access allowed');

hooks()->add_filter('staff_permissions', 'register_converted_leads_permissions');

function register_converted_leads_permissions($permissions, $data)
{
    $permissions['converted_leads'] = [
        'name'         => 'Converted Leads',
        'capabilities' => [
            'view'   => 'View (Global)',
            'create' => 'Create',
            'edit'   => 'Edit',
            'delete' => 'Delete',
        ]
    ];
    return $permissions;
}
