<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
/* ─── Root Tokens ──────────────────────────────────────────────── */
:root {
    --ael-green-start : #107C41;
    --ael-green-end   : #1D6F42;
    --ael-teal-start  : #00b09b;
    --ael-teal-end    : #128C7E;
    --ael-blue-start  : #1a73e8;
    --ael-blue-end    : #0d47a1;
    --ael-orange-start: #ff6d00;
    --ael-orange-end  : #e65100;
    --ael-purple-start: #7b2ff7;
    --ael-purple-end  : #4c1d95;
    --ael-radius      : 14px;
    --ael-shadow      : 0 4px 24px rgba(0,0,0,.10);
}

/* ─── Page Header ──────────────────────────────────────────────── */
.ael-hero {
    background: linear-gradient(135deg, var(--ael-green-start) 0%, var(--ael-green-end) 100%);
    border-radius: var(--ael-radius);
    color: #fff;
    padding: 28px 32px;
    margin-bottom: 26px;
    box-shadow: 0 6px 32px rgba(16,124,65,.30);
    position: relative;
    overflow: hidden;
}
.ael-hero::before {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 200px; height: 200px;
    background: rgba(255,255,255,.06);
    border-radius: 50%;
}
.ael-hero::after {
    content: '';
    position: absolute;
    bottom: -60px; left: 40%;
    width: 280px; height: 280px;
    background: rgba(255,255,255,.04);
    border-radius: 50%;
}
.ael-hero-title { margin: 0 0 6px; font-size: 26px; font-weight: 700; letter-spacing: -.3px; }
.ael-hero-sub   { margin: 0; opacity: .85; font-size: 13.5px; }
.ael-hero-actions { display:flex; gap:10px; flex-wrap:wrap; }

.ael-hero-actions .btn {
    font-weight: 600;
    border-radius: 8px;
    font-size: 13px;
    padding: 8px 18px;
    transition: transform .15s, box-shadow .15s;
}
.ael-hero-actions .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(0,0,0,.25); }

/* ─── Stat Cards ───────────────────────────────────────────────── */
.ael-stat-card {
    border-radius: var(--ael-radius);
    padding: 22px 24px;
    color: #fff;
    text-align: center;
    box-shadow: var(--ael-shadow);
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
}
.ael-stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 32px rgba(0,0,0,.16); }
.ael-stat-card::after {
    content: '';
    position: absolute;
    top: -20px; right: -20px;
    width: 90px; height: 90px;
    background: rgba(255,255,255,.10);
    border-radius: 50%;
}
.ael-stat-num  { font-size: 38px; font-weight: 800; line-height: 1; margin-bottom: 4px; }
.ael-stat-lbl  { font-size: 11.5px; text-transform: uppercase; letter-spacing: 1.2px; opacity: .90; }
.ael-stat-icon { font-size: 18px; margin-bottom: 6px; opacity: .80; }
.ael-g  { background: linear-gradient(135deg, var(--ael-green-start),  var(--ael-green-end)); }
.ael-t  { background: linear-gradient(135deg, var(--ael-teal-start),   var(--ael-teal-end)); }
.ael-b  { background: linear-gradient(135deg, var(--ael-blue-start),   var(--ael-blue-end)); }
.ael-o  { background: linear-gradient(135deg, var(--ael-orange-start), var(--ael-orange-end)); }
.ael-p  { background: linear-gradient(135deg, var(--ael-purple-start), var(--ael-purple-end)); }

/* ─── Toolbar ──────────────────────────────────────────────────── */
.ael-toolbar {
    background: #fff;
    border: 1px solid #e8edf3;
    border-radius: var(--ael-radius);
    padding: 14px 20px;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,.05);
}
.ael-toolbar .ael-search-wrap {
    flex: 1;
    min-width: 220px;
    position: relative;
}
.ael-toolbar .ael-search-wrap .fa {
    position: absolute;
    left: 12px; top: 50%;
    transform: translateY(-50%);
    color: #8ea0b8;
}
.ael-toolbar .ael-search-wrap input {
    padding-left: 34px;
    border-radius: 8px;
    border: 1px solid #d8e2ee;
    height: 36px;
    font-size: 13px;
    width: 100%;
    transition: border-color .2s, box-shadow .2s;
}
.ael-toolbar .ael-search-wrap input:focus {
    border-color: var(--ael-green-start);
    box-shadow: 0 0 0 3px rgba(16,124,65,.12);
    outline: none;
}
.ael-toolbar .ael-meta {
    font-size: 13px;
    color: #8395a7;
    white-space: nowrap;
    margin-left: auto;
}
.ael-toolbar .ael-meta strong { color: var(--ael-green-start); }

