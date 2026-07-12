<?php
    if (!defined('Profess0rPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
    }

    if (!canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'brand_settings', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }
?>

<div class="page-header d-print-none" aria-label="Page header">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
            <!-- Page pre-title -->
                <div class="page-pretitle">Brand Settings</div>
                <h2 class="page-title">Brand Settings</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Settings</h3>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4 <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'brand_settings', 'view', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" onclick="load_content('General Settings','<?php echo $site_url.$path_admin ?>/brand-setting/general-setting','nav-item-brand-setting')" style="cursor: pointer;">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <!-- Icon -->
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:50px;height:50px;">
                                        <svg xmlns="http://www.w3.org/2000/svg"  style="width: 24px; height: 24px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-settings"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
                                    </div>

                                    <!-- Text -->
                                    <div class="ms-3">
                                        <h5 class="card-title m-0 mb-1 fw-medium text-primary" style=" margin-top: -3px !important; ">
                                            Geneal Setting
                                        </h5>
                                        <p class="m-0 text-dark">Manage brand details, system preferences, and basic configuration.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'api_settings', 'view', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" onclick="load_content('Api Settings','<?php echo $site_url.$path_admin ?>/brand-setting/api-setting','nav-item-brand-setting')" style="cursor: pointer;">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <!-- Icon -->
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:50px;height:50px;">
                                        <svg xmlns="http://www.w3.org/2000/svg"  style="width: 24px; height: 24px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-key"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.555 3.843l3.602 3.602a2.877 2.877 0 0 1 0 4.069l-2.643 2.643a2.877 2.877 0 0 1 -4.069 0l-.301 -.301l-6.558 6.558a2 2 0 0 1 -1.239 .578l-.175 .008h-1.172a1 1 0 0 1 -.993 -.883l-.007 -.117v-1.172a2 2 0 0 1 .467 -1.284l.119 -.13l.414 -.414h2v-2h2v-2l2.144 -2.144l-.301 -.301a2.877 2.877 0 0 1 0 -4.069l2.643 -2.643a2.877 2.877 0 0 1 4.069 0" /><path d="M15 9h.01" /></svg>
                                    </div>

                                    <!-- Text -->
                                    <div class="ms-3">
                                        <h5 class="card-title m-0 mb-1 fw-medium text-primary" style=" margin-top: -3px !important; ">
                                            Api Settings
                                        </h5>
                                        <p class="m-0 text-dark">Configure API keys, tokens, and access permissions.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'brand_settings', 'view', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" onclick="load_content('SMTP Settings','<?php echo $site_url.$path_admin ?>/brand-setting/smtp-setting','nav-item-brand-setting')" style="cursor: pointer;">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <!-- Icon -->
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:50px;height:50px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 24px; height: 24px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-mail"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" /><path d="M3 7l9 6l9 -6" /></svg>
                                    </div>

                                    <!-- Text -->
                                    <div class="ms-3">
                                        <h5 class="card-title m-0 mb-1 fw-medium text-primary" style=" margin-top: -3px !important; ">
                                            SMTP Settings
                                        </h5>
                                        <p class="m-0 text-dark">Configure robust email delivery servers and protocols.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'theme_settings', 'view', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" onclick="load_content('Themes','<?php echo $site_url.$path_admin ?>/brand-setting/themes','nav-item-brand-setting')" style="cursor: pointer;">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <!-- Icon -->
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:50px;height:50px;">
                                        <svg xmlns="http://www.w3.org/2000/svg"  style="width: 24px; height: 24px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-brush"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21v-4a4 4 0 1 1 4 4h-4" /><path d="M21 3a16 16 0 0 0 -12.8 10.2" /><path d="M21 3a16 16 0 0 1 -10.2 12.8" /><path d="M10.6 9a9 9 0 0 1 4.4 4.4" /></svg>
                                    </div>

                                    <!-- Text -->
                                    <div class="ms-3">
                                        <h5 class="card-title m-0 mb-1 fw-medium text-primary" style=" margin-top: -3px !important; ">
                                            Themes
                                        </h5>
                                        <p class="m-0 text-dark">Browse, activate, and customize your checkout themes</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'faq_settings', 'view', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" onclick="load_content('FAQ Settings','<?php echo $site_url.$path_admin ?>/brand-setting/faq-setting','nav-item-brand-setting')" style="cursor: pointer;">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <!-- Icon -->
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:50px;height:50px;">
                                        <svg xmlns="http://www.w3.org/2000/svg"  style="width: 24px; height: 24px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-help-hexagon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19.875 6.27c.7 .398 1.13 1.143 1.125 1.948v7.284c0 .809 -.443 1.555 -1.158 1.948l-6.75 4.27a2.269 2.269 0 0 1 -2.184 0l-6.75 -4.27a2.225 2.225 0 0 1 -1.158 -1.948v-7.285c0 -.809 .443 -1.554 1.158 -1.947l6.75 -3.98a2.33 2.33 0 0 1 2.25 0l6.75 3.98h-.033" /><path d="M12 16v.01" /><path d="M12 13a2 2 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" /></svg>
                                    </div>

                                    <!-- Text -->
                                    <div class="ms-3">
                                        <h5 class="card-title m-0 mb-1 fw-medium text-primary" style=" margin-top: -3px !important; ">
                                            FAQ Settings
                                        </h5>
                                        <p class="m-0 text-dark">Manage frequently asked questions and help content.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'currency_settings', 'view', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" onclick="load_content('Currency Settings','<?php echo $site_url.$path_admin ?>/brand-setting/currency-setting','nav-item-brand-setting')" style="cursor: pointer;">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <!-- Icon -->
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:50px;height:50px;">
                                        <svg xmlns="http://www.w3.org/2000/svg"  style="width: 24px; height: 24px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-currency-dollar"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" /><path d="M12 3v3m0 12v3" /></svg>
                                    </div>

                                    <!-- Text -->
                                    <div class="ms-3">
                                        <h5 class="card-title m-0 mb-1 fw-medium text-primary" style=" margin-top: -3px !important; ">
                                            Currency Settings
                                        </h5>
                                        <p class="m-0 text-dark">Set supported currencies, symbols, and defaults.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>