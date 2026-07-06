<?php
if (!defined('Profess0rPay_INIT')) {
    http_response_code(403);
    exit('Direct access not allowed');
}

if ($global_user_login == true) {
    ?>
    <script>location.href = "<?php echo $site_url.$path_admin ?>/dashboard";</script>
    <?php
    exit();
} else {
    if ($global_user_2fa == true) {
        ?>
        <script>location.href = "<?php echo $site_url ?>2fa";</script>
        <?php
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="author" content="QubePlug Bangladesh">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - Profess0rPay</title>
    <link rel="shortcut icon" href="<?= $professorpay_favicon ?? '' ?>">
    <link rel="stylesheet" href="<?php echo $site_url ?>assets/css/tabler.min.css?v=1.5" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler-vendors.min.css" />

    <style>
      @import url("<?php echo $site_url ?>assets/css/inter.css");
    </style>
    <style>
        :root{
            --tblr-font-monospace: Monaco, Consolas, Liberation Mono, Courier New, monospace;
            --tblr-font-sans-serif: Inter Var, Inter, -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
            --tblr-font-serif: Georgia, Times New Roman, times, serif;
            --tblr-font-comic: Comic Sans MS, Comic Sans, Chalkboard SE, Comic Neue, sans-serif, cursive;
        }
    </style>
</head>
<body cz-shortcut-listen="true">
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <?php 
                    $display_logo = (!empty($global_response_brand['response'][0]['logo']) && $global_response_brand['response'][0]['logo'] !== '--') ? $global_response_brand['response'][0]['logo'] : ($professorpay_logo_light ?? '');
                ?>
                <img src="<?= $display_logo ?>" alt="" style=" height: 40px; ">
            </div>
            <div class="card card-md">
                <div class="card-body">
                    <h2 class="h2 text-center mb-4">Login to your account</h2>
                    <form action="" class="form-method">
                        <input type="hidden" name="action" value="login">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">

                        <div class="mb-3">
                            <label class="form-label">Email or Username</label>
                            <input type="text" class="form-control" name="username" placeholder="Enter email or username" value="<?php echo isset($pp_demo_mode) ? "demo@example.com" : ""; ?>" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">
                                Password
                                <span class="form-label-description">
                                    <a href="forgot">I forgot password</a>
                                </span>
                            </label>
                            <div class="input-group input-group-flat">
                                <input type="password" class="form-control password-input" name="password" placeholder="Enter password" value="<?php echo isset($pp_demo_mode) ? "12345678" : ""; ?>" required>

                                <span class="input-group-text password-toggle" onclick="togglePassword(this)">
                                    <a href="javascript:void(0)" class="link-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="Show password">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-eye">
                                            <path d="M10 12a2 2 0 1 0 4 0"></path>
                                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"></path>
                                        </svg>
                                    </a>
                                </span>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-check">
                                <input type="checkbox" class="form-check-input">
                                <span class="form-check-label">Remember me on this device</span>
                            </label>
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100">Sign in</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo $site_url ?>assets/js/tabler.min.js"></script>
    <script src="<?php echo $site_url ?>assets/js/jquery-3.6.4.min.js"></script>
    <script src="<?php echo $site_url ?>assets/js/custom-toast.js?v=1.2"></script>

    <script data-cfasync="false">
        function togglePassword(el) {
            const inputGroup = el.closest('.input-group') || el.parentElement;
            const passwordInput = inputGroup.querySelector('.password-input');
            const tooltipEl = el.querySelector('[data-bs-toggle="tooltip"]');

            if (!passwordInput) return;

            const isPassword = passwordInput.type === "password";

            // Toggle input type
            passwordInput.type = isPassword ? "text" : "password";

            // Update tooltip text
            const newTitle = isPassword ? "Hide password" : "Show password";
            tooltipEl.setAttribute("title", newTitle);
            tooltipEl.setAttribute("data-bs-original-title", newTitle);

            // Re-init Bootstrap tooltip (important)
            const tooltip = bootstrap.Tooltip.getInstance(tooltipEl);
            if (tooltip) {
                tooltip.dispose();
            }
            new bootstrap.Tooltip(tooltipEl);
        }

        $('.form-method').submit(function (e) {
            e.preventDefault();

            var btn = document.querySelector(".btn-primary").innerHTML;
            document.querySelector(".btn-primary").innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

            var formData = $(this).serialize();

            $.ajax({
                type: 'POST',
                url: 'login',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    document.querySelector(".btn-primary").innerHTML = btn;

                    $('input[name="csrf_token"]').val(response.csrf_token);

                    if (response.status === 'true') {
                        location.href = response.target;
                    } else {
                        createToast({
                            title: response.title,
                            description: response.message,
                            svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                            timeout: 6000
                        });
                    }
                },
                error: function (xhr, status, error) {
                    createToast({
                        title: 'Something Wrong!',
                        description: 'For further assistance, please contact our support team.',
                        svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                        timeout: 6000
                    });
                }
            });
        });
    </script>
</body>
</html>