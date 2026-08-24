<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <!-- Left Column: Template & Settings -->
            <div class="col-md-5">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                            <h4 class="tw-my-0 tw-font-bold tw-text-lg tw-text-neutral-700">
                                <i class="fa fa-whatsapp text-success" style="color: #25d366;"></i> WhatsApp Cold Campaign
                            </h4>
                            <a href="<?= admin_url('cold_wp/logs'); ?>" class="btn btn-default btn-sm">
                                <i class="fa fa-list"></i> View Message Logs
                            </a>
                        </div>
                        <hr class="hr-panel-separator" />

                        <!-- Form for template and image -->
                        <form id="campaign_form" enctype="multipart/form-data">
                            <input type="hidden" id="template_id" name="template_id" value="">
                            <div class="form-group">
                                <label for="template_title" class="control-label">Template Title</label>
                                <input type="text" id="template_title" name="title" class="form-control" placeholder="e.g. Loan Script 1">
                            </div>

                            <div class="form-group">
                                <label for="message_template" class="control-label">Message Template</label>
                                <textarea id="message_template" name="message_text" class="form-control" rows="8" placeholder="Hello {name}, we have an exciting loan offer for you!"></textarea>
                                <span class="text-muted"><i class="fa fa-info-circle"></i> Use placeholder <code>{name}</code> to personalize the message automatically.</span>
                            </div>

                            <div class="form-group">
                                <label for="media_image" class="control-label">Campaign Media Image (Optional)</label>
                                <input type="file" id="media_image" name="image" class="form-control" accept="image/*">
                                <span class="text-muted"><i class="fa fa-info-circle"></i> Since WhatsApp API does not support direct image pre-attachment via links, uploaded images will be shown below for easy copy-pasting (Ctrl+V) into WhatsApp.</span>
                            </div>

                            <!-- Save / Reset Buttons -->
                            <div class="form-group tw-mt-4 tw-flex tw-space-x-2">
                                <button type="button" class="btn btn-info tw-flex-1" id="save_template_btn">
                                    <i class="fa fa-save"></i> Save Template
                                </button>
                                <button type="button" class="btn btn-default" id="reset_template_btn" style="display: none;">
                                    <i class="fa fa-refresh"></i> Reset
                                </button>
                            </div>

                            <!-- Image Preview Area -->
                            <div id="image_preview_container" style="display: none; margin-top: 15px; border: 1px dashed #ddd; padding: 10px; text-align: center; border-radius: 4px;">
                                <p class="bold text-muted" style="margin-bottom: 5px;"><i class="fa fa-image"></i> Media Preview (Right-click to Copy Image)</p>
                                <img id="image_preview" src="#" style="max-height: 200px; max-width: 100%; object-fit: contain; border-radius: 4px;" />
                            </div>
                        </form>

                        <!-- Saved Templates Table -->
                        <div class="tw-mt-8">
                            <h4 class="tw-my-0 tw-font-bold tw-text-base tw-text-neutral-700 tw-mb-4">
                                <i class="fa fa-list"></i> Saved Templates
                            </h4>
                            <div class="table-responsive" style="max-height: 300px; overflow-y: auto; border: 1px solid #eee; border-radius: 4px;">
                                <table class="table table-hover table-bordered" id="templates_table" style="margin-bottom: 0;">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Message</th>
                                            <th>Media</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($templates as $tmpl) { ?>
                                            <tr id="tmpl_row_<?= $tmpl['id']; ?>" 
                                                data-id="<?= $tmpl['id']; ?>" 
                                                data-title="<?= e($tmpl['title']); ?>" 
                                                data-message="<?= e($tmpl['message_text']); ?>" 
                                                data-image="<?= $tmpl['image_path'] ? base_url($tmpl['image_path']) : ''; ?>"
                                                data-raw-image="<?= $tmpl['image_path']; ?>">
                                                <td class="bold tmpl-title"><?= e($tmpl['title']); ?></td>
                                                <td class="tmpl-message" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                    <?= e($tmpl['message_text']); ?>
                                                </td>
                                                <td class="tmpl-image text-center">
                                                    <?php if ($tmpl['image_path']) { ?>
                                                        <img src="<?= base_url($tmpl['image_path']); ?>" style="max-height: 30px; max-width: 50px; object-fit: contain; border-radius: 2px;">
                                                    <?php } else { ?>
                                                        <span class="text-muted">No Media</span>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-center" style="white-space: nowrap;">
                                                    <button type="button" class="btn btn-info btn-xs edit-template-btn" title="Load / Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-xs delete-template-btn" title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Queue & Sending -->
            <div class="col-md-7">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                            <h4 class="tw-my-0 tw-font-bold tw-text-lg tw-text-neutral-700">
                                Recipients Queue (<span id="queue_count"><?= count($leads); ?></span> leads)
                            </h4>
                            <span class="label label-info" id="sent_stats">0 / <?= count($leads); ?> Sent</span>
                        </div>
                        <hr class="hr-panel-separator" />

                        <?php if (count($leads) === 0) { ?>
                            <div class="alert alert-warning text-center" style="margin-top: 20px;">
                                <p class="bold" style="font-size: 16px;"><i class="fa fa-exclamation-triangle"></i> No Leads Selected!</p>
                                <p>Go to the Leads page, select multiple leads using checkboxes, and click <b>"Send Cold WP Message"</b> inside the Bulk Actions modal to load them into this queue.</p>
                                <a href="<?= admin_url('leads'); ?>" class="btn btn-primary" style="margin-top: 10px;">Go to Leads Page</a>
                            </div>
                        <?php } else { ?>
                            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Recipient</th>
                                            <th>Phone Number</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($leads as $lead) { ?>
                                            <tr id="lead_row_<?= $lead['id']; ?>" data-id="<?= $lead['id']; ?>" data-name="<?= e($lead['name']); ?>" data-company="<?= e($lead['company']); ?>" data-phone="<?= e($lead['phonenumber']); ?>">
                                                <td class="bold"><?= e($lead['name'] == '/' || empty($lead['name']) ? ($lead['company'] ? $lead['company'] : '/') : $lead['name']); ?></td>
                                                <td><?= e($lead['phonenumber']); ?></td>
                                                <td class="text-center status-td">
                                                    <span class="label label-default">Pending</span>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-success btn-xs send-wp-btn" onclick="sendWhatsApp(<?= $lead['id']; ?>);">
                                                        <i class="fa fa-whatsapp"></i> Send
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- WhatsApp Script Selection Modal for Campaign Page -->
<div class="modal fade" id="campaign_script_select_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document" style="margin-top: 15%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center bold"><i class="fa fa-whatsapp text-success"></i> Choose Message Script</h4>
            </div>
            <div class="modal-body text-center" style="padding: 20px;">
                <p style="font-size: 14px; margin-bottom: 20px;">Select the template you want to send to <br><strong id="campaign_wp_script_lead_name" class="text-primary"></strong></p>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <!-- Current Form Content Option -->
                    <button type="button" class="btn btn-success btn-block campaign-script-select-btn" data-use-form="true" style="font-weight: bold; margin-bottom: 10px;">
                        <i class="fa fa-pencil"></i> Use Current Form Message
                    </button>
                    <hr style="margin: 10px 0;" />
                    <!-- Saved Templates Options -->
                    <?php if (count($templates) > 0) { ?>
                        <?php foreach ($templates as $tmpl) { ?>
                            <button type="button" class="btn btn-primary btn-block campaign-script-select-btn" 
                                    data-message-template="<?= e($tmpl['message_text']); ?>" 
                                    data-image-path="<?= $tmpl['image_path']; ?>"
                                    style="font-weight: bold; margin-bottom: 5px;">
                                <?= e($tmpl['title']); ?>
                            </button>
                        <?php } ?>
                    <?php } else { ?>
                        <p class="text-muted text-center">No saved templates found.</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(function() {
    let uploadedImagePath = null;

    // Load / Edit template click handler
    $(document).on('click', '.edit-template-btn', function() {
        const row = $(this).closest('tr');
        const id = row.data('id');
        const title = row.data('title');
        const message = row.data('message');
        const image = row.data('image');
        const rawImage = row.data('raw-image');

        $('#template_id').val(id);
        $('#template_title').val(title);
        $('#message_template').val(message);

        if (image) {
            $('#image_preview').attr('src', image);
            $('#image_preview_container').show();
            uploadedImagePath = rawImage;
        } else {
            $('#image_preview_container').hide();
            uploadedImagePath = null;
        }

        $('#save_template_btn').html('<i class="fa fa-save"></i> Update Template');
        $('#reset_template_btn').show();
    });

    // Reset template form handler
    function resetTemplateForm() {
        $('#template_id').val('');
        $('#template_title').val('');
        $('#message_template').val('');
        $('#media_image').val('');
        $('#image_preview_container').hide();
        $('#image_preview').attr('src', '#');
        uploadedImagePath = null;

        $('#save_template_btn').html('<i class="fa fa-save"></i> Save Template');
        $('#reset_template_btn').hide();
    }

    $('#reset_template_btn').on('click', function() {
        resetTemplateForm();
    });

    // Save/Update template click handler
    $('#save_template_btn').on('click', function() {
        const title = $('#template_title').val().trim();
        const messageText = $('#message_template').val().trim();
        
        if (title === '') {
            alert_float('warning', 'Please enter a template title.');
            return;
        }
        if (messageText === '') {
            alert_float('warning', 'Please enter message template text.');
            return;
        }

        const form = $('#campaign_form')[0];
        const formData = new FormData(form);

        if (typeof(csrfData) !== 'undefined') {
            formData.append(csrfData.token_name, csrfData.hash);
        }

        $.ajax({
            url: admin_url + 'cold_wp/save_template',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                const res = JSON.parse(response);
                if (res.success) {
                    alert_float('success', res.message);
                    
                    const t = res.template;
                    const mediaHtml = t.image_path ? 
                        '<img src="' + t.image_path + '" style="max-height:30px; max-width:50px; object-fit:contain; border-radius:2px;">' : 
                        '<span class="text-muted">No Media</span>';
                    
                    const existingRow = $('#tmpl_row_' + t.id);
                    if (existingRow.length > 0) {
                        // Update existing row
                        existingRow.attr('data-title', t.title);
                        existingRow.attr('data-message', t.message_text);
                        existingRow.attr('data-image', t.image_path || '');
                        existingRow.attr('data-raw-image', t.raw_image_path || '');
                        
                        existingRow.find('.tmpl-title').text(t.title);
                        existingRow.find('.tmpl-message').text(t.message_text);
                        existingRow.find('.tmpl-image').html(mediaHtml);
                    } else {
                        // Append new row
                        const newRow = $('<tr id="tmpl_row_' + t.id + '"></tr>')
                            .attr('data-id', t.id)
                            .attr('data-title', t.title)
                            .attr('data-message', t.message_text)
                            .attr('data-image', t.image_path || '')
                            .attr('data-raw-image', t.raw_image_path || '');
                        
                        newRow.append('<td class="bold tmpl-title">' + $('<div>').text(t.title).html() + '</td>');
                        newRow.append('<td class="tmpl-message" style="max-width:150px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">' + $('<div>').text(t.message_text).html() + '</td>');
                        newRow.append('<td class="tmpl-image text-center">' + mediaHtml + '</td>');
                        newRow.append('<td class="text-center" style="white-space:nowrap;">' +
                            '<button type="button" class="btn btn-info btn-xs edit-template-btn" title="Load / Edit" style="margin-right:2px;"><i class="fa fa-edit"></i></button>' +
                            '<button type="button" class="btn btn-danger btn-xs delete-template-btn" title="Delete"><i class="fa fa-trash"></i></button>' +
                            '</td>');
                        
                        $('#templates_table tbody').append(newRow);
                    }
                    
                    resetTemplateForm();
                } else {
                    alert_float('danger', res.message);
                }
            },
            error: function() {
                alert_float('danger', 'Failed to save template.');
            }
        });
    });

    // Delete template click handler
    $(document).on('click', '.delete-template-btn', function() {
        const row = $(this).closest('tr');
        const id = row.data('id');

        if (confirm("Are you sure you want to delete this template?")) {
            $.ajax({
                url: admin_url + 'cold_wp/delete_template/' + id,
                type: 'POST',
                data: (typeof(csrfData) !== 'undefined' ? { [csrfData.token_name]: csrfData.hash } : {}),
                success: function(response) {
                    const res = JSON.parse(response);
                    if (res.success) {
                        alert_float('success', res.message);
                        row.remove();
                        if ($('#template_id').val() == id) {
                            resetTemplateForm();
                        }
                    } else {
                        alert_float('danger', res.message);
                    }
                },
                error: function() {
                    alert_float('danger', 'Failed to delete template.');
                }
            });
        }
    });

    // Handle Image Preview
    $('#media_image').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#image_preview').attr('src', e.target.result);
                $('#image_preview_container').show();
            }
            reader.readAsDataURL(file);
        } else {
            $('#image_preview_container').hide();
        }
    });

    let activeCampaignLead = null;

    // Send WhatsApp Action - opens campaign script select modal
    window.sendWhatsApp = function(leadId) {
        const row = $('#lead_row_' + leadId);
        const name = row.data('name');
        const company = row.data('company');
        const phone = row.data('phone');

        const cleanName = (name === '/' || name === '' || !name) ? (company ? company : 'there') : name;

        activeCampaignLead = {
            id: leadId,
            name: cleanName,
            phone: phone,
            row: row
        };

        $('#campaign_wp_script_lead_name').text(cleanName);
        $('#campaign_script_select_modal').modal('show');
    };

    // Handle campaign script select modal button click
    $(document).on('click', '.campaign-script-select-btn', function() {
        if (!activeCampaignLead) return;

        const lead = activeCampaignLead;
        const useForm = $(this).data('use-form');
        
        let templateText = '';
        let imagePath = '';

        if (useForm) {
            templateText = $('#message_template').val().trim();
            if (templateText === '') {
                alert_float('warning', 'Please enter a message template in the form first.');
                return;
            }
            imagePath = uploadedImagePath || '';
        } else {
            templateText = $(this).data('message-template');
            imagePath = $(this).data('image-path') || '';
        }

        const message = templateText.replace(/{name}/g, lead.name);

        // Hide the modal
        $('#campaign_script_select_modal').modal('hide');

        // Construct FormData for AJAX post
        const formData = new FormData();
        formData.append('lead_id', lead.id);
        formData.append('phone_number', lead.phone);
        formData.append('message_text', message);
        formData.append('image_path', imagePath);

        if (typeof(csrfData) !== 'undefined') {
            formData.append(csrfData.token_name, csrfData.hash);
        }

        // Add loading state or just send
        $.ajax({
            url: admin_url + 'cold_wp/log_send',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                const res = JSON.parse(response);
                if (res.success) {
                    // Update Row status
                    lead.row.find('.status-td').html('<span class="label label-success"><i class="fa fa-check"></i> Sent</span>');
                    lead.row.find('.send-wp-btn').removeClass('btn-success').addClass('btn-default').html('<i class="fa fa-refresh"></i> Re-send');
                    
                    // Update stats
                    updateStats();
                } else {
                    alert_float('danger', res.message);
                }
            },
            error: function() {
                alert_float('danger', 'Failed to log message status on the server.');
            }
        });

        // Reset active lead
        activeCampaignLead = null;
    });

    function updateStats() {
        const total = $('tr[id^="lead_row_"]').length;
        const sent = $('.status-td .label-success').length;
        $('#sent_stats').text(sent + ' / ' + total + ' Sent');
    }
});
</script>
</body>
</html>
