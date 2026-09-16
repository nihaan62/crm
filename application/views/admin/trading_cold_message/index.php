<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo $title; ?></h4>
                        <hr class="hr-panel-heading" />

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="media_files">1. Upload Images & Videos</label>
                                    <input type="file" id="media_files" multiple accept="image/*,video/*" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <button type="button" class="btn btn-info" id="btn_upload_media">Upload Media</button>
                                    <span id="media_upload_status" class="mleft10 text-success" style="display:none;"></span>
                                </div>
                                
                                <div class="form-group mtop25">
                                    <label for="message_text">2. Message Text (Use <b>{name}</b> to insert Name)</label>
                                    <textarea id="message_text" class="form-control" rows="6" placeholder="Hi {name}, ..."></textarea>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="excel_file">3. Upload Excel Sheet (with "Name" and "Number" columns)</label>
                                    <input type="file" id="excel_file" accept=".xlsx, .xls, .csv" class="form-control" />
                                </div>

                                <div id="excel_data_container" style="display:none;" class="mtop25">
                                    <h4>Excel Data Preview</h4>
                                    
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" id="select_all" checked>
                                        <label for="select_all">Select All</label>
                                    </div>
                                    
                                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-bordered table-striped" id="excel_table">
                                            <thead>
                                                <tr>
                                                    <th width="5%">Select</th>
                                                    <th>Name</th>
                                                    <th>Number</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="mtop20">
                                        <button type="button" class="btn btn-success" id="btn_start_sending">Start Sending</button>
                                        <span id="sending_progress" class="mleft10 text-info" style="font-weight:bold;"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<!-- Include SheetJS for reading Excel in browser -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
$(function(){
    var uploadedMediaPaths = [];
    var excelData = [];
    var isSending = false;

    // 1. Handle Media Upload
    $('#btn_upload_media').on('click', function(){
        var files = $('#media_files')[0].files;
        if(files.length === 0){
            alert('Please select files to upload.');
            return;
        }

        var formData = new FormData();
        for(var i=0; i<files.length; i++){
            formData.append('media[]', files[i]);
        }
        formData.append(csrfData.token_name, csrfData.hash);

        var $btn = $(this);
        $btn.button('loading');
        
        $.ajax({
            url: admin_url + 'trading_cold_message/upload_media',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(res) {
                $btn.button('reset');
                if(res.success){
                    uploadedMediaPaths = res.files;
                    $('#media_upload_status').text(res.files.length + ' file(s) uploaded successfully!').show();
                } else {
                    alert(res.message || 'Upload failed');
                    if(res.errors && res.errors.length > 0){
                        alert(res.errors.join("\n"));
                    }
                }
            },
            error: function(){
                $btn.button('reset');
                alert('An error occurred during file upload.');
            }
        });
    });

    // 2. Handle Excel File Parsing
    $('#excel_file').on('change', function(e){
        var file = e.target.files[0];
        if(!file) return;

        var reader = new FileReader();
        reader.onload = function(e) {
            var data = new Uint8Array(e.target.result);
            var workbook = XLSX.read(data, {type: 'array'});
            
            var firstSheetName = workbook.SheetNames[0];
            var worksheet = workbook.Sheets[firstSheetName];
            var json = XLSX.utils.sheet_to_json(worksheet, {raw: false});
            
            if(json.length > 0){
                renderTable(json);
            } else {
                alert('No data found in the Excel sheet.');
            }
        };
        reader.readAsArrayBuffer(file);
    });

    function renderTable(data){
        excelData = [];
        var tbody = $('#excel_table tbody');
        tbody.empty();
        
        $.each(data, function(index, row){
            // Try to find Name and Number columns (case-insensitive)
            var name = '';
            var number = '';
            
            $.each(row, function(key, val){
                var lowerKey = key.trim().toLowerCase();
                if(lowerKey === 'name') name = val;
                if(lowerKey === 'number' || lowerKey === 'phone' || lowerKey === 'phone number' || lowerKey === 'mobile') number = val;
            });
            
            if(number){
                excelData.push({
                    id: index,
                    name: name,
                    number: number,
                    status: 'Ready'
                });
                
                var tr = $('<tr id="row_'+index+'">');
                tr.append('<td><div class="checkbox"><input type="checkbox" class="send_chk" value="'+index+'" checked><label></label></div></td>');
                tr.append('<td>'+(name ? name : '-')+'</td>');
                tr.append('<td>'+number+'</td>');
                tr.append('<td class="status_col">Ready</td>');
                tbody.append(tr);
            }
        });
        
        if(excelData.length > 0){
            $('#excel_data_container').show();
        } else {
            alert('Could not find "Number" column in the Excel sheet.');
        }
    }

    // Select All
    $('#select_all').on('change', function(){
        var isChecked = $(this).prop('checked');
        $('.send_chk').prop('checked', isChecked);
    });

    // 3. Start Sending
    $('#btn_start_sending').on('click', function(){
        if(isSending) return;
        
        var messageText = $('#message_text').val();
        if(messageText.trim() === '' && uploadedMediaPaths.length === 0){
            alert('Please enter a message or upload media first.');
            return;
        }

        var selectedIds = [];
        $('.send_chk:checked').each(function(){
            selectedIds.push(parseInt($(this).val()));
        });
        
        if(selectedIds.length === 0){
            alert('Please select at least one number from the table.');
            return;
        }

        if(!confirm('Are you sure you want to send to ' + selectedIds.length + ' numbers?')){
            return;
        }

        isSending = true;
        $(this).prop('disabled', true);
        $('.send_chk').prop('disabled', true);
        $('#select_all').prop('disabled', true);
        
        var total = selectedIds.length;
        var currentIdx = 0;
        
        function sendNext(){
            if(currentIdx >= total){
                $('#sending_progress').text('Finished sending!');
                isSending = false;
                $('#btn_start_sending').prop('disabled', false);
                $('.send_chk').prop('disabled', false);
                $('#select_all').prop('disabled', false);
                return;
            }
            
            $('#sending_progress').text('Sending ' + (currentIdx + 1) + ' of ' + total + '...');
            
            var rowId = selectedIds[currentIdx];
            var rowData = excelData.find(x => x.id === rowId);
            
            var $row = $('#row_' + rowId);
            $row.find('.status_col').html('<span class="text-warning">Sending...</span>');
            
            $.post(admin_url + 'trading_cold_message/send_message', {
                phone_number: rowData.number,
                name: rowData.name,
                message_text: messageText,
                media_paths: uploadedMediaPaths
            }).done(function(res){
                res = JSON.parse(res);
                if(res.success){
                    $row.find('.status_col').html('<span class="text-success">Sent</span>');
                } else {
                    $row.find('.status_col').html('<span class="text-danger">Failed: '+res.message+'</span>');
                }
            }).fail(function(){
                $row.find('.status_col').html('<span class="text-danger">Server Error</span>');
            }).always(function(){
                currentIdx++;
                // Add a small delay between requests to avoid rate limiting
                setTimeout(sendNext, 1000); 
            });
        }
        
        sendNext();
    });
});
</script>
