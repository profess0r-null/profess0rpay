<?php
    if (!defined('Profess0rPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
    }

    if (!canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'brand_settings', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }

    if (!hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'theme_settings', 'view', $global_user_response['response'][0]['role'])) {
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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Themes</a></li>
                    </ol>
                </div>
                <h2 class="page-title">Themes</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Themes</h3>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php
                        $themes = [];

                        $themeDirs = glob(__DIR__ . '/../../../pp-modules/pp-themes/*', GLOB_ONLYDIR);

                        foreach ($themeDirs as $dir) {

                            if (!file_exists($dir . '/class.php')) {
                                continue;
                            }

                            require_once $dir . '/class.php';

                            $slug = basename($dir);

                            // twenty-six → TwentySixTheme
                            $class = str_replace(' ', '', ucwords(str_replace('-', ' ', $slug))) . 'Theme';

                            if (!class_exists($class)) {
                                continue;
                            }

                            $themeObj = new $class();
                            $themes[$slug] = $themeObj->info();
                        }

                        foreach ($themes as $slug => $theme) {
                    ?>
                            <div class="col-md-4 mb-3 theme-li <?php echo $slug?>">
                                <div class="card h-100 shadow-sm">
                                    <!-- Image -->
                                    <img src="<?php echo $site_url ?>pp-content/pp-modules/pp-themes/<?= $slug.'/'.htmlspecialchars($theme['logo']) ?>" class="card-img-top img-fluid p-3" alt="Liquid">

                                    <!-- Card Body -->
                                    <div class="card-body mt-0 pt-0">
                                        <h5 class="card-title m-0"><?= htmlspecialchars($theme['title']) ?></h5>
                                        <a href="javascript:void(0)" onclick="activeTheme('<?php echo $slug?>')" class="btn btn-primary mt-3 activeBTN active-btn-<?php echo $slug?> <?php echo ($global_response_brand['response'][0]['theme'] === $slug) ? 'd-none' : '';?> <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'theme_settings', 'edit', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" style=" min-height: 20px !important; padding-top: 5px; padding-bottom: 5px; padding-left: 10px; padding-right: 10px; font-size: 13px; " onclick=""><svg xmlns="http://www.w3.org/2000/svg" style="width: 22px;height: 22px;margin-right: 5px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-perspective"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6.141 4.163l12 1.714a1 1 0 0 1 .859 .99v10.266a1 1 0 0 1 -.859 .99l-12 1.714a1 1 0 0 1 -1.141 -.99v-13.694a1 1 0 0 1 1.141 -.99" /></svg> Active</a>
                                        <a href="javascript:void(0)" class="btn btn-light mt-3 manage-btn <?php echo ($global_response_brand['response'][0]['theme'] === $slug) ? '' : 'd-none';?> <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'theme_settings', 'edit', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" style=" min-height: 20px !important; padding-top: 5px; padding-bottom: 5px; padding-left: 10px; padding-right: 10px; font-size: 13px; " onclick="load_content('Manage Setting','<?php echo $site_url.$path_admin ?>/brand-setting/themes-setting?slug=<?php echo $slug?>','nav-item-brand-setting')"><svg xmlns="http://www.w3.org/2000/svg" style="width: 22px;height: 22px;margin-right: 5px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-settings"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg> Manage</a>
                                    </div>
                                </div>
                            </div>
                    <?php
                        } 
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>




<script data-cfasync="false">
    function activeTheme(slug){
        var my_action_confirmation_btn = document.querySelector("#my-action-confirmation-btn").value;
        var csrf_token_default = $('input[name="csrf_token_default"]').val();

        var btnClass = 'active-btn-'+slug;

        if(my_action_confirmation_btn !== ""){
            var btn = document.querySelector('.'+btnClass).innerHTML;

            document.querySelector('.'+btnClass).innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

            $.ajax({
                type: 'POST',
                url: '<?php echo $site_url.$path_admin ?>/dashboard',
                data: {action: "themes-new-active", csrf_token: csrf_token_default, slug: slug},
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

                        document.querySelectorAll('.theme-li').forEach(function(themeDiv) {
                            const activeBTN = themeDiv.querySelector('.activeBTN');
                            const manageBTN = themeDiv.querySelector('.manage-btn');

                            if (activeBTN) {
                                activeBTN.classList.remove('d-none');
                            }
                            if (manageBTN) {
                                manageBTN.classList.add('d-none');
                            }
                        });

                        document.querySelectorAll('.'+slug).forEach(function(themeDiv) {
                            const activeBTN = themeDiv.querySelector('.activeBTN');
                            const manageBTN = themeDiv.querySelector('.manage-btn');

                            if (activeBTN) {
                                activeBTN.classList.add('d-none');
                            }
                            if (manageBTN) {
                                manageBTN.classList.remove('d-none');
                            }
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
            show_action_confirmation_tab(btnClass, 'Active Theme', 'Confirm', 'btn-primary');
        }
    }
</script>