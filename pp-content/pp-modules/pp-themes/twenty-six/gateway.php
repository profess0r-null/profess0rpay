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
                location.href = '<?php echo pp_checkout_address().'?gateway='.urlencode($_GET['gateway']);?>';
            </script>
<?php
            exit();
        }
    }

    if(isset($_GET['gateway'])){
        $gateway_info = pp_gateway_info($_GET['gateway'], $data);

        if($gateway_info['status'] == false){
            http_response_code(403);
            exit('Direct access not allowed');
        }
    }else{
        http_response_code(403);
        exit('Direct access not allowed');
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
        .container{
            max-width: 650px; 
            width: 100%;
        }
        .company-logo{
            margin-top: 15px;
            height: 50px;
            margin-bottom: 15px;
        }
        .btn-primary {
            --tblr-btn-border-color: transparent;
            --tblr-btn-hover-border-color: transparent;
            --tblr-btn-active-border-color: transparent;
            --tblr-btn-color: <?php echo $gateway_info['gateway']['text_color'];?>;
            --tblr-btn-bg: <?php echo $gateway_info['gateway']['primary_color'];?>;
            --tblr-btn-hover-color: <?php echo $gateway_info['gateway']['text_color'];?>;
            --tblr-btn-hover-bg: <?php echo pp_hexToRgba($gateway_info['gateway']['primary_color'], 0.80)?>;
            --tblr-btn-active-color: <?php echo $gateway_info['gateway']['text_color'];?>;
            --tblr-btn-active-bg: <?php echo pp_hexToRgba($gateway_info['gateway']['primary_color'], 0.80)?>;
            --tblr-btn-disabled-bg: <?php echo $gateway_info['gateway']['primary_color'];?>;
            --tblr-btn-disabled-color: <?php echo $gateway_info['gateway']['text_color'];?>;
            --tblr-btn-box-shadow: <?php echo $gateway_info['gateway']['text_color'];?>;
        }
        .form-control:focus{
            border-color: <?php echo $gateway_info['gateway']['primary_color'];?>;
            box-shadow: var(--tblr-shadow-input), 0 0 0 .25rem <?php echo pp_hexToRgba($gateway_info['gateway']['primary_color'], 0.25)?>;
        }

        .payment-instructions{
            background-color: <?php echo $gateway_info['gateway']['primary_color'];?>;
            color: <?php echo $gateway_info['gateway']['text_color'];?>;

            border-radius: 10px;
            padding-top: 5px;
            padding-bottom: 5px;
            padding-left: 20px;
            padding-right: 20px;
            margin: 0px;
        }
        .payment-instructions li {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px 0;
            word-break: break-word;
            border-bottom: 1px solid <?php echo pp_hexToRgba($gateway_info['gateway']['text_color'], 0.25)?>;
        }

        .payment-instructions li .dot{
            width: 6px;
            height: 6px;
            border-radius: 100%;
            background-color: <?php echo $gateway_info['gateway']['text_color'];?>;
            min-width: 6px;
        }
        .payment-instructions li p{
            margin: 0;
        }

        .payment-instructions li .dynamic-value{
            font-weight: 600;
        }

        .payment-instructions li svg{
            width: 17px;
            height: 17px;
        }
        .payment-instructions li .button-icon{
            padding: 5px;
            margin-left: 10px;
            background-color: <?php echo $gateway_info['gateway']['text_color'];?>;
            color: <?php echo $gateway_info['gateway']['primary_color'];?>;
            border-radius: 5px;
            cursor: pointer;
        }

        .payment-instructions li:last-child {
            border-bottom: none;
        }

        .bp-modal {
            position: fixed;
            inset: 0;
            background: rgb(86 85 85 / 13%);
            backdrop-filter: blur(6px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 15px;
        }

        .bp-modal-content {
            position: relative;
            background: #FFFFFF;
            border-radius: 5px;
            padding: 10px;
            max-width: 95vw;
            max-height: 95vh;
            box-shadow: 0 00px 5px rgb(157 145 145 / 60%);
            animation: bpZoomIn 0.25s ease-out;
        }

        .bp-model-image-b{
            margin: 20px;
        }

        #bp-modal-image {
            display: block;
            max-width: 300px;
            border-radius: 10px;
            width: 100%;
        }

        .bp-close {
            position: absolute;
            top: -12px;
            right: -12px;
            width: 36px;
            height: 36px;
            background: #ff4d4f;
            color: #fff;
            font-size: 22px;
            font-weight: bold;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 0px 5px rgba(0, 0, 0, 0.4);
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .bp-close:hover {
            background: #ff1f1f;
            transform: scale(1.1);
        }

        @keyframes bpZoomIn {
            from {
                transform: scale(0.92);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        @media (max-width: 576px) {
            .bp-close {
                top: -10px;
                right: -10px;
                width: 32px;
                height: 32px;
                font-size: 20px;
            }
        }
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

        $bgStyle = 'background-color:#f8f9fa;';
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
<body style="<?= $bgStyle ?>; font-family: 'Anek Bangla', 'Inter', sans-serif;" loading="lazy">
    <?php if ($gateway_info['gateway']['tab'] == 'mfs'): ?>
        <style>
            /* Override inline body style from $bgStyle */
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                background: #e8e8e8 !important;
                background-color: #e8e8e8 !important;
                background-image: none !important;
                min-height: 100vh !important;
                overflow-x: hidden;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                box-sizing: border-box;
            }
        </style>
        <?php pp_gateway_render($_GET['gateway'] ?? '', $data); ?>
    <?php else: ?>
        <style>
            body {
                background-color: #f4f6f8 !important;
                display: block;
                min-height: 100vh;
                margin: 0;
                padding: 40px 15px;
                box-sizing: border-box;
                overflow-x: hidden;
            }
            .zini-gateway-card {
                max-width: 500px;
                width: 100%;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.05);
                margin: 0 auto;
                padding: 20px;
            }
        </style>
        <div class="zini-gateway-card">
            <div class="d-flex align-items-center mb-1" style="padding: 0 5px; margin-bottom: 15px;">
                <div onclick="window.history.length > 1 ? history.back() : location.replace('<?php echo pp_checkout_address();?>');" style="cursor: pointer; color: #555; transition: 0.2s;" onmouseover="this.style.color='#000'" onmouseout="this.style.color='#555'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                </div>
            </div>

            <?php if ($gateway_info['gateway']['tab'] != 'bank'): ?>
            <center>
                <?php 
                $g_logo = $gateway_info['gateway']['logo'];
                if (isset($gateway_info['gateway']['slug']) && strpos(strtolower($gateway_info['gateway']['slug']), 'bkash') !== false) {
                    $g_logo = pp_site_address() . 'assets/images/bkash.png';
                }
                ?>
                <img src="<?php echo $g_logo;?>" alt="" class="company-logo" style="margin-bottom: 20px;">
            </center>
            <?php endif; ?>

            <?php
                pp_gateway_render($_GET['gateway'] ?? '', $data);
            ?>

            <center class="footer-branding" style="margin-top: 24px; font-size: 12px; color: #888;"><?php echo $data['options']['watermark_text'];?></center>
        </div>


    <?php endif; ?>

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
                                <?php
                                    foreach ($gateway_info['supported_languages'] ?? [] as $code => $language) {
                                ?>
                                            <option value="<?= htmlspecialchars($code) ?>">
                                                <?= htmlspecialchars($language) ?>
                                            </option>
                                <?php
                                    }
                                ?>
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
        var ppLang = {
            copied:        '<?php echo addslashes($data['lang']['copied_successfully'])?>',
            copiedDesc:    '<?php echo addslashes($data['lang']['copy_content_copied'])?>',
            copyFailed:    '<?php echo addslashes($data['lang']['copy_failed'])?>',
            copyFailedDesc:'<?php echo addslashes($data['lang']['copy_failed_text'])?>',
            noContent:     '<?php echo addslashes($data['lang']['copy_no_content'])?>',
            somethingWrong:'<?php echo addslashes($data['lang']['something_wrong'])?>',
            supportText:   '<?php echo addslashes($data['lang']['support_contact_text'])?>',
        };

        function failed(title, message){
            createToast({
                title: title,
                description: message,
                svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                timeout: 1500,
                top: 20
            });
        }

        function success(){
            location.href = "<?php echo pp_checkout_address();?>";
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Only buttons that should auto-activate
            const autoButtons = document.querySelectorAll('.btn-group .btn');

            // All buttons for click handling
            const allButtons = document.querySelectorAll('.btn-group .btn, .btns-group .btns');

            const rows = {};

            // Attach click events to all buttons
            allButtons.forEach(btn => {
                const tab = btn.dataset.tab;
                if (!tab) return; // skip buttons without data-tab

                // Store row element if exists
                const row = document.getElementById('gateways-' + tab);
                if (row) rows[tab] = row;

                btn.addEventListener('click', function() {
                    // Remove active from all buttons
                    allButtons.forEach(b => b.classList.remove('active'));

                    // Add active only to clicked button
                    this.classList.add('active');

                    // Hide all rows
                    Object.values(rows).forEach(r => r.style.display = 'none');

                    // Show selected row if it exists
                    if (rows[tab]) rows[tab].style.display = rows[tab].classList.contains('row') ? 'flex' : 'block';
                });
            });

            // ✅ Auto-enable first available tab ONLY from .btn-group .btn
            if (autoButtons.length > 0) {
                autoButtons[0].click();
            }
        });

        function hitLanguage(){
            var language = document.querySelector("#model-languages").value;

            if(language !== ""){
                location.href = '<?php echo pp_checkout_address().'?gateway='.urlencode($_GET['gateway']);?>&lang='+language;
            }
        }

        $(document).ready(function() {
            // Form submission is now handled dynamically by pp-functions.php 
            // to support the new payment workflow and direct redirect.
        });
    </script>
    <!-- Cancel Invoice Modal -->
    <div id="zini-cancel-modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center; backdrop-filter: blur(3px);">
        <div id="zini-cancel-modal" style="background:#fff; border-radius:12px; width:90%; max-width:420px; padding:30px; text-align:center; box-shadow:0 10px 40px rgba(0,0,0,0.2); font-family: 'Inter', sans-serif;">
            
            <div id="zini-cancel-step1">
                <div style="width:56px; height:56px; background:#fff0f0; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#e63946" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" /></svg>
                </div>
                <h3 style="margin:0 0 12px; font-size:20px; font-weight:700; color:#333;">Cancel Payment</h3>
                <p style="margin:0 0 26px; font-size:14px; color:#666; line-height:1.5;">Are you sure you want to cancel this payment? You can choose another payment method.</p>
                <div style="display:flex; gap:12px; justify-content:center;">
                    <button onclick="closeCancelModal()" style="padding:12px 18px; border:none; background:#f1f3f5; color:#495057; border-radius:6px; font-weight:600; font-size:14px; cursor:pointer; flex:1; transition:0.2s;">Go Back</button>
                    <button onclick="processCancel()" style="padding:12px 18px; border:none; background:#e63946; color:#fff; border-radius:6px; font-weight:600; font-size:14px; cursor:pointer; flex:1; transition:0.2s;">Yes, Cancel</button>
                </div>
            </div>

            <div id="zini-cancel-step2" style="display:none;">
                <div style="width:56px; height:56px; background:#e63946; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                </div>
                <h3 style="margin:0 0 12px; font-size:20px; font-weight:700; color:#333;">Payment Canceled</h3>
                <p style="margin:0 0 26px; font-size:14px; color:#666; line-height:1.5;">Your payment process has been canceled.</p>
                
                <div style="margin-top:24px;">
                    <div style="font-size:12px; color:#888; margin-bottom:8px;">Redirecting in <span id="zini-cancel-timer">4</span> seconds...</div>
                    <div style="width:100%; height:4px; background:#f1f3f5; border-radius:2px; overflow:hidden;">
                        <div id="zini-cancel-progress" style="width:0%; height:100%; background:#e63946; transition:width 1s linear;"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script>
        let cancelIsInstant = false;
        function showCancelModal(instant = false) {
            cancelIsInstant = instant;
            document.getElementById('zini-cancel-modal-overlay').style.display = 'flex';
        }
        function closeCancelModal() {
            document.getElementById('zini-cancel-modal-overlay').style.display = 'none';
        }
        function processCancel() {
            if (cancelIsInstant) {
                if (window.history.length > 1) {
                    history.back();
                } else {
                    location.replace('<?php echo pp_checkout_address();?>');
                }
                return;
            }
            document.getElementById('zini-cancel-step1').style.display = 'none';
            document.getElementById('zini-cancel-step2').style.display = 'block';
            
            let timeLeft = 4;
            const timerEl = document.getElementById('zini-cancel-timer');
            const progressEl = document.getElementById('zini-cancel-progress');
            
            setTimeout(() => { progressEl.style.width = '25%'; }, 50);
            
            const interval = setInterval(() => {
                timeLeft--;
                timerEl.innerText = timeLeft;
                progressEl.style.width = ((4 - timeLeft) * 25 + 25) + '%';
                
                if (timeLeft <= 0) {
                    clearInterval(interval);
                    location.href = '<?php echo pp_checkout_address();?>';
                }
            }, 1000);
        }
    </script>
</body>
</html>
