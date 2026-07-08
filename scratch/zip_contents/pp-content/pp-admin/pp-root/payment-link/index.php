<?php
    if (!defined('Profess0rPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
    }

    if (!canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'payment_link', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }
?>

<style>
    .table-responsive table thead tr{
        height: 46px;
    }
    .table-responsive table tbody tr{
        height: 66px;
    }
</style>

<div class="page-header d-print-none" aria-label="Page header">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
            <!-- Page pre-title -->
                <div class="page-pretitle">Payment Link</div>
                <h2 class="page-title">Payment Link</h2>
            </div>

            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list align-items-center gap-3">
                    <span class="global-loaderSpinner"></span>
                   
                    <span  data-bs-target="#modal-createItem" data-bs-toggle="modal">
                        <a href="javascript:void(0)" class="btn btn-5 d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-link"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 15l6 -6" /><path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464" /><path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463" /></svg>
                            Default Link
                        </a>
                        <a href="javascript:void(0)" class="btn btn-6 d-sm-none btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-link"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 15l6 -6" /><path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464" /><path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463" /></svg>
                        </a>
                    </span>

                    <span onclick="load_content('Create Temp Link','<?php echo $site_url.$path_admin ?>/payment-link/create?type=temp','nav-item-payment-link')" class="<?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'payment_link', 'create', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>">
                        <a href="javascript:void(0)" class="btn btn-warning btn-5 d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 7v5l3 3" /></svg>
                            Create Temp Link
                        </a>
                        <a href="javascript:void(0)" class="btn btn-warning btn-6 d-sm-none btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 7v5l3 3" /></svg>
                        </a>
                    </span>

                    <span onclick="load_content('Create Payment Link','<?php echo $site_url.$path_admin ?>/payment-link/create','nav-item-payment-link')" class="<?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'payment_link', 'create', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>">
                        <a href="javascript:void(0)" class="btn btn-primary btn-5 d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                            Create Payment Link
                        </a>
                        <a href="javascript:void(0)" class="btn btn-primary btn-6 d-sm-none btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
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
        <div class="row row-deck row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="w-100" style="border-bottom: 1px solid #e8e7ec;">
                        <div class="filter-tab-data p-4 d-none">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title">Filters</h3>

                                <h5 class="text-danger" style=" font-size: 14px; cursor: pointer; " onclick="filter_hide_show_reset('filter-tab-data')">Reset</h5>
                            </div>

                            <div class="row g-3" style=" margin-top: -10px; margin-bottom: -25px; ">
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="fullname" class="form-label">Status</label>
                                        <div class="form-control-wrap">
                                            <select class="form-select" id="filter-status">
                                                <option value="">All</option>
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="username" class="form-label">Created From</label>
                                        <div class="form-control-wrap">
                                            <input placeholder="dd/mm/yyyy" type="date" class="form-control" id="filter-created-from"> 
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="email-address" class="form-label">Created Until</label>
                                        <div class="form-control-wrap">
                                            <input placeholder="dd/mm/yyyy" type="date" class="form-control" id="filter-created-until"> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: row-reverse; height: 53px; align-items: center; padding-right: 20px; font-size: 22px;">
                           <svg onclick="filter_hide_show('filter-tab-data')" style="cursor: pointer;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-filter"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v7l-6 2v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227z" /></svg>
                        </div>
                    </div>

                   <div class="card-body border-bottom py-3">
                        <div class="row g-4">
                            <div class="col-lg-6 col-md-6">
                                <div class="text-secondary">
                                    Show<div class="mx-2 d-inline-block"><input type="text" class="form-control form-control-sm show_limit" value="8" size="3" aria-label="count"></div>entries
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 d-flex align-items-center justify-content-right gap-2">
                                <div class="ms-auto text-secondary">
                                    Search:<div class="ms-2 d-inline-block"><input type="text" class="form-control form-control-sm search_input" aria-label="Search"></div>
                                </div>

                                <button class="btn btn-danger bulk-action d-none" data-bs-toggle="modal" data-bs-target="#model-bulkAction"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-square"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 5a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-14" /></svg> <span id="bulkActionBTN-count">(0)</span></button>
                            </div>
                        </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-selectable card-table table-vcenter text-nowrap datatable">
                      <thead>
                        <tr>
                            <th class="w-1"><input class="form-check-input m-0 align-middle select-all" type="checkbox" aria-label="Select all invoices"></th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Amount</th>
                            <th>Created Date</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                      </thead>
                      <tbody class="table-data-list">

                      </tbody>
                    </table>
                  </div>
                  <div class="card-footer">
                    <div class="row g-2 justify-content-center justify-content-sm-between">
                      <div class="col-auto d-flex align-items-center">
                        <p class="m-0 text-secondary table-data-list-entries"></p>
                      </div>
                      <div class="col-auto table-data-list-pagination">

                      </div>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="model-bulkAction" data-bs-keyboard="false" tabindex="-1" aria-labelledby="scrollableLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title model-bulkAction-title" id="scrollableLabel">Action for Selected Items</h5> 
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"> 
                <div class="form-group mt-1">
                    <label for="model-bulkAction-name" class="form-label">Action <span class="text-danger">*</span></label>
                    <div class="form-control-wrap">
                        <select class="form-select" id="model-bulkActionID">
                            <option value="" selected>Select a Action</option>
                            <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'payment_link', 'delete', $global_user_response['response'][0]['role']) ? '<option value="deleted">Delete Selected</option>' : '' ?>
                            <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'payment_link', 'edit', $global_user_response['response'][0]['role']) ? '<option value="activated">Active Selected</option>' : '' ?>
                            <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'payment_link', 'edit', $global_user_response['response'][0]['role']) ? '<option value="inactivated">Inactive Selected</option>' : '' ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary model-bulkAction-btn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="model-manageStatus" data-bs-keyboard="false" tabindex="-1" aria-labelledby="scrollableLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content">
            <form action="" class="form-manageStatus">
                <input type="hidden" name="action" value="paymentLink-manageStatus">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">

                <div class="modal-header">
                    <h5 class="modal-title model-manageStatus-title" id="scrollableLabel">Manage Status</h5> 
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body"> 
                    <div class="form-group mt-1">
                        <label for="model-manageStatus-name" class="form-label">Status <span class="text-danger">*</span></label>
                        <div class="form-control-wrap">
                            <input type="hidden" name="invoice-id">
                            <select class="form-select" name="status" id="invoice-status">
                                <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'payment_link', 'edit', $global_user_response['response'][0]['role']) ? '<option value="active">Active</option>' : '' ?>
                                <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'payment_link', 'edit', $global_user_response['response'][0]['role']) ? '<option value="inactive">Inactive</option>' : '' ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary btn-manageStatus">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>







