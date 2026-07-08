<?php
if (!defined('Profess0rPay_INIT')) {
    http_response_code(403);
    exit('Direct access not allowed');
}

    if (!canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'payment_link', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }

    if (!hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'payment_link', 'edit', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }

    $params = json_decode($_POST['params'] ?? '{}', true);

    $ref = getParam($params, 'p_id');

    if ($ref === null) {
        http_response_code(403);
        exit('Invalid payment link id');
    }else{
        $ref = escape_string($ref);

        $response_paymentLink = json_decode(getData($db_prefix.'payment_link','WHERE ref = "'.$ref.'" AND brand_id = "'.$global_response_brand['response'][0]['brand_id'].'"'),true);
        if($response_paymentLink['status'] == true){
            $response_product_info = json_decode($response_paymentLink['response'][0]['product_info'], true);
        }else{
            http_response_code(403);
            exit('Direct access not allowed');
        }
    }
    
    $is_temp_mode = (isset($params['type']) && $params['type'] == 'temp') || (isset($response_product_info['is_temporary']) && $response_product_info['is_temporary'] == 1);
?>

<style>
    .customer-list .d-flex {
        align-items: stretch; /* KEY FIX */
    }

    .customer-list .choices {
        width: 100%;
        margin-bottom: 0;
    }

    .customer-list .choices__inner {
        min-height: 40px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
    }

    .customer-list .btnCreateItem {
        display: flex;
        justify-content: center;
        align-self: stretch;
        padding: 0 12px;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        white-space: nowrap;
    }
</style>

