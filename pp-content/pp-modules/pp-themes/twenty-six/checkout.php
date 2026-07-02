<?php
    if (!defined('Profess0rPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
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

    if(isset($_GET['cancel'])){
        pp_set_transaction_status($data['transaction']['ref'], 'canceled');

        // Fetch gateway info for logo/color — use gateway_id from $data (now passed from index.php)
        global $db_prefix;
        $c_gateway_color = '#e2136e';
        $c_gateway_logo  = pp_site_address().'assets/images/bkash.png'; // default to bKash logo as requested
        $c_gateway_slug  = '';
        $c_gateway_id    = $data['transaction']['gateway_id'] ?? '';
        if(!empty($c_gateway_id)){
            $params_g = [':gateway_id' => $c_gateway_id];
            $res_g = json_decode(getData($db_prefix.'gateways','WHERE gateway_id = :gateway_id','* FROM',$params_g),true);
            if(!empty($res_g['response'][0])){
                $c_gateway_color = $res_g['response'][0]['primary_color'] ?? '#e2136e';
                $c_gateway_logo  = $res_g['response'][0]['logo'] ?? $c_gateway_logo;
                $c_gateway_slug  = $res_g['response'][0]['slug'] ?? '';
                if($c_gateway_slug == 'bkash-personal'){
                    $c_gateway_logo  = pp_site_address().'assets/images/bkash.png';
                    $c_gateway_color = '#e2136e';
                }
            }
        }

        // Brand info
        $c_amount   = money_round($data['transaction']['amount'] ?? 0, 2);
        if(floor($c_amount) == $c_amount) $c_amount = number_format($c_amount, 0, '.', '');
        $c_currency = $data['transaction']['currency'] ?? 'BDT';
        $c_amountStr = ($c_currency == 'BDT') ? '৳'.$c_amount : $c_amount.' '.$c_currency;
        $c_shopName = htmlspecialchars($data['brand']['name'] ?? '');
        if(empty($c_shopName) || strtolower($c_shopName) === 'default')
            $c_shopName = $data['site_name'] ?? 'Shop';
        $c_invoice = $data['transaction']['ref'];
        $c_favicon = $data['brand']['favicon'] ?? '';
        $c_logo    = $c_gateway_logo ?: ($data['brand']['logo'] ?? '');

        // Support info
        $c_support = [];
        if(isset($data['brand']['support']))
            $c_support = is_string($data['brand']['support']) ? json_decode($data['brand']['support'],true) : $data['brand']['support'];
        $c_wa = !empty($c_support['whatsapp']) ? preg_replace('/[^0-9]/','', $c_support['whatsapp']) : '';

        // Case 1: return_url set → redirect to merchant
        // Use raw_return_url — not the masked 'return_url' which is '--' for initiated transactions
        $return_url = $data['transaction']['raw_return_url'] ?? $data['transaction']['return_url'] ?? '';
        $has_return = (!empty($return_url) && $return_url !== '--');
        
        // Ensure no whitespace is breaking this
        $return_url = trim($return_url);
        $has_return = (!empty($return_url) && $return_url !== '--');

        if($has_return){
            $redirect_to = addQueryParams($return_url, [
                'pp_status'       => 'canceled',
                'transaction_ref' => $c_invoice,
            ]);
        }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Payment Canceled - <?php echo $c_shopName; ?></title>
    <link rel="shortcut icon" href="<?php echo $c_favicon; ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Anek+Bangla:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f4f7fe; padding: 20px 12px 32px; min-height: 100vh; }
        .wrapper { width: 100%; max-width: 420px; margin: 0 auto; background: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-radius: 16px; overflow: hidden; }
        .hdr { background: #fff; padding: 14px 20px 12px; text-align: center; border-bottom: 1px solid #f3f4f6; }
        .hdr img { height: 64px; object-fit: contain; }
        .info-bar { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid #e5e7eb; margin-bottom: 20px; }
        .shop-block { display: flex; align-items: center; gap: 12px; }
        .shop-icon { width: 44px; height: 44px; border-radius: 50%; background: #fce4ec; color: #e2136e; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .shop-name { font-size: 18px; font-weight: 500; color: #1f2937; font-family: 'Anek Bangla','Inter',sans-serif; }
        .shop-inv { font-size: 10px; color: #9ca3af; display: flex; align-items: center; gap: 4px; margin-top: 2px; }
        .inv-ref { display: inline-block; max-width: 140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .total { font-size: 24px; font-weight: 500; color: #2f2f2f; font-family: 'Anek Bangla',sans-serif; flex-shrink: 0; margin-left: 16px; }
        .sup-row { display: flex; justify-content: center; gap: 10px; margin: 0 0 16px; }
        .sup-btn { width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; border-radius: 14px; background: #fff; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        .status-card { background: #fff; border-radius: 12px; margin: 0 20px 20px; padding: 30px 20px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .s-icon { width: 60px; height: 60px; border-radius: 50%; background: #fee2e2; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; }
        .s-title { font-size: 22px; font-weight: 700; color: #b91c1c; margin-bottom: 10px; }
        .s-desc { font-size: 14px; color: #4b5563; line-height: 1.5; margin-bottom: 25px; }
        .detail-box { background: #f9fafb; border: 1px solid #f3f4f6; border-radius: 8px; padding: 15px; margin-bottom: 20px; text-align: left; }
        .detail-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .detail-row:last-child { margin-bottom: 0; }
        .d-label { font-size: 13px; color: #6b7280; font-weight: 500; }
        .d-val { font-size: 13px; color: #1f2937; font-weight: 600; }
        .badge-canceled { font-size: 13px; font-weight: 600; background: #fee2e2; color: #ef4444; padding: 2px 10px; border-radius: 20px; }
        .btn-merchant { display: block; width: 100%; text-align: center; padding: 12px; background: <?php echo $c_gateway_color; ?>; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; margin-top: 20px; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="hdr">
        <img src="<?php echo $c_logo; ?>" alt="<?php echo $c_shopName; ?>">
    </div>
    <div class="info-bar">
        <div class="shop-block">
            <div class="shop-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h2l2.1 9.2a2 2 0 0 0 1.9 1.4h9.2a2 2 0 0 0 1.9-1.4L22 6H7"></path><path d="M9 20a1 1 0 1 0 0 -2 1 1 0 0 0 0 2z"></path><path d="M20 20a1 1 0 1 0 0 -2 1 1 0 0 0 0 2z"></path></svg>
            </div>
            <div>
                <div class="shop-name"><?php echo $c_shopName; ?></div>
                <div class="shop-inv">
                    <span>Inv: </span><span class="inv-ref"><?php echo $c_invoice; ?></span>
                </div>
            </div>
        </div>
        <div class="total"><?php echo $c_amountStr; ?></div>
    </div>

    <?php if($c_wa || (!empty($c_support['telegram']) && $c_support['telegram'] != '--') || (!empty($c_support['email']) && $c_support['email'] != '--')): ?>
    <div class="sup-row">
        <div class="sup-btn" style="color:#4285f4; border:2px solid #bfdbfe;" onclick="document.getElementById('cancel-sup-modal').style.display='flex'">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 14h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7a9 9 0 0 1 18 0v7a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3"/></svg>
        </div>
        <?php if($c_wa): ?>
        <div class="sup-btn" style="color:#25d366; border:2px solid #bbf7d0;" onclick="window.open('https://wa.me/<?php echo $c_wa; ?>','_blank')">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9"/><path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1"/></svg>
        </div>
        <?php endif; ?>
    </div>
    <!-- Support Modal -->
    <div id="cancel-sup-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:20px;" onclick="if(event.target===this)this.style.display='none'">
        <div style="background:#fff;border-radius:16px;padding:24px;width:100%;max-width:380px;position:relative;">
            <button onclick="document.getElementById('cancel-sup-modal').style.display='none'" style="position:absolute;top:16px;right:16px;border:none;background:none;font-size:20px;cursor:pointer;color:#888;">✕</button>
            <h5 style="font-weight:700;margin:0 0 6px;font-size:16px;">Contact Support</h5>
            <p style="font-size:13px;color:#6b7280;margin-bottom:16px;">Need help? Contact <?php echo $c_shopName; ?> below:</p>
            <?php if(!empty($c_support['whatsapp']) && $c_support['whatsapp'] != '--'): ?>
            <a href="https://wa.me/<?php echo $c_support['whatsapp']; ?>?text=Hello, I need help (Invoice: <?php echo $c_invoice; ?>)" target="_blank" style="display:flex;align-items:center;gap:14px;background:#e8f5e9;color:#2e7d32;border-radius:8px;padding:14px;margin-bottom:10px;text-decoration:none;">
                <div style="background:#fff;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9"/><path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1"/></svg></div>
                <div><p style="margin:0;font-weight:600;font-size:14px;">WhatsApp</p><span style="font-size:12px;opacity:0.8;">+<?php echo $c_support['whatsapp']; ?></span></div>
            </a>
            <?php endif; ?>
            <?php if(!empty($c_support['email']) && $c_support['email'] != '--'): ?>
            <a href="mailto:<?php echo $c_support['email']; ?>?subject=Payment Help - <?php echo $c_invoice; ?>" style="display:flex;align-items:center;gap:14px;background:#fff3e0;color:#e65100;border-radius:8px;padding:14px;text-decoration:none;">
                <div style="background:#fff;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10"/><path d="M3 7l9 6l9 -6"/></svg></div>
                <div><p style="margin:0;font-weight:600;font-size:14px;">Email</p><span style="font-size:12px;opacity:0.8;"><?php echo $c_support['email']; ?></span></div>
            </a>
            <?php endif; ?>
            <p style="text-align:center;font-size:11px;color:#9ca3af;margin-top:14px;margin-bottom:0;">Invoice ID: <strong><?php echo $c_invoice; ?></strong></p>
        </div>
    </div>
    <?php endif; ?>

    <div class="status-card">
        <div class="s-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12"/><path d="M6 6l12 12"/></svg>
        </div>
        <h2 class="s-title">Payment Canceled</h2>
        <p class="s-desc">Your payment request has been canceled.</p>

        <div class="detail-box">
            <?php if($c_gateway_slug): ?>
            <div class="detail-row">
                <span class="d-label">Provider</span>
                <span class="d-val" style="text-transform:capitalize;"><?php echo str_replace(['-personal','-merchant','-agent'],'', $c_gateway_slug); ?></span>
            </div>
            <?php endif; ?>
            <div class="detail-row">
                <span class="d-label">Amount</span>
                <span class="d-val"><?php echo $c_amount; ?> <?php echo $c_currency; ?></span>
            </div>
            <div class="detail-row">
                <span class="d-label">Status</span>
                <span class="badge-canceled">Canceled</span>
            </div>
        </div>

        <?php if($has_return): ?>
        <a href="<?php echo $redirect_to; ?>" class="btn-merchant" style="margin-bottom:15px;">Return to Merchant</a>
        <div style="background:linear-gradient(145deg, #f8fafc, #f1f5f9); border:1px solid #e2e8f0; border-radius:10px; padding:16px; text-align:left; margin-top:16px; box-shadow:inset 0 2px 4px rgba(255,255,255,0.5);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <svg style="animation: timer-spin 3s linear infinite; color: #64748b;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg>
                    <span style="font-size:13px; font-weight:600; color:#475569;">Redirecting to Merchant</span>
                </div>
                <div style="font-size:12px; font-weight:700; color:<?php echo $c_gateway_color; ?>; background:<?php echo $c_gateway_color; ?>1A; padding:2px 8px; border-radius:12px;">
                    <span id="auto-redirect-timer">10</span>s
                </div>
            </div>
            <div style="width:100%; height:8px; background:#e2e8f0; border-radius:6px; overflow:hidden; position:relative;">
                <div id="auto-redirect-progress" style="width:0%; height:100%; background:linear-gradient(90deg, <?php echo $c_gateway_color; ?>CC, <?php echo $c_gateway_color; ?>); transition:width 1s linear; border-radius:6px; position:relative; overflow:hidden;">
                    <div style="position:absolute; top:0; left:0; right:0; bottom:0; background:linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent); animation: timer-shimmer 2s infinite;"></div>
                </div>
            </div>
        </div>
        <style>
            @keyframes timer-spin { 100% { transform: rotate(360deg); } }
            @keyframes timer-shimmer { 0% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }
        </style>
        <?php endif; ?>
    </div>
</div>
<?php if($has_return): ?>
<script>
    let autoTimeLeft = 10;
    const autoTimerEl = document.getElementById('auto-redirect-timer');
    const autoProgressEl = document.getElementById('auto-redirect-progress');
    
    setTimeout(() => { autoProgressEl.style.width = '10%'; }, 50);
    
    const autoInterval = setInterval(() => {
        autoTimeLeft--;
        if(autoTimerEl) autoTimerEl.innerText = autoTimeLeft;
        if(autoProgressEl) autoProgressEl.style.width = ((10 - autoTimeLeft) * 10 + 10) + '%';
        
        if (autoTimeLeft <= 0) {
            clearInterval(autoInterval);
            location.href = '<?php echo $redirect_to; ?>';
        }
    }, 1000);
</script>
<?php endif; ?>
</body>
</html>
<?php
        exit();
    }






    $pp_gateways_mfs = pp_gateways('mfs', $data);
    $pp_gateways_bank = pp_gateways('bank', $data);
    $pp_gateways_global = pp_gateways('global', $data);

    if ($data['transaction']['currency'] === 'USDT' || $data['transaction']['currency'] === 'USD') {
        $pp_gateways_mfs['status'] = false;
        $pp_gateways_bank['status'] = false;
        
        if ($pp_gateways_global['status'] === true && !empty($pp_gateways_global['gateway'])) {
            $filtered_crypto = [];
            $crypto_slugs = ['binance-personal', 'nowpayments', 'oxapay'];
            foreach ($pp_gateways_global['gateway'] as $gw) {
                if (in_array($gw['slug'], $crypto_slugs)) {
                    $filtered_crypto[] = $gw;
                }
            }
            $pp_gateways_global['gateway'] = $filtered_crypto;
            if (empty($filtered_crypto)) {
                $pp_gateways_global['status'] = false;
            }
        }
    }

    if(isset($_POST['action-v2']) && $_POST['action-v2'] == 'force-pending-status') {
        global $db_prefix;
        $trxid = escape_string($_POST['trxid'] ?? '');
        $ref = $data['transaction']['ref'];
        
        $columns = ['status', 'trx_id', 'updated_date'];
        $values = ['pending', $trxid, getCurrentDatetime('Y-m-d H:i:s')];
        $condition = "ref = '".$ref."'";
        updateData($db_prefix.'transaction', $columns, $values, $condition);
        
        $final_return_url = pp_checkout_address(); 
        echo json_encode(['status' => 'pending', 'redirect' => $final_return_url]);
        exit;
    }

    if (!function_exists('pp_render_gateway_grid')) {
        function pp_render_gateway_grid($gateways, $checkout_addr, $site_addr, $type) {
            if (empty($gateways)) return;
            
            $grouped = [];
            foreach ($gateways as $row) {
                $logo = (isset($row['slug']) && $row['slug'] == 'bkash-personal') ? rtrim($site_addr, '/') . '/assets/images/bkash.png' : $row['logo'];
                $grouped[$logo][] = $row;
            }
            
            foreach ($grouped as $logo => $items) {
                if (count($items) == 1) {
                    $row = $items[0];
                    ?>
                    <div class="gateway-card" onclick="location.href='<?php echo $checkout_addr; ?>?gateway=<?php echo $row['gateway_id']; ?>'">
                        <img src="<?php echo $logo; ?>" alt="<?php echo htmlspecialchars($row['display'] ?? ''); ?>" class="gateway-logo">
                    </div>
                    <?php
                } else {
                    $modal_id = 'modal-gateway-group-' . md5($logo . $type);
                    ?>
                    <div class="gateway-card" data-bs-toggle="modal" data-bs-target="#<?php echo $modal_id; ?>">
                        <img src="<?php echo $logo; ?>" alt="Gateway" class="gateway-logo">
                    </div>
                    <?php
                }
            }
        }

        function pp_render_gateway_modals($gateways, $checkout_addr, $site_addr, $type) {
            if (empty($gateways)) return;
            
            $grouped = [];
            foreach ($gateways as $row) {
                $logo = (isset($row['slug']) && $row['slug'] == 'bkash-personal') ? rtrim($site_addr, '/') . '/assets/images/bkash.png' : $row['logo'];
                $grouped[$logo][] = $row;
            }
            
            foreach ($grouped as $logo => $items) {
                if (count($items) > 1) {
                    $modal_id = 'modal-gateway-group-' . md5($logo . $type);
                    ?>
                    <div class="modal fade modal-invoice" id="<?php echo $modal_id; ?>" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" style="max-width: 384px;">
                            <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
                                <div class="modal-header" style="border-bottom: none; padding: 24px 24px 8px;">
                                    <h5 class="modal-title fw-bold" style="color: #1f2937; font-size: 1.125rem;">Select Payment Method</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="padding: 8px 24px 24px;">
                                    <p class="text-muted mb-4" style="font-size: 0.875rem; color: #6b7280 !important; line-height: 1.5;">This merchant offers multiple options for this gateway. Please choose one to continue.</p>
                                    <?php foreach ($items as $item) { 
                                        $title = !empty($item['display']) ? $item['display'] : 'Payment Method';
                                        // Custom logic to clean up slug for subtitle
                                        $subtitle = '';
                                        if (!empty($item['slug'])) {
                                            $parts = explode('-', $item['slug']);
                                            $subtitle = ucwords(str_replace('-', ' ', $item['slug']));
                                            if (count($parts) > 0) $subtitle = ucwords($parts[0]);
                                        }
                                    ?>
                                        <div class="gateway-option-card" onclick="location.href='<?php echo $checkout_addr; ?>?gateway=<?php echo $item['gateway_id']; ?>'">
                                            <img src="<?php echo $logo; ?>" alt="Logo">
                                            <div class="info">
                                                <div class="title"><?php echo htmlspecialchars($title); ?></div>
                                                <div class="subtitle"><?php echo htmlspecialchars($subtitle); ?></div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $data['lang']['checkout']?> - <?php echo $data['brand']['name'];?></title>
    <link rel="shortcut icon" href="<?php echo $data['brand']['favicon'];?>">
    <?php
       echo pp_assets('head');
    ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Anek+Bangla:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --zini-blue: #0284c7; /* Tailwind sky-600 */
            --zini-blue-light: #e0f2fe; /* Tailwind sky-100 */
            --zini-blue-border: #bae6fd; /* Tailwind sky-200 */
            --zini-bg: #f0f9ff; /* Tailwind sky-50 */
            --zini-text-main: #1e293b; /* slate-800 */
            --zini-text-muted: #64748b; /* slate-500 */
        }

        body {
            background-color: var(--zini-bg) !important;
            font-family: 'Anek Bangla', 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: flex-start;
            min-height: 100vh;
        }

        .container {
            max-width: 100%;
            width: 100%;
            margin: 0 auto;
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: var(--zini-bg);
        }

        /* Desktop Card Style */
        @media (min-width: 640px) {
            body {
                align-items: center;
                padding: 20px;
            }
            .container {
                max-width: 672px; /* sm:max-w-2xl */
                min-height: auto;
                background: rgba(255, 255, 255, 0.4);
                backdrop-filter: blur(12px);
                border-radius: 24px;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
                border: 1px solid rgba(255,255,255,0.6);
            }
        }

        /* Top Bar */
        .checkout-header-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 8px;
            padding: 8px;
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(8px);
            border: 1px solid #e0f2fe;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(2, 132, 199, 0.08);
        }
        @media (min-width: 640px) {
            .checkout-header-bar {
                margin: 12px;
                padding: 12px;
            }
        }

        .header-icon-btn {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #475569;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .header-icon-btn:hover { background: rgba(0,0,0,0.05); }
        .header-icon-btn svg { width: 28px; height: 28px; stroke-width: 2.5px; }

        /* Brand Row (Avatar + Name) */
        .brand-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            padding: 12px 12px 8px;
        }

        .company-logo {
            width: 72px;
            height: 72px;
            object-fit: cover;
            border-radius: 50% !important;
            border: 2px solid #fff;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            flex-shrink: 0;
        }

        .brand-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
        }

        .company-name {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--zini-text-main);
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: calc(100vw - 120px);
        }
        @media (max-width: 639px) {
            .company-name {
                font-size: 1.1rem;
            }
        }

        .view-details {
            font-size: 0.95rem;
            color: var(--zini-blue);
            background: var(--zini-blue-light);
            padding: 6px 18px;
            border-radius: 999px;
            cursor: pointer;
            font-weight: 600;
            border: 1px solid var(--zini-blue-border);
            transition: all 0.2s;
            margin-top: 4px;
        }
        @media (max-width: 639px) {
            .view-details {
                font-size: 0.65rem;
                padding: 2px 8px;
            }
        }
        .view-details:hover { background: #dbeafe; }

        /* Support Icons */
        .support-row {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 8px;
        }
        @media (min-width: 640px) {
            .support-row {
                gap: 16px;
                margin-top: 20px;
            }
        }

        .support-icon {
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            background: #fff;
            cursor: pointer;
            color: #4285f4;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            border: 2.5px solid #bfdbfe;
        }
        @media (max-width: 639px) {
            .support-icon {
                width: 40px;
                height: 40px;
                border-radius: 12px;
            }
        }
        @media (min-width: 640px) {
            .support-icon {
                width: 60px;
                height: 60px;
                border-radius: 20px;
            }
        }
        .support-icon:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08); }
        .support-icon.whatsapp { 
            color: #25d366; 
            border-color: #bbf7d0; 
        }
        .support-icon svg { width: 30px; height: 30px; }
        .support-icon.headphone svg { width: 27px; height: 27px; }
        @media (max-width: 639px) {
            .support-icon svg { width: 20px; height: 20px; }
            .support-icon.headphone svg { width: 18px; height: 18px; }
        }
        @media (min-width: 640px) {
            .support-icon svg { width: 32px; height: 32px; }
            .support-icon.headphone svg { width: 29px; height: 29px; }
        }

        /* Tabs */
        .tabs-container {
            display: flex;
            gap: 8px;
            margin: 24px 24px 16px;
            overflow-x: auto;
            scrollbar-width: none; /* Firefox */
        }
        .tabs-container::-webkit-scrollbar { display: none; } /* Chrome */
        @media (min-width: 640px) {
            .tabs-container {
                justify-content: center;
                gap: 16px;
                margin: 24px 32px 16px;
            }
        }

        .btn-tab {
            flex: 1;
            min-width: 0;
            padding: 10px 4px;
            background: #fff;
            color: var(--zini-text-muted);
            font-weight: 500;
            font-size: 13px;
            border-radius: 8px !important; /* Force all corners to be rounded */
            margin: 0 !important; /* Prevent global CSS margin interference */
            text-align: center;
            cursor: pointer;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            transition: all 0.2s;
            white-space: nowrap; /* Prevent text from wrapping to two lines */
            line-height: 1.3;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        @media (min-width: 640px) {
            .btn-tab {
                flex: none;
                width: 186px; /* Slightly reduced to guarantee fit */
                font-size: 16px;
                padding: 10px 12px;
            }
        }
        .btn-tab.active {
            background: var(--zini-blue);
            color: #fff;
            border-color: var(--zini-blue);
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
        }

        /* Gateways Grid */
        .gateways-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            padding: 0 24px 112px; /* Space for bottom button on mobile */
        }
        @media (min-width: 640px) {
            .gateways-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr); /* 3 columns strictly */
                gap: 16px;
                padding: 0 32px;
                margin-top: 32px;
            }
        }

        .gateway-card {
            background: #fff;
            border: 1px solid #f1f5f9;
            border-radius: 14px;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            height: 60px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
            transition: all 0.2s;
            width: 100%;
        }
        @media (max-width: 639px) {
            .gateway-card {
                height: 60px;
                border-radius: 10px;
                padding: 6px;
            }
        }
        @media (min-width: 640px) {
            .gateway-card {
                width: 180px;
                height: 80px;
            }
        }
        .gateway-card:hover {
            box-shadow: 0 8px 20px rgba(2, 132, 199, 0.1);
            border-color: #e0f2fe;
            transform: translateY(-2px);
        }
        .gateway-logo {
            max-width: 90%;
            max-height: 56px;
            object-fit: contain;
        }
        @media (max-width: 639px) {
            .gateway-logo {
                max-height: 48px;
                max-width: 90%;
            }
        }

        /* Bottom Pay Button */
        .pay-btn-wrapper {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 16px;
            z-index: 20;
            /* Background gradient to fade out list behind it */
            background: linear-gradient(to top, var(--zini-bg) 70%, transparent);
            max-width: 100%;
            margin: 0 auto;
        }
        
        @media (min-width: 640px) {
            .pay-btn-wrapper {
                position: static;
                background: none;
                padding: 0;
                margin: 32px 32px 24px; /* Align horizontal margin to 32px like the grid and tabs */
            }
        }

        .pay-btn-bottom {
            background: var(--zini-blue-light);
            color: #0369a1; /* text-sky-700 */
            font-weight: 700;
            font-size: 18px; /* text-lg */
            padding: 14px 0; /* py-3.5 */
            border-radius: 12px; /* rounded-xl */
            text-align: center;
            width: 100%;
            border: none;
            box-shadow: none;
            cursor: pointer;
            transition: all 0.2s;
            display: block;
        }
        .pay-btn-bottom:hover {
            background: #bae6fd;
            transform: translateY(-1px);
        }

        /* Modals & Utils */
        .invoice-detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            background: var(--zini-blue-light);
            border-radius: 999px;
            margin-bottom: 10px;
            font-size: 0.82rem;
        }
        .invoice-detail-row .label { color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 0.72rem; }
        .invoice-detail-row .value { font-weight: 700; color: #1e293b; }
        .invoice-detail-row.amount-row { background: #dbeafe; margin-top: 4px; }
        .modal-invoice .modal-content { border-radius: 16px; border: none; }
        .modal-invoice .modal-header { border-bottom: none; padding: 20px 20px 8px; }
        .modal-invoice .modal-body { padding: 8px 20px 20px; }
        .copy-invoice-btn {
            width: 100%; background: var(--zini-blue); color: #fff; border: none; border-radius: 10px;
            padding: 13px; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer;
        }
        .modal-support .modal-content { border-radius: 12px; border: none; }
        .modal-support .modal-header { border-bottom: none; padding-bottom: 0; }
        .modal-support .modal-body { padding-top: 10px; }
        .support-card {
            border-radius: 8px; padding: 15px; margin-bottom: 10px; display: flex; align-items: center; gap: 15px;
            cursor: pointer; text-decoration: none; transition: all 0.2s;
        }
        .support-card:hover { transform: translateY(-2px); }
        .support-card.whatsapp { background: #e8f5e9; color: #2e7d32; }
        .support-card.telegram { background: #e3f2fd; color: #1565c0; }
        .support-card.email { background: #fff3e0; color: #e65100; }
        .support-card .icon-wrapper { background: white; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; }
        .support-card .info p { margin: 0; font-size: 0.95rem; font-weight: 600; }
        .support-card .info span { font-size: 0.8rem; opacity: 0.8; }
        
        .gateway-option-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 0 16px;
            display: flex;
            align-items: center;
            gap: 16px;
            cursor: pointer;
            transition: all 0.2s;
            margin: 0 auto 12px auto;
            width: 100%;
            height: 71px;
        }
        .gateway-option-card:hover {
            background: #f9fafb;
            border-color: #d1d5db;
        }
        .gateway-option-card img {
            max-width: 65px;
            max-height: 40px;
            object-fit: contain;
        }
        .gateway-option-card .info {
            display: flex;
            flex-direction: column;
        }
        .gateway-option-card .title {
            font-weight: 600;
            color: #1f2937;
            font-size: 0.95rem;
        }
        .gateway-option-card .subtitle {
            font-size: 0.8rem;
            color: #6b7280;
            text-transform: capitalize;
        }

        .footer-branding { display: none !important; }
    </style>

    <?php
        $seoTitle = trim($data['options']['seo_title'] ?? '');
        $seoDesc  = trim($data['options']['seo_description'] ?? '');
        $seoKey   = trim($data['options']['seo_keywords'] ?? '');
        $analyticsCode = trim($data['options']['analytics_code'] ?? '');

        if ($seoTitle !== '' && $seoTitle !== '--') {
            echo '<title>' . htmlspecialchars($seoTitle) . '</title>' . PHP_EOL;
            echo '<meta name="title" content="' . htmlspecialchars($seoTitle) . '">' . PHP_EOL;
            echo '<meta property="og:title" content="' . htmlspecialchars($seoTitle) . '">' . PHP_EOL;
        }

        if ($seoDesc !== '' && $seoDesc !== '--') {
            echo '<meta name="description" content="' . htmlspecialchars($seoDesc) . '">' . PHP_EOL;
            echo '<meta property="og:description" content="' . htmlspecialchars($seoDesc) . '">' . PHP_EOL;
        }

        if ($seoKey !== '' && $seoKey !== '--') {
            echo '<meta name="keywords" content="' . htmlspecialchars($seoKey) . '">' . PHP_EOL;
        }

        if ($analyticsCode !== '' && $analyticsCode !== '--') {
            echo $analyticsCode;
        }

        $bgStyle = 'background-color:#e8f2fa;';
        if (!empty($data['options']['enable_bg_image']) &&$data['options']['enable_bg_image'] === 'enabled' &&!empty($data['options']['background_image'])) {
            $bgImage = $data['options']['background_image'];
            $bgStyle = "
                background-image: url('{$bgImage}');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                background-attachment: fixed;
            ";
        }
    ?>
</head>
<body style="font-family: 'Anek Bangla', 'Inter', sans-serif;">
    <?php
        $support = is_array($data['brand']['support']) ? $data['brand']['support'] : json_decode($data['brand']['support'] ?? '[]', true);
        $wa_num = !empty($support['whatsapp']) ? preg_replace('/[^0-9]/', '', $support['whatsapp']) : '';
    ?>
    <div class="container">
              
        <!-- Header -->
        <div class="checkout-header-bar">
            <div onclick="event.preventDefault(); event.stopPropagation(); history.back();" class="header-icon-btn" title="Back">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
            </div>
            <div onclick="event.preventDefault(); event.stopPropagation(); showCancelModal();" class="header-icon-btn" title="Close">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
            </div>
        </div>

        <!-- Logo & Name -->
        <div class="brand-row">
            <img src="<?php echo $data['brand']['favicon'];?>" alt="" class="company-logo">
            <div class="brand-info">
                <div class="company-name"><?php echo $data['brand']['name'];?></div>
                <div class="view-details" data-bs-toggle="modal" data-bs-target="#modal-invoice-details">View Details</div>
            </div>
        </div>

        <!-- Support Icons -->
        <div class="support-row">
            <div class="support-icon headphone" data-bs-toggle="modal" data-bs-target="#modal-contact-support" title="Contact Support">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-headphones"><path d="M3 14h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7a9 9 0 0 1 18 0v7a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3"/></svg>
            </div>
            <?php if($wa_num) { ?>
            <div class="support-icon whatsapp" onclick="window.open('https://wa.me/<?php echo $wa_num; ?>', '_blank')" title="WhatsApp">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9" /><path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1" /></svg>
            </div>
            <?php } else { ?>
            <div class="support-icon whatsapp" onclick="window.open('https://wa.me/', '_blank')" title="WhatsApp">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9" /><path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1" /></svg>
            </div>
            <?php } ?>
        </div>

        <!-- Tabs -->
        <div class="tabs-container">
            <?php if ($pp_gateways_mfs['status'] === true && !empty($pp_gateways_mfs['gateway'])) { ?>
                <div class="btn-tab active" data-tab="mfs"><?php echo str_replace(' ', "\n", $data['lang']['mobile_banking']); ?></div>
            <?php } ?>
            
            <?php if ($pp_gateways_bank['status'] === true && !empty($pp_gateways_bank['gateway'])) { ?>
                <div class="btn-tab" data-tab="bank"><?php echo str_replace(' ', "\n", $data['lang']['net_banking']); ?></div>
            <?php } ?>
            
            <?php if ($pp_gateways_global['status'] === true && !empty($pp_gateways_global['gateway'])) { ?>
                <div class="btn-tab" data-tab="global">Crypto</div>
            <?php } ?>
        </div>

        <!-- Gateways Grid -->
        <div id="gateways-mfs" class="gateways-grid" style="display: none;">
            <?php 
                if ($pp_gateways_mfs['status'] === true) {
                    pp_render_gateway_grid($pp_gateways_mfs['gateway'], pp_checkout_address(), pp_site_address(), 'mfs');
                }
            ?>
        </div>

        <div id="gateways-bank" class="gateways-grid" style="display: none;">
            <?php 
                if ($pp_gateways_bank['status'] === true) {
                    pp_render_gateway_grid($pp_gateways_bank['gateway'], pp_checkout_address(), pp_site_address(), 'bank');
                }
            ?>
        </div>

        <div id="gateways-global" class="gateways-grid" style="display: none;">
            <?php 
                if ($pp_gateways_global['status'] === true) {
                    pp_render_gateway_grid($pp_gateways_global['gateway'], pp_checkout_address(), pp_site_address(), 'global');
                }
            ?>
        </div>

        <div class="pay-btn-wrapper">
            <button class="pay-btn-bottom">
                Pay <?php 
                $amt = money_round($data['transaction']['amount'] ?? 0, 2);
                if (floor($amt) == $amt) {
                    $amt = number_format($amt, 0, '.', '');
                }
                echo $amt.' '.$data['transaction']['currency'];
                ?>
            </button>
        </div>

    </div>

    <!-- Invoice Details Modal -->
    <div class="modal fade modal-invoice" id="modal-invoice-details" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="color: var(--zini-blue);">Invoice Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="invoice-detail-row">
                        <span class="label">Invoice ID</span>
                        <span class="value" style="display:flex; align-items:center; gap:8px; max-width: 60vw;">
                            <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($data['transaction']['ref']); ?></span>
                            <div style="cursor:pointer; color:#64748b; display:flex; flex-shrink: 0;" onclick="copy_value('<?php echo htmlspecialchars($data['transaction']['ref']); ?>')" title="Copy">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="14px" width="14px" xmlns="http://www.w3.org/2000/svg"><path d="M320 448v40c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24V120c0-13.255 10.745-24 24-24h72v296c0 30.879 25.121 56 56 56h168zm0-344V0H152c-13.255 0-24 10.745-24 24v360c0 13.255 10.745 24 24 24h272c13.255 0 24-10.745 24-24V128H344c-13.2 0-24-10.8-24-24zm120.971-31.029L375.029 7.029A24 24 0 0 0 358.059 0H352v96h96v-6.059a24 24 0 0 0-7.029-16.97z"></path></svg>
                            </div>
                        </span>
                    </div>
                    <div class="invoice-detail-row">
                        <span class="label">Fee</span>
                        <span class="value"><?php 
                            $fee = money_round($data['transaction']['processing_fee'] ?? 0, 2);
                            if(floor($fee) == $fee) $fee = number_format($fee, 0, '.', '');
                            echo $fee.' '.$data['transaction']['currency'];
                        ?></span>
                    </div>
                    <div class="invoice-detail-row">
                        <span class="label">Customer Name</span>
                        <span class="value"><?php echo !empty($data['transaction']['customer_name']) ? htmlspecialchars($data['transaction']['customer_name']) : 'N/A'; ?></span>
                    </div>
                    <div class="invoice-detail-row">
                        <span class="label">Email</span>
                        <span class="value"><?php echo !empty($data['transaction']['customer_email']) ? htmlspecialchars($data['transaction']['customer_email']) : 'N/A'; ?></span>
                    </div>
                    <div class="invoice-detail-row amount-row">
                        <span class="label">Amount</span>
                        <span class="value"><?php 
                            $tot = money_round($data['transaction']['amount'] ?? 0, 2);
                            if(floor($tot) == $tot) $tot = number_format($tot, 0, '.', '');
                            echo $tot.' '.$data['transaction']['currency'];
                        ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Support Modal -->
    <div class="modal fade modal-support" id="modal-contact-support" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header px-4 pt-4">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon text-primary"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 15a2 2 0 0 1 2 -2h1a2 2 0 0 1 2 2v3a2 2 0 0 1 -2 2h-1a2 2 0 0 1 -2 -2l0 -3" /><path d="M15 15a2 2 0 0 1 2 -2h1a2 2 0 0 1 2 2v3a2 2 0 0 1 -2 2h-1a2 2 0 0 1 -2 -2l0 -3" /><path d="M4 15v-3a8 8 0 0 1 16 0v3" /></svg>
                        Contact Support
                    </h5> 
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pb-4"> 
                    <p class="text-muted small mb-4">Need help with your payment? Contact <?php echo $data['brand']['name'];?> through any of these channels:</p>

                    <?php if(!empty($support['whatsapp']) && $support['whatsapp'] != '--'): ?>
                        <a href="https://wa.me/<?php echo $support['whatsapp']?>?text=Hello, I need help with my payment (Invoice ID: <?php echo $data['transaction']['ref']; ?>)" target="_blank" class="support-card whatsapp">
                            <div class="icon-wrapper">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9" /><path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1" /></svg>
                            </div>
                            <div class="info">
                                <p>WhatsApp</p>
                                <span>+<?php echo $support['whatsapp']?></span>
                            </div>
                        </a>
                    <?php endif; ?>

                    <?php if(!empty($support['telegram']) && $support['telegram'] != '--' && $support['telegram'] != 'https://t.me/--'): ?>
                        <a href="<?php echo $support['telegram']?>" target="_blank" class="support-card telegram">
                            <div class="icon-wrapper">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 10l-4 4l6 6l4 -16l-18 7l4 2l2 6l3 -4" /></svg>
                            </div>
                            <div class="info">
                                <p>Telegram</p>
                                <span><?php echo str_replace('https://t.me/', '@', $support['telegram'])?></span>
                            </div>
                        </a>
                    <?php endif; ?>

                    <?php if(!empty($support['email']) && $support['email'] != '--'): ?>
                        <a href="mailto:<?php echo $support['email']?>?subject=Payment Help - <?php echo $data['transaction']['ref']; ?>" target="_blank" class="support-card email">
                            <div class="icon-wrapper">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10" /><path d="M3 7l9 6l9 -6" /></svg>
                            </div>
                            <div class="info">
                                <p>Email</p>
                                <span><?php echo $support['email']?></span>
                            </div>
                        </a>
                    <?php endif; ?>

                    <div class="text-center mt-4">
                        <p class="small text-muted" style="font-size: 0.75rem;">Include your invoice ID: <strong><?php echo $data['transaction']['ref']; ?></strong> when contacting support</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gateway Group Modals -->
    <?php
        if ($pp_gateways_mfs['status'] === true) pp_render_gateway_modals($pp_gateways_mfs['gateway'], pp_checkout_address(), pp_site_address(), 'mfs');
        if ($pp_gateways_bank['status'] === true) pp_render_gateway_modals($pp_gateways_bank['gateway'], pp_checkout_address(), pp_site_address(), 'bank');
        if ($pp_gateways_global['status'] === true) pp_render_gateway_modals($pp_gateways_global['gateway'], pp_checkout_address(), pp_site_address(), 'global');
    ?>

    <div class="modal fade" id="modal-language" data-bs-keyboard="false" tabindex="-1" aria-labelledby="scrollableLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scrollableLabel"><?php echo $data['lang']['select_language']?></h5> 
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body"> 
                    <div class="form-group mt-1">
                        <label for="" class="form-label"><?php echo $data['lang']['language']?> <span class="text-danger">*</span></label>
                        <div class="form-control-wrap">
                            <select class="form-select" id="model-languages" onchange="hitLanguage()">
                                <option value="" selected><?php echo $data['lang']['select_a_language']?></option>
                                <?php foreach ($data['supported_languages'] ?? [] as $code => $language): ?>
                                    <option value="<?= htmlspecialchars($code) ?>"><?= htmlspecialchars($language) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal"><?php echo $data['lang']['close']?></button>
                </div>
            </div>
        </div>
    </div>

    <?php
       echo pp_assets('footer');
    ?>

    <script data-cfasync="false">
        document.addEventListener('DOMContentLoaded', function() {
            const autoButtons = document.querySelectorAll('.tabs-container .btn-tab, .btn-group .btn, .btn-group .btn-tab');

            const allButtons = document.querySelectorAll('.tabs-container .btn-tab, .btn-group .btn, .btn-group .btn-tab, .btns-group .btns');

            const rows = {};

            allButtons.forEach(btn => {
                const tab = btn.dataset.tab;
                if (!tab) return; 

                const row = document.getElementById('gateways-' + tab);
                if (row) rows[tab] = row;

                btn.addEventListener('click', function() {
                    allButtons.forEach(b => b.classList.remove('active'));

                    this.classList.add('active');

                    Object.values(rows).forEach(r => r.style.display = 'none');

                    if (rows[tab]) {
                        if (rows[tab].classList.contains('gateways-grid')) {
                            rows[tab].style.display = 'grid';
                        } else if (rows[tab].classList.contains('row')) {
                            rows[tab].style.display = 'flex';
                        } else {
                            rows[tab].style.display = 'block';
                        }
                    }
                });
            });

            if (autoButtons.length > 0) {
                autoButtons[0].click();
            }
        });

        function hitLanguage(){
            var language = document.querySelector("#model-languages").value;

            if(language !== ""){
                location.href = '?lang='+language;
            }
        }

        function copy_value(content) {
            if (!content) return;
            navigator.clipboard.writeText(content).then(function() {
                if (typeof createToast === 'function') {
                    createToast({
                        title: 'Copied',
                        description: 'Invoice ID copied to clipboard',
                        timeout: 1500,
                        top: 20
                    });
                }
            });
        }
    </script>


    <!-- Cancel Invoice Modal -->
    <div id="zini-cancel-modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center; backdrop-filter: blur(3px);">
        <div id="zini-cancel-modal" style="background:#fff; border-radius:12px; width:90%; max-width:420px; padding:30px; text-align:center; box-shadow:0 10px 40px rgba(0,0,0,0.2); font-family: 'Inter', sans-serif;">
            <div id="zini-cancel-step1">
                <div style="width:52px; height:52px; background:#fee2e2; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path></svg>
                </div>
                <h3 style="margin:0 0 12px; font-size:20px; font-weight:700; color:#333;">Cancel Payment</h3>
                <p style="margin:0 0 26px; font-size:14px; color:#666; line-height:1.5;">Are you sure you want to cancel this payment? This action cannot be undone.</p>
                <div style="display:flex; gap:12px;">
                    <button onclick="closeCancelModal()" style="padding:12px 18px; border:none; background:#f1f3f5; color:#495057; border-radius:6px; font-weight:600; font-size:14px; cursor:pointer; flex:1; transition:0.2s;">Go Back</button>
                    <button onclick="processCancel()" style="padding:12px 18px; border:none; background:#e63946; color:#fff; border-radius:6px; font-weight:600; font-size:14px; cursor:pointer; flex:1; transition:0.2s;">Yes, Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function showCancelModal() {
            document.getElementById('zini-cancel-modal-overlay').style.display = 'flex';
        }
        function closeCancelModal() {
            document.getElementById('zini-cancel-modal-overlay').style.display = 'none';
        }
        function processCancel() {
            location.href = '?cancel';
        }
    </script>
</body>
</html>