<!--extra requirement-->
<!--extra requirement-->
<!--extra requirement-->
<div class="modal modal-blur fade" id="modal-createItem" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Default Link</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <div class="row mb-3 g-3">
                <div class="col-lg-12">
                  <div class="form-group">
                      <label class="form-label">Default Link Logo</label>
                      <?php
                          $current_default_logo = get_env('payment-link-default-logo', $global_response_brand['response'][0]['brand_id']);
                          if (!empty($current_default_logo) && $current_default_logo !== '--') {
                              echo '<div class="mb-2" id="default-logo-preview-box"><img src="'.$current_default_logo.'" style="max-height: 40px; border-radius: 4px; border: 1px solid #e2e8f0; padding: 4px; background: #fff; margin-right: 8px;"> <span style="color: #e63946; font-size: 13px; cursor: pointer; text-decoration: underline;" onclick="RemoveDefaultLogo()">Remove</span></div>';
                          }
                      ?>
                      <div class="form-control-wrap">
                          <input type="file" class="form-control" id="default_link_logo" name="default_link_logo" accept="image/*" onchange="DefaultchangeLogo(this)">
                      </div>
                      <small class="text-muted">Leave empty to use primary brand logo.</small>
                  </div>
                </div>

                <div class="col-lg-12">
                <div class="form-group">
                    <label class="form-label">Currency <span class="text-danger">*</span></label>
                    <div class="form-control-wrap">
                        <?php
                            $defaultCurrency = get_env('payment-link-default-currency', $global_response_brand['response'][0]['brand_id']);

                            $activeCurrency = (empty($defaultCurrency) || $defaultCurrency === '--') ? $global_brand_currency_code : $defaultCurrency;
                        ?>
                        <select class="js-select DefaultCurrency" data-search="true" data-remove="true" data-placeholder="Select currency" required onchange="DefaultchangeCurrency()">
                            <?php
                                $response_brand = json_decode(getData($db_prefix . 'currency', 'WHERE brand_id ="'.$global_response_brand['response'][0]['brand_id'].'" ORDER BY 1 DESC'), true);
                                if ($response_brand['status'] == true) {
                                    foreach ($response_brand['response'] as $row) {
                            ?>
                                        <option value="<?php echo $row['code'] ?>" <?php echo ($activeCurrency === $row['code']) ? 'selected' : '';?>><?php echo $row['code'] ?></option>
                            <?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
              </div>

              <div class="col-lg-12">
                <label class="form-label">Payment Link URL <span class="text-danger">*</span></label>
                <div class="form-control-wrap mb-2">
                    <div class="input-group">
                        <input type="text" class="form-control DefaultPaymentLink" readonly value="<?php echo $site_url?><?php echo $path_payment_link?>/default/<?php echo $global_response_brand['response'][0]['brand_id']?>">
                        
                        <button class="btn border bg-light btn-color-dark btn-hover-primary btn-icon btn-soft" type="button" onclick="copyDefaultPaymentLink()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-copy"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 9.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667l0 -8.666" /><path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" /></svg>
                        </button>
                    </div>
                </div>
              </div>

              <div class="col-lg-12 text-center">
                <div class="card d-inline-block">
                    <div class="card-body p-3 text-center">
                        <div id="qrcode"></div>
                    </div>
                </div>

                 <p class="mt-2 DefaultPaymentLinkText">Scan to access payment link for <?php echo $global_brand_currency_code?></p>


                <button type="button" class="btn btn-md btn-primary" onclick="downloadQR('png')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-download"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg> Download PNG
                </button>

                <button type="button" class="btn btn-md btn-secondary" onclick="downloadQR('svg')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-download"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg> Download SVG
                </button>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <a href="javascript:void(0)" class="btn btn-link link-secondary btn-3" data-bs-dismiss="modal"> Cancel </a>
          </div>
        </div>
    </div>
</div>
<!--extra requirement-->
<!--extra requirement-->
<!--extra requirement-->






<script data-cfasync="false">
    function copyDefaultPaymentLink(){
        var DefaultPaymentLink = document.querySelector(".DefaultPaymentLink").value;

        copyContent(DefaultPaymentLink, 'Copied!', 'Payment Link copied successfully.')
    }

    function DefaultchangeCurrency(){
        var DefaultCurrency = document.querySelector(".DefaultCurrency").value;

        document.querySelector(".DefaultPaymentLink").value = '<?php echo $site_url?><?php echo $path_payment_link?>/default/<?php echo $global_response_brand['response'][0]['brand_id']?>';

        const text = '<?php echo $site_url?><?php echo $path_payment_link?>/default/<?php echo $global_response_brand['response'][0]['brand_id']?>';
        if (!text) return alert('Enter some text');

        document.getElementById('qrcode').innerHTML = "";
        qr = new QRCode(document.getElementById("qrcode"), {
            text: text,
            width: 200,
            height: 200
        });

        var csrf_token_default = $('input[name="csrf_token_default"]').val();

        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            data: {action: "paymentLink-defaultLinkCurrency", csrf_token: csrf_token_default, DefaultCurrency: DefaultCurrency},
            dataType: 'json',
            success: function (response) {
                document.querySelectorAll('input[name="csrf_token"]').forEach(input => {
                    input.value = response.csrf_token;
                });
                document.querySelectorAll('input[name="csrf_token_default"]').forEach(input => {
                    input.value = response.csrf_token;
                });

                if (response.status === 'true') {

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

        document.querySelector(".DefaultPaymentLinkText").innerHTML = 'Scan to access payment link for '+DefaultCurrency;
    }

    function RemoveDefaultLogo() {
        if (!confirm('Are you sure you want to remove the default link logo? The primary brand logo will be used instead.')) return;
        
        var formData = new FormData();
        formData.append('action', 'paymentLink-removeDefaultLogo');
        formData.append('csrf_token', $('input[name="csrf_token_default"]').val());

        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                if(response.status == 'true'){
                    $('#default-logo-preview-box').remove();
                    createToast({ title: response.title, description: response.message, timeout: 5000 });
                } else {
                    createToast({ title: response.title, description: response.message, timeout: 6000 });
                }
            },
            error: function() {
                createToast({ title: 'Error', description: 'Failed to remove logo', timeout: 6000 });
            }
        });
    }

    function DefaultchangeLogo(input) {
        if (!input.files.length) return;
        var file = input.files[0];
        if (file.size > 2 * 1024 * 1024) {
            createToast({
                title: 'Action required!',
                description: 'The selected file exceeds the maximum allowed size of 2MB.',
                svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                timeout: 6000,
                top: 70
            });
            input.value = '';
            return;
        }

        var formData = new FormData();
        formData.append('action', 'paymentLink-defaultLinkLogo');
        formData.append('csrf_token', $('input[name="csrf_token_default"]').val());
        formData.append('logo_file', file);

        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
                document.querySelectorAll('input[name="csrf_token"]').forEach(el => {
                    el.value = response.csrf_token;
                });
                document.querySelectorAll('input[name="csrf_token_default"]').forEach(el => {
                    el.value = response.csrf_token;
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
            }
        });
    }

    function downloadQR(type) {
        const qrContainer = document.getElementById('qrcode');

        // Try canvas first
        const canvas = qrContainer.querySelector('canvas');
        const img = qrContainer.querySelector('img');

        if (type === 'png') {
            let dataURL;

            if (canvas) {
                dataURL = canvas.toDataURL("image/png");
            } else if (img) {
                dataURL = img.src;
            } else {
                alert('QR code not found');
                return;
            }

            const a = document.createElement('a');
            a.href = dataURL;
            a.download = 'payment-link-qr.png';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        if (type === 'svg') {
            if (!canvas) {
                alert('SVG download requires canvas QR');
                return;
            }

            const svg = `
            <svg xmlns="http://www.w3.org/2000/svg" width="${canvas.width}" height="${canvas.height}">
                <image href="${canvas.toDataURL('image/png')}"
                    width="${canvas.width}"
                    height="${canvas.height}" />
            </svg>
            `;

            const blob = new Blob([svg], { type: 'image/svg+xml;charset=utf-8;' });
            const url = URL.createObjectURL(blob);

            const a = document.createElement('a');
            a.href = url;
            a.download = 'payment-link-qr.svg';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);

            URL.revokeObjectURL(url);
        }
    }

    $('.form-manageStatus').submit(function (e) {
        e.preventDefault();

        var btn = document.querySelector('.btn-manageStatus').innerHTML;

        document.querySelector('.btn-manageStatus').innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            data: formData,
            dataType: 'json',
            success: function (response) {
                closeAllBootstrapModals();

                document.querySelector(".btn-manageStatus").innerHTML = btn;

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

                    load_data_list();
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

    $('.model-bulkAction-btn').click(function () {
        var my_action_confirmation_btn = document.querySelector("#my-action-confirmation-btn").value;
        var actionID = document.querySelector("#model-bulkActionID").value;
        var csrf_token_default = $('input[name="csrf_token_default"]').val();

        if(actionID == ""){
            createToast({
                title: 'Action Required',
                description: 'You haven’t selected any action. Please choose one to proceed.',
                svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                timeout: 6000,
                top: 70
            });
        }else{
            const selectedRows = Array.from(document.querySelectorAll('.rowCheckbox:checked')).map(cb => cb.closest('tr').dataset.id);

            var loaderSpinner = 'global-loaderSpinner';

            if(my_action_confirmation_btn !== ""){
                document.querySelector('.'+loaderSpinner).innerHTML = '<div class="spinner-border spinner-border-md text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';

                $.ajax({
                    type: 'POST',
                    url: '<?php echo $site_url.$path_admin ?>/dashboard',
                    data: {action: "paymentLink-bulk-action", csrf_token: csrf_token_default, actionID: actionID, selected_ids: JSON.stringify(selectedRows)},
                    dataType: 'json',
                    success: function (response) {
                        closeAllBootstrapModals();
                
                        document.querySelector("#my-action-confirmation-btn").value = '';

                        document.getElementById("model-bulkActionID").selectedIndex = 0;

                        document.querySelector('.'+loaderSpinner).innerHTML = '';

                        document.querySelectorAll('input[name="csrf_token"]').forEach(input => {
                            input.value = response.csrf_token;
                        });
                        document.querySelectorAll('input[name="csrf_token_default"]').forEach(input => {
                            input.value = response.csrf_token;
                        });

                        if (response.status === 'true') {
                            document.querySelectorAll('.select-all').forEach(cb => {
                                cb.checked = false;
                            });

                            document.querySelector('.bulk-action').classList.add('d-none');

                            createToast({
                                title: response.title,
                                description: response.message,
                                svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#5f38f9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>`,
                                timeout: 6000,
                                top: 70
                            });

                            load_data_list(1);
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
                show_action_confirmation_tab('model-bulkAction-btn', 'Confirm Action', 'Confirm', 'btn-danger');
            }
        }
    });
    
    function initCheckboxTable() {
        const selectAll = document.querySelector('.select-all');
        const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
        const bulkActionBTN = document.querySelector('.bulk-action');

        function updateSelection() {
            const selected = document.querySelectorAll('.rowCheckbox:checked');
            document.getElementById("bulkActionBTN-count").innerHTML = `(${selected.length})`;
            if (selected.length > 0) {
                bulkActionBTN.classList.remove('d-none');
            } else {
                bulkActionBTN.classList.add('d-none');
            }
        }

        selectAll.addEventListener('change', () => {
            rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
            updateSelection();
        });

        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                selectAll.checked = rowCheckboxes.length === document.querySelectorAll('.rowCheckbox:checked').length;
                updateSelection();
            });
        });
    }

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

                        load_data_list(1);
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

    function load_data_list(page = 1){
        currentPage = page;

        var csrf_token_default = $('input[name="csrf_token_default"]').val();
        var search_input = $('.search_input').val();
        var show_limit = $('.show_limit').val();

        var filter_status = $('#filter-status').val();
        var filter_start = $('#filter-created-from').val();
        var filter_end = $('#filter-created-until').val();

        let html = '';

        $(".table-data-list").html('<tr><td colspan="7" class="text-center text-muted"><div class="spinner-border text-primary" style="margin: 50px;">  <span class="visually-hidden">Loading...</span></div></td></tr>');

        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            data: {action: "paymentLink-list", csrf_token: csrf_token_default, search_input: search_input, show_limit: show_limit, page: page, filter_status: filter_status, filter_start: filter_start, filter_end: filter_end},
            dataType: 'json',
            success: function (res) {
                let html = '';

                document.querySelectorAll('input[name="csrf_token"]').forEach(input => {
                    input.value = res.csrf_token;
                });
                document.querySelectorAll('input[name="csrf_token_default"]').forEach(input => {
                    input.value = res.csrf_token;
                });

                if (res.status === 'true') {
                    res.response.forEach(item => {
                        let badge = 'secondary';

                        let allowEdit = <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'payment_link', 'edit', $global_user_response['response'][0]['role']) ? 'true' : 'false' ?>;
                        let allowDelete = <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'payment_link', 'delete', $global_user_response['response'][0]['role']) ? 'true' : 'false' ?>;

                        let redirectEdit = '';
                        let redirectDelete = '';
                        let redirectChangeStatus = '';
                        
                        if (allowEdit) {
                            redirectEdit = `style="cursor:pointer;" onclick="load_content('Edit Payment Link','<?php echo $site_url.$path_admin ?>/payment-link/edit?p_id=${item.id}','nav-item-payment-link')"`;
                            redirectChangeStatus = `onclick="openChangeStatusModel('${item.id}', '${item.status}')"`;
                        }

                        if (allowDelete) {
                            redirectDelete = `onclick="deleteItem('${item.id}')"`;
                        }

                        if (item.status === 'active') badge = 'primary';
                        if (item.status === 'inactive') badge = 'danger';

                        html += `
                            <tr data-id="${item.id}">
                                <td><input class="form-check-input m-0 align-middle table-selectable-check rowCheckbox" type="checkbox" aria-label="Select invoice"></td>
                                <td ${redirectEdit}>
                                    <div class="d-flex py-1 align-items-center">
                                        <div class="flex-fill">
                                            <div class="font-weight-medium">${item.title}</div>
                                            <div class="text-secondary">${item.description}</div>
                                        </div>
                                    </div>
                                </td>
                                <td ${redirectEdit}>${item.quantity}</td>
                                <td ${redirectEdit}>${item.amount}</td>
                                <td ${redirectEdit}>${item.created_date}</td>
                                <td>
                                    <span class="badge bg-${badge} me-1" ${redirectEdit}></span>
                                    <span ${redirectEdit} style="${allowEdit ? 'cursor:pointer;' : ''}">${item.status.charAt(0).toUpperCase() + item.status.slice(1)}</span>
                                    <button class="btn btn-sm btn-primary ms-2" onclick="copyContent('<?php echo $site_url.$path_payment_link ?>/${item.id}', 'Copied!', 'Payment Link copied successfully.')" title="Copy Link" style="border-radius: 4px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 9.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667l0 -8.666" /><path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" /></svg>
                                        Copy Link
                                    </button>
                                </td>
                                <td class="text-end">
                                    <span class="dropdown" style="position: unset;">
                                        <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">Actions</button>
                                        <div class="dropdown-menu dropdown-menu-end" style="">
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="copyContent('<?php echo $site_url.$path_payment_link ?>/${item.id}', 'Copied!', 'Payment Link copied successfully.')"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-copy"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 9.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667l0 -8.666" /><path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" /></svg> Copy Link </a>
                                            <a class="dropdown-item ${allowEdit ? '' : 'd-none'}" href="javascript:void(0)" ${redirectEdit}> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415" /><path d="M16 5l3 3" /></svg> Edit </a>
                                            <a class="dropdown-item btnDeleteItem-${item.id} ${allowDelete ? '' : 'd-none'}" href="javascript:void(0)" ${redirectDelete}> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg> Delete </a>
                                            <a class="dropdown-item ${allowEdit ? '' : 'd-none'}" href="javascript:void(0)" ${redirectChangeStatus}> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-status-change"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 18a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M16 18a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M6 12v-2a6 6 0 1 1 12 0v2" /><path d="M15 9l3 3l3 -3" /></svg> Change Status </a>
                                        </div>
                                    </span>
                                </td>
                            </tr>
                        `;
                    });

                    $(".table-data-list").html(html);

                    initCheckboxTable();

                    document.querySelector(".table-data-list-entries").innerHTML = res.datatableInfo;

                    $(".table-data-list-pagination").html(res.pagination);
                } else {
                    html = `<td colspan="7" class="text-center text-muted"> <div style="margin: 50px;"> <center> <svg xmlns="http://www.w3.org/2000/svg" style=" width: 40px; height: 40px; " viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-mood-cry"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 10l.01 0" /><path d="M15 10l.01 0" /><path d="M9.5 15.25a3.5 3.5 0 0 1 5 0" /><path d="M17.566 17.606a2 2 0 1 0 2.897 .03l-1.463 -1.636l-1.434 1.606z" /><path d="M20.865 13.517a8.937 8.937 0 0 0 .135 -1.517a9 9 0 1 0 -9 9c.69 0 1.36 -.076 2 -.222" /></svg> <p style=" font-weight: 600; font-size: 16px; margin-top: 7px; margin-bottom: 3px; ">`+res.title+`</p> <p style=" margin: 0; ">`+res.message+`</p> </center> </div> </td>`;
                    $(".table-data-list").html(html);
                    document.querySelector(".table-data-list-entries").innerHTML = 'Showing <strong>0 to 0</strong> of <strong>0 entries</strong>';

                    $(".table-data-list-pagination").html('<ul class="pagination m-0 ms-auto"><li class="page-item disabled"> <button class="page-link"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1"> <path d="M15 6l-6 6l6 6"></path> </svg> </button> </li><li class="page-item active"> <button class="page-link disabled" data-page="1">1</button> </li><li class="page-item disabled"> <button class="page-link" data-page="2"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1"> <path d="M9 6l6 6l-6 6"></path> </svg> </button> </li> </ul>');
                }

                DefaultchangeCurrency();
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
    }

    $(document).on('click', '.table-data-list-pagination button', function () {
        let page = $(this).data('page');
        load_data_list(page);
    });

    load_data_list(1);

    function filter_hide_show_reset(className) {
        const container = document.querySelector('.' + className);
        if (!container) return;

        // Reset inputs
        container.querySelectorAll('input').forEach(input => {
            input.value = '';
        });

        // Reset selects
        container.querySelectorAll('select').forEach(select => {
            select.selectedIndex = 0;
        });

        load_data_list(1);
    }

    document.querySelectorAll('.filter-tab-data input, .filter-tab-data select, .search_input, .show_limit').forEach(el => {
        el.addEventListener('change', function () {
            load_data_list(1);
        });
    });

    //extra requirement 
    //extra requirement 
    //extra requirement 
    function openChangeStatusModel(itemID, itemStatus){
        // Get modal element
        const modal = document.getElementById("model-manageStatus");

        // Set input values
        modal.querySelector('input[name="paymentLink-id"]').value = itemID || '';
        const select = modal.querySelector('#paymentLink-status');

        [...select.options].forEach(opt => {
            opt.selected = (opt.value == itemStatus);
        });

        // Show modal
        $('#model-manageStatus').modal('show');
    }
    //extra requirement 
    //extra requirement 
    //extra requirement 
</script>