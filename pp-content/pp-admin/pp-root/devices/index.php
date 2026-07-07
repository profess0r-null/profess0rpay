<?php
    if (!defined('Profess0rPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
    }

    if (!canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'device', $global_user_response['response'][0]['role'])) {
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
                <div class="page-pretitle">Devices</div>
                <h2 class="page-title">Devices</h2>
            </div>

            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list align-items-center gap-3">
                    <span class="global-loaderSpinner"></span>
                   
                    <span data-bs-target="#modal-createItem" data-bs-toggle="modal" onclick="iniConnectDeviceInfo()" class="<?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'device', 'connect', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>">
                        <a href="javascript:void(0)" class="btn btn-primary btn-5 d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                            Connect Device
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
                                                <option value="connected">Connected</option>
                                                <option value="disconnected">Disconnected</option>
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
                            <th>Device Name</th>
                            <th>Device Model</th>
                            <th>Android Level</th>
                            <th>Created Date</th>
                            <th>Last Sync</th>
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
                            <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'device', 'delete', $global_user_response['response'][0]['role']) ? '<option value="deleted">Delete Selected</option>' : '' ?>
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

<!-- Edit Device Name Modal -->
<div class="modal fade" id="modal-editDeviceName" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Device Name</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Device Name</label>
              <input type="text" class="form-control" id="edit-device-name-input" placeholder="e.g. My Galaxy S24">
              <input type="hidden" id="edit-device-id-input">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn me-auto" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="saveDeviceName()">Save changes</button>
          </div>
        </div>
    </div>
