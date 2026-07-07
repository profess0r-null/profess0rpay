<?php
    if (!defined('Profess0rPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
    }

    if(isset($_GET['receipt'])){
        pp_downloadReceiptPDF($data);
        exit;
    }

    if(isset($_GET['lang'])){
        if($_GET['lang'] !== ""){
            pp_set_lang($_GET['lang']);
?>
            <script>
                location.href = '?lang=';
            </script>
<?php
            exit();
        }
    }

    $status = strtolower($data['transaction']['status'] ?? 'pending');

    // SIMPLE: Only use return_url (merchant's site) for redirect after completion
    // For canceled: already handled by checkout.php — if user lands here somehow, just show status
    $return_url = $data['transaction']['return_url'] ?? '';
    $redirect_url = (!empty($return_url) && $return_url !== '--') ? $return_url : '';


    // Parse support settings from brand data
    $support = [];
    if (isset($data['brand']['support'])) {
        $support = is_string($data['brand']['support']) ? json_decode($data['brand']['support'], true) : $data['brand']['support'];
    }

    // AJAX status check for polling (no page reload)
    if (isset($_POST['action-v2']) && $_POST['action-v2'] === 'check-status') {
        global $db_prefix;
        $ref = escape_string($_POST['ref'] ?? '');
        if ($ref) {
            $params = [':ref' => $ref];
            $res = json_decode(getData($db_prefix.'transaction', 'WHERE ref = :ref', '* FROM', $params), true);
            if ($res['status'] == true && isset($res['response'][0]['status'])) {
                echo json_encode(['status' => strtolower($res['response'][0]['status'])]);
            } else {
                echo json_encode(['status' => 'pending']);
            }
        } else {
            echo json_encode(['status' => 'pending']);
        }
        exit;
    }

    // Completed: do NOT auto-redirect, serve the page normally and JS will handle UI update

    $gateway_color = '#e2136e'; 
    $gateway_logo = '';
    $gateway_slug = '';
    
    global $db_prefix;

    if(in_array($status, ['completed', 'pending', 'canceled', 'rejected'])){
        $params = [ ':ref' => $data['transaction']['ref'] ];
        $response_transaction_temp = json_decode(getData($db_prefix.'transaction','WHERE ref = :ref', '* FROM', $params),true);
        if($response_transaction_temp['status'] == true){
            $gateway_id = $response_transaction_temp['response'][0]['gateway_id'];
            $trx_id = $response_transaction_temp['response'][0]['trx_id'] ?? '';
            $source_info_raw = $response_transaction_temp['response'][0]['source_info'] ?? '--';
            $source_info_data = [];
            if ($source_info_raw !== '--') {
                $source_info_data = json_decode($source_info_raw, true) ?? [];
            }
            $params_g = [ ':gateway_id' => $gateway_id ];
            $response_gateway_temp = json_decode(getData($db_prefix.'gateways','WHERE gateway_id = :gateway_id', '* FROM', $params_g),true);
            if($response_gateway_temp['status'] == true){
                $gateway_color = $response_gateway_temp['response'][0]['primary_color'] ?? '#e2136e';
                $gateway_logo = $response_gateway_temp['response'][0]['logo'] ?? '';
                $gateway_slug = $response_gateway_temp['response'][0]['slug'] ?? '';
                if ($gateway_slug == 'bkash-personal') {
                    $gateway_logo = pp_site_address() . 'assets/images/bkash.png';
                }
            }
        }
    }

    $cartBg = '#fff3e0';
    $cartColor = $gateway_color;
    if ($gateway_slug == 'bkash-personal') {
        $gateway_color = '#e2136e';
        $cartBg = '#fce4ec';
        $cartColor = '#e2136e';
    } elseif ($gateway_slug == 'rocket-personal') {
        $gateway_color = '#7b2382';
        $cartBg = '#f4e7f7';
        $cartColor = '#7b2382';
    } elseif ($gateway_slug == 'nagad-personal') {
        $gateway_color = '#f76b2a';
        $cartBg = '#fff1e6';
        $cartColor = '#f76b2a';
    }

    $amount = money_round($data['transaction']['amount'] ?? 0, 2);
    if (floor($amount) == $amount) {
        $amount = number_format($amount, 0, '.', '');
    }
    $currency = $data['transaction']['currency'] ?? 'BDT';
    $amountStr = $amount;
    if($currency == 'BDT') $amountStr = '৳'.$amount;
    else $amountStr = $amount.' '.$currency;

    $shopName = htmlspecialchars($data['brand']['name']);
    if (empty($shopName) || strtolower($shopName) === 'default') {
        $shopName = isset($data['site_name']) ? htmlspecialchars($data['site_name']) : 'Profess0r Shop';
    }
    $invoice = $data['transaction']['ref'];

    $cartIconSVG = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h2l2.1 9.2a2 2 0 0 0 1.9 1.4h9.2a2 2 0 0 0 1.9-1.4L22 6H7"></path><path d="M9 20a1 1 0 1 0 0 -2 1 1 0 0 0 0 2z"></path><path d="M20 20a1 1 0 1 0 0 -2 1 1 0 0 0 0 2z"></path></svg>';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Status - <?php echo $data['brand']['name'];?></title>
    <?php echo pp_assets('head'); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Anek+Bangla:wght@400;500;600;700&display=swap" rel="stylesheet">    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7fe;
            margin: 0;
            padding: 20px 12px 32px;
            min-height: 100vh;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .completed-wrapper {
            width: 100%;
            max-width: 420px;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            min-height: auto;
            border-radius: 16px;
            overflow: hidden;
        }

        .zini-toast {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #e6f6ec;
            color: #047857;
            padding: 12px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            white-space: nowrap;
        }
        .zini-toast svg { width: 18px; height: 18px; color: #059669; }

        /* Canceled Screen */
        .canceled-card {
            background: #fff;
            border-radius: 12px;
            padding: 40px 30px;
            text-align: center;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            margin: 20px;
        }
        .canceled-icon-wrapper {
            background: #fee2e2;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .canceled-icon {
            background: #ef4444;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
        .canceled-title {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
        }
        .canceled-text {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.5;
            margin-bottom: 30px;
        }
        .canceled-redirect {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 10px;
        }
        .progress-bar-container {
            width: 100%;
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }
        .progress-bar-fill {
            height: 100%;
            background: #ef4444;
            width: 100%;
            border-radius: 4px;
            transition: width 1s linear;
        }

        /* Completed Screen */
        .completed-wrapper {
            width: 100%;
            max-width: 420px;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            min-height: auto;
            display: flex;
            flex-direction: column;
        }
        .completed-header {
            background: #fff;
            padding: 14px 20px 12px;
            text-align: center;
            border-bottom: 1px solid #f3f4f6;
        }
        .completed-header img {
            height: 64px;
            object-fit: contain;
        }
        .completed-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            background: #fff;
        }
        .zini-shop-block { display: flex; align-items: center; gap: 12px; }
        .zini-shop-icon { width: 44px; height: 44px; border-radius: 50%; background: <?= $cartBg ?>; color: <?= $cartColor ?>; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .zini-shop-name { font-size: 18px; font-weight: 500; color: #1f2937; line-height: 1.2; font-family: 'Anek Bangla', 'Inter', sans-serif; }
        .zini-shop-inv { font-size: 10px; color: #9ca3af; opacity: 0.95; display: flex; align-items: center; gap: 4px; margin-top: 2px; }
        .zini-total-amount { 
            font-size: 24px; 
            font-weight: 500; 
            color: #2f2f2f; 
            font-family: 'Anek Bangla', sans-serif;
            letter-spacing: -0.025em;
            margin-left: 16px;
            flex-shrink: 0;
        }
        .completed-body {
            background-color: <?= $gateway_color ?>;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
            color: #fff;
            text-align: center;
        }
        .completed-icon-wrapper {
            background: #fff;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .completed-icon-wrapper svg {
            color: <?= $gateway_color ?>;
            width: 40px;
            height: 40px;
        }
        .completed-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 12px;
        }
        .completed-text {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 40px;
        }
        .redirect-box {
            background: rgba(0,0,0,0.1);
            border-radius: 8px;
            padding: 20px;
            width: 100%;
            margin-bottom: 30px;
        }
        .redirect-box .small-text {
            font-size: 12px;
            opacity: 0.8;
            margin-bottom: 10px;
        }
        .redirect-box .countdown-number {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .completed-footer-text {
            font-size: 13px;
            opacity: 0.8;
            margin-top: auto;
        }
        
        @media (max-width: 480px) {
            body { background: <?= $status == 'completed' ? $gateway_color : '#f4f7fe' ?>; }
            .completed-wrapper { box-shadow: none; }
        }
    </style>
</head>
<body>

    <?php if (in_array($status, ['pending', 'completed', 'canceled', 'rejected'])) { ?>
        <div class="completed-wrapper" style="background-color: #f4f7fe; min-height: 100vh;">
            <div class="completed-header">
                <?php if($gateway_logo) { ?>
                    <img src="<?= $gateway_logo ?>" alt="Gateway">
                <?php } else { ?>
                    <img src="<?= $data['brand']['logo'] ?>" alt="Brand">
                <?php } ?>
            </div>
            <div class="completed-info" style="border-bottom: 1px solid #e5e7eb; margin-bottom: 20px;">
                <div class="zini-shop-block">
                    <div class="zini-shop-icon">
                        <?= $cartIconSVG ?>
                    </div>
                    <div>
                        <div class="zini-shop-name"><?= $shopName ?></div>
                        <div class="zini-shop-inv" style="display: flex; align-items: center; gap: 6px; flex-wrap: nowrap; min-width: 0;">
                            <span style="display: flex; align-items: center; gap: 4px; min-width: 0; flex: 1;">
                                <span style="white-space: nowrap; flex-shrink: 0;">Inv:</span>
                                <span style="display: inline-block; min-width: 0; max-width: 140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; vertical-align: bottom;"><?= $invoice ?></span>
                            </span>
                            <svg onclick="pp_copy('<?= $invoice ?>', 'Invoice Copied!', this)" style="cursor: pointer; flex-shrink: 0; color: <?= $gateway_color ?>; transform: translateY(-1px);" stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="11px" width="11px" xmlns="http://www.w3.org/2000/svg"><path d="M320 448v40c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24V120c0-13.255 10.745-24 24-24h72v296c0 30.879 25.121 56 56 56h168zm0-344V0H152c-13.255 0-24 10.745-24 24v360c0 13.255 10.745 24 24 24h272c13.255 0 24-10.745 24-24V128H344c-13.2 0-24-10.8-24-24zm120.971-31.029L375.029 7.029A24 24 0 0 0 358.059 0H352v96h96v-6.059a24 24 0 0 0-7.029-16.97z"></path></svg>
                        </div>
                    </div>
                </div>
                <div class="zini-total-amount">
                    <?= $amountStr ?>
                </div>
            </div>

            <?php
                $wa_num_status = '';
                if (!empty($support['whatsapp'])) {
                    $wa_num_status = preg_replace('/[^0-9]/', '', $support['whatsapp']);
                }
            ?>
            <!-- Support Icons -->
            <div style="display: flex; justify-content: center; gap: 10px; margin: 0 0 16px 0;">
                <?php if(!empty($support['whatsapp']) && $support['whatsapp'] != '--' || !empty($support['telegram']) && $support['telegram'] != '--' || !empty($support['email']) && $support['email'] != '--'): ?>
                <div onclick="document.getElementById('status-support-modal').style.display='flex'" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; border-radius: 14px; background: #fff; cursor: pointer; color: #4285f4; box-shadow: 0 4px 12px rgba(0,0,0,0.06); border: 2px solid #bfdbfe;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 14h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7a9 9 0 0 1 18 0v7a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3"/></svg>
                </div>
                <?php endif; ?>
                <?php if($wa_num_status): ?>
                <div onclick="window.open('https://wa.me/<?= $wa_num_status ?>', '_blank')" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; border-radius: 14px; background: #fff; cursor: pointer; color: #25d366; box-shadow: 0 4px 12px rgba(0,0,0,0.06); border: 2px solid #bbf7d0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9" /><path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1" /></svg>
                </div>
                <?php endif; ?>
            </div>

            <!-- Support Modal -->
            <div id="status-support-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center; padding:20px;" onclick="if(event.target===this)this.style.display='none'">
                <div style="background:#fff; border-radius:16px; padding:24px; width:100%; max-width:380px; position:relative;">
                    <button onclick="document.getElementById('status-support-modal').style.display='none'" style="position:absolute; top:16px; right:16px; border:none; background:none; font-size:20px; cursor:pointer; color:#888;">✕</button>
                    <h5 style="font-weight:700; margin:0 0 6px; font-size:16px;">Contact Support</h5>
                    <p style="font-size:13px; color:#6b7280; margin-bottom:16px;">Need help with your payment? Contact <?= $shopName ?> below:</p>
                    <?php if(!empty($support['whatsapp']) && $support['whatsapp'] != '--'): ?>
                    <a href="https://wa.me/<?= $support['whatsapp'] ?>?text=Hello, I need help with my payment (Invoice: <?= $invoice ?>)" target="_blank" style="display:flex; align-items:center; gap:14px; background:#e8f5e9; color:#2e7d32; border-radius:8px; padding:14px; margin-bottom:10px; text-decoration:none;">
                        <div style="background:#fff; border-radius:50%; width:36px; height:36px; display:flex; align-items:center; justify-content:center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9" /><path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1" /></svg>
                        </div>
                        <div><p style="margin:0; font-weight:600; font-size:14px;">WhatsApp</p><span style="font-size:12px; opacity:0.8;">+<?= $support['whatsapp'] ?></span></div>
                    </a>
                    <?php endif; ?>
                    <?php if(!empty($support['telegram']) && $support['telegram'] != '--' && $support['telegram'] != 'https://t.me/--'): ?>
                    <a href="<?= $support['telegram'] ?>" target="_blank" style="display:flex; align-items:center; gap:14px; background:#e3f2fd; color:#1565c0; border-radius:8px; padding:14px; margin-bottom:10px; text-decoration:none;">
                        <div style="background:#fff; border-radius:50%; width:36px; height:36px; display:flex; align-items:center; justify-content:center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 10l-4 4l6 6l4 -16l-18 7l4 2l2 6l3 -4" /></svg>
                        </div>
                        <div><p style="margin:0; font-weight:600; font-size:14px;">Telegram</p><span style="font-size:12px; opacity:0.8;"><?= str_replace('https://t.me/', '@', $support['telegram']) ?></span></div>
                    </a>
                    <?php endif; ?>
                    <?php if(!empty($support['email']) && $support['email'] != '--'): ?>
                    <a href="mailto:<?= $support['email'] ?>?subject=Payment Help - <?= $invoice ?>" style="display:flex; align-items:center; gap:14px; background:#fff3e0; color:#e65100; border-radius:8px; padding:14px; text-decoration:none;">
                        <div style="background:#fff; border-radius:50%; width:36px; height:36px; display:flex; align-items:center; justify-content:center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10" /><path d="M3 7l9 6l9 -6" /></svg>
                        </div>
                        <div><p style="margin:0; font-weight:600; font-size:14px;">Email</p><span style="font-size:12px; opacity:0.8;"><?= $support['email'] ?></span></div>
                    </a>
                    <?php endif; ?>
                    <p style="text-align:center; font-size:11px; color:#9ca3af; margin-top:14px; margin-bottom:0;">Invoice ID: <strong><?= $invoice ?></strong></p>
                </div>
            </div>

            <div style="background: #fff; border-radius: 12px; margin: 0 20px 20px 20px; padding: 30px 20px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                <div id="status-icon-box" style="background: <?= $status == 'completed' ? '#dcfce7' : (in_array($status, ['canceled', 'rejected']) ? '#fee2e2' : $cartBg) ?>; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <?php if($status == 'completed') { ?>
                    <svg id="status-icon-svg" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                    <?php } elseif(in_array($status, ['canceled', 'rejected'])) { ?>
                    <svg id="status-icon-svg" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                    <?php } else { ?>
                    <svg id="status-icon-svg" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="<?= $cartColor ?>" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><polyline points="12 7 12 12 15 15" /></svg>
                    <?php } ?>
                </div>
                <h2 id="status-title" style="font-size: 22px; font-weight: 700; color: <?= $status == 'completed' ? '#15803d' : (in_array($status, ['canceled', 'rejected']) ? '#b91c1c' : '#1f2937') ?>; margin-bottom: 10px;"><?= $status == 'completed' ? 'Payment Completed' : (in_array($status, ['canceled', 'rejected']) ? 'Payment Canceled' : 'Payment Pending') ?></h2>
                <p id="status-desc" style="font-size: 14px; color: #4b5563; line-height: 1.5; margin-bottom: 25px;"><?= $status == 'completed' ? 'Your payment has been successfully approved by the admin.' : (in_array($status, ['canceled', 'rejected']) ? 'Your payment request has been canceled or rejected.' : 'Your payment details have been received. Please wait while an admin verifies the payment.') ?></p>
                
                <div style="background: #f9fafb; border: 1px solid #f3f4f6; border-radius: 8px; padding: 15px; margin-bottom: 20px; text-align: left;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="font-size: 13px; color: #6b7280; font-weight: 500;">Provider</span>
                        <span style="font-size: 13px; color: #1f2937; font-weight: 600; text-transform: capitalize;"><?= str_replace('-personal', '', str_replace('-merchant', '', str_replace('-agent', '', $gateway_slug))) ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="font-size: 13px; color: #6b7280; font-weight: 500;">Amount</span>
                        <span style="font-size: 13px; color: #1f2937; font-weight: 600;"><?= $amount ?> <?= $currency ?></span>
                    </div>
                    <?php if (!empty($trx_id) && $trx_id !== '--') { ?>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="font-size: 13px; color: #6b7280; font-weight: 500;">Transaction ID</span>
                        <span style="font-size: 13px; color: #1f2937; font-weight: 600; word-break: break-all; text-align: right; margin-left: 10px;"><?= htmlspecialchars($trx_id) ?></span>
                    </div>
                    <?php } ?>
                    <?php if (!empty($source_info_data)) { 
                        foreach ($source_info_data as $info) { 
                            if ($info['label'] === 'Binance Order ID') continue; // Already shown as Transaction ID if matched
                    ?>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="font-size: 13px; color: #6b7280; font-weight: 500;"><?= htmlspecialchars($info['label']) ?></span>
                        <span style="font-size: 13px; color: #1f2937; font-weight: 600;"><?= htmlspecialchars($info['value']) ?></span>
                    </div>
                    <?php } } ?>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-size: 13px; color: #6b7280; font-weight: 500;">Status</span>
                        <?php if($status == 'completed') { ?>
                        <span id="status-badge" style="font-size: 13px; font-weight: 600; background: #dcfce7; color: #16a34a; padding: 2px 10px; border-radius: 20px;">Approved</span>
                        <?php } elseif(in_array($status, ['canceled', 'rejected'])) { ?>
                        <span id="status-badge" style="font-size: 13px; font-weight: 600; background: #fee2e2; color: #ef4444; padding: 2px 10px; border-radius: 20px;">Canceled</span>
                        <?php } else { ?>
                        <span id="status-badge" style="font-size: 13px; font-weight: 600; background: #fef3c7; color: #d97706; padding: 2px 10px; border-radius: 20px;">Pending</span>
                        <?php } ?>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 20px;">
                    <?php
                        $another_payment_url = pp_site_address();
                        $btn_text = "Make Another Payment";
                        $has_return_url = false;

                        if (!empty($redirect_url)) {
                            $another_payment_url = $redirect_url;
                            $btn_text = "Return to Merchant";
                            $has_return_url = true;
                        }
                        $metadata_raw = $data['transaction']['metadata'] ?? '{}';
                        $metadata = is_string($metadata_raw) ? json_decode($metadata_raw, true) : $metadata_raw;
                        $is_temp_link = isset($metadata['is_temporary']) && $metadata['is_temporary'] == 1;

                        $btn_style = 'display: block; width: 100%; text-align: center; padding: 12px; background: ' . $gateway_color . '; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; transition: opacity 0.2s;';
                        if ($status == 'pending') {
                            $btn_style = 'display: none;';
                        }

                        if (!$is_temp_link) {
                    ?>
                    <a href="<?= $another_payment_url ?>" id="btn-make-another" style="<?= $btn_style ?>"><?= $btn_text ?></a>
                    <?php if(($status == 'completed' || $status == 'pending') && $has_return_url): ?>
                    <div style="background:linear-gradient(145deg, #f8fafc, #f1f5f9); border:1px solid #e2e8f0; border-radius:10px; padding:16px; text-align:left; margin-top:10px; box-shadow:inset 0 2px 4px rgba(255,255,255,0.5);">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <svg style="animation: timer-spin 3s linear infinite; color: #64748b;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg>
                                <span style="font-size:13px; font-weight:600; color:#475569;">Redirecting to Merchant</span>
                            </div>
                            <div style="font-size:12px; font-weight:700; color:<?= $gateway_color ?>; background:<?= $gateway_color ?>1A; padding:2px 8px; border-radius:12px;">
                                <span id="auto-redirect-timer">8</span>s
                            </div>
                        </div>
                        <div style="width:100%; height:8px; background:#e2e8f0; border-radius:6px; overflow:hidden; position:relative;">
                            <div id="auto-redirect-progress" style="width:0%; height:100%; background:linear-gradient(90deg, <?= $gateway_color ?>CC, <?= $gateway_color ?>); transition:width 1s linear; border-radius:6px; position:relative; overflow:hidden;">
                                <div style="position:absolute; top:0; left:0; right:0; bottom:0; background:linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent); animation: timer-shimmer 2s infinite;"></div>
                            </div>
                        </div>
                    </div>
                    <style>
                        @keyframes timer-spin { 100% { transform: rotate(360deg); } }
                        @keyframes timer-shimmer { 0% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }
                    </style>
                    <?php endif; ?>
                    <?php } ?>
                </div>
            </div>

        </div>

        <?php if(($status == 'completed' || $status == 'pending') && isset($has_return_url) && $has_return_url) { ?>
        <script>
            let autoTimeLeft = 8;
            const autoTimerEl = document.getElementById('auto-redirect-timer');
            const autoProgressEl = document.getElementById('auto-redirect-progress');
            
            setTimeout(() => { if(autoProgressEl) autoProgressEl.style.width = '12.5%'; }, 50);
            
            const autoInterval = setInterval(() => {
                autoTimeLeft--;
                if(autoTimerEl) autoTimerEl.innerText = autoTimeLeft;
                if(autoProgressEl) autoProgressEl.style.width = ((8 - autoTimeLeft) * 12.5 + 12.5) + '%';
                
                if (autoTimeLeft <= 0) {
                    clearInterval(autoInterval);
                    window.location.href = "<?= $another_payment_url ?>";
                }
            }, 1000);
        </script>
        <?php } elseif(in_array($status, ['canceled', 'rejected']) && !empty($another_payment_url)) { ?>
        <script>
            // Instant redirect for canceled/rejected
            window.location.href = "<?= $another_payment_url ?>";
        </script>
        <?php } ?>

    <?php } else { 
        // For other statuses (e.g. refunded), keep a simple clean fallback
    ?>
        <div style="background: #fff; padding: 40px; border-radius: 12px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
            <h2 style="color: #333; margin-bottom: 10px;"><?= ucfirst($status) ?></h2>
            <p style="color: #666; margin-bottom: 20px;">The transaction status is <?= $status ?>.</p>
            <?php if($redirect_url) { ?>
                <a href="<?= $redirect_url ?>" style="display: inline-block; background: #e2136e; color: #fff; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 500;">Return to Merchant</a>
            <?php } ?>
        </div>
    <?php } ?>

    <?php echo pp_assets('footer'); ?>
</body>
</html>
