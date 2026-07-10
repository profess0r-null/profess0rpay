<?php
// Shared Premium Receipt Template for Profess0rPay
// Expects $receiptData array to be populated before inclusion.

// Sanitize & format defaults
$r_amount = $receiptData['amount'] ?? '0.00';
$r_currency = $receiptData['currency'] ?? 'BDT';
$r_amountDisplay = $r_amount . ' ' . $r_currency;
$r_trxid = $receiptData['trxid'] ?? '--';
$r_date = $receiptData['date'] ?? date('d M Y');
$r_time = $receiptData['time'] ?? date('h:i A');
$r_method = $receiptData['method'] ?? 'Unknown';
$r_methodLogo = $receiptData['method_logo'] ?? '';
$r_sender = $receiptData['sender'] ?? '--';
$r_merchant = $receiptData['merchant'] ?? 'Profess0rPay Store';
$r_status = $receiptData['status'] ?? 'Successful';
$r_fee = $receiptData['fee'] ?? '0.00';
$r_feeDisplay = $r_fee . ' ' . $r_currency;
$r_received = (float)str_replace(',', '', $r_amount) - (float)str_replace(',', '', $r_fee);
$r_receivedDisplay = number_format($r_received, 2) . ' ' . $r_currency;
?>
<div id="pp-premium-receipt-container" style="position: absolute; top: -9999px; left: -9999px; width: 0; height: 0; overflow: hidden; opacity: 0; z-index: -1;">
    <div id="pp-premium-receipt" style="width: 1080px; height: 1400px; background-color: #f3f4f6; font-family: 'Inter', 'Anek Bangla', sans-serif; display: flex; align-items: center; justify-content: center; position: relative; box-sizing: border-box; overflow: hidden;">
        
        <!-- Background Pattern / Watermark -->
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); opacity: 0.04; pointer-events: none;">
            <svg width="800" height="800" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M12 2L2 7l10 5 10-5-10-5zm0 14.5l-10-5v5.5l10 5 10-5V11.5l-10 5z"/></svg>
        </div>

        <!-- Receipt Card -->
        <div style="width: 960px; background: #ffffff; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.08); display: flex; flex-direction: column; overflow: hidden; z-index: 1;">
            
            <!-- Header Section -->
            <div style="padding: 60px 60px 40px; text-align: center; border-bottom: 2px dashed #e5e7eb; position: relative;">
                <!-- Cutout Circles -->
                <div style="position: absolute; bottom: -20px; left: -20px; width: 40px; height: 40px; background-color: #f3f4f6; border-radius: 50%; box-shadow: inset -5px 0 10px rgba(0,0,0,0.03);"></div>
                <div style="position: absolute; bottom: -20px; right: -20px; width: 40px; height: 40px; background-color: #f3f4f6; border-radius: 50%; box-shadow: inset 5px 0 10px rgba(0,0,0,0.03);"></div>

