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

                            <!-- Image Preview Area -->
                            <div id="image_preview_container" style="display: none; margin-top: 15px; border: 1px dashed #ddd; padding: 10px; text-align: center; border-radius: 4px;">
                                <p class="bold text-muted" style="margin-bottom: 5px;"><i class="fa fa-image"></i> Media Preview (Right-click to Copy Image)</p>
                                <img id="image_preview" src="#" style="max-height: 200px; max-width: 100%; object-fit: contain; border-radius: 4px;" />
                            </div>
                        </form>
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
                                            <tr id="lead_row_<?= $lead['id']; ?>" data-id="<?= $lead['id']; ?>" data-name="<?= e($lead['name']); ?>" data-phone="<?= e($lead['phonenumber']); ?>">
                                                <td class="bold"><?= e($lead['name']); ?></td>
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

<?php init_tail(); ?>

<script>
$(function() {
    let uploadedImagePath = null;

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

    // Send WhatsApp Action
    window.sendWhatsApp = function(leadId) {
        const row = $('#lead_row_' + leadId);
        const name = row.data('name');
        const phone = row.data('phone');
        
        let templateText = $('#message_template').val().trim();
        if (templateText === '') {
            alert_float('warning', 'Please enter a message template.');
            return;
        }

        // Replace placeholder
        const message = templateText.replace(/{name}/g, name);

        // Copy text to clipboard
        navigator.clipboard.writeText(message).then(function() {
            alert_float('info', 'Message text copied to clipboard! You can paste (Ctrl+V) it in WhatsApp.');
        }, function(err) {
            console.error('Could not copy text: ', err);
        });

        // Prepare Form Data for background logging
        const form = $('#campaign_form')[0];
        const formData = new FormData(form);
        formData.append('lead_id', leadId);
        formData.append('phone_number', phone);
        formData.append('message_text', message);
        if (uploadedImagePath) {
            formData.append('image_path', uploadedImagePath);
        }
        if (typeof(csrfData) !== 'undefined') {
            formData.append(csrfData.token_name, csrfData.hash);
        }

        // Log the send action via AJAX
        $.ajax({
            url: admin_url + 'cold_wp/log_send',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                const res = JSON.parse(response);
                if (res.success) {
                    if (res.image_path) {
                        uploadedImagePath = res.image_path; // Cache the uploaded image path for subsequent sends
                    }
                    
                    // Update Row status
                    row.find('.status-td').html('<span class="label label-success"><i class="fa fa-check"></i> Sent</span>');
                    row.find('.send-wp-btn').removeClass('btn-success').addClass('btn-default').html('<i class="fa fa-refresh"></i> Re-send');
                    
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

        // Clean phone number (remove non-numeric chars except leading plus)
        let cleanPhone = phone.toString().replace(/[^0-9]/g, '');
        
        // Open WhatsApp Web/App in new tab
        const waUrl = 'https://api.whatsapp.com/send?phone=' + cleanPhone + '&text=' + encodeURIComponent(message);
        window.open(waUrl, '_blank');
    };

    function updateStats() {
        const total = $('tr[id^="lead_row_"]').length;
        const sent = $('.status-td .label-success').length;
        $('#sent_stats').text(sent + ' / ' + total + ' Sent');
    }
});
</script>
</body>
</html>
