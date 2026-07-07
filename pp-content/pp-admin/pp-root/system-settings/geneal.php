<?php
if (!defined('Profess0rPay_INIT')) {
    http_response_code(403);
    exit('Direct access not allowed');
}

    if (!canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'system_settings', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }

    if (!hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'system_settings', 'manage_general', $global_user_response['response'][0]['role'])) {
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
                        <li class="breadcrumb-item"><a href="javascript:void(0)" onclick="load_content('System Settings','<?php echo $site_url.$path_admin ?>/system-settings','nav-item-system-settings')">System Settings</a></li>
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">General Settings</a></li>
                    </ol>
                </div>
                <h2 class="page-title">General Settings</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row g-gs">
            <div class="col-12 col-xxl-4">
                <h2 class="card-title m-0 mb-1">Application Settings</h2>
                <p>Configure the general settings for your application</p>
            </div>
            <div class="col-12 col-xxl-8">
                <div class="card p-2">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="default_timezone" class="form-label">Default Timezone<span class="text-danger">*</span></label>
                                    <div class="form-control-wrap">
                                        <?php
                                            $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
                                        ?>
                                        <select class="js-select" id="default_timezone" data-search="true" data-remove="true" data-placeholder="Select timezone" required>
                                            <?php
                                                $selectedTimezone = get_env('geneal-application-settings-default_timezone') === '--' || (get_env('geneal-application-settings-default_timezone') === '') ? '' : get_env('geneal-application-settings-default_timezone');
                                            ?>

                                            <?php foreach ($timezones as $tz): ?>
                                                <option value="<?= $tz ?>" <?= ($tz === $selectedTimezone) ? 'selected' : '' ?>>
                                                    <?= $tz ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="webhook_attempts_limit" class="form-label">Webhook Attempt Limit <svg xmlns="http://www.w3.org/2000/svg" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" style=" width: 20px; height: 20px; " viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-info-circle" aria-label="The number of times the system will retry sending the webhook if it fails. Set 0 to disable retries." data-bs-original-title="The number of times the system will retry sending the webhook if it fails. Set 0 to disable retries."><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path><path d="M12 9h.01"></path><path d="M11 12h1v4h1"></path></svg></label>
                                    <div class="form-control-wrap">
                                        <?php
                                            $selectedwebhook_attempts_limit = get_env('geneal-application-settings-webhook_attempts_limit') === '--' || (get_env('geneal-application-settings-webhook_attempts_limit') === '') ? '1' : get_env('geneal-application-settings-webhook_attempts_limit');
                                        ?>

                                        <select class="js-select" id="webhook_attempts_limit" data-search="true" data-remove="true" data-placeholder="Select attempt limit" required>
                                            <?php for ($i = 0; $i <= 10; $i++): ?>
                                                <option value="<?= $i ?>" <?= ($i == $selectedwebhook_attempts_limit) ? 'selected' : '' ?>>
                                                    <?= $i ?> <?= $i === 1 ? 'Attempt' : 'Attempts' ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <label class="form-label">Homepage Redirect</label>
                                <div class="form-control-wrap mb-2">
                                    <div class="input-group">
                                        <span class="input-group-text"> https:// </span>
                                        <input type="text" class="form-control" id="homepageRedirect" placeholder="example.com or example.com/custom-page" value="<?= get_env('geneal-application-settings-homepageRedirect'); ?>">
                                    </div>
                                </div>
                                <small class="form-hint">
                                    Visitors to the base domain will be redirected here. Use a valid domain or url.
                                </small>
                            </div>

                            <div class="col-lg-12">
                                <label class="form-label">Admin path</label>
                                <div class="form-control-wrap mb-2">
                                    <div class="input-group">
                                        <span class="input-group-text"> <?php echo $site_url?> </span>
                                        <input type="text" class="form-control" id="adminPath" placeholder="admin" value="<?= get_env('geneal-application-settings-adminPath'); ?>">
                                    </div>
                                </div>
                                <small class="form-hint">
                                    Lowercase letters, numbers, and dashes only. Example: admin, console, portal
                                </small>
                            </div>

                            <div class="col-lg-12">
                                <label class="form-label">Invoice path</label>
                                <div class="form-control-wrap mb-2">
                                    <div class="input-group">
                                        <span class="input-group-text"> <?php echo $site_url?> </span>
                                        <input type="text" class="form-control" id="invoicePath" placeholder="invoice" value="<?= get_env('geneal-application-settings-invoicePath'); ?>">
                                    </div>
                                </div>
                                <small class="form-hint">
                                    Lowercase letters, numbers, and dashes only. Example: invoice, myinvoice
                                </small>
                            </div>

                            <div class="col-lg-12">
                                <label class="form-label">Payment Link path</label>
                                <div class="form-control-wrap mb-2">
                                    <div class="input-group">
                                        <span class="input-group-text"> <?php echo $site_url?> </span>
                                        <input type="text" class="form-control" id="paymentLinkPath" placeholder="payment-link" value="<?= get_env('geneal-application-settings-paymentLinkPath'); ?>">
                                    </div>
                                </div>
                                <small class="form-hint">
                                    Lowercase letters, numbers, and dashes only. Example: payment-link, payment_link
                                </small>
                            </div>

                            <div class="col-lg-12">
                                <label class="form-label">Checkout path</label>
                                <div class="form-control-wrap mb-2">
                                    <div class="input-group">
                                        <span class="input-group-text"> <?php echo $site_url?> </span>
                                        <input type="text" class="form-control" id="paymentPath" placeholder="payment" value="<?= get_env('geneal-application-settings-paymentPath'); ?>">
                                    </div>
                                </div>
                                <small class="form-hint">
                                    Lowercase letters, numbers, and dashes only. Example: payment
                                </small>
                            </div>

                            <div class="col-lg-12">
                                <label class="form-label">Cron path</label>
                                <div class="form-control-wrap mb-2">
                                    <div class="input-group">
                                        <span class="input-group-text"> <?php echo $site_url?> </span>
                                        <input type="text" class="form-control" id="cronPath" placeholder="cron" value="<?= get_env('geneal-application-settings-cronPath'); ?>">
                                    </div>
                                </div>
                                <small class="form-hint">
                                    Lowercase letters, numbers, and dashes only. Example: cron
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end pt-3">
                    <button class="btn btn-primary btn-geneal-application-settings">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</div>


<script data-cfasync="false">
    $('.btn-geneal-application-settings').click(function () {
        var csrf_token_default = $('input[name="csrf_token_default"]').val();

        var homepageRedirect = $('#homepageRedirect').val();
        var adminPath = $('#adminPath').val();
        var invoicePath = $('#invoicePath').val();
        var paymentLinkPath = $('#paymentLinkPath').val();
        var paymentPath = $('#paymentPath').val();
        var cronPath = $('#cronPath').val();
        var default_timezone = $('#default_timezone').val();
        var webhook_attempts_limit = $('#webhook_attempts_limit').val();

        var pathRegex = /^[a-z0-9-]+$/;
        var pathsToValidate = [
            { name: 'Admin path', value: adminPath },
            { name: 'Invoice path', value: invoicePath },
            { name: 'Payment Link path', value: paymentLinkPath },
            { name: 'Checkout path', value: paymentPath },
            { name: 'Cron path', value: cronPath }
        ];

        for (var i = 0; i < pathsToValidate.length; i++) {
            var item = pathsToValidate[i];
            if (item.value && !pathRegex.test(item.value)) {
                createToast({
                    title: 'Invalid Input',
                    description: item.name + ' must contain only lowercase letters, numbers, and dashes.',
                    svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                    timeout: 6000,
                    top: 70
                });
                return;
            }
        }

        var btnClass = 'btn-geneal-application-settings';

        var btn = document.querySelector('.'+btnClass).innerHTML;

        document.querySelector('.'+btnClass).innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            data: {action: "geneal-application-settings", csrf_token: csrf_token_default, default_timezone: default_timezone, webhook_attempts_limit: webhook_attempts_limit, homepageRedirect: homepageRedirect, adminPath: adminPath, invoicePath: invoicePath, paymentLinkPath: paymentLinkPath, paymentPath: paymentPath, cronPath: cronPath},
            dataType: 'json',
            success: function (response) {
                closeAllBootstrapModals();
        
                document.querySelector("#my-action-confirmation-btn").value = '';

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
    });
</script>