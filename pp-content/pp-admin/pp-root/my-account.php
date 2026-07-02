<?php
    if (!defined('Profess0rPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
    }
?>

<div class="page-header d-print-none" aria-label="Page header">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
            <!-- Page pre-title -->
                <div class="page-pretitle">My Account</div>
                <h2 class="page-title">My Account</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row g-gs">
            <div class="col-12 col-xxl-4">
                <h2 class="card-title m-0 mb-1">Profile Information</h2>
                <p>Update your account profile information and email address.</p>
            </div>
            <div class="col-12 col-xxl-8">
                <form action="" class="form-my-account-profile-information">
                    <input type="hidden" name="action" value="my-account-profile-information">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">

                    <div class="card card-gutter-md p-2">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="fullname" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Full Name" value="<?php echo $global_user_response['response'][0]['full_name'];?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="<?php echo $global_user_response['response'][0]['username'];?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="email-address" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="email" class="form-control" id="email-address" name="email-address" placeholder="Email Address" value="<?php echo $global_user_response['response'][0]['email'];?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="password" class="form-label">New Password</label>
                                        <div class="form-control-wrap">
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end pt-3">
                        <button class="btn btn-primary btn-my-account-profile-information">Save changes</div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-gs mt-5">
            <div class="col-12 col-xxl-4">
                <h2 class="card-title m-0 mb-1">Browser Sessions</h2>
                <p>Manage and log out your active sessions on other browsers and devices.</p>
            </div>
            <div class="col-12 col-xxl-8">
                <div class="card card-gutter-md p-2">
                    <div class="card-body">
                        <p>If necessary, you may log out of all of your other browser sessions across all of your devices. If you feel your account has been compromised, you should also update your password.</p>
                    </div>
                </div>

                <div class="text-end pt-3">
                    <form action="" class="form-my-account-browser-sessions">
                        <input type="hidden" name="action" value="my-account-account-browser-sessions">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">

                        <button class="btn btn-primary btn-my-account-browser-sessions">Log Out Other Browser Sessions</button> 
                    </form>
                </div>
            </div>
        </div>

        <div class="row g-gs mt-5">
            <div class="col-12 col-xxl-4">
                <h2 class="card-title m-0 mb-1">Two-factor authentication (2FA)</h2>
                <p>Add additional security to your account using two factor authentication.</p>
            </div>
            <div class="col-12 col-xxl-8">
                <div class="card card-gutter-md p-2">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <label class="form-label">Authenticator app</label>

                            <?php
                                if($global_user_response['response'][0]['2fa_status'] == "enable"){
                            ?>
                                    <span class="badge text-bg-primary">Enabled</span>
                            <?php
                                }else{
                            ?>
                                    <span class="badge text-bg-danger">Disabled</span>
                            <?php
                                }
                            ?>
                        </div>

                        <?php
                            if($global_user_response['response'][0]['2fa_status'] == "enable"){
                        ?>
                                <div class="btn btn-md btn-danger mt-1" data-bs-toggle="modal" data-bs-target="#btn-my-account-two-factor-authentication" type="submit">Disable</div>
                        <?php
                            }else{
                        ?>
                                <div class="btn btn-md btn-primary mt-1" data-bs-toggle="modal" data-bs-target="#btn-my-account-two-factor-authentication" type="submit">Enable</div>
                        <?php
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    if($global_user_response['response'][0]['2fa_status'] == "enable"){
?>
        <div class="modal fade" id="btn-my-account-two-factor-authentication" data-bs-keyboard="false" tabindex="-1" aria-labelledby="scrollableLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top">
                <div class="modal-content">
                    <form action="" class="form-my-account-two-factor-authentication">
                        <input type="hidden" name="action" value="my-account-account-two-factor-authentication">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">

                        <div class="modal-header">
                            <h5 class="modal-title" id="scrollableLabel">Two-factor authentication</h5> 
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body"> 
                            <p>To disable 2FA, please enter the authentication code from your Two-Factor Authentication app (e.g., Google Authenticator) and then confirm.</p>

                            <div class="form-group mt-1">
                                <label for="auth-code" class="form-label">Enter the 6-digit code from the authenticator app <span class="text-danger">*</span></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="auth-code" name="auth-code" placeholder="Enter code" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
                            <button class="btn btn-primary btn-my-account-two-factor-authentication">Confirm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <script data-cfasync="false">
            $('.form-my-account-two-factor-authentication').submit(function (e) {
                e.preventDefault();

                var btn = document.querySelector(".btn-my-account-two-factor-authentication").innerHTML;
                document.querySelector(".btn-my-account-two-factor-authentication").innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

                var formData = $(this).serialize();

                $.ajax({
                    type: 'POST',
                    url: '<?php echo $site_url.$path_admin ?>/dashboard',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        document.querySelector(".form-my-account-two-factor-authentication").reset();

                        closeAllBootstrapModals();

                        document.querySelector(".btn-my-account-two-factor-authentication").innerHTML = btn;

                        document.querySelectorAll('input[name="csrf_token"]').forEach(input => {
                            input.value = response.csrf_token;
                        });
                        document.querySelectorAll('input[name="csrf_token_default"]').forEach(input => {
                            input.value = response.csrf_token;
                        });

                        if (response.status === 'true') {
                            createToast({
                                title: response.title,
                                description: response.message,
                                svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#5f38f9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>`,
                                timeout: 6000,
                                top: 70
                            });
                            location.href = '<?php echo $site_url.$path_admin ?>/my-account';
                        } else {
                            createToast({
                                title: response.title,
                                description: response.message,
                                svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                                timeout: 6000,
                                top: 70
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        createToast({
                            title: 'Something Wrong!',
                            description: 'For further assistance, please contact our support team.',
                            svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                            timeout: 6000,
                            top: 70
                        });
                    }
                });
            });
        </script>

<?php
    }else{
?>
        <div class="modal fade" id="btn-my-account-two-factor-authentication" data-bs-keyboard="false" tabindex="-1" aria-labelledby="scrollableLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top">
                <div class="modal-content">
                    <form action="" class="form-my-account-two-factor-authentication">
                        <input type="hidden" name="action" value="my-account-account-two-factor-authentication">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">

                        <div class="modal-header">
                            <h5 class="modal-title" id="scrollableLabel">Two-factor authentication</h5> 
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body"> 
                            <div class="alert alert-info" role="alert">
                                <p class="m-0">You'll need an app like <strong>Google Authenticator</strong> (
                                <a href="https://itunes.apple.com/us/app/google-authenticator/id388497605"
                                target="_blank"
                                class="alert-link">
                                    iOS
                                </a>,
                                <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2"
                                target="_blank"
                                class="alert-link">
                                    Android
                                </a>
                                ) to complete this process.</p>
                            </div>

                            <p>Scan this QR code with your authenticator app:</p>
                            
                            <?php
                                $userEncoded = urlencode($global_user_response['response'][0]['email']);
                                $issuerEncoded = urlencode("Profess0rPay");
                                $secretEncoded = urlencode($global_user_response['response'][0]['2fa_secret']);
                                
                                $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?data=" . urlencode("otpauth://totp/{$issuerEncoded}:{$userEncoded}?secret={$secretEncoded}&issuer={$issuerEncoded}") . "&size=300x300";
                            ?>
                            
                            <center>
                                <img src="<?php echo $qrCodeUrl; ?>" style=" max-width: 180px; " class="img-fluid border p-3 rounded m-2 two-factor-authentication-qr-code" alt="">
                            </center>

                            <p class="mt-3">Or enter this code manually: <strong><?php echo $global_user_response['response'][0]['2fa_secret']?></strong></p> 
                            
                            <div class="form-group mt-1">
                                <label for="auth-code" class="form-label">Enter the 6-digit code from the authenticator app <span class="text-danger">*</span></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="auth-code" name="auth-code" placeholder="Enter code" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
                            <button class="btn btn-primary btn-my-account-two-factor-authentication">Confirm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <script data-cfasync="false">
            $('.form-my-account-two-factor-authentication').submit(function (e) {
                e.preventDefault();

                var btn = document.querySelector(".btn-my-account-two-factor-authentication").innerHTML;
                document.querySelector(".btn-my-account-two-factor-authentication").innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

                var formData = $(this).serialize();

                $.ajax({
                    type: 'POST',
                    url: '<?php echo $site_url.$path_admin ?>/dashboard',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        document.querySelector(".form-my-account-two-factor-authentication").reset();

                        closeAllBootstrapModals();

                        document.querySelector(".btn-my-account-two-factor-authentication").innerHTML = btn;

                        document.querySelectorAll('input[name="csrf_token"]').forEach(input => {
                            input.value = response.csrf_token;
                        });
                        document.querySelectorAll('input[name="csrf_token_default"]').forEach(input => {
                            input.value = response.csrf_token;
                        });

                        if (response.status === 'true') {
                            createToast({
                                title: response.title,
                                description: response.message,
                                svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#5f38f9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>`,
                                timeout: 6000,
                                top: 70
                            });

                            location.href = '<?php echo $site_url.$path_admin ?>/my-account';
                        } else {
                            createToast({
                                title: response.title,
                                description: response.message,
                                svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                                timeout: 6000,
                                top: 70
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        createToast({
                            title: 'Something Wrong!',
                            description: 'For further assistance, please contact our support team.',
                            svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                            timeout: 6000,
                            top: 70
                        });
                    }
                });
            });
        </script>

<?php
    }
?>




<script data-cfasync="false">
    $('.form-my-account-browser-sessions').submit(function (e) {
        e.preventDefault();

        var formData = new FormData(this);

        var my_two_step_verify_code = document.querySelector("#my-two-step-verify-code").value;

        var btnClass = 'btn-my-account-browser-sessions';

        if(my_two_step_verify_code !== ""){
            closeAllBootstrapModals();
            
            formData.append('my-two-step-verify-code', my_two_step_verify_code);

            var btn = document.querySelector('.'+btnClass).innerHTML;

            document.querySelector('.'+btnClass).innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

            $.ajax({
                type: 'POST',
                url: '<?php echo $site_url.$path_admin ?>/dashboard',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false, 
                success: function (response) {
                    document.querySelector("#my-two-step-verify-code").value = '';

                    document.querySelector('.'+btnClass).innerHTML = btn;

                    document.querySelectorAll('input[name="csrf_token"]').forEach(input => {
                        input.value = response.csrf_token;
                    });
                    document.querySelectorAll('input[name="csrf_token_default"]').forEach(input => {
                        input.value = response.csrf_token;
                    });

                    if (response.status === 'true') {
                        createToast({
                            title: response.title,
                            description: response.message,
                            svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#5f38f9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>`,
                            timeout: 6000,
                            top: 70
                        });
                    } else {
                        createToast({
                            title: response.title,
                            description: response.message,
                            svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                            timeout: 6000,
                            top: 70
                        });
                    }
                },
                error: function (xhr, status, error) {
                    createToast({
                        title: 'Something Wrong!',
                        description: 'For further assistance, please contact our support team.',
                        svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                        timeout: 6000,
                        top: 70
                    });
                }
            });
        }else{
            show_two_step_verify_tab(btnClass);
        }
    });


    $('.form-my-account-profile-information').submit(function (e) {
        e.preventDefault();

        var formData = new FormData(this);

        var my_two_step_verify_code = document.querySelector("#my-two-step-verify-code").value;

        var btnClass = 'btn-my-account-profile-information';

        if(my_two_step_verify_code !== ""){
            closeAllBootstrapModals();

            formData.append('my-two-step-verify-code', my_two_step_verify_code);

            var btn = document.querySelector('.'+btnClass).innerHTML;

            document.querySelector('.'+btnClass).innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

            $.ajax({
                type: 'POST',
                url: '<?php echo $site_url.$path_admin ?>/dashboard',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false, 
                success: function (response) {
                    document.querySelector("#my-two-step-verify-code").value = '';

                    document.querySelector('.'+btnClass).innerHTML = btn;

                    document.querySelectorAll('input[name="csrf_token"]').forEach(input => {
                        input.value = response.csrf_token;
                    });
                    document.querySelectorAll('input[name="csrf_token_default"]').forEach(input => {
                        input.value = response.csrf_token;
                    });

                    if (response.status === 'true') {
                        createToast({
                            title: response.title,
                            description: response.message,
                            svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#5f38f9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>`,
                            timeout: 6000,
                            top: 70
                        });
                    } else {
                        createToast({
                            title: response.title,
                            description: response.message,
                            svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                            timeout: 6000,
                            top: 70
                        });
                    }
                },
                error: function (xhr, status, error) {
                    createToast({
                        title: 'Something Wrong!',
                        description: 'For further assistance, please contact our support team.',
                        svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                        timeout: 6000,
                        top: 70
                    });
                }
            });
        }else{
            show_two_step_verify_tab(btnClass);
        }
    });
</script>