<div class="page-header d-print-none" aria-label="Page header">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
            <!-- Page pre-title -->
                <div class="page-pretitle">
                    <ol class="breadcrumb breadcrumb-arrow mb-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0)" onclick="load_content('Payment Link','<?php echo $site_url.$path_admin ?>/payment-link','nav-item-payment-link')">Payment Link</a></li>
                        <li class="breadcrumb-item active"><a href="javascript:void(0)"><?php echo $is_temp_mode ? 'Edit Temp Link' : 'Edit Payment Link'; ?></a></li>
                    </ol>
                </div>
                <h2 class="page-title"><?php echo $is_temp_mode ? 'Edit Temp Link' : 'Edit Payment Link'; ?></h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <div class="btn btn-primary" onclick="copyContent('<?php echo $site_url.$path_payment_link ?>/<?php echo $ref;?>', 'Copied!', 'Payment Link copied successfully.')"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-copy"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 9.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667l0 -8.666" /><path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" /></svg> Copy Link</div>
                    <button class="btn btn-danger btnDeleteItem-<?php echo $ref;?> <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'payment_link', 'delete', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" onclick="deleteItem('<?php echo $ref;?>')"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg> Delete</button>
                </div>
                <!-- BEGIN MODAL -->
                <!-- END MODAL -->
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row g-gs">
            <div class="col-12 col-xxl-12">
                <form class="form-paymentLink-edit" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="paymentLink-edit">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">
                    <input type="hidden" name="paymentLinkID" value="<?php echo $response_paymentLink['response'][0]['ref']?>">
                    <input type="hidden" name="deleted_items" id="deleted_items" value="">

                    <div class="row g-3">
                        <div class="col-lg-12">
                            <div class="card p-2">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-lg-8">
                                            <label for="username" class="form-label">Product Title <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="title" placeholder="Product Title" required="" value="<?php echo $response_product_info['title']?>">
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label for="quantity" class="form-label mb-0">Quantity</label>
                                                <div class="custom-control custom-checkbox custom-control-sm">
                                                    <?php $is_unlimited = ($response_paymentLink['response'][0]['quantity'] == -1); ?>
                                                    <input type="checkbox" class="custom-control-input" id="limitQuantity" onchange="toggleQuantityInput(this)" <?php echo !$is_unlimited ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="limitQuantity">Limit Quantity</label>
                                                </div>
                                            </div>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="quantity" id="quantityInput" placeholder="Unlimited" <?php echo $is_unlimited ? 'disabled' : 'required'; ?> value="<?php echo !$is_unlimited ? $response_paymentLink['response'][0]['quantity'] : ''; ?>">
                                            </div>
                                            <script>
                                                function toggleQuantityInput(checkbox) {
                                                    const input = document.getElementById('quantityInput');
                                                    if(checkbox.checked) {
                                                        input.disabled = false;
                                                        input.required = true;
                                                        input.focus();
                                                    } else {
                                                        input.disabled = true;
                                                        input.required = false;
                                                        input.value = '';
                                                    }
                                                }
                                            </script>
                                        </div>

                                        <div class="col-lg-12">
                                            <label for="username" class="form-label">Product Description</label>
                                            <div class="input-group">
                                                <textarea name="description" class="form-control"><?php echo $response_product_info['description']?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <label for="return_url" class="form-label">Redirect Link</label>
                                            <div class="input-group">
                                                <input type="text" name="return_url" class="form-control" placeholder="https://yourwebsite.com/success" value="<?php echo $response_paymentLink['response'][0]['return_url'] ?? ''; ?>">
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <label for="image" class="form-label">Product Image</label>
                                            <?php if(!empty($response_product_info['image'])): ?>
                                                <div class="mb-2">
                                                    <img src="<?php echo $response_product_info['image']; ?>" alt="Product Image" style="max-height: 100px; border-radius: 8px;">
                                                </div>
                                            <?php endif; ?>
                                            <div class="input-group">
                                                <input type="file" name="image" class="form-control" accept="image/*">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label">Currency <span class="text-danger">*</span></label>
                                                <div class="form-control-wrap">
                                                    <select class="js-select in-currency" name="currency" data-search="true" data-remove="true" data-placeholder="Select currency" required onchange="FNcurrency()">
                                                        <?php
                                                            $response_brand = json_decode(getData($db_prefix . 'currency', 'WHERE brand_id ="'.$global_response_brand['response'][0]['brand_id'].'" ORDER BY 1 DESC'), true);
                                                            if ($response_brand['status'] == true) {
                                                                foreach ($response_brand['response'] as $row) {
                                                        ?>
                                                                    <option value="<?php echo $row['code'] ?>" <?php echo ($response_paymentLink['response'][0]['currency'] == $row['code']) ? 'selected' : '';?>><?php echo $row['code'] ?></option>
                                                        <?php
                                                                }
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-3">
                                            <label for="username" class="form-label">Amount <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white currency-code">BDT</span>
                                                <input type="text" class="form-control invoice-amount" name="amount" aria-label="Amount (to the nearest dollar)" value="<?php echo money_round($response_paymentLink['response'][0]['amount'])?>" required="">
                                            </div>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label for="username" class="form-label">Expiry Date</label>
                                                <div class="form-control-wrap">
                                                    <input type="date" class="form-control" name="expiry_date" placeholder="dd/mm/yyyy" value="<?php echo ($response_paymentLink['response'][0]['expired_date'] === '--') ? '' : $response_paymentLink['response'][0]['expired_date'];?>"> 
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label for="email-address" class="form-label">Status <span class="text-danger">*</span></label>
                                                <div class="form-control-wrap">
                                                    <select class="js-select" name="status" data-search="true" data-remove="true" data-placeholder="Select a status" required>
                                                        <option value="active" <?= ($response_paymentLink['response'][0]['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                                                        <option value="inactive" <?= ($response_paymentLink['response'][0]['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if ($is_temp_mode): ?>
                                        <input type="hidden" name="is_temporary" value="1">
                                        
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label class="form-label">Allowed Payment Gateways <span class="text-muted">(Optional)</span></label>
                                                <div class="form-control-wrap">
                                                    <select class="js-select" name="allowed_gateways[]" multiple data-search="true" data-remove="true" data-placeholder="Select gateways (leave empty for all)">
                                                        <?php
                                                            $saved_gateways = isset($response_product_info['allowed_gateways']) ? (is_array($response_product_info['allowed_gateways']) ? $response_product_info['allowed_gateways'] : []) : [];
                                                            $response_gateway = json_decode(getData($db_prefix . 'gateways', 'WHERE brand_id ="'.$global_response_brand['response'][0]['brand_id'].'" AND status="active" ORDER BY 1 DESC'), true);
                                                            if ($response_gateway['status'] == true) {
                                                                foreach ($response_gateway['response'] as $gw) {
                                                                    $selected = in_array($gw['gateway_id'], $saved_gateways) ? 'selected' : '';
                                                                    echo '<option value="' . $gw['gateway_id'] . '" '.$selected.'>' . htmlspecialchars($gw['display'] ?? $gw['name']) . '</option>';
                                                                }
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!--items--> 
                            <!--items--> 
                            <!--items--> 
                            <div class="item-list">
                                <?php
                                    $response = json_decode(getData($db_prefix.'payment_link_field','WHERE paymentLinkID ="'.$ref.'"'),true);
                                    foreach($response['response'] as $row){
                                        $uniqueID = uniqid();
                                ?>
                                        <input type="hidden" name="items[item-card-<?php echo $uniqueID?>][fieldID]" value="<?php echo $row['id']?>">
                                        <div class="card mt-3" id="item-card-<?php echo $uniqueID?>">
                                            <div class="card-header align-items-center justify-content-between">
                                                <h3 class="card-title">Input field</h3>
                                                <svg class="remove-item text-danger" style=" cursor: pointer; "  onclick="delete_item('item-card-<?php echo $uniqueID?>', '<?php echo $row['id']?>')" xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                            </div>
                                            <div class="card-body p-4 pt-3">
                                                <div class="row g-3">
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <label for="formType" class="form-label">Form Type <span class="text-danger">*</span></label>
                                                            <div class="form-control-wrap">
                                                                <select class="js-select" name="items[item-card-<?php echo $uniqueID?>][formType]" id="formType" data-search="true" data-remove="true" data-placeholder="Select a type" onchange="formTypeF('item-card-<?php echo $uniqueID?>')" required>
                                                                    <option value="text" <?= ($row['formType'] === 'text') ? 'selected' : '' ?>>Text</option>
                                                                    <option value="textarea" <?= ($row['formType'] === 'textarea') ? 'selected' : '' ?>>Textarea</option>
                                                                    <option value="select" <?= ($row['formType'] === 'select') ? 'selected' : '' ?>>Select</option>
                                                                    <option value="file" <?= ($row['formType'] === 'file') ? 'selected' : '' ?>>File</option>
                                                                    <option value="checkbox" <?= ($row['formType'] === 'checkbox') ? 'selected' : '' ?>>Checkbox</option>
                                                                    <option value="radio" <?= ($row['formType'] === 'radio') ? 'selected' : '' ?>>Radio</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Field Name <span class="text-danger">*</span></label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control" name="items[item-card-<?php echo $uniqueID?>][fieldName]" id="fieldName" placeholder="Enter field name" value="<?php echo $row['fieldName']?>" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <label for="required" class="form-label">Required <span class="text-danger">*</span></label>
                                                            <div class="form-control-wrap">
                                                                <select class="js-select" name="items[item-card-<?php echo $uniqueID?>][required]" id="required" data-search="true" data-remove="true" data-placeholder="Select a type" required>
                                                                    <option value="true" <?= ($row['required'] === 'true') ? 'selected' : '' ?>>Yes</option>
                                                                    <option value="false" <?= ($row['required'] === 'false') ? 'selected' : '' ?>>No</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 formType-File <?= ($row['formType'] === 'file') ? '' : 'd-none' ?>">
                                                        <div class="form-group">
                                                            <label for="fileExtensions" class="form-label">File Extensions <span class="text-danger">*</span></label>
                                                            <div class="form-control-wrap">
                                                                <select class="js-select" name="items[item-card-<?php echo $uniqueID?>][fileExtensions][]" multiple id="fileExtensions" data-search="true" data-remove="true" data-placeholder="Select extensions">
                                                                    <?php $selectedValues = array_map('trim', explode(',', $row['value'])); ?>

                                                                    <option value="jpg"  <?= in_array('jpg',  $selectedValues) ? 'selected' : '' ?>>JPG</option>
                                                                    <option value="jpeg" <?= in_array('jpeg', $selectedValues) ? 'selected' : '' ?>>JPEG</option>
                                                                    <option value="png"  <?= in_array('png',  $selectedValues) ? 'selected' : '' ?>>PNG</option>
                                                                    <option value="gif"  <?= in_array('gif',  $selectedValues) ? 'selected' : '' ?>>GIF</option>
                                                                    <option value="webp" <?= in_array('webp', $selectedValues) ? 'selected' : '' ?>>WEBP</option>
                                                                    <option value="bmp"  <?= in_array('bmp',  $selectedValues) ? 'selected' : '' ?>>BMP</option>
                                                                    <option value="svg"  <?= in_array('svg',  $selectedValues) ? 'selected' : '' ?>>SVG</option>
                                                                    <option value="ico"  <?= in_array('ico',  $selectedValues) ? 'selected' : '' ?>>ICO</option>
                                                                    <option value="tiff" <?= in_array('tiff', $selectedValues) ? 'selected' : '' ?>>TIFF</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 formType-Select <?= ($row['formType'] === 'select') ? '' : 'd-none' ?>">
                                                        <div class="form-group">
                                                            <label class="form-label">Add Options <span class="text-danger">*</span></label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="js-tags form-control" id="items[item-card-<?php echo $uniqueID?>][addOptions][]" value="<?php echo $row['value']?>" placeholder="Type and press Enter">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                <?php
                                    }
                                ?>
                            </div>
                            <!--items--> 
                            <!--items--> 
                            <!--items--> 

                            <center>
                                <a href="javascript:void(0)" class="btn bg-white text-dark mt-3 mb-3" onclick="add_new_item()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                                    <span>Add New Field</span>
                                </a>
                            </center>
                        </div>
                    </div>
                    
                    <div class="pt-3">
                        <button class="btn btn-primary btn-paymentLink-edit" type="submit">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script data-cfasync="false">
    function deleteItem(ItemID){
        var my_action_confirmation_btn = document.querySelector("#my-action-confirmation-btn").value;
        var csrf_token_default = $('input[name="csrf_token_default"]').val();

        var btnClass = 'btnDeleteItem-'+ItemID;

        if(my_action_confirmation_btn !== ""){
            var btn = document.querySelector('#model-my-action-confirmation-btn').innerHTML;

            document.querySelector('#model-my-action-confirmation-btn').innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

            $.ajax({
                type: 'POST',
                url: '<?php echo $site_url.$path_admin ?>/dashboard',
                data: {action: "paymentLink-delete", csrf_token: csrf_token_default, ItemID: ItemID},
                dataType: 'json',
                success: function (response) {
                    closeAllBootstrapModals();
            
                    document.querySelector("#my-action-confirmation-btn").value = '';

                    document.querySelector('#model-my-action-confirmation-btn').innerHTML = btn;

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

                        load_content('Invoice','<?php echo $site_url.$path_admin ?>/invoice','nav-item-invoice');
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
            show_action_confirmation_tab(btnClass, 'Delete Payment Link', 'Delete', 'btn-danger');
        }
    }

    function FNcurrency(){
        var currency_main = document.querySelector(".in-currency").value;

        document.querySelectorAll('.currency-code').forEach(el => {
            el.innerHTML = currency_main; 
        });
    }
    FNcurrency();

    function add_new_item(){
        const itemList = document.querySelector('.item-list');

        const uniqueId = 'item-card-' + Date.now() + '-' + Math.floor(Math.random() * 1000);

        const itemCard = document.createElement('div');
        itemCard.classList.add('card', 'mt-3');
        itemCard.id = uniqueId; // ✅ unique ID added
        itemCard.innerHTML = `
            <input type="hidden" name="items[${uniqueId}][fieldID]" value="">

            <div class="card-header align-items-center justify-content-between">
                <h3 class="card-title">Input field</h3>
                <svg class="remove-item text-danger" style=" cursor: pointer; " xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
            </div>
            <div class="card-body p-4 pt-3">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="formType" class="form-label">Form Type <span class="text-danger">*</span></label>
                            <div class="form-control-wrap">
                                <select class="js-select" name="items[${uniqueId}][formType]" id="formType" data-search="true" data-remove="true" data-placeholder="Select a type" onchange="formTypeF('${uniqueId}')" required>
                                    <option value="text" selected>Text</option>
                                    <option value="textarea">Textarea</option>
                                    <option value="select">Select</option>
                                    <option value="file">File</option>
                                    <option value="checkbox">Checkbox</option>
                                    <option value="radio">Radio</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-label">Field Name <span class="text-danger">*</span></label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" name="items[${uniqueId}][fieldName]" id="fieldName" placeholder="Enter field name"  required>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="required" class="form-label">Required <span class="text-danger">*</span></label>
                            <div class="form-control-wrap">
                                <select class="js-select" name="items[${uniqueId}][required]" id="required" data-search="true" data-remove="true" data-placeholder="Select a type" required>
                                    <option value="true" selected>Yes</option>
                                    <option value="false">No</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 formType-File d-none">
                        <div class="form-group">
                            <label for="fileExtensions" class="form-label">File Extensions <span class="text-danger">*</span></label>
                            <div class="form-control-wrap">
                                <select class="js-select" name="items[${uniqueId}][fileExtensions][]" multiple id="fileExtensions" data-search="true" data-remove="true" data-placeholder="Select extensions">
                                    <option value="jpg">JPG</option>
                                    <option value="jpeg">JPEG</option>
                                    <option value="png">PNG</option>
                                    <option value="gif">GIF</option>
                                    <option value="webp">WEBP</option>
                                    <option value="bmp">BMP</option>
                                    <option value="svg">SVG</option>
                                    <option value="ico">ICO</option>
                                    <option value="tiff">TIFF</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 formType-Select d-none">
                        <div class="form-group">
                            <label class="form-label">Add Options <span class="text-danger">*</span></label>
                            <div class="form-control-wrap">
                                <input type="text" class="js-tags form-control" id="items[${uniqueId}][addOptions][]" placeholder="Type and press Enter">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Append item card to the list
        itemList.appendChild(itemCard);

        initChoices('.js-select');
        initTags();

        // Add event listener to remove icon
        const removeBtn = itemCard.querySelector('.remove-item');
        removeBtn.addEventListener('click', function() {
            itemCard.remove();
        });
    }

    function delete_item(divID, id) {
        var item = document.querySelector("#" + divID);
        var deletedInput = document.getElementById('deleted_items');

        if (item) {
            item.remove();
        }

        // Add ID to hidden input (comma-separated)
        if (deletedInput) {
            let current = deletedInput.value ? deletedInput.value.split(',') : [];

            if (!current.includes(String(id))) {
                current.push(id);
            }

            deletedInput.value = current.join(',');
        }
    }


    function formTypeF(itemID) {
        var item = document.querySelector("#" + itemID);

        var formType = item.querySelector('#formType').value;

        if (formType === "select" || formType === "file" || formType === "checkbox" || formType === "radio") {
            if (formType === "file") {
                item.querySelector('.formType-File').classList.remove('d-none');
                item.querySelector('.formType-Select').classList.add('d-none');
            }else{
                item.querySelector('.formType-File').classList.add('d-none');
                item.querySelector('.formType-Select').classList.remove('d-none');
            }
        } else {
            item.querySelector('.formType-File').classList.add('d-none');
            item.querySelector('.formType-Select').classList.add('d-none');
        }
    }

    $('.form-paymentLink-edit').submit(function (e) {
        e.preventDefault();

        var btn = document.querySelector(".btn-paymentLink-edit").innerHTML;
        document.querySelector(".btn-paymentLink-edit").innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

        var formData = new FormData(this);

        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
                closeAllBootstrapModals();

                document.querySelector(".btn-paymentLink-edit").innerHTML = btn;

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