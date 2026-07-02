<?php
if (!defined('Profess0rPay_INIT')) {
    http_response_code(403);
    exit('Direct access not allowed');
}

    if (!canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'system_settings', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }

    if (!hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'system_settings', 'manage_update', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }

    $update_available = false;
    $update_channel = get_env('system-settings-update_channel') === '--' || (get_env('system-settings-update_channel') === '') ? 'stable' : get_env('system-settings-update_channel');
    $lasted_update_version_name = get_env('last-update-version-name');
    $lasted_update_version = get_env('last-update-version');

    if($lasted_update_version == "" || $lasted_update_version == "--"){
        
    }else{
        if (version_compare($lasted_update_version, $professorpay_current_version['version_code'], '>')) {
            $update_available = true;
        }
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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Update</a></li>
                    </ol>
                </div>
                <h2 class="page-title">Update</h2>
            </div>


            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list align-items-center gap-3">
                    <span class="global-loaderSpinner"></span>
                   
                    <span data-bs-target="#modal-createItem" data-bs-toggle="modal">
                        <a href="javascript:void(0)" class="btn btn-dark btn-5 d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-settings"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
                            Setting
                        </a>
                        <a href="javascript:void(0)" class="btn btn-dark btn-6 d-sm-none btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-settings"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
                        </a>
                    </span>

                    <span class="btn-check-update">
                        <a href="javascript:void(0)" class="btn btn-primary btn-5 d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-refresh"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" /><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" /></svg>
                            Check for Updates
                        </a>
                        <a href="javascript:void(0)" class="btn btn-primary btn-6 d-sm-none btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-refresh"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" /><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" /></svg>
                        </a>
                    </span>
                </div>
                <!-- BEGIN MODAL -->
                <!-- END MODAL -->
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card mb-4 <?php echo ($update_available == true) ? 'd-none' : ''?>">
            <div class="card-header">
                <div class="card-title">System is up to date</div>
            </div>
            <div class="card-body p-4">
                <div class="row align-items-center gy-3">
                    <!-- Left Content -->
                    <div class="col-12 col-md-8">
                        <div class="d-flex align-items-start align-items-md-center">
                            <div>
                                <p class="text-black mb-2">
                                    Your application is running the <?php echo $professorpay_current_version['version_channel']?> (<?php echo $professorpay_current_version['version_name']?>) version.
                                </p>

                                <div class="d-flex flex-wrap align-items-center text-muted">
                                    <span class="me-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 7v5l3 3" /></svg>
                                        Last checked: 
                                        <?php
                                            if(get_env('last-auto-update-check') == "--" || get_env('last-auto-update-check') == ""){
                                                echo 'Unknow';
                                            }else{
                                                echo convertUTCtoUserTZ(get_env('last-auto-update-check'), ($global_response_brand['response'][0]['timezone'] === '--' || $global_response_brand['response'][0]['timezone'] === '') ? 'Asia/Dhaka' : $global_response_brand['response'][0]['timezone'], "M d, Y h:i A");
                                            }
                                        ?>
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Right Content -->
                    <div class="col-12 col-md-4 text-start text-md-end">
                        <div class="text-muted mb-1">
                            Next automatic check:
                        </div>
                        <div class="fw-semibold">
                            <?php
                                if(get_env('last-auto-update-check') == "--" || get_env('last-auto-update-check') == ""){
                                    echo 'Unknow';
                                }else{
                                    $lastCheck = get_env('last-auto-update-check'); // e.g. 2026-01-19 12:10:36

                                    // fallback if empty or invalid
                                    if (empty($lastCheck) || $lastCheck === '--') {
                                        $lastCheckTime = time();
                                    } else {
                                        $lastCheckTime = strtotime($lastCheck);
                                    }

                                    // add 10 hours
                                    $nextCheckTime = $lastCheckTime + (10 * 3600);

                                    // formatted next check time
                                    $nextCheck = date('Y-m-d H:i:s', $nextCheckTime);

                                    echo convertUTCtoUserTZ($nextCheck, ($global_response_brand['response'][0]['timezone'] === '--' || $global_response_brand['response'][0]['timezone'] === '') ? 'Asia/Dhaka' : $global_response_brand['response'][0]['timezone'], "M d, Y h:i A");
                                }
                            ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="card mb-4 border-success <?php echo ($update_available == true) ? '' : 'd-none'?>">
            <div class="card-header">
                <div class="card-title text-success">
                    Update Available 🎉
                </div>
            </div>

            <div class="card-body p-4">
                <div class="row align-items-center gy-3">

                    <div class="col-12 col-md-8">
                        <p class="mb-2">
                            A new <?= ucfirst($update_channel) ?> version is available.
                        </p>

                        <ul class="list-unstyled text-muted mb-0">
                            <li>Current: <strong><?= $professorpay_current_version['version_name'] ?></strong></li>
                            <li>
                                Latest: 
                                <strong><?= $lasted_update_version_name ?></strong>
                                <a href="https://updates.professorpay.com/?version=<?= $lasted_update_version ?>" target="blank" class="ms-1 text-success"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-info-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 9h.01" /><path d="M11 12h1v4h1" /></svg></a>
                            </li>
                        </ul>
                    </div>

                    <div class="col-12 col-md-4 text-start text-md-end">
                        <div class="btn btn-success downloadUpdate">
                            Download Update
                        </div>

                        <div class="btn btn-danger installUpdate d-none">
                            Install
                        </div>
                    </div>

                </div>
            </div>
        </div>


    </div>
</div>


<div class="modal modal-blur fade" id="modal-createItem" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Update Settings</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Update Channel</label>
                    <select class="form-select js-select" id="update_channel" data-search="true" data-remove="true" data-placeholder="Select update channel">
                        <option value = "stable" <?php echo ($update_channel == "stable") ? 'selected' : ''?>>Stable releases only</option>
                        <option value = "beta" <?php echo ($update_channel == "beta") ? 'selected' : ''?>>Beta releases</option>
                    </select>
                </div>

                <?php
                    $automatic_update = get_env('system-settings-automatic_update') === '--' || (get_env('system-settings-automatic_update') === '') ? '' : get_env('system-settings-automatic_update');
                    $create_backup = get_env('system-settings-create_backup') === '--' || (get_env('system-settings-create_backup') === '') ? '' : get_env('system-settings-create_backup');
                ?>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" id="automatic_update" type="checkbox" <?php echo ($automatic_update == "yes") ? 'checked' : ''?>>
                        <label class="form-check-label">
                            Automatic updates check
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" id="create_backup" type="checkbox" <?php echo ($create_backup == "yes" || $create_backup == "") ? 'checked' : ''?>>
                        <label class="form-check-label">
                            Create backup before updating
                        </label>
                    </div>
                </div>
          </div>
          <div class="modal-footer">
            <a href="javascript:void(0)" class="btn btn-link link-secondary btn-3" data-bs-dismiss="modal"> Cancel </a>
            <a href="javascript:void(0)" class="btn btn-primary btn-5 ms-auto btn-update-setting">Save Settings</a>
          </div>
        </div>
    </div>
</div>

<script data-cfasync="false">
    $('.installUpdate').click(function () {
        var csrf_token_default = $('input[name="csrf_token_default"]').val();

        var btnClass = 'installUpdate';

        var btn = document.querySelector('.'+btnClass).innerHTML;

        document.querySelector('.'+btnClass).innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            data: {action: "system-settings-update-install", csrf_token: csrf_token_default },
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

                    location.reload();
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

    $('.downloadUpdate').click(function () {
        var csrf_token_default = $('input[name="csrf_token_default"]').val();

        var btnClass = 'downloadUpdate';

        var btn = document.querySelector('.'+btnClass).innerHTML;

        document.querySelector('.'+btnClass).innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            data: {action: "system-settings-update-download", csrf_token: csrf_token_default },
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

                    document.querySelector('.downloadUpdate').classList.add('d-none');
                    document.querySelector('.installUpdate').classList.remove('d-none');
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

    $('.btn-check-update').click(function () {
        var csrf_token_default = $('input[name="csrf_token_default"]').val();

        var btnClass = 'btn-check-update';

        var btn = document.querySelector('.'+btnClass).innerHTML;

        document.querySelector('.'+btnClass).innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            data: {action: "system-settings-update-check", csrf_token: csrf_token_default },
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

                    load_content('System Settings','<?php echo $site_url.$path_admin ?>/system-settings/update','nav-item-system-settings');
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

    $('.btn-update-setting').click(function () {
        var csrf_token_default = $('input[name="csrf_token_default"]').val();

        var update_channel = $('#update_channel').val();
        var automatic_update = $('#automatic_update').prop('checked') ? 'yes' : 'no';
        var create_backup    = $('#create_backup').prop('checked') ? 'yes' : 'no';

        var btnClass = 'btn-update-setting';

        var btn = document.querySelector('.'+btnClass).innerHTML;

        document.querySelector('.'+btnClass).innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            data: {action: "system-settings-update-setting", csrf_token: csrf_token_default, update_channel: update_channel, automatic_update: automatic_update, create_backup: create_backup },
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