<?php
                // Dynamic styling based on status
                $h_color = '#22c55e'; // Green
                $h_bg = '#dcfce7';
                $h_title = 'Payment Successful';
                $h_subtitle = 'Payment Completed';
                $h_icon = '<path d="M5 12l5 5l10 -10" />';

                if (strtolower($r_status) === 'pending') {
                    $h_color = '#f59e0b'; // Amber/Orange
                    $h_bg = '#fef3c7';
                    $h_title = 'Payment Pending';
                    $h_subtitle = 'Verification in Progress';
                    $h_icon = '<path d="M12 8l0 4l2 2" /><path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5" />'; // Clock/Progress icon
                } elseif (in_array(strtolower($r_status), ['canceled', 'failed', 'rejected'])) {
                    $h_color = '#ef4444'; // Red
                    $h_bg = '#fee2e2';
                    $h_title = 'Payment Failed';
                    $h_subtitle = 'Transaction ' . ucfirst($r_status);
                    $h_icon = '<path d="M18 6l-12 12" /><path d="M6 6l12 12" />'; // Cross icon
                }
                ?>
                <div id="receipt-icon-container" style="width: 100px; height: 100px; background: <?= $h_bg ?>; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px;">
                    <svg id="receipt-icon-svg" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="<?= $h_color ?>" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><?= $h_icon ?></svg>
                </div>
                
                <h1 id="receipt-title" style="color: #111827; font-size: 38px; font-weight: 700; margin: 0 0 15px;"><?= $h_title ?></h1>
                <div id="receipt-subtitle" style="color: #6b7280; font-size: 24px; font-weight: 500; margin-bottom: 30px;"><?= $h_subtitle ?></div>
                
                <div style="font-size: 56px; font-weight: 800; color: #111827; letter-spacing: -1px; margin: 0; display: flex; justify-content: center; align-items: center; gap: 10px;">
                    <?= $r_amountDisplay ?>
                </div>
            </div>

            <!-- Details Section -->
            <div style="padding: 50px 60px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px 30px;">
                    <div>
                        <div style="color: #6b7280; font-size: 18px; font-weight: 500; margin-bottom: 8px;">Transaction ID</div>
                        <div id="receipt-trxid-val" style="color: #111827; font-size: 24px; font-weight: 600; word-break: break-all;"><?= htmlspecialchars($r_trxid) ?></div>
                    </div>
                    
                    <div>
                        <div style="color: #6b7280; font-size: 18px; font-weight: 500; margin-bottom: 8px;">Date & Time</div>
                        <div style="color: #111827; font-size: 24px; font-weight: 600;"><?= htmlspecialchars($r_date) ?> &bull; <?= htmlspecialchars($r_time) ?></div>
                    </div>
                    
                    <div>
                        <div style="color: #6b7280; font-size: 18px; font-weight: 500; margin-bottom: 8px;">Payment Method</div>
                        <div style="color: #111827; font-size: 24px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                            <?php if ($r_methodLogo): ?>
                                <img src="<?= $r_methodLogo ?>" alt="Logo" style="height: 24px; width: auto; max-width: 40px; object-fit: contain;">
                            <?php endif; ?>
                            <?= htmlspecialchars($r_method) ?>
                        </div>
                    </div>
                    
                    <div>
                        <div style="color: #6b7280; font-size: 18px; font-weight: 500; margin-bottom: 8px;">Sender</div>
                        <div id="receipt-sender-val" style="color: #111827; font-size: 24px; font-weight: 600;"><?= htmlspecialchars($r_sender) ?></div>
                    </div>

                    <div style="grid-column: span 2;">
                        <div style="color: #6b7280; font-size: 18px; font-weight: 500; margin-bottom: 8px;">Merchant</div>
                        <div style="color: #111827; font-size: 24px; font-weight: 600;"><?= htmlspecialchars($r_merchant) ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Summary Footer -->
            <div style="background: #f8fafc; padding: 40px 60px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: flex-end;">
                <div>
                    <div style="color: #6b7280; font-size: 14px; font-weight: 600; margin-bottom: 4px;">Status: <span id="receipt-status-val" style="color: <?= $h_color ?>;"><?= ucfirst($r_status) ?></span></div>
                    <div style="color: #9ca3af; font-size: 14px; font-weight: 500;">Fee: <?= htmlspecialchars($r_feeDisplay) ?></div>
                </div>
                <div style="text-align: right;">
                    <div style="color: #6b7280; font-size: 18px; margin-bottom: 5px;">Total Received</div>
                    <div style="color: #111827; font-size: 28px; font-weight: 700;"><?= $r_receivedDisplay ?></div>
                </div>
            </div>

            <!-- Profess0rPay Branding -->
            <div style="padding: 25px 60px; text-align: center; border-top: 1px dashed #e5e7eb; background: #fff;">
                <div style="color: #9ca3af; font-size: 16px; font-weight: 500; display: flex; justify-content: center; align-items: center; gap: 8px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 12l2 2l4 -4" /><path d="M12 3a9 9 0 1 0 0 18a9 9 0 0 0 0 -18z" /></svg>
                    Generated securely by Profess0rPay
                </div>
            </div>
        </div>
    </div>
</div>
