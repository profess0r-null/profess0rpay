<?php
    if (!defined('Profess0rPay_INIT')) {
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
    <title><?php echo $data['lang']['payment_link'] ?? 'Payment Link'; ?> - <?php echo $data['brand']['name'];?></title>
    <link rel="shortcut icon" href="<?php echo $data['brand']['favicon'];?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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

        // Clean Background
        $bgStyle = 'background: #f4f7fe; min-height: 100vh;';
        if (!empty($data['options']['enable_bg_image']) && $data['options']['enable_bg_image'] === 'enabled' && !empty($data['options']['background_image'])) {
            $bgImage = $data['options']['background_image'];
            $bgStyle = "background-image: url('{$bgImage}'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: fixed; min-height: 100vh;";
        }
    ?>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #1f2937;
        }
        
        .btn-primary {
            --tblr-btn-border-color: transparent;
            --tblr-btn-hover-border-color: transparent;
            --tblr-btn-active-border-color: transparent;
            --tblr-btn-color: #ffffff;
            --tblr-btn-bg: <?php echo !empty($data['options']['primary_color']) ? $data['options']['primary_color'] : '#0455A4'; ?>;
            --tblr-btn-hover-color: #ffffff;
            --tblr-btn-hover-bg: <?php echo pp_hexToRgba(!empty($data['options']['primary_color']) ? $data['options']['primary_color'] : '#0455A4', 0.9); ?>;
            --tblr-btn-active-color: #ffffff;
            --tblr-btn-active-bg: <?php echo pp_hexToRgba(!empty($data['options']['primary_color']) ? $data['options']['primary_color'] : '#0455A4', 0.95); ?>;
            --tblr-btn-box-shadow: 0 4px 12px <?php echo pp_hexToRgba(!empty($data['options']['primary_color']) ? $data['options']['primary_color'] : '#0455A4', 0.2); ?>;
            border: none;
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
        }

        .clean-card {
            background: #ffffff;
            border-radius: 24px;
            border: none;
            box-shadow: 0 20px 40px rgba(0,0,0,0.06), 0 1px 3px rgba(0,0,0,0.05);
            overflow: hidden;
            position: relative;
        }

        .form-control, .form-select {
            background-color: #f9fafb !important;
            border: 1px solid #e5e7eb !important;
            color: #1f2937 !important;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 15px;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            background-color: #ffffff !important;
            border-color: <?php echo $data['options']['primary_color'] ?? '#e2136e'; ?> !important;
            box-shadow: 0 0 0 4px <?php echo pp_hexToRgba($data['options']['primary_color'] ?? '#e2136e', 0.15); ?> !important;
        }

        .form-label {
            color: #4b5563 !important;
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 14px;
        }

        .brand-logo-container {
            width: 85px; 
            height: 85px; 
            margin: 0 auto 16px auto; 
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            border-radius: 50%;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            padding: 4px;
        }

        .brand-logo-container img {
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
            border-radius: 50%;
        }

        .brand-title {
            font-weight: 800; 
            font-size: 24px; 
            color: #111827;
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin-bottom: 4px;
        }

        .brand-subtitle {
            color: #6b7280; 
            font-size: 14px; 
            font-weight: 500;
        }
        .btn-pay-now {
            background: linear-gradient(135deg, var(--tblr-btn-bg) 0%, <?php echo pp_hexToRgba($data['options']['primary_color'] ?? '#e2136e', 0.8); ?> 100%);
            border: none;
            color: #fff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px <?php echo pp_hexToRgba($data['options']['primary_color'] ?? '#e2136e', 0.3); ?>;
        }
        .btn-pay-now:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px <?php echo pp_hexToRgba($data['options']['primary_color'] ?? '#e2136e', 0.4); ?>;
        }
        .btn-pay-now:active {
            transform: translateY(1px);
        }
    </style>
</head>
<body style="<?= $bgStyle ?>">
    <div class="container container-tight py-5">
        <div class="text-center mb-4 mt-2">
            <div class="brand-logo-container">
                <?php 
                    $default_link_logo = get_env('payment-link-default-logo', $data['brand']['id']);
                    $display_logo = (!empty($default_link_logo) && $default_link_logo !== '--') ? $default_link_logo : $data['brand']['logo'];
                ?>
                <img src="<?php echo $display_logo;?>" alt="Logo">
            </div>
            
            <h1 class="brand-title">
                <?php echo ($data['brand']['name'] == "--") ? $data['brand']['identifyName'] : $data['brand']['name']; ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="#3b82f6" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 6px;"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path><path d="m9 12 2 2 4-4"></path></svg>
            </h1>
        </div>
        
        <div class="card clean-card">
          <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <h2 style="font-weight: 800; color: #111827; font-size: 22px; margin-bottom: 6px;">Enter Amount to Pay</h2>
                <p style="color: #6b7280; font-size: 14px;">Please fill in your details and the amount in BDT.</p>
            </div>

            <form action="" method="POST" id="form" enctype="multipart/form-data">
                <?php pp_renderFormFields('payment-link-default', $data); ?>
                
                <div class="mt-4 pt-2">
                    <button type="submit" id="payButton" class="btn btn-primary btn-pay-now w-100 py-3" style="font-size: 16px; font-weight: 700; border-radius: 12px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M13 3l0 7l6 0l-8 11l0 -7l-6 0l8 -11" /></svg> 
                        <?php echo $data['lang']['pay_now'] ?? 'Pay Now'; ?>
                    </button>
                </div>
            </form>
          </div>
        </div>
        
        <div class="text-center mt-4">
            <span style="font-size: 12px; color: #9ca3af; font-weight: 500;">Secured by Profess0rPay</span>
        </div>
    </div>

    <?php
       echo pp_assets('footer');
    ?>

    <script data-cfasync="false">
        $(document).ready(function() {
            // Prevent initial zero in amount field
            $('input[name="amount"]').on('input', function() {
                if ($(this).val() === '0') {
                    $(this).val('');
                }
            });

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
                        document.querySelector("#payButton").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M13 3l0 7l6 0l-8 11l0 -7l-6 0l8 -11" /></svg> <?php echo $data['lang']['pay_now'] ?? 'Pay Now'; ?>';

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
                        document.querySelector("#payButton").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M13 3l0 7l6 0l-8 11l0 -7l-6 0l8 -11" /></svg> <?php echo $data['lang']['pay_now'] ?? 'Pay Now'; ?>';
                        createToast({
                            title: 'Error ' + xhr.status,
                            description: xhr.responseText ? xhr.responseText.substring(0, 100) : 'No response text',
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


