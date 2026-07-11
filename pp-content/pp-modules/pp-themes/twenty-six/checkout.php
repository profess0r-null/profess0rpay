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

        // Determine redirect destination
        $return_url = trim($data['transaction']['raw_return_url'] ?? $data['transaction']['return_url'] ?? '');
        $has_return  = (!empty($return_url) && $return_url !== '--');

        if ($has_return) {
            // eCommerce: redirect back to merchant with cancel status
            $redirect_to = addQueryParams($return_url, [
                'pp_status'       => 'canceled',
                'transaction_ref' => $c_invoice,
            ]);
        } else {
            // Default/custom payment link: use brand's redirect_url setting
            $brand_redir = trim($data['brand']['redirect_url'] ?? '');
            if (!empty($brand_redir) && $brand_redir !== '--') {
                $redirect_to = $brand_redir;
                $has_return  = true;
            } else {
                // If NO return_url and NO brand redirect_url, fallback to the brand's payment link page to generate a new invoice
                $brand_slug = $data['brand']['slug'] ?? '';
                $redirect_to = pp_site_address() . $brand_slug;
            }
            // Always set true so the progress bar and redirect timer always work
            $has_return = true;
        }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Invoice Canceled – <?php echo htmlspecialchars($c_shopName); ?></title>
    <link rel="shortcut icon" href="<?php echo $c_favicon; ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Anek+Bangla:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 16px;
        }
        .cancel-card {
            background: #ffffff;
            border-radius: 12px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            padding: 40px 30px;
            text-align: center;
            border: 1px solid #f3f4f6;
        }
        .cancel-icon-wrapper {
            width: 80px;
            height: 80px;
            background: rgba(239, 68, 68, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 0 0 8px rgba(239, 68, 68, 0.05);
            animation: gentle-zoom 2.5s ease-in-out infinite;
        }
        
        @keyframes gentle-zoom {
            0% { transform: scale(1); }
            50% { transform: scale(1.08); }
            100% { transform: scale(1); }
        }

        .cancel-icon {
            width: 52px;
            height: 52px;
            background: #ef4444;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
        }
        .cancel-icon svg {
            width: 26px;
            height: 26px;
            stroke: #ffffff;
            stroke-width: 5;
        }
        .cancel-title {
            font-size: 22px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 10px;
            letter-spacing: -0.3px;
        }
        .cancel-desc {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.5;
            margin-bottom: 30px;
            padding: 0 10px;
        }
        
        /* Redirect section */
        .redirect-section {
            margin-top: 10px;
        }
        .redirect-text {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 8px;
            font-weight: 500;
        }
        .progress-track {
            width: 100%;
            height: 10px; /* Thicker progress bar */
            background: #f3f4f6;
            border-radius: 5px;
            overflow: hidden;
            position: relative;
        }
        .progress-fill {
            height: 100%;
            width: 100%;
            background: #ef4444;
            border-radius: 5px;
            transition: width 1s linear;
        }
    </style>
</head>
<body>
    <div class="cancel-card">
        <div class="cancel-icon-wrapper">
            <div class="cancel-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="5" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M18 6l-12 12"/>
                    <path d="M6 6l12 12"/>
                </svg>
            </div>
        </div>
        
        <h2 class="cancel-title">Invoice Canceled</h2>
        <p class="cancel-desc">Your request to cancel the invoice has been processed successfully.</p>

        <?php if($has_return): ?>
        <div class="redirect-section">
            <div class="redirect-text">Redirecting in <span id="r-sec">5</span> seconds...</div>
            <div class="progress-track">
                <div class="progress-fill" id="r-fill"></div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php if($has_return): ?>
    <script>
        var zcUrl   = <?php echo json_encode($redirect_to); ?>;
        var zcLeft  = 5;
        var zcSecEl = document.getElementById('r-sec');
        var zcFill  = document.getElementById('r-fill');
        var zcTimer;

        function zcRedirect() { 
            clearInterval(zcTimer); 
            window.location.replace(zcUrl); 
        }

        // Start from 100% and shrink down to 0%
        // Initially the width is 100% in CSS.
        // We calculate width dynamically: at 5s = 100%, 4s = 80%, etc.
        
        zcTimer = setInterval(function() {
            zcLeft--;
            if (zcSecEl) zcSecEl.textContent = zcLeft;
            if (zcFill)  zcFill.style.width  = (zcLeft * 20) + '%';
            
            if (zcLeft <= 0) { 
                zcRedirect(); 
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
                $logo = (isset($row['slug']) && strpos(strtolower($row['slug']), 'bkash') !== false) ? rtrim($site_addr, '/') . '/assets/images/bkash.png?v='.(file_exists(__DIR__.'/../../../../assets/images/bkash.png') ? filemtime(__DIR__.'/../../../../assets/images/bkash.png') : time()) : $row['logo'];
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
                $logo = (isset($row['slug']) && strpos(strtolower($row['slug']), 'bkash') !== false) ? rtrim($site_addr, '/') . '/assets/images/bkash.png?v='.(file_exists(__DIR__.'/../../../../assets/images/bkash.png') ? filemtime(__DIR__.'/../../../../assets/images/bkash.png') : time()) : $row['logo'];
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
            <div onclick="event.preventDefault(); event.stopPropagation(); <?php echo $has_return ? 'location.href=\''.addslashes($return_url).'\';' : 'history.back();'; ?>" class="header-icon-btn" title="Back">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
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
                            <div style="cursor:pointer; color:#64748b; display:flex; flex-shrink: 0;" onclick="pp_copy('<?php echo htmlspecialchars($data['transaction']['ref']); ?>', 'Invoice ID copied!')" title="Copy">
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
                        <span class="value"><?php echo !empty($data['transaction']['customer']['name']) ? htmlspecialchars($data['transaction']['customer']['name']) : 'N/A'; ?></span>
                    </div>
                    <div class="invoice-detail-row">
                        <span class="label">Email</span>
                        <span class="value"><?php echo !empty($data['transaction']['customer']['email']) ? htmlspecialchars($data['transaction']['customer']['email']) : 'N/A'; ?></span>
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
