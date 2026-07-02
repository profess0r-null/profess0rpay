<?php
    if (!defined('Profess0rPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
    }

    if($global_user_2fa == true){

    }else{
        if($global_user_login == true){
?>
            <script>location.href = "<?php echo $site_url.$path_admin?>/dashboard";</script>
<?php
            exit();
        }else{
?>
            <script>location.href = "<?php echo $site_url?>login";</script>
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
    <title>Two-Factor Authentication - Profess0rPay</title>
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
            <img src="<?= $professorpay_logo_light ?? '' ?>" alt="" style=" height: 40px; ">
        </div>
        <form class="card card-md form-method">
            <input type="hidden" name="action" value="2fa-verify">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">

            <div class="card-body">
              <h2 class="card-title card-title-lg text-center mb-4">Authenticate Your Account</h2>
              <p class="my-4 text-center">Login by verifying your 2FA via Google Authenticator</p>
              <div class="my-5">
                <div class="row g-4">
                  <div class="col">
                    <div class="row g-2">
                      <div class="col">
                        <input type="text" name="code_one" class="form-control form-control-lg text-center px-3 py-3" maxlength="1" inputmode="numeric" pattern="[0-9]*" data-code-input="">
                      </div>
                      <div class="col">
                        <input type="text" name="code_two" class="form-control form-control-lg text-center px-3 py-3" maxlength="1" inputmode="numeric" pattern="[0-9]*" data-code-input="">
                      </div>
                      <div class="col">
                        <input type="text" name="code_three" class="form-control form-control-lg text-center px-3 py-3" maxlength="1" inputmode="numeric" pattern="[0-9]*" data-code-input="">
                      </div>
                    </div>
                  </div>
                  <div class="col">
                    <div class="row g-2">
                      <div class="col">
                        <input type="text" name="code_four" class="form-control form-control-lg text-center px-3 py-3" maxlength="1" inputmode="numeric" pattern="[0-9]*" data-code-input="">
                      </div>
                      <div class="col">
                        <input type="text" name="code_five" class="form-control form-control-lg text-center px-3 py-3" maxlength="1" inputmode="numeric" pattern="[0-9]*" data-code-input="">
                      </div>
                      <div class="col">
                        <input type="text" name="code_six" class="form-control form-control-lg text-center px-3 py-3" maxlength="1" inputmode="numeric" pattern="[0-9]*" data-code-input="">
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-footer">
                <div class="btn-list flex-nowrap">
                  <a href="?logout" class="btn btn-3 w-100"> Logout </a>
                  <button class="btn btn-primary btn-3 w-100"> Verify </button>
                </div>
              </div>
            </div>
        </form>
      </div>
    </div>

    <script src="<?php echo $site_url ?>assets/js/tabler.min.js"></script>
    <script src="<?php echo $site_url ?>assets/js/jquery-3.6.4.min.js"></script>
    <script src="<?php echo $site_url ?>assets/js/custom-toast.js?v=1.2"></script>

    <script data-cfasync="false">
        document.addEventListener("DOMContentLoaded", function () {
            var inputs = document.querySelectorAll("[data-code-input]");
            // Attach an event listener to each input element
            for (let i = 0; i < inputs.length; i++) {
            inputs[i].addEventListener("input", function (e) {
                // If the input field has a character, and there is a next input field, focus it
                if (e.target.value.length === e.target.maxLength && i + 1 < inputs.length) {
                inputs[i + 1].focus();
                }
            });
            inputs[i].addEventListener("keydown", function (e) {
                // If the input field is empty and the keyCode for Backspace (8) is detected, and there is a previous input field, focus it
                if (e.target.value.length === 0 && e.keyCode === 8 && i > 0) {
                inputs[i - 1].focus();
                }
            });
            }
        });

        $('.form-method').submit(function(e) {
            e.preventDefault(); 

            var btn = document.querySelector(".btn-primary").innerHTML;
            document.querySelector(".btn-primary").innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

            var formData = $(this).serialize();

            $.ajax({
                type: 'POST',
                url: '2fa',
                data: formData,
                dataType: 'json',
                success: function(response) {
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