<?php
    if (!defined('Profess0rPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
    }

    if (!canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'transaction', $global_user_response['response'][0]['role'])) {
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
                <div class="page-pretitle">Transactions</div>
                <h2 class="page-title">Transactions</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Premium Analytics / Summary Cards -->
        <div class="row row-cards mb-3" id="transaction-summary-cards">
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-success text-white avatar" style="border-radius: 8px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" /><path d="M12 3v3m0 12v3" /></svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium" style="font-size: 15px; color: #475569;">Total Revenue</div>
                                <div class="text-dark fw-bold" style="font-size: 20px;" id="summary-revenue">৳ 0.00</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-primary text-white avatar" style="border-radius: 8px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium" style="font-size: 15px; color: #475569;">Successful</div>
                                <div class="text-dark fw-bold" style="font-size: 20px;" id="summary-successful">0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-warning text-white avatar" style="border-radius: 8px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 8v4l3 3" /><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /></svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium" style="font-size: 15px; color: #475569;">Pending</div>
                                <div class="text-dark fw-bold" style="font-size: 20px;" id="summary-pending">0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-danger text-white avatar" style="border-radius: 8px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium" style="font-size: 15px; color: #475569;">Failed / Canceled</div>
                                <div class="text-dark fw-bold" style="font-size: 20px;" id="summary-failed">0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row-deck row-cards">
            <div class="col-12 mb-2 d-flex justify-content-center">
                <div>
                    <div class="card p-2">
                        <ul class="nav nav-pills gap-2" role="tablist" id="statusTabs" style="font-weight: 500; font-size: .875rem;">
                            <li class="nav-item">
                                <button class="nav-link active" data-type="all">
                                    All
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-type="completed">
                                    Completed
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-type="pending">
                                    Pending
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-type="refunded">
                                    Refunded
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-type="canceled">
                                    Canceled
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="w-100" style="border-bottom: 1px solid #e8e7ec;">
                        <div style="display: flex; flex-direction: row-reverse; height: 53px; align-items: center; padding-right: 20px; font-size: 22px;">
                           <svg data-bs-toggle="offcanvas" href="#filterOffcanvas" role="button" aria-controls="filterOffcanvas" style="cursor: pointer; color: #475569;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-filter"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v7l-6 2v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227z" /></svg>
                        </div>
                    </div>

                    <!-- Offcanvas Filter -->
                    <div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
                      <div class="offcanvas-header">
                        <h2 class="offcanvas-title" id="filterOffcanvasLabel">Filter Transactions</h2>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                      </div>
                      <div class="offcanvas-body filter-tab-data">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="filter-status">
                                <option value="">All Statuses</option>
                                <option value="completed">Completed</option>
                                <option value="pending">Pending</option>
                                <option value="refunded">Refunded</option>
                                <option value="canceled">Canceled</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Created From</label>
                            <input type="date" class="form-control" id="filter-created-from">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Created Until</label>
                            <input type="date" class="form-control" id="filter-created-until">
                        </div>
                        <div class="mt-4">
                            <button class="btn btn-primary w-100 mb-2" data-bs-dismiss="offcanvas" onclick="load_data_list(1)">Apply Filters</button>
                            <button class="btn btn-light w-100" onclick="filter_hide_show_reset('filter-tab-data'); load_data_list(1);" data-bs-dismiss="offcanvas">Reset</button>
                        </div>
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

                                <button class="btn btn-danger bulk-action d-none" data-bs-toggle="modal" data-bs-target="#model-bulkAction"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg> <span id="bulkActionBTN-count">(0)</span></button>
                            </div>
                        </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-selectable card-table table-vcenter text-nowrap datatable">
                      <thead>
                        <tr>
                            <th class="w-1"><input class="form-check-input m-0 align-middle select-all" type="checkbox" aria-label="Select all invoices"></th>
                            <th>Customer</th>
                            <th>Gateway</th>
                            <th>Amount</th>
                            <th>Net Amount</th>
                            <th>Transaction ID</th>
                            <th>Date</th>
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
                            <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'transaction', 'delete', $global_user_response['response'][0]['role']) ? '<option value="deleted">Delete Selected</option>' : '' ?>
                            <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'transaction', 'approve', $global_user_response['response'][0]['role']) ? '<option value="approved">Approve Selected</option>' : '' ?>
                            <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'transaction', 'cancel', $global_user_response['response'][0]['role']) ? '<option value="canceled">Cancel Selected</option>' : '' ?>
                            <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'transaction', 'refund', $global_user_response['response'][0]['role']) ? '<option value="refunded">Refund Selected</option>' : '' ?>
                            <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'transaction', 'send_ipn', $global_user_response['response'][0]['role']) ? '<option value="ipnsend">Trigger IPN for Selected</option>' : '' ?>
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