</div>
<!--extra requirement-->
<!--extra requirement-->
<!--extra requirement-->
<div class="modal modal-blur fade" id="modal-createItem" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Connect Device</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
            <div class="modal-body">

                <!-- Info Alert -->
                <div class="alert alert-info" role="alert">
                    <p class="m-0">
                        Download the <strong>Profess0rPay Companion App</strong> (
                            <a href="https://play.google.com/store/apps/details?id=com.qubeplug.billpax_tools"
                            target="_blank"
                            class="alert-link">
                                Android
                            </a>
                            )
                            on your mobile device to connect it with your account.
                    </p>
                </div>

                <!-- QR Code Section -->
                <p>Scan this QR code in your Profess0rPay Companion app to connect your device automatically:</p>

                <div class="col-lg-12 text-center">
                    <div class="card d-inline-block">
                        <div class="card-body p-3 text-center loading-process">
                            <!--<div id="qrcode"></div> !-->
                            <div class="spinner-border spinner-border-md text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                        </div>
                    </div>
                </div>

                <p class="mt-3">Or connect manually using your credentials:</p> 

                <!-- Manual Connection Inputs -->
                <div class="form-group mt-2">
                    <label for="base-url" class="form-label">Base URL <span class="text-danger">*</span></label>
                    <div class="form-control-wrap">
                        <div class="input-group">
                            <input type="text" class="form-control" id="base-url" name="base-url" placeholder="https://yourdomain.com" value="<?php echo $site_url ?>" readonly>

                            <button class="btn border bg-light btn-color-dark btn-hover-primary btn-icon btn-soft" type="button" onclick="copyBaseUrl()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-copy"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 9.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667l0 -8.666" /><path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" /></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-2">
                    <label for="one-time-password" class="form-label">One-Time Password <span class="text-danger">*</span></label>
                    <div class="form-control-wrap">
                        <div class="input-group">
                            <input type="text" class="form-control" id="one-time-password" name="one-time-password" placeholder="" readonly>

                            <button class="btn border bg-light btn-color-dark btn-hover-primary btn-icon btn-soft" type="button" onclick="copyOnetimePass()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-copy"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 9.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667l0 -8.666" /><path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" /></svg>
                            </button>
                        </div>
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
    function iniModelConnectDevice(){
        var model = document.querySelector("#modal-createItem");
        var baseUrl = model.querySelector("#base-url").value;
        var pass = model.querySelector("#one-time-password").value;

        var text = baseUrl+'----'+pass;

        model.querySelector('#qrcode').innerHTML = "";
        qr = new QRCode(model.querySelector('#qrcode'), {
            text: text,
            width: 200,
            height: 200
        });
    }

    function iniConnectDeviceInfo(){
        var csrf_token_default = $('input[name="csrf_token_default"]').val();

        // Get modal element
        var model = document.querySelector("#modal-createItem");

        model.querySelector("#one-time-password").value = '';

        model.querySelector('.loading-process').innerHTML = '<div class="spinner-border spinner-border-md text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';

        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            data: {action: "device-connect-info", csrf_token: csrf_token_default},
            dataType: 'json',
            success: function (response) {
                document.querySelectorAll('input[name="csrf_token"]').forEach(input => {
                    input.value = response.csrf_token;
                });
                document.querySelectorAll('input[name="csrf_token_default"]').forEach(input => {
                    input.value = response.csrf_token;
                });

                if (response.status === 'true') {
                    model.querySelector("#one-time-password").value = response.otp;

                    model.querySelector('.loading-process').innerHTML = '<div id="qrcode"></div>';

                    iniModelConnectDevice();
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
    }

    function copyBaseUrl(){
        var model = document.querySelector("#modal-createItem");
        var baseUrl = model.querySelector("#base-url").value;

        copyContent(baseUrl, 'Copied!', 'Base url copied successfully.')
    }

    function copyOnetimePass(){
        var model = document.querySelector("#modal-createItem");
        var pass = model.querySelector("#one-time-password").value;

        copyContent(pass, 'Copied!', 'One-Time Password copied successfully.')
    }

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
                    data: {action: "device-bulk-action", csrf_token: csrf_token_default, actionID: actionID, selected_ids: JSON.stringify(selectedRows)},
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
                data: {action: "device-delete", csrf_token: csrf_token_default, ItemID: ItemID},
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
            show_action_confirmation_tab(btnClass, 'Delete Device', 'Delete', 'btn-danger');
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

        $(".table-data-list").html('<tr><td colspan="5" class="text-center text-muted"><div class="spinner-border text-primary" style="margin: 50px;">  <span class="visually-hidden">Loading...</span></div></td></tr>');

        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            data: {action: "device-list", csrf_token: csrf_token_default, search_input: search_input, show_limit: show_limit, page: page, filter_status: filter_status, filter_start: filter_start, filter_end: filter_end},
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
                        let allowBalanceVerification = <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'device', 'balance_verification_for', $global_user_response['response'][0]['role']) ? 'true' : 'false' ?>;
                        let allowDelete = <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'device', 'delete', $global_user_response['response'][0]['role']) ? 'true' : 'false' ?>;
                        let allowEdit = <?= (hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'device', 'edit', $global_user_response['response'][0]['role']) || hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'device', 'delete', $global_user_response['response'][0]['role'])) ? 'true' : 'false' ?>;
                        let redirectEdit = '';
                        let redirectDelete = '';
                        let redirectBalanceVerification = '';

                        if (allowBalanceVerification) {
                            redirectBalanceVerification = `onclick="load_content('Balance Verification','<?php echo $site_url.$path_admin ?>/devices/balance-verification?d_id=${item.id}','nav-item-devices')"`;
                        }

                        if (allowDelete) {
                            redirectDelete = `onclick="deleteItem('${item.id}')"`;
                        }

                        let safeName = String(item.name).replace(/"/g, '&quot;').replace(/'/g, '\\\'').replace(/(\r\n|\n|\r)/gm, " ");

                        html += `
                            <tr data-id="${item.id}">
                                <td><input class="form-check-input m-0 align-middle table-selectable-check rowCheckbox" type="checkbox" aria-label="Select invoice"></td>
                                <td ${redirectEdit}>${item.name}</td>
                                <td ${redirectEdit}>${item.model}</td>
                                <td ${redirectEdit}>${item.android_level}</td>
                                <td ${redirectEdit}>${item.created_date}</td>
                                <td ${redirectEdit}>${item.last_sync}</td>
                                <td class="text-end">
                                    <span class="dropdown" style="position: unset;">
                                        <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">Actions</button>
                                        <div class="dropdown-menu dropdown-menu-end" style="">
                                            <a class="dropdown-item ${allowEdit ? '' : 'd-none'}" href="javascript:void(0)" onclick="openEditDeviceNameModal('${item.id}', '${safeName}')"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-pencil"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg> Edit Name </a>
                                            <a class="dropdown-item ${allowBalanceVerification ? '' : 'd-none'}" href="javascript:void(0)" ${redirectBalanceVerification}> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-rosette-discount-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 7.2a2.2 2.2 0 0 1 2.2 -2.2h1a2.2 2.2 0 0 0 1.55 -.64l.7 -.7a2.2 2.2 0 0 1 3.12 0l.7 .7c.412 .41 .97 .64 1.55 .64h1a2.2 2.2 0 0 1 2.2 2.2v1c0 .58 .23 1.138 .64 1.55l.7 .7a2.2 2.2 0 0 1 0 3.12l-.7 .7a2.2 2.2 0 0 0 -.64 1.55v1a2.2 2.2 0 0 1 -2.2 2.2h-1a2.2 2.2 0 0 0 -1.55 .64l-.7 .7a2.2 2.2 0 0 1 -3.12 0l-.7 -.7a2.2 2.2 0 0 0 -1.55 -.64h-1a2.2 2.2 0 0 1 -2.2 -2.2v-1a2.2 2.2 0 0 0 -.64 -1.55l-.7 -.7a2.2 2.2 0 0 1 0 -3.12l.7 -.7a2.2 2.2 0 0 0 .64 -1.55v-1" /><path d="M9 12l2 2l4 -4" /></svg> Balance Verification </a>
                                            <a class="dropdown-item btnDeleteItem-${item.id} ${allowDelete ? '' : 'd-none'}" href="javascript:void(0)" ${redirectDelete}> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg> Delete </a>
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

                    initToolTips();
                } else {
                    html = `<td colspan="6" class="text-center text-muted"> <div style="margin: 50px;"> <center> <svg xmlns="http://www.w3.org/2000/svg" style=" width: 40px; height: 40px; " viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-mood-cry"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 10l.01 0" /><path d="M15 10l.01 0" /><path d="M9.5 15.25a3.5 3.5 0 0 1 5 0" /><path d="M17.566 17.606a2 2 0 1 0 2.897 .03l-1.463 -1.636l-1.434 1.606z" /><path d="M20.865 13.517a8.937 8.937 0 0 0 .135 -1.517a9 9 0 1 0 -9 9c.69 0 1.36 -.076 2 -.222" /></svg> <p style=" font-weight: 600; font-size: 16px; margin-top: 7px; margin-bottom: 3px; ">`+res.title+`</p> <p style=" margin: 0; ">`+res.message+`</p> </center> </div> </td>`;
                    $(".table-data-list").html(html);
                    document.querySelector(".table-data-list-entries").innerHTML = 'Showing <strong>0 to 0</strong> of <strong>0 entries</strong>';

                    $(".table-data-list-pagination").html('<ul class="pagination m-0 ms-auto"><li class="page-item disabled"> <button class="page-link"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1"> <path d="M15 6l-6 6l6 6"></path> </svg> </button> </li><li class="page-item active"> <button class="page-link disabled" data-page="1">1</button> </li><li class="page-item disabled"> <button class="page-link" data-page="2"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1"> <path d="M9 6l6 6l-6 6"></path> </svg> </button> </li> </ul>');
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

    window.openEditDeviceNameModal = function(deviceId, currentName) {
        document.getElementById('edit-device-id-input').value = deviceId;
        document.getElementById('edit-device-name-input').value = currentName;
        $('#modal-editDeviceName').modal('show');
    };

    window.saveDeviceName = function() {
        let deviceId = document.getElementById('edit-device-id-input').value;
        let newName = document.getElementById('edit-device-name-input').value;
        let csrf_token_default = $('input[name="csrf_token_default"]').val();
        
        if (!newName.trim()) {
            createToast({ title: 'Error', description: 'Device name cannot be empty.', timeout: 3000, top: 70 });
            return;
        }

        let formData = new FormData();
        formData.append('action', 'edit-device-name');
        formData.append('device_id', deviceId);
        formData.append('name', newName);
        formData.append('csrf_token', csrf_token_default);

        $.ajax({
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (res) {
                if (res.status === 'true') {
                    document.querySelectorAll('input[name="csrf_token"]').forEach(input => {
                        input.value = res.csrf_token;
                    });
                    document.querySelectorAll('input[name="csrf_token_default"]').forEach(input => {
                        input.value = res.csrf_token;
                    });

                    $('#modal-editDeviceName').modal('hide');
                    load_data_list(1);
                    createToast({ title: 'Success', description: res.message, timeout: 3000, top: 70 });
                } else {
                    createToast({ title: 'Error', description: res.message, timeout: 3000, top: 70 });
                }
            },
            error: function () {
                createToast({ title: 'Error', description: 'Failed to update device name.', timeout: 3000, top: 70 });
            }
        });
    };
</script>