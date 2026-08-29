<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
.ael-header {
    background: linear-gradient(135deg, #107C41 0%, #1D6F42 100%);
    border-radius: 12px;
    color: #fff;
    padding: 26px 30px;
    margin-bottom: 24px;
    box-shadow: 0 4px 20px rgba(16,124,65,0.35);
}
.ael-header h2 { margin: 0 0 6px; font-size: 24px; font-weight: 700; }
.ael-header p  { margin: 0; opacity: .88; font-size: 14px; }
.ael-stat {
    border-radius: 10px;
    padding: 18px 20px;
    color: #fff;
    text-align: center;
    font-weight: 600;
    box-shadow: 0 3px 14px rgba(0,0,0,.12);
    margin-bottom: 20px;
}
.ael-stat .num { font-size: 32px; font-weight: 700; line-height: 1; margin-bottom: 4px; }
.ael-stat .lbl { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; opacity: .88; }
.ael-green  { background: linear-gradient(135deg,#107C41,#1D6F42); }
.ael-teal   { background: linear-gradient(135deg,#25D366,#128C7E); }
.ael-blue   { background: linear-gradient(135deg,#1a73e8,#0d47a1); }
.ael-orange { background: linear-gradient(135deg,#ff6d00,#e65100); }

.ael-filter-bar {
    background: #f8f9fa;
    border: 1px solid #e3e8ef;
    border-radius: 10px;
    padding: 14px 18px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}
.ael-table th { background: #eef7f2; font-weight: 600; font-size: 12px; color: #107C41; white-space: nowrap; }
.ael-table td { font-size: 12px; vertical-align: middle !important; max-width: 250px; overflow: hidden; text-overflow: ellipsis; }
.badge-platform { background: #e3f2fd; color: #1565c0; border-radius: 4px; padding: 2px 6px; font-weight: 600; font-size: 11px; }
.badge-status { background: #e8f5e9; color: #2e7d32; border-radius: 4px; padding: 2px 6px; font-weight: 600; font-size: 11px; }
</style>

<div id="wrapper">
    <div class="content-inner">
        <div class="container-fluid">

            <!-- Header -->
            <div class="ael-header">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                    <div>
                        <h2><i class="fa-solid fa-file-excel" style="margin-right:10px;"></i>Ads Excel List</h2>
                        <p>Live synchronized Facebook / Instagram ad leads fetched directly from Google Sheets.</p>
                    </div>
                    <div>
                        <a href="<?= admin_url('settings?group=general'); ?>" class="btn btn-default btn-sm" style="font-weight:600;">
                            <i class="fa fa-cog me-1"></i> Configure Settings (Limit: <?= $lead_count; ?>)
                        </a>
                        <a href="<?= e($sheet_url); ?>" target="_blank" class="btn btn-warning btn-sm" style="font-weight:600;">
                            <i class="fa fa-external-link me-1"></i> Open Google Sheet
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="row">
                <div class="col-sm-4">
                    <div class="ael-stat ael-green">
                        <div class="num"><?= count($rows); ?></div>
                        <div class="lbl">Leads Displayed</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="ael-stat ael-blue">
                        <div class="num"><?= $lead_count; ?></div>
                        <div class="lbl">Configured Limit (Settings)</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="ael-stat ael-teal">
                        <div class="num"><?= $total_sheet_rows; ?></div>
                        <div class="lbl">Total Rows in Sheet</div>
                    </div>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="ael-filter-bar">
                <label style="font-weight:600; margin-bottom:0;"><i class="fa fa-search me-1"></i> Search Leads:</label>
                <div style="flex:1; min-width:220px;">
                    <input type="text" id="aelSearchInput" class="form-control input-sm" placeholder="Type name, phone, email, campaign..." />
                </div>
                <div style="margin-left:auto; font-size:13px;" class="text-muted">
                    Showing top <strong><?= count($rows); ?></strong> leads (Limit configured in General Settings)
                </div>
            </div>

            <!-- Table Panel -->
            <div class="panel_s">
                <div class="panel-body">
                    <?php if (empty($rows)): ?>
                    <div class="alert alert-warning text-center" style="padding:30px;">
                        <i class="fa-solid fa-file-excel" style="font-size:36px; display:block; margin-bottom:10px;"></i>
                        No leads fetched from Google Sheet.<br>
                        <small class="text-muted">Please check your Google Sheet link in <a href="<?= admin_url('settings?group=general'); ?>">Setup -> Settings -> General Settings</a>.</small>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered ael-table" id="aelTable">
                            <thead>
                                <tr>
                                    <th style="width:30px;">#</th>
                                    <?php foreach ($headers as $h): ?>
                                        <?php
                                        // Clean up column title for display
                                        $cleanTitle = ucwords(str_replace(['_', '?', '.', '4.', '5.', '6.'], [' ', '', '', '', '', ''], $h));
                                        ?>
                                        <th><?= e(trim($cleanTitle)); ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $idx = 1; foreach ($rows as $row): ?>
                                <tr>
                                    <td class="text-muted"><?= $idx++; ?></td>
                                    <?php foreach ($headers as $h): ?>
                                        <?php
                                        $val = isset($row[$h]) ? $row[$h] : '';
                                        $hLower = strtolower($h);
                                        ?>
                                        <td>
                                            <?php if (strpos($hLower, 'phone') !== false && !empty($val)): ?>
                                                <?php
                                                $cleanPhone = preg_replace('/[^0-9]/', '', $val);
                                                ?>
                                                <a href="https://wa.me/<?= e($cleanPhone); ?>" target="_blank" class="text-success bold">
                                                    <i class="fa-brands fa-whatsapp me-1"></i><?= e($val); ?>
                                                </a>
                                            <?php elseif (strpos($hLower, 'email') !== false && !empty($val)): ?>
                                                <a href="mailto:<?= e($val); ?>"><?= e($val); ?></a>
                                            <?php elseif ($hLower === 'platform' && !empty($val)): ?>
                                                <span class="badge-platform"><?= e(strtoupper($val)); ?></span>
                                            <?php elseif ($hLower === 'lead_status' && !empty($val)): ?>
                                                <span class="badge-status"><?= e(strtoupper($val)); ?></span>
                                            <?php else: ?>
                                                <?= e($val); ?>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('aelSearchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            var filter = this.value.toLowerCase();
            var rows = document.querySelectorAll('#aelTable tbody tr');
            rows.forEach(function(row) {
                var text = row.textContent.toLowerCase();
                row.style.display = text.indexOf(filter) > -1 ? '' : 'none';
            });
        });
    }
});
</script>
<?php init_tail(); ?>
