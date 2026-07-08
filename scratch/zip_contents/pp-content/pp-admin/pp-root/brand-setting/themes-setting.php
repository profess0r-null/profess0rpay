<?php
    if (!defined('Profess0rPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
    }

    if (!canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'brand_settings', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }

    if (!hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'theme_settings', 'edit', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }

    $params = json_decode($_POST['params'] ?? '{}', true);

    $slug = getParam($params, 'slug');

    if ($slug === null) {
        http_response_code(403);
        exit('Invalid slug');
    }else{
        $slug = escape_string($slug);

        if($global_response_brand['response'][0]['theme'] !== $slug){
            http_response_code(403);
            exit('Invalid slug');
        }else{
            if(file_exists(__DIR__.'/../../../pp-modules/pp-themes/'.$slug.'/class.php')){
                require_once __DIR__.'/../../../pp-modules/pp-themes/'.$slug.'/class.php';

                $class = str_replace(' ', '', ucwords(str_replace('-', ' ', $slug))) . 'Theme';

                $theme = new $class();

                $fields = $theme->fields();

                $supported_languages = $theme->supported_languages();

                $themeSlug = $slug;
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
                        <li class="breadcrumb-item"><a href="javascript:void(0)" onclick="load_content('Brand Settings','<?php echo $site_url.$path_admin ?>/brand-setting','nav-item-brand-setting')">Brand Settings</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)" onclick="load_content('Themes','<?php echo $site_url.$path_admin ?>/brand-setting/themes','nav-item-brand-setting')">Themes</a></li>
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Theme Setting</a></li>
                    </ol>
                </div>
                <h2 class="page-title">Theme Setting</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row g-gs">
            <div class="col-12 col-xxl-12">

            <form class="form-submit" enctype="multipart/form-data">
                <input type="hidden" name="action" value="theme-setting-update">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Configuration</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <?php foreach($fields as $field):

                                $optionName = $themeSlug . '-' . $field['name'];

                                $value = (($field['value'] ?? '') === '--') ? '' : ($field['value'] ?? '');

                                $value = (get_env($optionName, $global_response_brand['response'][0]['brand_id']) == "--" || get_env($optionName, $global_response_brand['response'][0]['brand_id']) == "") ? $value : get_env($optionName, $global_response_brand['response'][0]['brand_id']); // your brand_id = 'both'

                                // Handle multi-select stored as JSON
                                if(!empty($field['multiple']) && !empty($value)){
                                    $value = is_array($value) ? $value : json_decode($value, true);
                                }

                            ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><?= $field['label'] ?> <?php if(!empty($field['required'])): ?><span class="text-danger">*</span><?php endif; ?></label>
                                    <div class="form-control-wrap">
                                        <?php
                                        switch($field['type']) {

                                            case 'text':
                                                echo "<input type='text' class='form-control' name='{$field['name']}' value='".htmlspecialchars($value)."' placeholder='".($field['placeholder'] ?? '')."' ".(!empty($field['required']) ? 'required' : '').">";
                                                break;

                                            case 'color':
                                                echo "<input type='color' class='form-control' name='{$field['name']}' value='".htmlspecialchars($value)."' placeholder='".($field['placeholder'] ?? '')."' ".(!empty($field['required']) ? 'required' : '').">";
                                                break;

                                            case 'textarea':
                                                echo "<textarea class='form-control' name='{$field['name']}' placeholder='".($field['placeholder'] ?? '')."' ".(!empty($field['required']) ? 'required' : '').">".htmlspecialchars($value)."</textarea>";
                                                break;

                                            case 'select':
                                                $multiple = !empty($field['multiple']);
                                                $name = $multiple ? $field['name'].'[]' : $field['name'];
                                                $valueArray = $multiple ? (array)$value : [$value];

                                                echo "<select class='form-select js-select' data-search='true' data-remove='true' name='$name' ".($multiple ? 'multiple' : '')." ".(!empty($field['required']) ? 'required' : '').">";
                                                foreach($field['options'] as $k=>$v){
                                                    $selected = in_array($k, $valueArray) ? 'selected' : '';
                                                    echo "<option value='$k' $selected>$v</option>";
                                                }
                                                echo "</select>";
                                                break;

                                            case 'checkbox':
                                                $checked = $value ? 'checked' : '';
                                                echo "<div class='form-check form-switch'>
                                                        <input class='form-check-input' type='checkbox' name='{$field['name']}' value='1' $checked>
                                                    </div>";
                                                break;

                                            case 'image':
                                                echo '
                                                    <div class="form-group">
                                                        <div class="form-control-wrap">
                                                            <input type="file" class="form-control img-input" name="'.$field['name'].'" data-preview="'.$field['name'].'" accept="image/*" style=" max-width: 100%; max-height: 100%; " '.(!empty($field['required']) ? 'required' : '').'>
                                                        </div>
                                                    </div>

                                                    <div class="border rounded p-2 mt-2 d-flex align-items-center justify-content-center" style=" height: 90px; max-width: 300px; ">
                                                        <img src="'.$value.'" accept="image/*" alt="" id="'.$field['name'].'" style=" max-width: 100%; max-height: 100%; ">
                                                    </div>
                                                ';
                                                break;

                                            case 'radio':
                                                foreach($field['options'] as $k=>$v){
                                                    $checked = $value == $k ? 'checked' : '';
                                                    echo "<div class='form-check'>
                                                            <input class='form-check-input' type='radio' name='{$field['name']}' value='$k' $checked ".(!empty($field['required']) ? 'required' : '').">
                                                            <label class='form-check-label'>$v</label>
                                                        </div>";
                                                }
                                                break;
                                        }
                                        ?>
                                    </div>

                                    <?php
                                        if (!empty($field['hint'])) {
                                            echo '<small class="form-hint mt-2">' . $field['hint'] . '</small>';
                                        }
                                    ?>
                                </div>
                            </div>
                            <?php endforeach; ?>

                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Supported Languages</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($supported_languages)): ?>
                            <?php foreach ($supported_languages as $language): ?>
                                <span class="badge bg-primary text-white me-1 mb-1"><?php echo htmlspecialchars($language); ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No supported languages available.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="text-end pt-3">
                    <button class="btn btn-primary btn-saveChanges" type="submit">Save Changes</button>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>


<script data-cfasync="false">
    function initImagePreview(selector, options = {}) {
        const settings = {
            maxSize: options.maxSize || 2 * 1024 * 1024, // 2MB
            allowedTypes: options.allowedTypes || ['image/jpeg', 'image/png'],
        };

        document.querySelectorAll(selector).forEach(input => {
            input.addEventListener('change', function () {
                const file = this.files[0];
                const previewId = this.dataset.preview;
                const preview = document.getElementById(previewId);

                if (!file || !preview) return;

                if (!settings.allowedTypes.includes(file.type)) {
                    createToast({
                        title: 'Action required!',
                        description: 'The selected file is not a supported image format.',
                        svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                        timeout: 6000,
                        top: 70
                    });
                    this.value = '';
                    preview.style.display = 'none';
                    return;
                }

                if (file.size > settings.maxSize) {
                    createToast({
                        title: 'Action required!',
                        description: 'Image size exceeds the maximum allowed limit (Max: 2 MB).',
                        svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                        timeout: 6000,
                        top: 70
                    });

                    this.value = '';
                    preview.style.display = 'none';
                    return;
                }

                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            });
        });
    }

    // Init once
    initImagePreview('.img-input', {
        maxSize: 2 * 1024 * 1024 // 2MB
    });

    $('.form-submit').submit(function (e) {
        e.preventDefault();
        let formData = new FormData(this);

        // Client-side validation
        $('input[type="file"]').each(function () {
            if (!this.files.length) return;

            let file = this.files[0];

            if (!file.type.startsWith('image/')) {
                createToast({
                    title: 'Action required!',
                    description: 'The selected file is not a supported image format.',
                    svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                    timeout: 6000,
                    top: 70
                });
                return false;
            }

            if (file.size > 2 * 1024 * 1024) {
                createToast({
                    title: 'Action required!',
                    description: 'Image size exceeds the maximum allowed limit (Max: 2 MB).',
                    svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                    timeout: 6000,
                    top: 70
                });
                return false;
            }
        });

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
