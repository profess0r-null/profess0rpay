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
    <title><?php echo $data['lang']['payment_link']?> - <?php echo $data['brand']['name'];?></title>
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

        $bgStyle = '';
        if (!empty($data['options']['enable_bg_image']) && $data['options']['enable_bg_image'] === 'enabled' && !empty($data['options']['background_image'])) {
            $bgImage = $data['options']['background_image'];
            $bgStyle = "background-image: url('{$bgImage}'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: fixed;";
        } else {
            $primary = !empty($data['options']['primary_color']) ? $data['options']['primary_color'] : '#0455A4';
            $bgStyle = "background: linear-gradient(135deg, " . pp_hexToRgba($primary, 0.04) . " 0%, " . pp_hexToRgba($primary, 0.12) . " 100%);";
        }
    ?>

    <style>
        .btn-primary {
            --tblr-btn-border-color: transparent;
            --tblr-btn-hover-border-color: transparent;
            --tblr-btn-active-border-color: transparent;
            --tblr-btn-color: <?php echo !empty($data['options']['text_color']) ? $data['options']['text_color'] : '#ffffff'; ?>;
            --tblr-btn-bg: <?php echo !empty($data['options']['primary_color']) ? $data['options']['primary_color'] : '#0455A4'; ?>;
            --tblr-btn-hover-color: <?php echo !empty($data['options']['text_color']) ? $data['options']['text_color'] : '#ffffff'; ?>;
            --tblr-btn-hover-bg: <?php echo pp_hexToRgba(!empty($data['options']['primary_color']) ? $data['options']['primary_color'] : '#0455A4', 0.9); ?>;
            --tblr-btn-active-color: <?php echo !empty($data['options']['text_color']) ? $data['options']['text_color'] : '#ffffff'; ?>;
            --tblr-btn-active-bg: <?php echo pp_hexToRgba(!empty($data['options']['primary_color']) ? $data['options']['primary_color'] : '#0455A4', 0.95); ?>;
            --tblr-btn-box-shadow: 0 4px 12px <?php echo pp_hexToRgba(!empty($data['options']['primary_color']) ? $data['options']['primary_color'] : '#0455A4', 0.2); ?>;
        }

        .btn-pay-now {
            background: linear-gradient(135deg, var(--tblr-btn-bg) 0%, <?php echo pp_hexToRgba(!empty($data['options']['primary_color']) ? $data['options']['primary_color'] : '#0455A4', 0.8); ?> 100%);
            border: none;
            color: #fff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px <?php echo pp_hexToRgba(!empty($data['options']['primary_color']) ? $data['options']['primary_color'] : '#0455A4', 0.3); ?>;
        }
        .btn-pay-now:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px <?php echo pp_hexToRgba(!empty($data['options']['primary_color']) ? $data['options']['primary_color'] : '#0455A4', 0.4); ?>;
        }
        .btn-pay-now:active {
            transform: translateY(1px);
        }
    </style>
