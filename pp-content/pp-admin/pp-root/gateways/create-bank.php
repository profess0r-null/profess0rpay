<?php
    if (!defined('Profess0rPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
    }

    if (!canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'gateways', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }

    if (!hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'gateways', 'create', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }

    $fields = [
        [
            'name'  => 'bank_name',
            'label' => 'Bank Name',
            'type'  => 'text',
            'value' => '',        // default
            'required' => true,
            'placeholder' => 'Enter bank name'
        ],
        [
            'name'  => 'account_holder_name',
            'label' => 'Account Holder Name',
            'type'  => 'text',
            'value' => '',        // default
            'required' => true,
            'placeholder' => 'Enter account holder name'
        ],
        [
            'name'  => 'account_number',
            'label' => 'Account Number',
            'type'  => 'text',
            'value' => '',        // default
            'required' => true,
            'placeholder' => 'Enter account number'
        ],
        [
            'name'  => 'branch_name',
            'label' => 'Branch Name',
            'type'  => 'text',
            'value' => '',        // default
            'required' => true,
            'placeholder' => 'Enter branch name'
        ],
        [
            'name'  => 'routing_number',
            'label' => 'Routing Number',
            'type'  => 'text',
            'value' => '',        // default
            'required' => true,
            'placeholder' => 'Enter routing number'
        ],
        [
            'name'  => 'swift_code',
            'label' => 'SWIFT/BIC Code',
            'type'  => 'text',
            'value' => '',        // default
            'required' => false,
            'placeholder' => 'Enter code'
        ]
    ];
?>

<div class="page-header d-print-none" aria-label="Page header">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
            <!-- Page pre-title -->
                <div class="page-pretitle">
                    <ol class="breadcrumb breadcrumb-arrow mb-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0)" onclick="load_content('Gateways','<?php echo $site_url.$path_admin ?>/gateways','nav-item-gateways')">Gateways</a></li>
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Create Bank Gateway</a></li>
                    </ol>
                </div>
                <h2 class="page-title">Create Bank Gateway</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row g-gs">
            <div class="col-12 col-xxl-12">

            <form class="form-submit" enctype="multipart/form-data">
                <input type="hidden" name="action" value="gateway-setting-create">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Gateway Name <span class="text-danger">*</span></label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" name="gateway_name" value="" placeholder="Enter gateway name" required=""> 
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Display Name <span class="text-danger">*</span></label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" name="display_name" value="" placeholder="Enter display name" required=""> 
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Min Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text gt-currency"> <?php echo $global_response_brand['response'][0]['currency_code']?> </span>
                                        <input type="text" class="form-control" name="min_amount" value="0" placeholder="Enter min amount" required=""> 
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Max Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text gt-currency"> <?php echo $global_response_brand['response'][0]['currency_code']?> </span>
                                        <input type="text" class="form-control" name="max_amount" value="0" placeholder="Enter max amount" required=""> 
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Fixed Charge <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text gt-currency"> <?php echo $global_response_brand['response'][0]['currency_code']?> </span>
                                        <input type="text" class="form-control" name="fixed_charge" value="0" placeholder="Enter fixed charge" required=""> 
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Percentage Charge <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"> % </span>
                                        <input type="text" class="form-control" name="percentage_charge" value="0" placeholder="Enter percentage charge" required=""> 
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Fixed Discount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text gt-currency"> <?php echo $global_response_brand['response'][0]['currency_code']?> </span>
                                        <input type="text" class="form-control" name="fixed_discount" value="0" placeholder="Enter fixed discount" required=""> 
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Percentage Discount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"> % </span>
                                        <input type="text" class="form-control" name="percentage_discount" value="0" placeholder="Enter percentage discount" required=""> 
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="currency" class="form-label">Currency<span class="text-danger">*</span></label>
                                    <div class="form-control-wrap">
                                        <select class="js-select" id="currency" name="currency" data-search="true" data-remove="true" data-placeholder="Select currency" required onchange="FNcurrency()">
                                            <?php
                                                $response_brand = json_decode(
                                                    getData($db_prefix . 'currency', 'WHERE brand_id ="'.$global_response_brand['response'][0]['brand_id'].'" ORDER BY 1 DESC'), 
                                                    true
                                                );

                                                if ($response_brand['status'] == true) {
                                                    foreach ($response_brand['response'] as $row) {
                                                        $isSelected = ($row['code'] === $global_response_brand['response'][0]['currency_code']) ? 'selected' : '';
                                                        echo '<option value="'.$row['code'].'" '.$isSelected.'>'.$row['code'].'</option>';
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="status" class="form-label">Status<span class="text-danger">*</span></label>
                                    <div class="form-control-wrap">
                                        <select class="js-select" id="status" name="status" data-search="true" data-remove="true" data-placeholder="Select status" required onchange="FNcurrency()">
                                            <option value="active" selected>Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Assets</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="gateway_logo" class="form-label">Gateway Logo <svg xmlns="http://www.w3.org/2000/svg" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" title="Logo size should in JPG, JPEG, PNG (500 x 250 pixels) format." style=" width: 20px; height: 20px; " viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-info-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 9h.01" /><path d="M11 12h1v4h1" /></svg></label>
                                    <div class="form-control-wrap">
                                        <input type="file" class="form-control img-input" id="gateway_logo" name="gateway_logo" data-preview="preview2" accept="image/*" style=" max-width: 100%; max-height: 100%; ">
                                    </div>
                                </div>

                                <div class="border rounded p-2 mt-2 d-flex align-items-center justify-content-center" style=" height: 90px; max-width: 300px; ">
                                    <img src="" accept="image/*" alt="" id="preview2" style=" max-width: 100%; max-height: 100%; ">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Colors</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Primary Color <span class="text-danger">*</span></label>
                                    <div class="form-control-wrap">
                                        <input type="color" class="form-control" name="primary_color" value="" placeholder="Enter gateway name" required="" readonly> 
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Text Color <span class="text-danger">*</span></label>
                                    <div class="form-control-wrap">
                                        <input type="color" class="form-control" name="text_color" value="" placeholder="Enter display name" required=""> 
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Button Color <span class="text-danger">*</span></label>
                                    <div class="form-control-wrap">
                                        <input type="color" class="form-control" name="btn_color" value="" placeholder="Enter display name" required=""> 
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Button Text Color <span class="text-danger">*</span></label>
                                    <div class="form-control-wrap">
                                        <input type="color" class="form-control" name="btn_text_color" value="" placeholder="Enter display name" required=""> 
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Configuration</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <?php foreach($fields as $field):
                                $value = '';

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

                                            case 'textarea':
                                                echo "<textarea class='form-control' name='{$field['name']}' placeholder='".($field['placeholder'] ?? '')."'>".htmlspecialchars($value)."</textarea>";
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
                                                if(!empty($field['hint'])) echo "<small class='form-hint'>{$field['hint']}</small>";
                                                break;

                                            case 'radio':
                                                foreach($field['options'] as $k=>$v){
                                                    $checked = $value == $k ? 'checked' : '';
                                                    echo "<div class='form-check'>
                                                            <input class='form-check-input' type='radio' name='{$field['name']}' value='$k' $checked>
                                                            <label class='form-check-label'>$v</label>
                                                        </div>";
                                                }
                                                break;
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>

                        </div>
                    </div>
                </div>

                <div class="text-end pt-3">
                    <button class="btn btn-primary btn-saveChanges" type="submit">Create</button>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>


<script data-cfasync="false">
    function FNcurrency() {
        var default_currency = document.querySelector("#currency").value;

        document.querySelectorAll(".gt-currency").forEach(function (el) {
            el.innerHTML = default_currency;
        });
    }

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

                    if (response.gateway_id) {
                        load_content('Edit Gateway', '<?php echo rtrim($site_url, "/") . "/" . $path_admin ?>/gateways/edit?ref=' + response.gateway_id, 'nav-item-gateways');
                    } else {
                        load_content('Gateways','<?php echo $site_url.$path_admin ?>/gateways','nav-item-gateways')
                    }
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
