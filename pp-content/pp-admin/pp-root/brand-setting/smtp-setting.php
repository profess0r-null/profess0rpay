<?php
if (!defined('Profess0rPay_INIT')) {
    http_response_code(403);
    exit('Direct access not allowed');
}

    if (!canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'brand_settings', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }

    if (!hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'brand_settings', 'view', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }
?>

<div class="page-header d-print-none" aria-label="Page header">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
            <!-- Page pre-title -->
                <div class="page-pretitle">
                    <ol class="breadcrumb breadcrumb-arrow mb-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0)" onclick="load_content('Brand Settings','<?php echo $site_url.$path_admin ?>/brand-setting','nav-item-brand-setting')">Brand Settings</a></li>
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">SMTP Settings</a></li>
                    </ol>
                </div>
                <h2 class="page-title">SMTP Settings</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">
            <form action="" class="form-smtp-setting" enctype="multipart/form-data">
                <input type="hidden" name="action" value="smtp-setting">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">

                <div class="col-12">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">SMTP Configuration</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">SMTP Status</label>
                                    <select class="form-select" name="smtp_status">
                                        <option value="enabled" <?= getEnvValue($db_prefix, $global_response_brand['response'][0]['brand_id'], 'smtp_status') == 'enabled' ? 'selected' : '' ?>>Enabled</option>
                                        <option value="disabled" <?= getEnvValue($db_prefix, $global_response_brand['response'][0]['brand_id'], 'smtp_status') == 'disabled' ? 'selected' : '' ?>>Disabled</option>
                                    </select>
                                    <small class="form-hint">Enable SMTP to securely route emails through an external server. If disabled, defaults to native mail().</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">SMTP Host</label>
                                    <input type="text" class="form-control" name="smtp_host" placeholder="e.g. smtp.gmail.com" value="<?= getEnvValue($db_prefix, $global_response_brand['response'][0]['brand_id'], 'smtp_host'); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">SMTP Port</label>
                                    <input type="number" class="form-control" name="smtp_port" placeholder="e.g. 465 or 587" value="<?= getEnvValue($db_prefix, $global_response_brand['response'][0]['brand_id'], 'smtp_port'); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Encryption</label>
                                    <select class="form-select" name="smtp_encryption">
                                        <option value="tls" <?= getEnvValue($db_prefix, $global_response_brand['response'][0]['brand_id'], 'smtp_encryption') == 'tls' ? 'selected' : '' ?>>TLS (Recommended for 587)</option>
                                        <option value="ssl" <?= getEnvValue($db_prefix, $global_response_brand['response'][0]['brand_id'], 'smtp_encryption') == 'ssl' ? 'selected' : '' ?>>SSL (Recommended for 465)</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">SMTP Username</label>
                                    <input type="text" class="form-control" name="smtp_username" placeholder="e.g. your_email@gmail.com" value="<?= getEnvValue($db_prefix, $global_response_brand['response'][0]['brand_id'], 'smtp_username'); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">SMTP Password</label>
                                    <input type="password" class="form-control" name="smtp_password" placeholder="Enter SMTP password or App Password" value="<?= getEnvValue($db_prefix, $global_response_brand['response'][0]['brand_id'], 'smtp_password'); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'brand_settings', 'edit', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>">
                            <button type="submit" class="btn btn-primary btn-save-smtp">Save Settings</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$('.form-smtp-setting').on('submit', function (e) {
    e.preventDefault();
    let form = $(this);
    let submitBtn = $('.btn-save-smtp');
    let originalText = submitBtn.text();

    $.ajax({
        type: 'POST',
        url: '<?php echo $site_url.$path_admin ?>/dashboard',
        data: form.serialize(),
        beforeSend: function () {
            submitBtn.text('Saving...').prop('disabled', true);
        },
        success: function (response) {
            let res = JSON.parse(response);
            if (res.status == 'true') {
                showToast('success', res.title, res.message);
                $("input[name='csrf_token']").val(res.csrf_token);
            } else {
                showToast('error', res.title, res.message);
                $("input[name='csrf_token']").val(res.csrf_token);
            }
        },
        error: function () {
            showToast('error', 'Error', 'Something went wrong. Please try again.');
        },
        complete: function () {
            submitBtn.text(originalText).prop('disabled', false);
        }
    });
});
</script>