<!-- Premium Modal Styling -->
<style>
#quickViewModal .modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    overflow: hidden;
}
#quickViewModal .modal-header {
    background-color: #0054a6; /* Deep blue header like screenshot */
    color: #ffffff;
    border-bottom: none;
    padding: 1rem 1.5rem;
}
#quickViewModal .modal-title {
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}
#quickViewModal .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
    opacity: 0.8;
}
#quickViewModal .btn-close:hover {
    opacity: 1;
}
#quickViewBody {
    background-color: #f1f5f9;
    padding: 1.5rem;
}
</style>

<!-- Quick View Modal -->
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="quickViewModalLabel">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-invoice"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 7l1 0" /><path d="M9 13l6 0" /><path d="M13 17l2 0" /></svg>
            Transaction Details
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="quickViewBody">
        <div class="text-center my-5">
            <div class="spinner-border text-primary" role="status"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<script data-cfasync="false">
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

            var loaderSpinner = document.querySelector('#model-my-action-confirmation-btn').innerHTML;

            if(my_action_confirmation_btn !== ""){
                document.querySelector('#model-my-action-confirmation-btn').innerHTML = '<div class="spinner-border spinner-border-sm text-white" role="status"><span class="visually-hidden">Loading...</span></div>';

                $.ajax({
                    type: 'POST',
                    url: '<?php echo $site_url.$path_admin ?>/dashboard',
                    data: {action: "transaction-bulk-action", csrf_token: csrf_token_default, actionID: actionID, selected_ids: JSON.stringify(selectedRows)},
                    dataType: 'json',
                    success: function (response) {
                        closeAllBootstrapModals();
                        document.querySelector("#my-action-confirmation-btn").value = '';
                        document.getElementById("model-bulkActionID").selectedIndex = 0;
                        document.querySelector('#model-my-action-confirmation-btn').innerHTML = loaderSpinner;

                        if (response && response.status === 'true') {
                            window.location.reload();
                        } else {
                            window.location.reload();
                        }
                    },
                    error: function (xhr, status, error) {
                        window.location.reload();
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
                data: {action: "transaction-delete", csrf_token: csrf_token_default, ItemID: ItemID},
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
            show_action_confirmation_tab(btnClass, 'Delete Transaction', 'Delete', 'btn-danger');
        }
    }

    function quickActionItem(ItemID, actionID) {
        var csrf_token_default = $('input[name="csrf_token_default"]').val();
        var btnClass = 'btnQuickAction-'+ItemID+'-'+actionID;
        var my_action_confirmation_btn = document.querySelector("#my-action-confirmation-btn").value;

        if(my_action_confirmation_btn !== '') {
            var btn = document.querySelector('#model-my-action-confirmation-btn').innerHTML;
            document.querySelector('#model-my-action-confirmation-btn').innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

            $.ajax({
                type: 'POST',
                url: '<?php echo $site_url.$path_admin ?>/dashboard',
                data: {action: "transaction-bulk-action", csrf_token: csrf_token_default, actionID: actionID, selected_ids: JSON.stringify([ItemID])},
                dataType: 'json',
                success: function (response) {
                    closeAllBootstrapModals();
                    document.querySelector("#my-action-confirmation-btn").value = '';
                    document.querySelector('#model-my-action-confirmation-btn').innerHTML = btn;

                    if (response && response.status === 'true') {
                        window.location.reload();
                    } else {
                        window.location.reload();
                    }
                },
                error: function(xhr) {
                    window.location.reload();
                }
            });
        } else {
            var label = actionID === 'approved' ? 'Approve' : (actionID === 'canceled' ? 'Reject' : 'Delete');
            var color = actionID === 'approved' ? 'btn-success' : (actionID === 'canceled' ? 'btn-warning' : 'btn-danger');
            show_action_confirmation_tab(btnClass, label + ' Transaction', label, color);
        }
    }

    function load_data_list(page = 1){
        currentPage = page;

        var csrf_token_default = $('input[name="csrf_token_default"]').val();
        var search_input = $('.search_input').val();
        var show_limit = $('.show_limit').val();

        var tabType = document.querySelector('#statusTabs .nav-link.active')?.dataset.type;

        var filter_status = $('#filter-status').val();
        var filter_start = $('#filter-created-from').val();
        var filter_end = $('#filter-created-until').val();

        let html = '';

        $(".table-data-list").html('<tr><td colspan="5" class="text-center text-muted"><div class="spinner-border text-primary" style="margin: 50px;">  <span class="visually-hidden">Loading...</span></div></td></tr>');

        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            data: {action: "transaction-list", csrf_token: csrf_token_default, search_input: search_input, show_limit: show_limit, tabType: tabType, page: page, filter_status: filter_status, filter_start: filter_start, filter_end: filter_end},
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
                    if (res.summary) {
                        document.getElementById('summary-revenue').innerText = res.summary.revenue;
                        document.getElementById('summary-successful').innerText = res.summary.successful;
                        document.getElementById('summary-pending').innerText = res.summary.pending;
                        document.getElementById('summary-failed').innerText = res.summary.failed;
                    }

                    res.response.forEach(item => {
                        let badge = 'secondary';

                        let allowEdit = <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'transaction', 'edit', $global_user_response['response'][0]['role']) ? 'true' : 'false' ?>;
                        let allowDelete = <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'transaction', 'delete', $global_user_response['response'][0]['role']) ? 'true' : 'false' ?>;

                        let redirectEdit = '';
                        let redirectDelete = '';
                        
                        if (allowEdit) {
                            redirectEdit = `style="cursor:pointer;" onclick="openQuickView('${item.id}')"`;
                        }

                        if (allowDelete) {
                            redirectDelete = `onclick="deleteItem('${item.id}')"`;
                        }

                        let initials = item.name.substring(0, 2).toUpperCase();
                        let badgeBg = 'bg-secondary';
                        let badgeText = 'text-white';
                        
                        if (item.status === 'completed') { badgeBg = 'bg-success-lt'; badgeText = 'text-success'; }
                        if (item.status === 'pending') { badgeBg = 'bg-warning-lt'; badgeText = 'text-warning'; }
                        if (item.status === 'refunded') { badgeBg = 'bg-indigo-lt'; badgeText = 'text-indigo'; }
                        if (item.status === 'canceled') { badgeBg = 'bg-danger-lt'; badgeText = 'text-danger'; }

                        let allowApprove = <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'transaction', 'approve', $global_user_response['response'][0]['role']) ? 'true' : 'false' ?>;

                        html += `
                            <tr data-id="${item.id}" style="transition: all 0.2s ease;">
                                <td><input class="form-check-input m-0 align-middle table-selectable-check rowCheckbox" type="checkbox" aria-label="Select invoice"></td>
                                <td ${redirectEdit}>
                                    <div class="d-flex py-1 align-items-center">
                                        <span class="avatar me-3 rounded-circle" style="background-color: #f1f5f9; color: #475569; font-size: 13px; font-weight: 600; width: 36px; height: 36px;">${initials}</span>
                                        <div class="flex-fill">
                                            <div class="font-weight-bold" style="color: #1e293b; font-size: 14px;">${item.name}</div>
                                            <div class="text-secondary" style="font-size: 12px; margin-top: 2px;">${item.email && item.email.trim() !== '' ? item.email : item.mobile}</div>
                                        </div>
                                    </div>
                                </td>
                                <td ${redirectEdit}>
                                    <span class="text-dark fw-medium">${item.gateway}</span>
                                    ${item.is_temporary ? '<br><span class="badge bg-purple-lt text-purple mt-1" style="font-size: 10px; padding: 2px 6px;">Temp Link</span>' : ''}
                                </td>
                                <td ${redirectEdit} class="text-dark fw-bold">${item.amount}</td>
                                <td ${redirectEdit} class="text-success fw-bold">${item.net_amount}</td>
                                <td ${redirectEdit}>
                                    <span style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 2px 6px; font-family: monospace; font-size: 12px; color: #475569;">${item.trx_id}</span>
                                </td>
                                <td ${redirectEdit} class="text-secondary" style="font-size: 13px;">${item.created_date}</td>
                                <td ${redirectEdit}><span class="badge ${badgeBg} ${badgeText}" style="padding: 4px 8px; border-radius: 6px;">${item.status.charAt(0).toUpperCase() + item.status.slice(1)}</span></td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        ${item.status === 'pending' && allowApprove ? `
                                        <button class="btn btn-sm btn-success btnQuickAction-${item.id}-approved" title="Approve" onclick="quickActionItem('${item.id}', 'approved')" style="padding:4px 8px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                        </button>
                                        <button class="btn btn-sm btn-warning btnQuickAction-${item.id}-canceled" title="Reject" onclick="quickActionItem('${item.id}', 'canceled')" style="padding:4px 8px; color:#fff;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                                        </button>
                                        ` : ''}
                                        <span class="dropdown" style="position: unset;">
                                            <button class="btn btn-sm dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">•••</button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item ${allowEdit ? '' : 'd-none'}" href="javascript:void(0)" ${redirectEdit}> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415" /><path d="M16 5l3 3" /></svg> Edit </a>
                                                <a class="dropdown-item btnDeleteItem-${item.id} ${allowDelete ? '' : 'd-none'}" href="javascript:void(0)" ${redirectDelete}> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg> Delete </a>
                                            </div>
                                        </span>
                                    </div>
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

    function openQuickView(id) {
        $('#quickViewModal').modal('show');
        
        document.getElementById('quickViewBody').innerHTML = '<div class="text-center my-5"><div class="spinner-border text-primary" role="status"></div></div>';
        
        var csrf_token_default = $('input[name="csrf_token_default"]').val();
        
        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            data: {action: "transaction-quick-view", csrf_token: csrf_token_default, t_id: id},
            dataType: 'json',
            success: function (res) {
                if (res.status === 'true') {
                    document.getElementById('quickViewBody').innerHTML = res.html;
                } else {
                    document.getElementById('quickViewBody').innerHTML = '<div class="p-4 text-center text-danger">' + res.message + '</div>';
                }
                
                if (res.csrf_token) {
                    $('input[name="csrf_token_default"]').val(res.csrf_token);
                }
            },
            error: function () {
                document.getElementById('quickViewBody').innerHTML = '<div class="p-4 text-center text-danger">Failed to load details.</div>';
            }
        });
    }

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

    document.querySelectorAll('#statusTabs .nav-link').forEach(btn => {
        btn.addEventListener('click', function () {

            document.querySelectorAll('#statusTabs .nav-link').forEach(b => b.classList.remove('active'));

            this.classList.add('active');

            const type = this.dataset.type;

            load_data_list(1);
        });
    });

    function pp_copy(text, msg = 'Copied!', el = null) {
        let origHtml = '';
        if(el) {
            origHtml = el.innerHTML;
            el.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>';
            setTimeout(() => { el.innerHTML = origHtml; }, 1500);
        }

        if (!text) {
            if (typeof createToast === "function") {
                createToast({
                    title: "Error",
                    description: "No content to copy",
                    svg: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>',
                    timeout: 1500,
                    top: 20
                });
            } else {
                alert("No content to copy");
            }
            return;
        }

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(() => {
                if (typeof createToast === "function") {
                    createToast({ title: "Success", description: msg, type: "success" });
                } else {
                    alert(msg);
                }
            }).catch(err => {
                console.error("Failed to copy!", err);
            });
        } else {
            let textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.left = "-999999px";
            textArea.style.top = "-999999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                if (typeof createToast === "function") {
                    createToast({ title: "Success", description: msg, type: "success" });
                } else {
                    alert(msg);
                }
            } catch (err) {
                console.error('Fallback: Oops, unable to copy', err);
            }
            document.body.removeChild(textArea);
        }
    }

    function ipnItem(ItemID) {
        var csrf_token_default = document.querySelector('input[name="csrf_token_default"]').value;
        var btnClass = '.btnIpnItem-' + ItemID;
        var btn = document.querySelector(btnClass);
        if (btn) {
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
            btn.disabled = true;
        }

        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            data: {action: "transaction-ipn", csrf_token: csrf_token_default, ItemID: ItemID},
            dataType: 'json',
            success: function (response) {
                if (response.status === "true" || response.status === true) {
                    if (typeof createToast === "function") {
                        createToast({ title: 'Success', description: response.message, timeout: 6000, top: 70 });
                    }
                } else {
                    if (typeof createToast === "function") {
                        createToast({ title: response.title || 'Error', description: response.message || 'Something went wrong', timeout: 6000, top: 70 });
                    }
                }
                if (btn) {
                    btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-webhook" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4.876 13.61a4 4 0 1 0 6.124 3.39h6" /><path d="M15.066 20.502a4 4 0 1 0 1.934 -7.502c-.706 0 -1.424 .179 -2 .5l-3 -5.5" /><path d="M16 8a4 4 0 1 0 -8 0c0 1.506 .77 2.818 2 3.5l-3 5.5" /></svg> Trigger Webhook';
                    btn.disabled = false;
                }
            },
            error: function () {
                if (typeof createToast === "function") {
                    createToast({ title: 'Error', description: 'Network error or server unavailable.', timeout: 6000, top: 70 });
                }
                if (btn) {
                    btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-webhook" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4.876 13.61a4 4 0 1 0 6.124 3.39h6" /><path d="M15.066 20.502a4 4 0 1 0 1.934 -7.502c-.706 0 -1.424 .179 -2 .5l-3 -5.5" /><path d="M16 8a4 4 0 1 0 -8 0c0 1.506 .77 2.818 2 3.5l-3 5.5" /></svg> Trigger Webhook';
                    btn.disabled = false;
                }
            }
        });
    }
</script>