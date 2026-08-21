<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons tw-mb-4">
                            <h4 class="tw-my-0 tw-font-bold tw-text-xl tw-mb-4 text-primary">
                                <i class="fa fa-check-circle-o"></i> Converted Leads Workspace
                            </h4>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="panel-table-full">
                            <?php
                            $table_data = [
                                'Status',
                                'Name',
                                'Number',
                                'Load Type',
                                'Actions'
                            ];
                            render_datatable(
                                $table_data,
                                'converted_leads',
                                ['customizable-table'],
                                [
                                    'id'                         => 'converted_leads',
                                    'data-last-order-identifier' => 'converted_leads',
                                ]
                            );
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once APPPATH . 'views/admin/leads/loan_details_modal.php'; ?>
<?php include_once APPPATH . 'views/admin/leads/status.php'; ?>
<?php init_tail(); ?>

<script>
$(function() {
    // Initialize Converted Leads DataTable
    initDataTable('.table-converted_leads', admin_url + 'leads/converted_leads_table', undefined, undefined, 'undefined', [1, 'desc']);

    // Handle copying portal links
    $(document).on('click', '.copy-link-btn', function(e) {
        e.preventDefault();
        const btn = $(this);
        const link = btn.data('link');

        // Create temporary input element to copy
        const tempInput = document.createElement('input');
        tempInput.value = link;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);

        // Visual feedback
        btn.removeClass('btn-success').addClass('btn-default').html('<i class="fa fa-check"></i> Copied!');
        setTimeout(function() {
            btn.removeClass('btn-default').addClass('btn-success').html('<i class="fa fa-copy"></i> Copy Link');
        }, 2000);
        
        alert_float('success', 'Client Portal link copied to clipboard!');
    });
});
</script>
</body>
</html>
