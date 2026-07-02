<?php
    if (!defined('Profess0rPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
    }

    if (!canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'addons', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }

    if (!hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'addons', 'edit', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }

    $params = json_decode($_POST['params'] ?? '{}', true);

    $ref = getParam($params, 'ref');

    if ($ref === null) {
        http_response_code(403);
        exit('Invalid slug');
    }else{
        $ref = escape_string($ref);

        $response_addon = json_decode(getData($db_prefix.'addon','WHERE addon_id = "'.$ref.'"'),true);
        if($response_addon['status'] == false){
            http_response_code(403);
            exit('Invalid slug');
        }else{
            if(file_exists(__DIR__ . '/../../../pp-modules/pp-addons/'.$response_addon['response'][0]['slug'].'/class.php')){
                require_once __DIR__ . '/../../../pp-modules/pp-addons/'.$response_addon['response'][0]['slug'].'/class.php';

                $slug = basename(__DIR__ . '/../../../pp-modules/pp-addons/'.$response_addon['response'][0]['slug']);

                // twenty-six → TwentySixTheme
                $class = str_replace(' ', '', ucwords(str_replace('-', ' ', $slug))) . 'Addon';

                if (class_exists($class)) {
                    $addonObj = new $class();

                    if (method_exists($addonObj, 'info')) {
                        $addonInfo = $addonObj->info();
                    }else{
                        http_response_code(403);
                        exit('Invalid info');
                    }
                }else{
                    http_response_code(403);
                    exit('Invalid slug');
                }
            }else{
                http_response_code(403);
                exit('Invalid slug');
            }
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
                        <li class="breadcrumb-item"><a href="javascript:void(0)" onclick="load_content('Addons','<?php echo $site_url.$path_admin ?>/addons','nav-item-addons')">Addons</a></li>
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Addon Setting</a></li>
                    </ol>
                </div>
                <h2 class="page-title">Addon Setting</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row g-gs">
            <div class="col-12 col-xxl-12">
                <form class="form-submit" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="addon-setting-update">
                    <input type="hidden" name="addon-id" value="<?php echo $response_addon['response'][0]['addon_id']?>">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Addon Name <span class="text-danger">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" name="addon_name" value="<?php echo $response_addon['response'][0]['name']?>" placeholder="Enter addon name" required="" readonly> 
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="status" class="form-label">Status<span class="text-danger">*</span></label>
                                        <div class="form-control-wrap">
                                            <select class="js-select" id="status" name="status" data-search="true" data-remove="true" data-placeholder="Select status" required onchange="FNcurrency()">
                                                <option value="active" <?php echo ($response_addon['response'][0]['status'] == "active") ? 'selected' : '';?>>Active</option>
                                                <option value="inactive" <?php echo ($response_addon['response'][0]['status'] == "inactive") ? 'selected' : '';?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end pt-3">
                        <button class="btn btn-primary btn-saveChanges" type="submit">Save Changes</button>
                    </div>
                </form>

                <?php
                   if (method_exists($addonObj, 'configuration')) {
                       echo $addonObj->configuration();
                   }
                ?>
            </div>
        </div>
    </div>
</div>


<script data-cfasync="false">
    $('.form-submit').submit(function (e) {
        e.preventDefault();
        let formData = new FormData(this);

        var btnClass = 'btn-saveChanges';

        var btn = document.querySelector('.'+btnClass).innerHTML;

        document.querySelector('.'+btnClass).innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            data: formData,
            contentType: false, // IMPORTANT
            processData: false, // IMPORTANT
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