/* ─── Table Panel ──────────────────────────────────────────────── */
.ael-panel {
    background: #fff;
    border-radius: var(--ael-radius);
    box-shadow: var(--ael-shadow);
    border: 1px solid #e8edf3;
    overflow: hidden;
}
.ael-panel .ael-panel-header {
    padding: 14px 20px;
    border-bottom: 1px solid #eef2f7;
    display: flex;
    align-items: center;
    gap: 10px;
    background: #fafbfc;
}
.ael-panel .ael-panel-header h5 {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: #2d3748;
}
.ael-panel .ael-panel-header .badge-count {
    background: var(--ael-green-start);
    color: #fff;
    border-radius: 20px;
    padding: 2px 10px;
    font-size: 11px;
    font-weight: 700;
}

/* ─── Table Styles ─────────────────────────────────────────────── */
.ael-table { margin: 0 !important; }
.ael-table thead th {
    background: #f0f6f2 !important;
    font-weight: 700;
    font-size: 11.5px;
    color: var(--ael-green-start);
    white-space: nowrap;
    text-transform: uppercase;
    letter-spacing: .5px;
    border-bottom: 2px solid #d6eadc !important;
    padding: 10px 14px !important;
    vertical-align: middle !important;
}
.ael-table tbody tr { transition: background .12s; }
.ael-table tbody tr:hover { background: #f7fdf9 !important; }
.ael-table td {
    font-size: 12.5px;
    vertical-align: middle !important;
    padding: 9px 14px !important;
    max-width: 240px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #3a4a5c;
}
.ael-table td:first-child { color: #aab5c2; font-weight: 600; font-size: 11.5px; }

/* ─── Cell Badges ──────────────────────────────────────────────── */
.badge-platform {
    display: inline-flex; align-items: center; gap: 4px;
    border-radius: 5px; padding: 3px 8px;
    font-weight: 700; font-size: 11px; letter-spacing: .4px;
}
.badge-platform.fb  { background: #e8f0fe; color: #1a73e8; }
.badge-platform.ig  { background: #fce4ec; color: #c62828; }
.badge-platform.gg  { background: #e8f5e9; color: #2e7d32; }
.badge-platform.def { background: #f3e5f5; color: #6a1b9a; }

.badge-status {
    display: inline-block;
    border-radius: 20px; padding: 3px 10px;
    font-weight: 600; font-size: 11px;
}
.badge-status.created   { background: #e3f2fd; color: #1565c0; }
.badge-status.converted { background: #e8f5e9; color: #2e7d32; }
.badge-status.junk      { background: #fce4ec; color: #b71c1c; }
.badge-status.default   { background: #f5f5f5; color: #616161; }

.badge-organic {
    display: inline-block;
    border-radius: 4px; padding: 2px 7px;
    font-size: 11px; font-weight: 600;
}
.badge-organic.yes { background: #e8f5e9; color: #2e7d32; }
.badge-organic.no  { background: #fafafa; color: #9e9e9e; border: 1px solid #e0e0e0; }

.phone-link { color: #25D366 !important; font-weight: 600; }
.phone-link:hover { color: #128C7E !important; }
.phone-link i { margin-right: 3px; }

/* ─── Empty State ──────────────────────────────────────────────── */
.ael-empty {
    padding: 60px 20px;
    text-align: center;
    color: #8395a7;
}
.ael-empty i { font-size: 48px; display: block; margin-bottom: 16px; color: #d1dae6; }
.ael-empty h5 { font-size: 18px; font-weight: 700; color: #4a5568; margin-bottom: 6px; }

/* ─── DataTables Overrides ─────────────────────────────────────── */
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_length { display: none; }
.dataTables_wrapper .dataTables_paginate { padding: 12px 16px; }
.dataTables_wrapper .dataTables_info    { padding: 12px 16px; font-size: 12.5px; color: #8395a7; }
.paginate_button { border-radius: 6px !important; }
.paginate_button.current { background: var(--ael-green-start) !important; border-color: var(--ael-green-start) !important; color: #fff !important; }
</style>

<div id="wrapper">
    <div class="content-inner">
        <div class="container-fluid">

            <!-- ── Hero Header ─────────────────────────────────── -->
            <div class="ael-hero">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; position:relative; z-index:1;">
                    <div>
                        <h2 class="ael-hero-title">
                            <i class="fa-solid fa-file-excel" style="margin-right:10px;"></i>Ads Excel List
                        </h2>
                        <p class="ael-hero-sub">
                            Live synchronized Facebook &amp; Instagram ad leads fetched directly from Google Sheets.
                        </p>
                    </div>
                    <div class="ael-hero-actions">
                        <a href="<?= admin_url('settings?group=general'); ?>" class="btn btn-default">
                            <i class="fa fa-cog"></i>&nbsp; Configure Settings
                        </a>
                        <a href="<?= e($sheet_url); ?>" target="_blank" class="btn btn-warning">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>&nbsp; Open Google Sheet
                        </a>
                    </div>
                </div>
            </div>

            <!-- ── Stat Cards ──────────────────────────────────── -->
            <div class="row">
                <div class="col-sm-6 col-md-4">
                    <div class="ael-stat-card ael-g">
                        <div class="ael-stat-icon"><i class="fa-solid fa-users"></i></div>
                        <div class="ael-stat-num"><?= count($rows); ?></div>
                        <div class="ael-stat-lbl">Leads Displayed</div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div class="ael-stat-card ael-b">
                        <div class="ael-stat-icon"><i class="fa-solid fa-sliders"></i></div>
                        <div class="ael-stat-num"><?= $lead_count; ?></div>
                        <div class="ael-stat-lbl">Configured Limit</div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="ael-stat-card ael-t">
                        <div class="ael-stat-icon"><i class="fa-solid fa-table-cells"></i></div>
                        <div class="ael-stat-num"><?= $total_sheet_rows; ?></div>
                        <div class="ael-stat-lbl">Total Rows in Sheet</div>
                    </div>
                </div>
            </div>

            <!-- ── Toolbar ─────────────────────────────────────── -->
            <div class="ael-toolbar">
                <div class="ael-search-wrap">
                    <i class="fa fa-search"></i>
                    <input type="text" id="aelSearchInput" placeholder="Search name, phone, email, platform..." />
                </div>
                <div class="ael-meta">
                    Showing <strong><?= count($rows); ?></strong> of <strong><?= $total_sheet_rows; ?></strong> total rows
                </div>
            </div>

            <!-- ── Table Panel ─────────────────────────────────── -->
            <div class="ael-panel">
                <div class="ael-panel-header">
                    <i class="fa-solid fa-table-list" style="color:var(--ael-green-start);"></i>
                    <h5>Lead Records</h5>
                    <span class="badge-count"><?= count($rows); ?></span>
                </div>

                 <?php if (empty($rows)): ?>
                <div class="ael-empty">
                    <i class="fa-solid fa-file-excel"></i>
                    <h5>No Leads Found</h5>
                    <?php if (!empty($fetch_error)): ?>
                        <div class="alert alert-danger" style="display:inline-block; max-width:600px; margin-top:10px; font-weight:600; text-align:left;">
                            <i class="fa fa-exclamation-triangle" style="font-size:16px; display:inline; margin-right:8px; color:#a94442;"></i>
                            <?= e($fetch_error); ?>
                        </div>
                    <?php else: ?>
                        <p>No leads could be fetched from the Google Sheet.<br>
                        <small>Check your Google Sheet link in <a href="<?= admin_url('settings?group=general'); ?>">General Settings</a>.</small></p>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered ael-table" id="aelTable">
                        <thead>
                            <tr>
                                <th style="width:38px;">#</th>
                                <?php foreach ($headers as $h): ?>
                                    <?php
                                    $cleanTitle = ucwords(str_replace(
                                        ['_', '?', '.', '4.', '5.', '6.'],
                                        [' ', '',  '',  '',   '',   ''],
                                        $h
                                    ));
                                    ?>
                                    <th><?= e(trim($cleanTitle)); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $idx = 1; foreach ($rows as $row): ?>
                            <tr>
                                <td><?= $idx++; ?></td>
                                <?php foreach ($headers as $h): ?>
                                    <?php
                                    $val    = isset($row[$h]) ? $row[$h] : '';
                                    $hLower = strtolower($h);
                                    ?>
                                    <td title="<?= e($val); ?>">
                                        <?php if (strpos($hLower, 'phone') !== false && !empty($val)): ?>
                                            <?php $cleanPhone = preg_replace('/[^0-9]/', '', $val); ?>
                                            <a href="https://wa.me/<?= e($cleanPhone); ?>" target="_blank" class="phone-link">
                                                <i class="fa-brands fa-whatsapp"></i><?= e($val); ?>
                                            </a>

                                        <?php elseif (strpos($hLower, 'email') !== false && !empty($val)): ?>
                                            <a href="mailto:<?= e($val); ?>" style="color:#1a73e8;"><?= e($val); ?></a>

                                        <?php elseif ($hLower === 'platform' && !empty($val)): ?>
                                            <?php
                                            $pUp  = strtoupper(trim($val));
                                            $pClass = ($pUp === 'FB' || $pUp === 'FACEBOOK') ? 'fb'
                                                    : (($pUp === 'IG' || $pUp === 'INSTAGRAM') ? 'ig'
                                                    : (($pUp === 'GG' || $pUp === 'GOOGLE') ? 'gg' : 'def'));
                                            $icon = ($pClass === 'fb') ? 'fa-brands fa-facebook-f'
                                                  : (($pClass === 'ig') ? 'fa-brands fa-instagram'
                                                  : (($pClass === 'gg') ? 'fa-brands fa-google' : 'fa-solid fa-ad'));
                                            ?>
                                            <span class="badge-platform <?= $pClass; ?>">
                                                <i class="<?= $icon; ?>"></i> <?= e($pUp); ?>
                                            </span>

                                        <?php elseif ($hLower === 'lead_status' && !empty($val)): ?>
                                            <?php
                                            $sLower = strtolower(trim($val));
                                            $sClass = ($sLower === 'created')   ? 'created'
                                                    : (($sLower === 'converted') ? 'converted'
                                                    : (($sLower === 'junk')      ? 'junk' : 'default'));
                                            ?>
                                            <span class="badge-status <?= $sClass; ?>"><?= e(strtoupper($val)); ?></span>

                                        <?php elseif ($hLower === 'is_organic' || $hLower === 'organic'): ?>
                                            <?php $isYes = in_array(strtolower($val), ['true', '1', 'yes']); ?>
                                            <span class="badge-organic <?= $isYes ? 'yes' : 'no'; ?>">
                                                <?= $isYes ? 'Organic' : 'Paid'; ?>
                                            </span>

                                        <?php elseif (strpos($hLower, 'form') !== false && !empty($val)): ?>
                                            <span style="color:#6c757d; font-size:11.5px;"><i class="fa fa-file-alt me-1"></i><?= e($val); ?></span>

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
            </div><!-- /.ael-panel -->

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Live Search ── */
    var searchInput = document.getElementById('aelSearchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            var filter = this.value.toLowerCase();
            var rows   = document.querySelectorAll('#aelTable tbody tr');
            var shown  = 0;
            rows.forEach(function (row) {
                var matches = row.textContent.toLowerCase().indexOf(filter) > -1;
                row.style.display = matches ? '' : 'none';
                if (matches) shown++;
            });
        });
    }

    /* ── Tooltip on truncated cells ── */
    document.querySelectorAll('.ael-table td').forEach(function (td) {
        if (td.scrollWidth > td.clientWidth) {
            td.style.cursor = 'pointer';
        }
    });
});
</script>
<?php init_tail(); ?>
