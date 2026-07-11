<?php
    if (!defined('Profess0rPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
    }

    if (!canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'dashboard', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }
?>

<style>
/* Modern Premium SaaS Styling */
body {
    background-color: #f8fafc;
    color: #334155;
}
.card {
    border: none !important;
    border-radius: 16px !important;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04) !important;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.welcome-banner {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    color: #fff;
    padding: 32px;
    border-radius: 16px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.welcome-banner::after {
    content: '';
    position: absolute;
    top: 0; right: 0; bottom: 0; left: 0;
    background: radial-gradient(circle at top right, rgba(99, 102, 241, 0.2), transparent 60%);
    pointer-events: none;
}
.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}
.stat-icon-primary { background: #e0e7ff; color: #4f46e5; }
.stat-icon-warning { background: #fef3c7; color: #d97706; }
.stat-icon-success { background: #dcfce7; color: #16a34a; }
.stat-icon-info { background: #e0f2fe; color: #0284c7; }

.table-custom thead th {
    background: transparent !important;
    border-bottom: 2px solid #f1f5f9 !important;
    color: var(--tblr-muted) !important;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 0.5px;
}
.table-custom tbody tr {
    border-bottom: 1px solid #f1f5f9;
}
.table-custom tbody tr:last-child {
    border-bottom: none;
}
.table-custom td {
    vertical-align: middle;
    padding: 1rem 0.5rem;
}
.btn-modern {
    border-radius: 8px;
    font-weight: 500;
    padding: 6px 12px;
    white-space: nowrap;
}
@media (max-width: 575px) {
    .btn-modern {
        font-size: 11px;
        padding: 6px 8px;
    }
    .welcome-banner .gap-2 {
        gap: 0.35rem !important;
    }
    .header-actions {
        overflow: hidden !important;
        width: 100%;
    }
    .header-actions .flex-nowrap {
        width: 100%;
        gap: 4px !important;
        justify-content: space-between;
    }
    .header-actions .btn-modern {
        font-size: 12px;
        padding: 6px 4px;
        flex: 1;
        justify-content: center;
    }
    .header-actions .btn-modern svg {
        width: 14px;
        height: 14px;
        margin-right: 2px !important;
    }
}
</style>

<div class="page-body">
    <div class="container-xl">
        <!-- Hidden input for CSRF Token required by AJAX actions -->
        <input type="hidden" name="csrf_token_default" value="<?= $csrf_token ?>">

        <!-- Header Actions -->
        <div class="header-actions d-flex justify-content-start justify-content-md-end align-items-center mb-4">
            <div class="d-flex flex-nowrap flex-md-wrap gap-2 pb-1">
                <a href="<?= $site_url.$path_admin ?>/payment-link" class="btn btn-primary btn-modern d-flex align-items-center" style="background:#4f46e5; border-color:#4f46e5;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-link me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 15l6 -6" /><path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464" /><path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463" /></svg> Payment Link</a>
                <a href="<?= $site_url.$path_admin ?>/gateways" class="btn btn-light btn-modern text-dark border-0 bg-white shadow-sm d-flex align-items-center"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-wallet me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" /><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" /></svg> Gateway</a>
                <a href="<?= $site_url.$path_admin ?>/transaction" class="btn btn-light btn-modern text-dark border-0 bg-white shadow-sm d-flex align-items-center"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-receipt me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-3 2m4 -14h6m-6 4h6m-2 4h2" /></svg> Transactions</a>
            </div>
        </div>

        <?php
            // Calculate Stats
            $total_payments = 0;
            $pending_payments = 0;
            $todays_revenue = 0;
            $total_customers = 0;

            // Total Payments
            $res = json_decode(getData($db_prefix.'transaction', ' WHERE brand_id = "'.$global_response_brand['response'][0]['brand_id'].'" AND status NOT IN ("initiated")'), true);
            if($res['status'] == true) { $total_payments = count($res['response']); }

            // Pending Payments
            $res = json_decode(getData($db_prefix.'transaction', ' WHERE brand_id = "'.$global_response_brand['response'][0]['brand_id'].'" AND status = "pending"'), true);
            if($res['status'] == true) { $pending_payments = count($res['response']); }

            // Today's Revenue (local_net_amount for completed today, accounting for timezone)
            $tz_string = ($global_response_brand['response'][0]['timezone'] === '--' || empty($global_response_brand['response'][0]['timezone'])) ? 'Asia/Dhaka' : $global_response_brand['response'][0]['timezone'];
            
            try {
                $start_of_day_tz = new DateTime('today', new DateTimeZone($tz_string));
                $start_of_day_utc = clone $start_of_day_tz;
                $start_of_day_utc->setTimezone(new DateTimeZone('UTC'));
                $start_utc_str = $start_of_day_utc->format('Y-m-d H:i:s');
                
                $end_of_day_tz = new DateTime('tomorrow', new DateTimeZone($tz_string));
                $end_of_day_utc = clone $end_of_day_tz;
                $end_of_day_utc->setTimezone(new DateTimeZone('UTC'));
                $end_utc_str = $end_of_day_utc->format('Y-m-d H:i:s');
            } catch (Exception $e) {
                // fallback if timezone string is invalid
                $start_utc_str = date('Y-m-d 00:00:00');
                $end_utc_str = date('Y-m-d 23:59:59');
            }

            $res = json_decode(getData($db_prefix.'transaction', ' WHERE brand_id = "'.$global_response_brand['response'][0]['brand_id'].'" AND status = "completed" AND created_date >= "'.$start_utc_str.'" AND created_date < "'.$end_utc_str.'"'), true);
            if($res['status'] == true) {
                foreach($res['response'] as $row) {
                    $todays_revenue += (float)$row['amount']; // or local_net_amount based on logic
                }
            }

            // Customers
            $res = json_decode(getData($db_prefix.'customer', ' WHERE brand_id = "'.$global_response_brand['response'][0]['brand_id'].'"'), true);
            if($res['status'] == true) { $total_customers = count($res['response']); }
        ?>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon stat-icon-primary me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 5m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" /><path d="M3 10l18 0" /><path d="M7 15l.01 0" /><path d="M11 15l2 0" /></svg>
                        </div>
                        <div>
                            <div class="text-secondary fw-medium" style="font-size: 13px;">Total Payments</div>
                            <div class="h2 m-0 fw-bold"><?= number_format($total_payments) ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon stat-icon-warning me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>
                        </div>
                        <div>
                            <div class="text-secondary fw-medium" style="font-size: 13px;">Pending Payments</div>
                            <div class="h2 m-0 fw-bold"><?= number_format($pending_payments) ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon stat-icon-success me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M8 12l2 2l4 -4" /></svg>
                        </div>
                        <div>
                            <div class="text-secondary fw-medium" style="font-size: 13px;">Today's Revenue</div>
                            <div class="h2 m-0 fw-bold"><?= number_format($todays_revenue, 2) ?> <?= $global_response_brand['response'][0]['currency_code'] ?? 'BDT' ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon stat-icon-info me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
                        </div>
                        <div>
                            <div class="text-secondary fw-medium" style="font-size: 13px;">Customers</div>
                            <div class="h2 m-0 fw-bold"><?= number_format($total_customers) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column: Tables -->
            <div class="col-12">
                <!-- Pending Requests -->
                <div class="card mb-4">
                    <div class="card-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h3 class="card-title fw-bold m-0" style="font-size: 18px;">Pending Requests</h3>
                        <a href="<?= $site_url.$path_admin ?>/transaction" class="text-primary fw-medium" style="font-size:13px; text-decoration: none;">View All</a>
                    </div>
                    <div class="card-body px-4 pb-4 pt-2">
                        <div class="table-responsive">
                            <table class="table table-custom table-vcenter text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Payment Method</th>
                                        <th>Amount</th>
                                        <th>Trx/Order ID</th>
                                        <th>Date</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $gateways_map = [];
                                        $gateways_query = json_decode(getData($db_prefix.'gateways', ' WHERE brand_id ="'.$global_response_brand['response'][0]['brand_id'].'"'), true);
                                        if($gateways_query['status'] == true && !empty($gateways_query['response'])){
                                            foreach($gateways_query['response'] as $gw){
                                                $gateways_map[$gw['gateway_id']] = $gw['name'];
                                            }
                                        }

                                        $pending_query = json_decode(getData($db_prefix.'transaction', ' WHERE brand_id = "'.$global_response_brand['response'][0]['brand_id'].'" AND status = "pending" ORDER BY id DESC LIMIT 10'), true);
                                        if($pending_query['status'] == true && !empty($pending_query['response'])):
                                            $allowApprove = hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'transaction', 'approve', $global_user_response['response'][0]['role']);
                                            foreach($pending_query['response'] as $tx):
                                                $customerInfo = json_decode($tx['customer_info'], true);
                                                $custName = $customerInfo['name'] ?? '--';
                                                $custEmail = $customerInfo['email'] ?? $customerInfo['mobile'] ?? '--';
                                                
                                                $tz = ($global_response_brand['response'][0]['timezone'] === '--' || empty($global_response_brand['response'][0]['timezone'])) ? 'Asia/Dhaka' : $global_response_brand['response'][0]['timezone'];
                                                $dateFormatted = convertUTCtoUserTZ($tx['created_date'], $tz, "M d, Y");
                                                $timeFormatted = convertUTCtoUserTZ($tx['created_date'], $tz, "h:i A");
                                    ?>
                                    <tr>
                                        <td>
                                            <?php $gateway_name = $gateways_map[$tx['gateway_id']] ?? $tx['gateway_id']; ?>
                                            <span class="badge bg-secondary-lt text-secondary fw-bold" style="font-size:12px; border-radius:4px; padding:6px 10px;">
                                                <?= htmlspecialchars(strtoupper($gateway_name != '--' ? $gateway_name : 'Unknown')) ?>
                                            </span>
                                        </td>
                                        <td class="fw-bold text-dark"><?= number_format($tx['amount'], 2, '.', '') ?> <span class="text-muted fw-normal" style="font-size:10px;"><?= $tx['currency'] ?></span></td>
                                        <td>
                                            <?php if ($tx['trx_id'] != '--' && $tx['trx_id'] != ''): ?>
                                                <div class="fw-medium text-dark" style="font-size:13px;">Trx: <?= htmlspecialchars($tx['trx_id']) ?></div>
                                            <?php endif; ?>
                                            <?php if ($tx['sender'] != '--' && $tx['sender'] != ''): ?>
                                                <div class="fw-medium text-dark" style="font-size:13px;">Sender: <?= htmlspecialchars($tx['sender']) ?></div>
                                            <?php endif; ?>
                                            <?php if (($tx['trx_id'] == '--' || $tx['trx_id'] == '') && ($tx['sender'] == '--' || $tx['sender'] == '')): ?>
                                                <div class="fw-medium text-dark" style="font-size:13px;">N/A</div>
                                            <?php endif; ?>
                                            <div class="text-secondary" style="font-size:11px;">Ord: <?= htmlspecialchars($tx['ref'] != '--' ? $tx['ref'] : 'N/A') ?></div>
                                        </td>
                                        <td>
                                            <div class="text-dark" style="font-size:13px;"><?= $dateFormatted ?></div>
                                            <div class="text-secondary" style="font-size:11px;"><?= $timeFormatted ?></div>
                                        </td>
                                        <td class="text-end">
                                            <?php if($allowApprove): ?>
                                            <button class="btn btn-sm btn-modern btn-success btnQuickAction-<?= $tx['ref'] ?>-approved" onclick="quickActionItem('<?= $tx['ref'] ?>', 'approved')">Approve</button>
                                            <button class="btn btn-sm btn-modern btn-light text-danger btnQuickAction-<?= $tx['ref'] ?>-canceled" onclick="quickActionItem('<?= $tx['ref'] ?>', 'canceled')">Reject</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; else: ?>
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No pending requests right now.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="card">
                    <div class="card-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h3 class="card-title fw-bold m-0" style="font-size: 18px;">Recent Transactions</h3>
                    </div>
                    <div class="card-body px-4 pb-4 pt-2">
                        <div class="table-responsive">
                            <table class="table table-custom table-vcenter text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Payment Method</th>
                                        <th>Amount</th>
                                        <th>Trx/Order ID</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $completed_query = json_decode(getData($db_prefix.'transaction', ' WHERE brand_id = "'.$global_response_brand['response'][0]['brand_id'].'" AND status = "completed" ORDER BY id DESC LIMIT 10'), true);
                                        if($completed_query['status'] == true && !empty($completed_query['response'])):
                                            foreach($completed_query['response'] as $tx):
                                                $customerInfo = json_decode($tx['customer_info'], true);
                                                $custName = $customerInfo['name'] ?? '--';
                                                
                                                $tz = ($global_response_brand['response'][0]['timezone'] === '--' || empty($global_response_brand['response'][0]['timezone'])) ? 'Asia/Dhaka' : $global_response_brand['response'][0]['timezone'];
                                                $dateFormatted = convertUTCtoUserTZ($tx['created_date'], $tz, "M d, Y");
                                                $timeFormatted = convertUTCtoUserTZ($tx['created_date'], $tz, "h:i A");
                                    ?>
                                    <tr>
                                        <td>
                                            <?php $gateway_name = $gateways_map[$tx['gateway_id']] ?? $tx['gateway_id']; ?>
                                            <span class="badge bg-secondary-lt text-secondary fw-bold" style="font-size:12px; border-radius:4px; padding:6px 10px;">
                                                <?= htmlspecialchars(strtoupper($gateway_name != '--' ? $gateway_name : 'Unknown')) ?>
                                            </span>
                                        </td>
                                        <td class="fw-bold text-success">+<?= number_format($tx['amount'], 2, '.', '') ?> <span class="text-muted fw-normal" style="font-size:10px;"><?= $tx['currency'] ?></span></td>
                                        <td>
                                            <?php if ($tx['trx_id'] != '--' && $tx['trx_id'] != ''): ?>
                                                <div class="fw-medium text-dark" style="font-size:13px;">Trx: <?= htmlspecialchars($tx['trx_id']) ?></div>
                                            <?php endif; ?>
                                            <?php if ($tx['sender'] != '--' && $tx['sender'] != ''): ?>
                                                <div class="fw-medium text-dark" style="font-size:13px;">Sender: <?= htmlspecialchars($tx['sender']) ?></div>
                                            <?php endif; ?>
                                            <?php if (($tx['trx_id'] == '--' || $tx['trx_id'] == '') && ($tx['sender'] == '--' || $tx['sender'] == '')): ?>
                                                <div class="fw-medium text-dark" style="font-size:13px;">N/A</div>
                                            <?php endif; ?>
                                            <div class="text-secondary" style="font-size:11px;">Ord: <?= htmlspecialchars($tx['ref'] != '--' ? $tx['ref'] : 'N/A') ?></div>
                                        </td>
                                        <td><span class="badge bg-success-lt text-success" style="border-radius:6px; padding: 4px 8px;">Completed</span></td>
                                        <td>
                                            <div class="text-dark" style="font-size:13px;"><?= $dateFormatted ?></div>
                                            <div class="text-secondary" style="font-size:11px;"><?= $timeFormatted ?></div>
                                        </td>
                                    </tr>
                                    <?php endforeach; else: ?>
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No recent transactions.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Chart -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h3 class="card-title fw-bold m-0" style="font-size: 18px;">Revenue Overview</h3>
                        <div>
                            <select class="form-select form-select-sm border-0 bg-light text-secondary fw-medium" id="dateFilter-transaction-statistics" onchange="handleFilterChangeTransactionStatistics(this.value)" style="border-radius: 8px;">
                                <option value="this_month" selected>This month</option>
                                <option value="this_year">This year</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4 pt-0">
                        <div class="position-relative">
                            <span class="dashboard-transaction-statistics-loading position-absolute top-0 end-0 mt-2"></span>
                            <div id="chart-transaction-statistics" style="height: 300px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    function quickActionItem(ItemID, actionID) {
        var csrf_token_default = $('input[name="csrf_token_default"]').val();
        var btnClass = 'btnQuickAction-'+ItemID+'-'+actionID;
        
        var my_action_confirmation_btn = document.querySelector("#my-action-confirmation-btn") ? document.querySelector("#my-action-confirmation-btn").value : '';

        if(my_action_confirmation_btn !== '') {
            var btnInner = document.querySelector('#model-my-action-confirmation-btn').innerHTML;
            document.querySelector('#model-my-action-confirmation-btn').innerHTML = '<div class="spinner-border spinner-border-sm text-white" role="status"><span class="visually-hidden">Loading...</span></div>';

            $.ajax({
                type: 'POST',
                url: '<?php echo $site_url.$path_admin ?>/dashboard',
                data: {action: "transaction-bulk-action", csrf_token: csrf_token_default, actionID: actionID, selected_ids: JSON.stringify([ItemID])},
                dataType: 'json',
                success: function (response) {
                    closeAllBootstrapModals();
                    document.querySelector("#my-action-confirmation-btn").value = '';
                    document.querySelector('#model-my-action-confirmation-btn').innerHTML = btnInner;
                    window.location.reload();
                },
                error: function(xhr) {
                    window.location.reload();
                }
            });
        } else {
            var label = actionID === 'approved' ? 'Approve' : 'Reject';
            var color = actionID === 'approved' ? 'btn-success' : 'btn-danger';
            show_action_confirmation_tab(btnClass, label + ' Transaction', label, color);
        }
    }

    // Chart logic
    function load_dashboard_transaction_statistics(){
        var csrf_token_default = $('input[name="csrf_token_default"]').val();
        var date = $('#dateFilter-transaction-statistics').val();
        var start = '';
        var end = '';
        
        let loader = document.querySelector(".dashboard-transaction-statistics-loading");
        if(loader) loader.innerHTML = '<div class="spinner-border spinner-border-sm text-primary"></div>';

        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            data: {action: "dashboard-transaction-statistics", csrf_token: csrf_token_default, date: date, start: start, end: end},
            dataType: 'json',
            success: function (res) {
                if(loader) loader.innerHTML = '';
                if(res.csrf_token) {
                    document.querySelectorAll('input[name="csrf_token"], input[name="csrf_token_default"]').forEach(input => input.value = res.csrf_token);
                }

                if (res.status === 'true') {
                    if (window.chartTransactionStatistics) { window.chartTransactionStatistics.destroy(); window.chartTransactionStatistics = null; }

                    window.chartTransactionStatistics = new ApexCharts(
                      document.getElementById("chart-transaction-statistics"),
                      {
                        chart: {
                          type: "area",
                          height: 300,
                          fontFamily: "inherit",
                          toolbar: { show: false },
                          animations: { enabled: false }
                        },
                        stroke: { width: 2, curve: "smooth" },
                        fill: {
                            type: "gradient",
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.4,
                                opacityTo: 0.0,
                                stops: [0, 100]
                            }
                        },
                        series: [
                          { name: "Total", data: res.total },
                          { name: "Complete", data: res.complete }
                        ],
                        xaxis: {
                          categories: res.labels,
                          tooltip: { enabled: false },
                          axisBorder: { show: false },
                          axisTicks: { show: false }
                        },
                        yaxis: {
                          labels: { style: { colors: '#94a3b8' } }
                        },
                        grid: { strokeDashArray: 4, borderColor: '#f1f5f9' },
                        tooltip: { theme: "light" },
                        legend: { show: true, position: "top", horizontalAlign: 'right' },
                        colors: ["#6366f1", "#10b981"]
                      }
                    );
                    window.chartTransactionStatistics.render();
                }
            }
        });
    }

    function handleFilterChangeTransactionStatistics(value) {
        load_dashboard_transaction_statistics();
    }

    (function waitForApexCharts() {
        if (typeof ApexCharts !== 'undefined') {
            load_dashboard_transaction_statistics();
        } else {
            setTimeout(waitForApexCharts, 200);
        }
    })();

})();
</script>

