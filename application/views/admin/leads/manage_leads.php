<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content" id="vueApp">
        <div class="row">
            <div class="col-md-12">
                <div class="leads-overview tw-mb-6<?= $isKanBan ? ' hide' : ''; ?>">
                    <h4 class="tw-my-0 tw-font-bold tw-text-xl tw-mb-2">
                        <?= _l('leads'); ?>
                    </h4>
                    <div class="tw-grid tw-gap-2 sm:tw-grid-flow-col sm:tw-auto-cols-max tw-overflow-x-auto">
                        <?php foreach ($summary as $status) { ?>
                            <?php if (isset($status['junk']) || isset($status['lost'])) { ?>
                                <span class="label label-danger" data-toggle="tooltip">
                                    <?= $status['total']; ?>
                                    <?= e($status['name']); ?>
                                    -
                                    <?= $status['percent']; ?>%
                                </span>
                            <?php } else { ?>
                                <button type="button"
                                    @click="extra.leadsRules = <?= app\services\utilities\Js::from($table->findRule('status')->setValue([$status['id']])); ?>"
                                    class="tw-bg-transparent tw-border tw-border-solid tw-border-neutral-300 tw-shadow-sm tw-py-1 tw-px-2 tw-rounded-lg tw-text-sm hover:tw-bg-neutral-200/60 tw-text-neutral-700 hover:tw-text-neutral-600 focus:tw-text-neutral-600 text-left">
                                    <span class="tw-font-semibold tw-mr-1 rtl:tw-ml-1">
                                        <?= e($status['total']); ?>
                                    </span>
                                    <span style="color:<?= e($status['color']); ?>">
                                        <?= e($status['name']); ?>
                                    </span>
                                </button>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>

                <div class="_buttons tw-mb-2">
                    <div class="tw-flex tw-items-center tw-justify-between tw-space-x-2 rtl:tw-space-x-reverse">
                        <div class="tw-flex tw-items-center tw-space-x-1 rtl:tw-space-x-reverse">
                            <a href="#" onclick="init_lead(); return false;" class="btn btn-primary" id="new-lead-btn">
                                <i class="fa-regular fa-plus"></i>
                                <?= _l('new_lead'); ?>
                            </a>

                            <a href="<?= admin_url('leads/switch_kanban/' . $switch_kanban); ?>"
                                class="btn btn-default hidden-xs !tw-px-3" data-toggle="tooltip" data-placement="top"
                                data-title="<?= $switch_kanban == 1 ? _l('leads_switch_to_kanban') : _l('switch_to_list_view'); ?>">
                                <?php if ($switch_kanban == 1) { ?>
                                    <i class="fa-solid fa-grip-vertical"></i>
                                <?php } else { ?>
                                    <i class="fa-solid fa-table-list"></i>
                                <?php } ?>
                            </a>
                            <?php if (is_admin() || get_option('allow_non_admin_members_to_import_leads') == '1') { ?>
                                <a href="<?= admin_url('leads/import'); ?>" class="hidden-xs btn btn-default">
                                    <i class="fa-solid fa-upload tw-mr-1"></i>
                                    <?= _l('import_leads'); ?>
                                </a>
                            <?php } ?>
                            <div class="tw-inline-block" style="min-width: 150px; vertical-align: middle;">
                                <select name="view_batch_name" class="selectpicker" data-width="100%"
                                    data-none-selected-text="All Sections" data-live-search="true">
                                    <option value="">All Sections</option>
                                    <?php
                                    $batches = $this->db->select('DISTINCT(batch_name)')->where('batch_name IS NOT NULL')->where('batch_name !=', '')->order_by('batch_name', 'asc')->get(db_prefix() . 'leads')->result_array();
                                    foreach ($batches as $batch) {
                                        echo '<option value="' . e($batch['batch_name']) . '">' . e($batch['batch_name']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <?php $selected_cat = $this->input->get('category') ?? ''; ?>
                            <div class="tw-inline-block"
                                style="min-width: 180px; vertical-align: middle; margin-left: 5px;">
                                <select name="view_lead_category" class="selectpicker" data-width="100%"
                                    data-none-selected-text="All Leads" data-live-search="true">
                                    <option value="" <?= ($selected_cat === '') ? 'selected' : ''; ?>
                                        data-subtext="Default">All Leads</option>
                                    <option value="converted" <?= ($selected_cat === 'converted') ? 'selected' : ''; ?>>
                                        Converted Leads</option>
                                    <option value="cold_wp" <?= ($selected_cat === 'cold_wp') ? 'selected' : ''; ?>>Cold WP
                                        Messages</option>
                                    <option value="ads_wp" <?= ($selected_cat === 'ads_wp') ? 'selected' : ''; ?>>Ads
                                        WhatsApp Leads</option>
                                    <option value="ads_excel_list" <?= ($selected_cat === 'ads_excel_list') ? 'selected' : ''; ?>>Ads Excel List</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <?php if ($this->session->userdata('leads_kanban_view') == 'true') { ?>
                                <div class="leads-search">
                                    <div data-toggle="tooltip" data-placement="top"
                                        data-title="<?= _l('search_by_tags'); ?>">
                                        <?= render_input('search', '', '', 'search', ['data-name' => 'search', 'onkeyup' => 'leads_kanban();', 'placeholder' => _l('leads_search')], [], 'no-margin') ?>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="tw-inline">
                                    <app-filters id="<?= $table->id(); ?>" view="<?= $table->viewName(); ?>"
                                        :rules="extra.leadsRules || <?= app\services\utilities\Js::from($this->input->get('status') ? $table->findRule('status')->setValue([$this->input->get('status')]) : []); ?>"
                                        :saved-filters="<?= $table->filtersJs(); ?>"
                                        :available-rules="<?= $table->rulesJs(); ?>">
                                    </app-filters>
                                </div>
                            <?php } ?>
                            <?= form_hidden('sort_type'); ?>
                            <?= form_hidden('sort', (get_option('default_leads_kanban_sort') != '' ? get_option('default_leads_kanban_sort_type') : '')); ?>
                        </div>
                    </div>
                </div>
                <div id="normal-leads-container">
                    <div class="<?= $isKanBan ? '' : 'panel_s'; ?>">
                        <div class="<?= $isKanBan ? '' : 'panel-body'; ?>">
                            <div class="tab-content">
                                <?php
                                if ($isKanBan) { ?>
                                    <div class="active kan-ban-tab tw-mt-4" id="kan-ban-tab" style="overflow:auto;">
                                        <div class="kanban-leads-sort">
                                            <span class="bold"><?= _l('leads_sort_by'); ?>:
                                            </span>
                                            <a href="#" onclick="leads_kanban_sort('dateadded'); return false"
                                                class="dateadded">
                                                <?php if (get_option('default_leads_kanban_sort') == 'dateadded') {
                                                    echo '<i class="kanban-sort-icon fa fa-sort-amount-' . strtolower(get_option('default_leads_kanban_sort_type')) . '"></i> ';
                                                } ?>     <?= _l('leads_sort_by_datecreated'); ?>
                                            </a>
                                            |
                                            <a href="#" onclick="leads_kanban_sort('leadorder');return false;"
                                                class="leadorder">
                                                <?php if (get_option('default_leads_kanban_sort') == 'leadorder') {
                                                    echo '<i class="kanban-sort-icon fa fa-sort-amount-' . strtolower(get_option('default_leads_kanban_sort_type')) . '"></i> ';
                                                } ?>     <?= _l('leads_sort_by_kanban_order'); ?>
                                            </a>
                                            |
                                            <a href="#" onclick="leads_kanban_sort('lastcontact');return false;"
                                                class="lastcontact">
                                                <?php if (get_option('default_leads_kanban_sort') == 'lastcontact') {
                                                    echo '<i class="kanban-sort-icon fa fa-sort-amount-' . strtolower(get_option('default_leads_kanban_sort_type')) . '"></i> ';
                                                } ?>     <?= _l('leads_sort_by_lastcontact'); ?>
                                            </a>
                                        </div>
                                        <div class="row">
                                            <div class="container-fluid leads-kan-ban">
                                                <div id="kan-ban"></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div class="row" id="leads-table">
                                        <div class="col-md-12">
                                            <a href="#" data-toggle="modal" data-table=".table-leads"
                                                data-target="#leads_bulk_actions"
                                                class="hide bulk-actions-btn table-btn"><?= _l('bulk_actions'); ?></a>
                                            <div class="modal fade bulk_actions" id="leads_bulk_actions" tabindex="-1"
                                                role="dialog">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close"><span
                                                                    aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title">
                                                                <?= _l('bulk_actions'); ?>
                                                            </h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php if (is_admin() || staff_can('delete', 'leads')) { ?>
                                                                <div class="checkbox checkbox-danger">
                                                                    <input type="checkbox" name="mass_delete" id="mass_delete">
                                                                    <label for="mass_delete"><?= _l('mass_delete'); ?></label>
                                                                </div>
                                                                <hr class="mass_delete_separator" />
                                                            <?php } ?>
                                                            <div id="bulk_change">
                                                                <div class="form-group">
                                                                    <div class="checkbox checkbox-primary checkbox-inline">
                                                                        <input type="checkbox" name="leads_bulk_mark_lost"
                                                                            id="leads_bulk_mark_lost" value="1">
                                                                        <label for="leads_bulk_mark_lost">
                                                                            <?= _l('lead_mark_as_lost'); ?>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <?= render_select('move_to_status_leads_bulk', $statuses, ['id', 'name'], 'ticket_single_change_status'); ?>
                                                                <?= render_select('move_to_source_leads_bulk', $sources, ['id', 'name'], 'lead_source');
                                                                echo render_datetime_input('leads_bulk_last_contact', 'leads_dt_last_contact');
                                                                echo render_select('assign_to_leads_bulk', $staff, ['staffid', ['firstname', 'lastname']], 'leads_dt_assigned');
                                                                ?>
                                                                <div class="form-group">
                                                                    <?= '<p><b><i class="fa fa-tag" aria-hidden="true"></i> ' . _l('tags') . ':</b></p>'; ?>
                                                                    <input type="text" class="tagsinput" id="tags_bulk"
                                                                        name="tags_bulk" value="" data-role="tagsinput">
                                                                </div>
                                                                <hr />
                                                                <div class="form-group no-mbot">
                                                                    <div class="radio radio-primary radio-inline">
                                                                        <input type="radio" name="leads_bulk_visibility"
                                                                            id="leads_bulk_public" value="public">
                                                                        <label for="leads_bulk_public">
                                                                            <?= _l('lead_public'); ?>
                                                                        </label>
                                                                    </div>
                                                                    <div class="radio radio-primary radio-inline">
                                                                        <input type="radio" name="leads_bulk_visibility"
                                                                            id="leads_bulk_private" value="private">
                                                                        <label for="leads_bulk_private">
                                                                            <?= _l('private'); ?>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default"
                                                                data-dismiss="modal"><?= _l('close'); ?></button>
                                                            <a href="#" class="btn btn-success"
                                                                style="background-color: #25d366; border-color: #25d366; color: #fff;"
                                                                onclick="send_cold_wp_bulk_action(); return false;"><i
                                                                    class="fa fa-whatsapp"></i> Send Cold WP Message</a>
                                                            <a href="#" class="btn btn-primary"
                                                                onclick="leads_bulk_action(this); return false;"><?= _l('confirm'); ?></a>
                                                        </div>
                                                    </div>
                                                    <!-- /.modal-content -->
                                                </div>
                                                <!-- /.modal-dialog -->
                                            </div>
                                            <!-- /.modal -->
                                            <?php

                                            $table_data = [];
                                            $_table_data = [
                                                '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="leads"><label></label></div>',
                                                [
                                                    'name' => _l('the_number_sign'),
                                                    'th_attrs' => ['class' => 'toggleable', 'id' => 'th-number'],
                                                ],
                                                [
                                                    'name' => _l('leads_dt_name'),
                                                    'th_attrs' => ['class' => 'toggleable', 'id' => 'th-name'],
                                                ],
                                            ];
                                            if (is_gdpr() && get_option('gdpr_enable_consent_for_leads') == '1') {
                                                $_table_data[] = [
                                                    'name' => _l('gdpr_consent') . ' (' . _l('gdpr_short') . ')',
                                                    'th_attrs' => ['id' => 'th-consent', 'class' => 'not-export'],
                                                ];
                                            }
                                            $_table_data[] = [
                                                'name' => _l('lead_company'),
                                                'th_attrs' => ['class' => 'toggleable', 'id' => 'th-company'],
                                            ];
                                            $_table_data[] = [
                                                'name' => _l('leads_dt_email'),
                                                'th_attrs' => ['class' => 'toggleable', 'id' => 'th-email'],
                                            ];
                                            $_table_data[] = [
                                                'name' => _l('leads_dt_phonenumber'),
                                                'th_attrs' => ['class' => 'toggleable', 'id' => 'th-phone'],
                                            ];
                                            $_table_data[] = [
                                                'name' => _l('leads_dt_lead_value'),
                                                'th_attrs' => ['class' => 'toggleable', 'id' => 'th-lead-value'],
                                            ];
                                            $_table_data[] = [
                                                'name' => _l('tags'),
                                                'th_attrs' => ['class' => 'toggleable', 'id' => 'th-tags'],
                                            ];
                                            $_table_data[] = [
                                                'name' => _l('leads_dt_assigned'),
                                                'th_attrs' => ['class' => 'toggleable', 'id' => 'th-assigned'],
                                            ];
                                            $_table_data[] = [
                                                'name' => _l('leads_dt_status'),
                                                'th_attrs' => ['class' => 'toggleable', 'id' => 'th-status'],
                                            ];
                                            $_table_data[] = [
                                                'name' => 'Notes',
                                                'th_attrs' => ['class' => 'toggleable', 'id' => 'th-notes'],
                                            ];
                                            $_table_data[] = [
                                                'name' => _l('leads_source'),
                                                'th_attrs' => ['class' => 'toggleable', 'id' => 'th-source'],
                                            ];
                                            $_table_data[] = [
                                                'name' => _l('leads_dt_last_contact'),
                                                'th_attrs' => ['class' => 'toggleable', 'id' => 'th-last-contact'],
                                            ];
                                            $_table_data[] = [
                                                'name' => _l('leads_dt_datecreated'),
                                                'th_attrs' => ['class' => 'date-created toggleable', 'id' => 'th-date-created'],
                                            ];

                                            if (is_admin()) {
                                                $_table_data[] = [
                                                    'name' => 'Lead clicked',
                                                    'th_attrs' => ['class' => 'toggleable', 'id' => 'th-lead-clicked'],
                                                ];
                                            }

                                            $_table_data[] = [
                                                'name' => 'Details',
                                                'th_attrs' => ['class' => 'toggleable', 'id' => 'th-details'],
                                            ];

                                            foreach ($_table_data as $_t) {
                                                array_push($table_data, $_t);
                                            }
                                            $custom_fields = get_custom_fields('leads', ['show_on_table' => 1]);

                                            foreach ($custom_fields as $field) {
                                                array_push($table_data, [
                                                    'name' => $field['name'],
                                                    'th_attrs' => ['data-type' => $field['type'], 'data-custom-field' => 1],
                                                ]);
                                            }
                                            $table_data = hooks()->apply_filters('leads_table_columns', $table_data);
                                            ?>
                                            <div class="panel-table-full">
                                                <?php
                                                render_datatable(
                                                    $table_data,
                                                    'leads',
                                                    ['customizable-table number-index-2'],
                                                    [
                                                        'id' => 'leads',
                                                        'data-last-order-identifier' => 'leads',
                                                        'data-default-order' => get_table_last_order('leads'),
                                                    ]
                                                );
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="excel-leads-container" style="display: none;">
                    <div class="panel_s">
                        <div class="panel-body" id="excel-leads-content">
                            <div class="text-center" style="padding: 40px;">
                                <i class="fa-solid fa-circle-notch fa-spin fa-2x" style="color: #107C41;"></i>
                                <p style="margin-top: 10px; font-weight: 600; color: #666;">Loading Excel Leads...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script id="hidden-columns-table-leads" type="text/json">
    <?= get_staff_meta(get_staff_user_id(), 'hidden-columns-table-leads'); ?>
</script>
<?php include_once APPPATH . 'views/admin/leads/status.php'; ?>
<?php include_once APPPATH . 'views/admin/leads/loan_details_modal.php'; ?>

<!-- Call Options Modal -->
<div class="modal fade" id="lead_call_options_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document" style="margin-top: 15%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center bold"><i class="fa fa-phone"></i> Call Options</h4>
            </div>
            <div class="modal-body text-center" style="padding: 20px;">
                <p style="font-size: 15px; margin-bottom: 20px;">Select how you want to contact <br><strong
                        id="call_opt_phone" class="text-primary"></strong></p>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="#" id="normal_call_link" class="btn btn-primary btn-block btn-lg"
                        style="font-weight: bold; margin-bottom: 5px;">
                        <i class="fa fa-phone-square"></i> Normal Call
                    </a>
                    <a href="#" id="whatsapp_call_link" target="_blank" class="btn btn-success btn-block btn-lg"
                        style="font-weight: bold; background-color: #25d366; border-color: #25d366; color: #fff;">
                        <i class="fa fa-whatsapp"></i> WhatsApp Call
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- WhatsApp Script Selection Modal -->
<div class="modal fade" id="whatsapp_script_select_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title bold"><i class="fa fa-whatsapp text-success"></i> Send WhatsApp Message</h4>
            </div>
            <form id="lead_whatsapp_modal_form" enctype="multipart/form-data">
                <div class="modal-body" style="padding: 20px;">
                    <p style="font-size: 14px; margin-bottom: 15px;">Sending message to <strong id="wp_script_lead_name"
                            class="text-primary"></strong> (<span id="wp_script_lead_phone" class="text-muted"></span>)
                    </p>

                    <div class="form-group">
                        <label for="modal_script_select" class="control-label">Load Template Script</label>
                        <select id="modal_script_select" class="form-control" style="width: 100%;">
                            <option value="">-- Choose Script --</option>
                            <?php
                            $db_templates = $this->db->order_by('title', 'asc')->get(db_prefix() . 'cold_wp_templates')->result_array();
                            foreach ($db_templates as $tmpl) {
                                ?>
                                <option value="<?= $tmpl['id']; ?>" data-message="<?= e($tmpl['message_text']); ?>"
                                    data-image="<?= $tmpl['image_path'] ? base_url($tmpl['image_path']) : ''; ?>"
                                    data-raw-image="<?= $tmpl['image_path']; ?>">
                                    <?= e($tmpl['title']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="modal_message_text" class="control-label">Message Content</label>
                        <textarea id="modal_message_text" name="message_text" class="form-control" rows="6"
                            placeholder="Type your message here..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="modal_media_image" class="control-label">Upload Custom Media Image
                            (Optional)</label>
                        <input type="file" id="modal_media_image" name="image" class="form-control" accept="image/*">
                        <input type="hidden" id="modal_image_path" name="image_path" value="">
                    </div>

                    <div id="modal_image_preview_container"
                        style="display: none; text-align: center; border: 1px dashed #ddd; padding: 10px; border-radius: 4px; margin-top: 10px;">
                        <p class="bold text-muted" style="margin-bottom: 5px;"><i class="fa fa-image"></i> Media Preview
                        </p>
                        <img id="modal_image_preview" src="#"
                            style="max-height: 150px; max-width: 100%; object-fit: contain; border-radius: 4px;" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success"
                        style="background-color:#25d366; border-color:#25d366; color:#fff; font-weight:bold;">
                        <i class="fa fa-paper-plane"></i> Send via API
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    var openLeadID = '<?= e($leadid); ?>';
    $(function () {
        leads_kanban();
        $('#leads_bulk_mark_lost').on('change', function () {
            $('#move_to_status_leads_bulk').prop('disabled', $(this).prop('checked') == true);
            $('#move_to_status_leads_bulk').selectpicker('refresh')
        });
        $('#move_to_status_leads_bulk').on('change', function () {
            if ($(this).selectpicker('val') != '') {
                $('#leads_bulk_mark_lost').prop('disabled', true);
                $('#leads_bulk_mark_lost').prop('checked', false);
            } else {
                $('#leads_bulk_mark_lost').prop('disabled', false);
            }
        });

        // Handle phone link clicks
        $(document).on('click', '.lead-phone-click', function (e) {
            e.preventDefault();
            const phone = $(this).data('phone');
            const leadId = $(this).data('id');

            let cleanPhone = phone.toString().replace(/[^0-9+]/g, '');

            $('#call_opt_phone').text(phone);
            $('#normal_call_link').attr('href', 'tel:' + cleanPhone).data('id', leadId);
            $('#whatsapp_call_link').attr('href', 'https://api.whatsapp.com/send?phone=' + cleanPhone).data('id', leadId);

            $('#lead_call_options_modal').modal('show');

            if (leadId) {
                $.post(admin_url + 'leads/track_click/' + leadId + '/1');
            }
        });

        // Handle clicks inside call options modal (track click 2)
        $(document).on('click', '#normal_call_link, #whatsapp_call_link', function () {
            const leadId = $(this).data('id');
            if (leadId) {
                $.post(admin_url + 'leads/track_click/' + leadId + '/2');
            }
        });

        let activeWpButton = null;

        // Handle single WhatsApp send button click - opens script selection modal
        $(document).on('click', '.send-single-wp', function (e) {
            e.preventDefault();
            activeWpButton = $(this);
            const name = activeWpButton.data('name');
            const company = activeWpButton.data('company');
            const phone = activeWpButton.data('phone');
            const cleanName = (name === '/' || name === '' || !name) ? (company ? company : 'there') : name;

            // Reset modal fields
            $('#lead_whatsapp_modal_form')[0].reset();
            $('#modal_script_select').val('').trigger('change');
            $('#modal_image_preview_container').hide();
            $('#modal_image_preview').attr('src', '#');
            $('#modal_image_path').val('');

            $('#wp_script_lead_name').text(cleanName);
            $('#wp_script_lead_phone').text(phone);
            $('#whatsapp_script_select_modal').modal('show');
        });

        // Handle script select change in the modal
        $(document).on('change', '#modal_script_select', function () {
            const selectedOpt = $(this).find('option:selected');
            if (selectedOpt.val() === '') {
                $('#modal_message_text').val('');
                $('#modal_image_preview_container').hide();
                $('#modal_image_path').val('');
                return;
            }

            const rawMessage = selectedOpt.data('message') || '';
            const imageUrl = selectedOpt.data('image') || '';
            const rawImagePath = selectedOpt.data('raw-image') || '';

            if (activeWpButton) {
                const name = activeWpButton.data('name');
                const company = activeWpButton.data('company');
                const cleanName = (name === '/' || name === '' || !name) ? (company ? company : 'there') : name;

                const formattedMessage = rawMessage.replace(/{name}/g, cleanName);
                $('#modal_message_text').val(formattedMessage);
            } else {
                $('#modal_message_text').val(rawMessage);
            }

            if (imageUrl) {
                $('#modal_image_preview').attr('src', imageUrl);
                $('#modal_image_preview_container').show();
                $('#modal_image_path').val(rawImagePath);
            } else {
                $('#modal_image_preview_container').hide();
                $('#modal_image_path').val('');
            }
        });

        // Handle image selection preview inside the modal
        $(document).on('change', '#modal_media_image', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $('#modal_image_preview').attr('src', e.target.result);
                    $('#modal_image_preview_container').show();
                }
                reader.readAsDataURL(file);
            }
        });

        // Handle modal form submit to send via background API
        $(document).on('submit', '#lead_whatsapp_modal_form', function (e) {
            e.preventDefault();
            if (!activeWpButton) return;

            const btn = activeWpButton;
            const leadId = btn.data('id');
            const phone = btn.data('phone');
            const message = $('#modal_message_text').val().trim();

            if (message === '') {
                alert_float('warning', 'Please enter a message template.');
                return;
            }

            // Hide the modal
            $('#whatsapp_script_select_modal').modal('hide');

            const formData = new FormData(this);
            formData.append('lead_id', leadId);
            formData.append('phone_number', phone);

            if (typeof (csrfData) !== 'undefined') {
                formData.append(csrfData.token_name, csrfData.hash);
            }

            // Send AJAX
            $.ajax({
                url: admin_url + 'cold_wp/log_send',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    const res = JSON.parse(response);
                    if (res.success) {
                        // Change button appearance to grey/Re-send (clickable)
                        btn.removeClass('btn-success')
                            .addClass('btn-default')
                            .css({
                                'background-color': '#dcdcdc',
                                'border-color': '#dcdcdc',
                                'color': '#777'
                            })
                            .attr('title', 'sended')
                            .html('<i class="fa fa-refresh"></i> Re-send');
                    } else {
                        alert_float('danger', res.message);
                    }
                },
                error: function () {
                    alert_float('danger', 'Failed to log message status on the server.');
                }
            });

            activeWpButton = null;
        });

        // Listen to DataTables pre-XHR event to append the custom filter parameter
        $('table.table-leads').on('preXhr.dt', function (e, settings, data) {
            data.batch_name = $('select[name="view_batch_name"]').val();
            data.lead_category = $('select[name="view_lead_category"]').val();
        });

        function handleLeadCategoryChange() {
            var category = $('select[name="view_lead_category"]').val();

            // Toggle the Open Google Sheet action button
            if (category === 'ads_excel_list') {
                $('#excel-open-sheet-btn').show();
            } else {
                $('#excel-open-sheet-btn').hide();
            }

            // Always display standard leads layout (excel AJAX sub-view is no longer swapped in)
            $('#excel-leads-container').hide();
            $('.leads-overview').show();
            $('#normal-leads-container').show();
            $('#new-lead-btn').show();

            // If we are on Kanban view
            if ($('#kan-ban').length > 0) {
                var hiddenInput = $('#kanban-params input[name="lead_category"]');
                if (hiddenInput.length === 0) {
                    hiddenInput = $('<input>').attr({
                        type: 'hidden',
                        name: 'lead_category'
                    });
                    $('#kanban-params').append(hiddenInput);
                }
                hiddenInput.val(category);
                leads_kanban();
            } else {
                // If we are on List view (DataTables)
                if ($.fn.DataTable.isDataTable('.table-leads')) {
                    $('.table-leads').DataTable().ajax.reload();
                }
            }
        }

        $('body').on('change', 'select[name="view_lead_category"]', function () {
            handleLeadCategoryChange();
        });

        // Trigger on load if a category parameter exists
        if ($('select[name="view_lead_category"]').val() !== '') {
            handleLeadCategoryChange();
        }

        // When the batch name filter select changes
        $('body').on('change', 'select[name="view_batch_name"]', function () {
            var val = $(this).val();
            // 1. If we are on Kanban view
            if ($('#kan-ban').length > 0) {
                // Find or create the hidden input in #kanban-params
                var hiddenInput = $('#kanban-params input[name="batch_name"]');
                if (hiddenInput.length === 0) {
                    hiddenInput = $('<input>').attr({
                        type: 'hidden',
                        name: 'batch_name'
                    });
                    $('#kanban-params').append(hiddenInput);
                }
                hiddenInput.val(val);
                leads_kanban();
            } else {
                // 2. If we are on List view (DataTables)
                if ($.fn.DataTable.isDataTable('.table-leads')) {
                    $('.table-leads').DataTable().ajax.reload();
                }
            }
        });
    });

    function send_cold_wp_bulk_action() {
        var ids = [];
        var rows = $('.table-leads').find('tbody tr');
        $.each(rows, function () {
            var checkbox = $(this).find('td:first-child input[type="checkbox"]');
            if (checkbox.prop('checked') === true) {
                ids.push(checkbox.val());
            }
        });

        if (ids.length === 0) {
            alert_float('warning', 'Please select at least one lead.');
            return;
        }

        window.location.href = admin_url + 'cold_wp?ids=' + ids.join(',');
    }
</script>

<!-- Lead Chat Notes Modal -->
<div class="modal fade" id="leadChatNotesModal" tabindex="-1" role="dialog" aria-labelledby="leadChatNotesModalLabel">
    <div class="modal-dialog modal-md" role="document" style="max-width: 550px;">
        <div class="modal-content"
            style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <!-- Modal Header -->
            <div class="modal-header"
                style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: white; padding: 16px 20px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    style="color: white; opacity: 0.9; font-size: 24px; margin-top: -2px;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="leadChatNotesModalLabel"
                    style="font-weight: 700; display: flex; align-items: center; gap: 8px;">
                    <i class="fa fa-comments" style="font-size: 20px;"></i>
                    <span>Lead Notes Chat</span>
                </h4>
            </div>

            <!-- Modal Body (Chat Canvas) -->
            <div class="modal-body" style="background: #f3f4f6; padding: 0; position: relative;">
                <!-- Chat Bubbles History -->
                <div class="lead-chat-body"
                    style="height: 380px; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 12px; scroll-behavior: smooth;">
                    <!-- Loading state -->
                    <div class="text-center chat-loading-state" style="margin-top: 100px;">
                        <i class="fa fa-spinner fa-spin fa-2x" style="color: #6366f1;"></i>
                        <p style="margin-top: 8px; color: #6b7280; font-weight: 600;">Loading chat history...</p>
                    </div>
                </div>

                <!-- Voice Recording Overlay -->
                <div class="voice-recording-overlay"
                    style="display: none; position: absolute; bottom: 0; left: 0; right: 0; background: rgba(255,255,255,0.95); padding: 12px 20px; align-items: center; justify-content: space-between; border-top: 1px solid #e5e7eb; z-index: 10; animation: slideUp 0.2s ease-out;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span class="recording-dot"
                            style="width: 10px; height: 10px; background-color: #ef4444; border-radius: 50%; display: inline-block; animation: blink 1s infinite;"></span>
                        <span style="font-weight: 600; color: #374151; font-size: 13px;">Recording Audio...</span>
                        <span class="recording-timer"
                            style="font-weight: 700; color: #ef4444; font-size: 13px;">00:00</span>
                    </div>
                    <button type="button" class="btn btn-danger btn-xs stop-recording-btn"
                        style="padding: 4px 10px; border-radius: 20px; font-weight: 600;">
                        <i class="fa fa-stop"></i> Stop & Send
                    </button>
                </div>
            </div>

            <!-- Modal Footer (Chat Bar) -->
            <div class="modal-footer"
                style="padding: 12px 16px; background: white; border-top: 1px solid #e5e7eb; display: flex; align-items: center; gap: 8px;">
                <!-- Media Upload Button -->
                <button type="button" class="btn btn-default btn-chat-media"
                    style="padding: 8px 12px; border-radius: 50%; width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border: 1px solid #d1d5db; background: #f9fafb; color: #4b5563;"
                    title="Upload Media">
                    <i class="fa fa-paperclip" style="font-size: 15px;"></i>
                </button>
                <input type="file" id="leadChatMediaInput" style="display: none;" />

                <!-- Textarea Message Input -->
                <textarea id="leadChatMessageInput" rows="1" placeholder="Type notes here..."
                    style="flex-grow: 1; border-radius: 20px; border: 1px solid #d1d5db; padding: 8px 16px; resize: none; max-height: 80px; font-size: 13px; line-height: 1.4; outline: none; background: #f9fafb; transition: all 0.2s;"
                    oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"></textarea>

                <!-- Voice Recording Button -->
                <button type="button" class="btn btn-default btn-chat-mic"
                    style="padding: 8px 12px; border-radius: 50%; width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border: 1px solid #d1d5db; background: #f9fafb; color: #ef4444;"
                    title="Record Voice Note">
                    <i class="fa fa-microphone" style="font-size: 15px;"></i>
                </button>

                <!-- Send Button -->
                <button type="button" class="btn btn-primary btn-chat-send"
                    style="padding: 8px 12px; border-radius: 50%; width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; background: #4f46e5; border: none; color: white;"
                    title="Send Notes">
                    <i class="fa fa-paper-plane" style="font-size: 14px;"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Chat Styles */
    .lead-chat-bubble {
        max-width: 75%;
        padding: 10px 14px;
        border-radius: 16px;
        font-size: 13px;
        line-height: 1.4;
        position: relative;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .lead-chat-bubble.staff {
        align-self: flex-end;
        background: #6366f1;
        color: white;
        border-bottom-right-radius: 4px;
    }

    .lead-chat-bubble.others {
        align-self: flex-start;
        background: white;
        color: #1f2937;
        border-bottom-left-radius: 4px;
        border: 1px solid #e5e7eb;
    }

    .lead-chat-bubble .chat-meta {
        font-size: 10px;
        margin-top: 4px;
        opacity: 0.8;
        text-align: right;
        display: block;
    }

    .lead-chat-bubble.others .chat-meta {
        color: #6b7280;
    }

    .lead-chat-bubble.staff .chat-meta {
        color: #e0e7ff;
    }

    .lead-chat-bubble .chat-sender {
        font-weight: 700;
        font-size: 11px;
        display: block;
        margin-bottom: 2px;
    }

    .lead-chat-bubble.staff .chat-sender {
        color: #c7d2fe;
    }

    .lead-chat-bubble.others .chat-sender {
        color: #4f46e5;
    }

    /* Audio player wrapper */
    .chat-audio-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 4px;
    }

    .chat-audio-wrap audio {
        height: 32px;
        max-width: 220px;
        outline: none;
    }

    /* Media preview container */
    .chat-media-preview img {
        max-width: 100%;
        border-radius: 8px;
        margin-top: 4px;
        max-height: 150px;
        object-fit: cover;
    }

    @keyframes blink {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.3;
        }
    }

    @keyframes slideUp {
        from {
            transform: translateY(100%);
        }

        to {
            transform: translateY(0);
        }
    }
</style>

<script>
    $(function () {
        var activeChatLeadId = null;
        var mediaRecorder = null;
        var audioChunks = [];
        var recordInterval = null;
        var recordSeconds = 0;

        // Open chat modal
        $('body').on('click', '.lead-chat-notes-btn', function () {
            var btn = $(this);
            activeChatLeadId = btn.data('lead-id');
            var name = btn.data('lead-name');

            $('#leadChatNotesModalLabel span').text('Notes Chat - ' + name);
            $('#leadChatMessageInput').val('');
            $('.lead-chat-body').html('<div class="text-center chat-loading-state" style="margin-top: 100px;"><i class="fa fa-spinner fa-spin fa-2x" style="color: #6366f1;"></i><p style="margin-top: 8px; color: #6b7280; font-weight: 600;">Loading chat history...</p></div>');

            $('#leadChatNotesModal').modal('show');

            // Fetch chat history
            $.getJSON(admin_url + 'leads/get_chat_history_ajax/' + activeChatLeadId, function (res) {
                if (res.success) {
                    var body = $('.lead-chat-body');
                    body.empty();

                    if (res.history.length === 0) {
                        body.html('<div class="text-center no-history" style="margin-top: 100px; color: #6b7280;"><i class="fa fa-comments-o fa-3x" style="color: #d1d5db; margin-bottom: 12px;"></i><p style="font-weight: 600; font-size:14px;">No notes logged yet.</p><p style="font-size:12px;">Type a message or record a voice note below.</p></div>');
                    } else {
                        $.each(res.history, function (idx, item) {
                            appendChatBubble(item, res.current_staff_id);
                        });
                        scrollChatToBottom();
                    }
                } else {
                    alert_float('danger', 'Failed to load notes history.');
                }
            });
        });

        // Helper to format date
        function formatChatDate(dateStr) {
            if (!dateStr) return '';
            var d = new Date(dateStr.replace(/-/g, "/"));
            var hours = d.getHours();
            var minutes = d.getMinutes();
            var ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12;
            minutes = minutes < 10 ? '0' + minutes : minutes;
            return hours + ':' + minutes + ' ' + ampm;
        }

        // Append chat bubble to UI
        function appendChatBubble(item, currentStaffId) {
            $('.no-history').remove();
            var body = $('.lead-chat-body');
            var isSelf = (item.addedfrom == currentStaffId);

            var bubbleClass = isSelf ? 'staff' : 'others';
            var bubble = $('<div class="lead-chat-bubble ' + bubbleClass + '"></div>');

            // Add sender name
            bubble.append('<span class="chat-sender">' + (isSelf ? 'You' : item.staff_name) + '</span>');

            if (item.type === 'text') {
                bubble.append('<div class="chat-text-content">' + nl2br(escapeHtml(item.content)) + '</div>');
            } else {
                // Media attachment
                var isAudio = (item.filetype && item.filetype.indexOf('audio') !== -1) || item.file_name.endsWith('.wav') || item.file_name.endsWith('.mp3');
                var isImage = (item.filetype && item.filetype.indexOf('image') !== -1) || item.file_name.endsWith('.jpg') || item.file_name.endsWith('.jpeg') || item.file_name.endsWith('.png') || item.file_name.endsWith('.gif');

                if (isAudio) {
                    bubble.append('<div class="chat-audio-wrap"><audio controls src="' + item.file_url + '"></audio></div>');
                } else if (isImage) {
                    bubble.append('<div class="chat-media-preview"><a href="' + item.file_url + '" target="_blank"><img src="' + item.file_url + '" alt="media"/></a></div>');
                } else {
                    bubble.append('<div class="chat-file-wrap"><a href="' + item.file_url + '" target="_blank" style="color: inherit; text-decoration: underline; font-weight: 600;"><i class="fa fa-file"></i> ' + escapeHtml(item.file_name) + '</a></div>');
                }
            }

            // Add timestamp
            bubble.append('<span class="chat-meta">' + formatChatDate(item.dateadded) + '</span>');

            body.append(bubble);
        }

        function scrollChatToBottom() {
            var body = $('.lead-chat-body');
            if (body.length > 0 && body[0].scrollHeight) {
                body.scrollTop(body[0].scrollHeight);
            }
        }

        function escapeHtml(text) {
            if (!text) return '';
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function nl2br(str) {
            return str.replace(/(?:\r\n|\r|\n)/g, '<br>');
        }

        // Send Text message notes
        $('.btn-chat-send').click(function () {
            var input = $('#leadChatMessageInput');
            var val = input.val().trim();
            if (val === '') return;

            var sendBtn = $(this);
            sendBtn.prop('disabled', true);

            $.post(admin_url + 'leads/add_chat_note_ajax', {
                lead_id: activeChatLeadId,
                description: val
            }, function (res) {
                sendBtn.prop('disabled', false);
                if (res.success) {
                    input.val('');
                    input.css('height', ''); // reset height
                    appendChatBubble(res.note, res.note.addedfrom);
                    scrollChatToBottom();
                    updateTableRowNotesCount(activeChatLeadId);
                } else {
                    alert_float('danger', res.message || 'Failed to send note.');
                }
            }, 'json');
        });

        // Enter key to send message
        $('#leadChatMessageInput').keydown(function (e) {
            if (e.which === 13 && !e.shiftKey) {
                e.preventDefault();
                $('.btn-chat-send').click();
            }
        });

        // File attachments upload
        $('.btn-chat-media').click(function () {
            $('#leadChatMediaInput').click();
        });

        $('#leadChatMediaInput').change(function () {
            var fileInput = this;
            if (fileInput.files.length === 0) return;

            var file = fileInput.files[0];
            var formData = new FormData();
            formData.append('lead_id', activeChatLeadId);
            formData.append('file', file);

            var body = $('.lead-chat-body');
            var placeholder = $('<div class="lead-chat-bubble staff chat-uploading-placeholder" style="opacity: 0.7;"><i class="fa fa-spinner fa-spin"></i> Uploading file...</div>');
            body.append(placeholder);
            scrollChatToBottom();

            $.ajax({
                url: admin_url + 'leads/upload_chat_media_ajax',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (res) {
                    placeholder.remove();
                    fileInput.value = '';
                    if (res.success) {
                        appendChatBubble(res.file, res.file.addedfrom);
                        scrollChatToBottom();
                        updateTableRowNotesCount(activeChatLeadId);
                    } else {
                        alert_float('danger', res.message || 'Failed to upload media file.');
                    }
                },
                error: function () {
                    placeholder.remove();
                    fileInput.value = '';
                    alert_float('danger', 'Error uploading file.');
                }
            });
        });

        function updateTableRowNotesCount(leadId) {
            var rowBtn = $('.lead-chat-notes-btn[data-lead-id="' + leadId + '"]');
            if (rowBtn.length > 0) {
                var badge = rowBtn.find('.badge');
                var currentCount = badge.length > 0 ? parseInt(badge.text()) : 0;
                var newCount = currentCount + 1;

                if (badge.length > 0) {
                    badge.text(newCount);
                } else {
                    rowBtn.append(' <span class="badge" style="background: #6366f1; color: white; padding: 2px 6px; font-size: 10px; border-radius: 10px; margin-left: 2px;">' + newCount + '</span>');
                }
            }
        }

        // Audio Voice Note Recording
        $('.btn-chat-mic').click(function () {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert_float('warning', 'Your browser does not support audio recording.');
                return;
            }

            navigator.mediaDevices.getUserMedia({ audio: true }).then(function (stream) {
                audioChunks = [];
                mediaRecorder = new MediaRecorder(stream);

                mediaRecorder.ondataavailable = function (e) {
                    audioChunks.push(e.data);
                };

                mediaRecorder.onstop = function () {
                    var audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
                    var audioFile = new File([audioBlob], "voice_note_" + new Date().getTime() + ".wav", { type: "audio/wav" });

                    var formData = new FormData();
                    formData.append('lead_id', activeChatLeadId);
                    formData.append('file', audioFile);

                    var body = $('.lead-chat-body');
                    var placeholder = $('<div class="lead-chat-bubble staff chat-uploading-placeholder" style="opacity: 0.7;"><i class="fa fa-spinner fa-spin"></i> Uploading voice note...</div>');
                    body.append(placeholder);
                    scrollChatToBottom();

                    $.ajax({
                        url: admin_url + 'leads/upload_chat_media_ajax',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function (res) {
                            placeholder.remove();
                            if (res.success) {
                                appendChatBubble(res.file, res.file.addedfrom);
                                scrollChatToBottom();
                                updateTableRowNotesCount(activeChatLeadId);
                            } else {
                                alert_float('danger', res.message || 'Failed to save voice note.');
                            }
                        },
                        error: function () {
                            placeholder.remove();
                            alert_float('danger', 'Error uploading voice note.');
                        }
                    });

                    stream.getTracks().forEach(track => track.stop());
                };

                mediaRecorder.start();
                $('.voice-recording-overlay').css('display', 'flex');

                recordSeconds = 0;
                $('.recording-timer').text('00:00');
                clearInterval(recordInterval);
                recordInterval = setInterval(function () {
                    recordSeconds++;
                    var m = Math.floor(recordSeconds / 60);
                    var s = recordSeconds % 60;
                    m = m < 10 ? '0' + m : m;
                    s = s < 10 ? '0' + s : s;
                    $('.recording-timer').text(m + ':' + s);
                }, 1000);

            }).catch(function (err) {
                console.error(err);
                alert_float('danger', 'Microphone access denied or not available.');
            });
        });

        $('.stop-recording-btn').click(function () {
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                mediaRecorder.stop();
                clearInterval(recordInterval);
                $('.voice-recording-overlay').hide();
            }
        });
    });
</script>
</body>

</html>