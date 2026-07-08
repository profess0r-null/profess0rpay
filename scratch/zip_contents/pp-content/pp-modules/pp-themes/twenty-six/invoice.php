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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $data['lang']['invoice']?> - <?php echo $data['brand']['name'];?></title>
    <link rel="shortcut icon" href="<?php echo $data['brand']['favicon'];?>">
    <?php
       echo pp_assets('head');
    ?>

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

        $bgStyle = 'background-color:#f8f9fa;';
        if (!empty($data['options']['enable_bg_image']) && $data['options']['enable_bg_image'] === 'enabled' && !empty($data['options']['background_image'])) {
            $bgImage = $data['options']['background_image'];
            $bgStyle = "background-image: url('{$bgImage}'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: fixed;";
        }
    ?>

        .container {
            margin-top: 40px !important;
            margin-bottom: 40px !important;
        }

        .invoice-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 900px;
            margin: 0 auto;
        }

        .invoice-header {
            padding: 40px;
            background: linear-gradient(135deg, rgba(255,255,255,0.05), rgba(255,255,255,0));
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .invoice-body {
            padding: 40px;
        }

        .invoice-footer {
            padding: 30px 40px;
            background: #f8f9fa;
            border-top: 1px solid rgba(0,0,0,0.05);
            text-align: center;
        }

        .info-block {
            background-color: #f8f9fc;
            border-radius: 12px;
            padding: 25px;
            border-left: 4px solid <?php echo $data['options']['primary_color'];?>;
            height: 100%;
        }

        .info-label {
            font-size: 0.85rem;
            color: #858796;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .table-invoice {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 20px;
        }

        .table-invoice th {
            background-color: #f8f9fc;
            color: #4e73df;
            padding: 16px 20px;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e3e6f0;
        }

        .table-invoice th:first-child { border-top-left-radius: 10px; border-bottom-left-radius: 10px; }
        .table-invoice th:last-child { border-top-right-radius: 10px; border-bottom-right-radius: 10px; }

        .table-invoice td {
            padding: 20px;
            vertical-align: middle;
            border-bottom: 1px solid #eaecf4;
            color: #5a5c69;
            font-size: 1rem;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .status-paid {
            background-color: rgba(28, 200, 138, 0.1);
            color: #1cc88a;
        }

        .status-unpaid {
            background-color: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
        }

        .summary-block {
            background-color: #f8f9fc;
            border-radius: 12px;
            padding: 30px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 1.05rem;
            color: #5a5c69;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            font-size: 1.4rem;
            font-weight: 700;
            border-top: 2px solid #e3e6f0;
            padding-top: 15px;
            margin-top: 15px;
        }

        @media only screen and (max-width: 768px) {
            .container { margin-top: 10px !important; margin-bottom: 10px !important; }
            .invoice-card { border-radius: 0; }
            .invoice-header, .invoice-body, .invoice-footer { padding: 25px 20px; }
            .summary-total { font-size: 1.2rem; }
        }

        @media print {
            body { background: white !important; }
            .container { margin: 0 !important; width: 100% !important; max-width: 100% !important; }
            .invoice-card { box-shadow: none !important; border-radius: 0 !important; }
            .no-print { display: none !important; }
            .status-badge { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .table-invoice th, .summary-block, .info-block { background-color: #f8f9fc !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }

        .btn-primary {
            --tblr-btn-color: #ffffff;
            --tblr-btn-bg: linear-gradient(45deg, <?php echo $data['options']['primary_color'];?>, #4776E6);
            --tblr-btn-hover-color: #ffffff;
            --tblr-btn-hover-bg: linear-gradient(45deg, #4776E6, <?php echo $data['options']['primary_color'];?>);
            --tblr-btn-box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            border: none;
            padding: 10px 24px;
            font-size: 1.05rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-success {
            background: linear-gradient(45deg, #1cc88a, #13855c);
            color: white;
            border: none;
            padding: 10px 24px;
            font-size: 1.05rem;
            font-weight: 600;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(28, 200, 138, 0.3);
            transition: all 0.3s ease;
        }
    </style>
</head>
<body style="<?= $bgStyle ?> font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <div class="container invoice-card">
        
        <div class="invoice-header">
            <div>
                <img src="<?php echo $data['brand']['logo'];?>" alt="Logo" style="height: 48px; margin-bottom: 15px; border-radius: 8px;">
                <h3 style="margin: 0; color: #2c3e50; font-weight: 800; letter-spacing: 1px;"><?php echo strtoupper($data['lang']['invoice']); ?></h3>
                <p style="margin: 5px 0 0; color: #858796; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: calc(100vw - 120px);">#<?php echo $data['invoice']['invoice_id'] ?? 'INV-001'; ?></p>
            </div>
            <div style="text-align: right;">
                <div style="cursor: pointer; display: inline-block; color: <?php echo $data['options']['primary_color'];?>; margin-bottom: 15px;" data-bs-target="#modal-language" data-bs-toggle="modal" class="no-print">
                    <svg xmlns="http://www.w3.org/2000/svg" style="padding: 8px; background-color: <?php echo pp_hexToRgba($data['options']['primary_color'], 0.1)?>; border-radius: 50%; width: 42px; height: 42px; transition: transform 0.3s ease;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-language"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6.371c0 4.418 -2.239 6.629 -5 6.629" /><path d="M4 6.371h7" /><path d="M5 9c0 2.144 2.252 3.908 6 4" /><path d="M12 20l4 -9l4 9" /><path d="M19.1 18h-6.2" /><path d="M6.694 3l.793 .582" /></svg>
                </div>
                <div>
                    <?php if($data['invoice']['status'] == "paid"){ ?>
                        <span class="status-badge status-paid"><?php echo $data['lang']['badge_' . $data['invoice']['status']] ?? htmlspecialchars(ucfirst($data['invoice']['status'])); ?></span>
                    <?php } else { ?>
                        <span class="status-badge status-unpaid"><?php echo $data['lang']['badge_' . $data['invoice']['status']] ?? htmlspecialchars(ucfirst($data['invoice']['status'])); ?></span>
                    <?php } ?>
                </div>
            </div>
        </div>
        
        <div class="invoice-body">
            <div class="row" style="margin-top: 10px;">
                <div class="col-md-4 mb-4">
                    <div class="info-block">
                        <div class="info-label"><?php echo $data['lang']['invoice_date']?></div>
                        <div class="info-value mb-3"><?php echo $data['invoice']['created_date']?></div>
                        
                        <div class="info-label"><?php echo $data['lang']['due_date']?></div>
                        <div class="info-value mb-3"><?php echo $data['invoice']['due_date']?></div>
                        
                        <div class="info-label"><?php echo $data['lang']['payment_method']?></div>
                        <div class="info-value"><?php echo ($data['invoice']['status'] == "paid") ? htmlspecialchars($data['invoice']['gateway'] ?? '') : '--'?></div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="info-block" style="border-left-color: #4e73df;">
                        <div class="info-label" style="display: flex; align-items: center; gap: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-building-store"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M3 7v1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1h-18l2 -4h14l2 4" /><path d="M5 21l0 -10.15" /><path d="M19 21l0 -10.15" /><path d="M9 21v-4a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v4" /></svg>
                            <?php echo $data['lang']['bill_from']?>
                        </div>
                        <div class="info-value mb-2"><?php echo $data['brand']['name'];?></div>
                        <div style="color: #6c757d; font-size: 0.95rem;">
                            <?php echo $data['brand']['support']['email'];?><br>
                            <?php echo $data['brand']['support']['phone'];?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="info-block" style="border-left-color: #1cc88a;">
                        <div class="info-label" style="display: flex; align-items: center; gap: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                            <?php echo $data['lang']['bill_to']?>
                        </div>
                        <div class="info-value mb-2"><?php echo $data['invoice']['customer']['name'];?></div>
                        <div style="color: #6c757d; font-size: 0.95rem;">
                            <?php echo $data['invoice']['customer']['email'];?><br>
                            <?php echo $data['invoice']['customer']['mobile'];?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive" style="margin-top: 10px;">
                <table class="table-invoice">
                    <thead>
                        <tr>
                            <th style="width: 5%; text-align: center;">#</th>
                            <th style="width: 45%;"><?php echo $data['lang']['description']?></th>
                            <th style="width: 10%; text-align: center;"><?php echo $data['lang']['qty']?></th>
                            <th style="width: 20%; text-align: right;"><?php echo $data['lang']['unit_price']?></th>
                            <th style="width: 20%; text-align: right;"><?php echo $data['lang']['amount']?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $subtotal = 0;
                            $totalDiscount = 0;
                            $totalVAT = 0;
                            $grandTotal = 0;

                            if (!empty($data['items'])):
                                $counter = 1;
                                foreach ($data['items'] as &$item):

                                    $itemTotalBeforeDiscount = ($item['unitPrice'] ?? 0) * ($item['quantity'] ?? 0);

                                    $discountAmount = $item['discount'] ?? 0;

                                    $priceAfterDiscount = $itemTotalBeforeDiscount - $discountAmount;

                                    $vatAmount = $priceAfterDiscount * (($item['vat'] ?? 0) / 100);

                                    $item['total'] = $priceAfterDiscount + $vatAmount;

                                    $subtotal += $itemTotalBeforeDiscount;
                                    $totalDiscount += $discountAmount;
                                    $totalVAT += $vatAmount;
                                    $grandTotal += $item['total'];
                        ?>
                                    <tr style="transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8f9fc'" onmouseout="this.style.backgroundColor='transparent'">
                                        <td style="text-align: center; font-weight: 500;"><?= $counter; ?></td>
                                        <td>
                                            <div style="color: #2c3e50; font-weight: 500;"><?= htmlspecialchars($item['description']); ?></div>
                                        </td>
                                        <td style="text-align: center; font-weight: 500;"><?= htmlspecialchars($item['quantity']); ?></td>
                                        <td style="text-align: right; font-weight: 500;"><?= money_round($item['unitPrice'] ?? 0, 2).$data['invoice']['currency']; ?></td>
                                        <td style="text-align: right; font-weight: 600; color: #2c3e50;"><?= money_round($item['total'], 2).$data['invoice']['currency']; ?></td>
                                    </tr>
                        <?php
                                    $counter++;
                                endforeach;
                                unset($item);
                            endif;
                        ?>
                    </tbody>
                </table>
            </div>
            
            <div class="row" style="margin-top: 40px;">
                <div class="col-md-7 mb-4">
                    <?php if(!empty($data['invoice']['note'])){ ?>
                        <div style="padding-right: 30px;">
                            <h5 style="color: #4e73df; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.9rem; margin-bottom: 10px;">
                                <?php echo $data['lang']['note']?>
                            </h5>
                            <p style="color: #858796; line-height: 1.6; font-size: 0.95rem;">
                                <?php echo $data['invoice']['note'];?>
                            </p>
                        </div>
                    <?php } ?>
                </div>
                <div class="col-md-5">
                    <div class="summary-block">
                        <div class="summary-row">
                            <span><?php echo $data['lang']['subtotal']?>:</span>
                            <span style="font-weight: 600;"><?php echo money_round($subtotal, 2).$data['invoice']['currency']?></span>
                        </div>
                        <?php if(($data['invoice']['shippingFee'] ?? 0) > 0){ ?>
                        <div class="summary-row">
                            <span><?php echo $data['lang']['shipping']?>:</span>
                            <span style="font-weight: 600;"><?php echo money_round($data['invoice']['shippingFee'] ?? 0, 2).$data['invoice']['currency']?></span>
                        </div>
                        <?php } ?>
                        <?php if($totalVAT > 0){ ?>
                        <div class="summary-row">
                            <span><?php echo $data['lang']['tax']?>:</span>
                            <span style="font-weight: 600;"><?php echo money_round($totalVAT, 2).$data['invoice']['currency']?></span>
                        </div>
                        <?php } ?>
                        <?php if($totalDiscount > 0){ ?>
                        <div class="summary-row" style="color: #1cc88a;">
                            <span><?php echo $data['lang']['discount']?>:</span>
                            <span style="font-weight: 600;">-<?php echo money_round($totalDiscount, 2).$data['invoice']['currency']?></span>
                        </div>
                        <?php } ?>
                        
                        <div class="summary-total" style="color: <?php echo ($data['invoice']['status'] == 'paid') ? '#1cc88a' : '#e74c3c'; ?>;">
                            <span><?php echo ($data['invoice']['status'] == "paid") ? $data['lang']['total'] : $data['lang']['total_due']; ?>:</span>
                            <span><?php echo money_round($grandTotal + ($data['invoice']['shippingFee'] ?? 0), 2).$data['invoice']['currency']?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            
        <div class="invoice-footer">
            <div class="row align-items-center">
                <div class="col-md-6 text-md-start mb-3 mb-md-0">
                    <p style="color: #858796; margin-bottom: 0; font-size: 0.95rem; line-height: 1.5;">
                        <strong style="color: #2c3e50; font-weight: 600;"><?php echo $data['brand']['name'];?></strong><br>
                        <?php echo $data['brand']['address']['street'];?>, <?php echo $data['brand']['address']['city'];?> - <?php echo $data['brand']['address']['postal'];?><br>
                        <?php echo $data['brand']['address']['country'];?>
                    </p>
                    <p style="color: #a0a2b0; margin-top: 10px; font-size: 0.85rem; font-style: italic;">
                        <?php echo $data['lang']['no_signature']?>
                    </p>
                </div>
                <div class="col-md-6 d-flex flex-md-row-reverse justify-content-md-start justify-content-center align-items-center gap-3">
                    <button onclick="window.print()" class="btn btn-primary no-print">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-printer me-2" style="margin-right: 8px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 15a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2l0 -4" /></svg> <?php echo $data['lang']['print_invoice']?>
                    </button>
                    <?php if($data['invoice']['status'] == "unpaid"){ ?>
                        <form action="" method="POST" id="form" enctype="multipart/form-data" class="no-print">
                            <?php pp_renderFormFields('invoice', $data); ?>
                            <button id="payButton" class="btn btn-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-credit-card me-2" style="margin-right: 8px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 8a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3l0 -8" /><path d="M3 10l18 0" /><path d="M7 15l.01 0" /><path d="M11 15l2 0" /></svg> <?php echo $data['lang']['pay_now']?>
                            </button>
                        </form>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

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
        function hitLanguage(){
            var language = document.querySelector("#model-languages").value;

            if(language !== ""){
                location.href = '?lang='+language;
            }
        }

        $(document).ready(function() {
            $('#form').on('submit', function(e) {
                e.preventDefault(); 

                var formData = $(this).serialize(); 

                document.querySelector("#payButton").innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

                $.ajax({
                    url: '<?php echo pp_site_address(); ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: formData, 
                    success: function(data) {
                        document.querySelector("#payButton").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-credit-card"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 8a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3l0 -8" /><path d="M3 10l18 0" /><path d="M7 15l.01 0" /><path d="M11 15l2 0" /></svg> <?php echo $data['lang']['pay_now']?>';

                        if (data.status == "true") {
                            location.href = data.redirect;
                        } else {
                            createToast({
                                title: data.title,
                                description: data.message,
                                svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                                timeout: 6000
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        createToast({
                            title: '<?php echo addslashes($data['lang']['something_wrong'])?>',
                            description: '<?php echo addslashes($data['lang']['support_contact_text'])?>',
                            svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                            timeout: 6000
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