</head>
<body style="<?= $bgStyle ?> font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div class="container py-5" style="max-width: 1000px;">
        <?php if($data['paymentLink']['status'] === 'expired_temp'): ?>
            <div class="card card-md border-0" style="max-width: 500px; margin: 0 auto; box-shadow: 0 15px 35px rgba(0,0,0,0.06); border-radius: 24px;">
                <div class="card-body text-center py-5">
                    <div class="mb-4 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#d63939" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>
                    </div>
                    <h2 class="mb-3 fw-bold">Link Expired</h2>
                    <p class="text-muted">This payment link has expired.</p>
                </div>
            </div>
        <?php elseif($data['paymentLink']['status'] !== "active"): ?>
            <div class="card card-md border-0" style="max-width: 500px; margin: 0 auto; box-shadow: 0 15px 35px rgba(0,0,0,0.06); border-radius: 24px;">
                <div class="card-body text-center py-5">
                    <div class="mb-4 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#d63939" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM4.646 4.646a.5.5 0 0 0 0 .708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646a.5.5 0 0 0-.708 0z"/></svg>
                    </div>
                    <h2 class="mb-3 fw-bold"><?php echo $data['lang']['product_not_active']?></h2>
                    <p class="text-muted"><?php echo $data['lang']['product_not_active_text']?></p>
                </div>
            </div>
        <?php else: ?>
            <div class="card border-0" style="box-shadow: 0 24px 60px -12px rgba(0,0,0,0.15), 0 4px 24px -4px rgba(0,0,0,0.08); border-radius: 28px; overflow: hidden; background: #fff; transition: all 0.3s ease;">
                <div class="row g-0">
                    <!-- Left side: Product Info -->
                    <div class="col-lg-6 position-relative" style="background: linear-gradient(135deg, <?php echo pp_hexToRgba(!empty($data['options']['primary_color']) ? $data['options']['primary_color'] : '#0455A4', 0.08); ?> 0%, <?php echo pp_hexToRgba(!empty($data['options']['primary_color']) ? $data['options']['primary_color'] : '#0455A4', 0.02); ?> 100%);">
                        <div style="position: absolute; top: -100px; left: -100px; width: 250px; height: 250px; background: <?php echo pp_hexToRgba(!empty($data['options']['primary_color']) ? $data['options']['primary_color'] : '#0455A4', 0.2); ?>; border-radius: 50%; filter: blur(80px);"></div>
                        <div class="p-4 p-md-5 d-flex flex-column h-100 position-relative z-1">
                            <?php if(!empty($data['paymentLink']['product']['image'])): ?>
                                <div class="mb-4 text-center">
                                    <img src="<?php echo $data['paymentLink']['product']['image'];?>" alt="Product Image" class="img-fluid" style="max-height: 380px; border-radius: 20px; object-fit: cover; width: 100%; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
                                </div>
                            <?php else: ?>
                                <div class="mb-5 d-flex justify-content-center">
                                    <div style="width: 120px; height: 120px; background: linear-gradient(135deg, <?php echo pp_hexToRgba(!empty($data['options']['primary_color']) ? $data['options']['primary_color'] : '#0455A4', 0.15);?>, <?php echo pp_hexToRgba(!empty($data['options']['primary_color']) ? $data['options']['primary_color'] : '#0455A4', 0.05);?>); border-radius: 30%; display: flex; align-items: center; justify-content: center; box-shadow: 0 15px 30px rgba(0,0,0,0.08); transform: rotate(-5deg);">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="<?php echo !empty($data['options']['primary_color']) ? $data['options']['primary_color'] : '#0455A4';?>" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="transform: rotate(5deg);"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mt-auto text-center pb-2">
                                <h1 class="fw-bold mb-3" style="color: #111827; font-size: 32px; letter-spacing: -0.5px; line-height: 1.2;"><?php echo $data['paymentLink']['product']['title'];?></h1>
                                <p class="text-secondary" style="font-size: 16px; line-height: 1.6; font-weight: 500;"><?php echo nl2br(htmlspecialchars($data['paymentLink']['product']['description']));?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Right side: Payment Form -->
                    <div class="col-lg-6">
                        <div class="p-4 p-md-5">
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                                <h3 class="m-0 fw-bold" style="color: #2d3748; font-size: 22px;">Payment Details</h3>

                            </div>

                            <form action="" method="POST" id="form" enctype="multipart/form-data">
                                <?php pp_renderFormFields('payment-link', $data); ?>
                                <div class="mt-5">
                                    <button type="submit" id="payButton" class="btn btn-primary btn-pay-now w-100 py-3" style="font-size: 18px; font-weight: 700; border-radius: 12px; letter-spacing: 0.5px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M13 3l0 7l6 0l-8 11l0 -7l-6 0l8 -11" /></svg> 
                                        <?php echo $data['lang']['pay_now']?>
                                    </button>
                                </div>
                            </form>
                            
                            <div class="text-center mt-4 pt-3">
                                <small class="text-muted d-flex align-items-center justify-content-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-success"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3" /><path d="M12 11m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M12 12l0 2.5" /></svg>
                                    Secured and encrypted payment
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
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

                var formData = new FormData(this); 

                document.querySelector("#payButton").innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

                $.ajax({
                    url: '<?php echo pp_site_address(); ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: formData, 
                    processData: false,
                    contentType: false,
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

