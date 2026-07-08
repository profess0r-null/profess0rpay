<?php
if (!defined('Profess0rPay_INIT')) {
    http_response_code(403);
    exit('Direct access not allowed');
}

    if (!canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'invoice', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }

    if (!hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'invoice', 'create', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }
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
                        <li class="breadcrumb-item"><a href="javascript:void(0)" onclick="load_content('Invoice','<?php echo $site_url.$path_admin ?>/invoice','nav-item-invoice')">Invoice</a></li>
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Create Invoice</a></li>
                    </ol>
                </div>
                <h2 class="page-title">Create Invoice</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row g-gs">
            <div class="col-12 col-xxl-12">
                <form class="form-invoice-create">
                    <input type="hidden" name="action" value="invoice-create">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">

                    <div class="row g-3">
                        <div class="col-lg-8">
                            <div class="card p-2">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-lg-12">
                                            <div class="form-group customer-list">
                                                <label for="customers" class="form-label">Customers <span class="text-danger">*</span></label>
                                                <div class="d-flex">
                                                    <select class="js-select customersList" name="customers[]" multiple data-search="true" data-remove="true" data-placeholder="Select customers" required>
                                                        <?php
                                                            $response_brand = json_decode(getData($db_prefix . 'customer', 'WHERE status = "active" AND brand_id ="'.$global_response_brand['response'][0]['brand_id'].'" ORDER BY 1 DESC'), true);
                                                            if ($response_brand['status'] == true) {
                                                                foreach ($response_brand['response'] as $row) {
                                                        ?>
                                                                    <option value="<?php echo $row['ref'] ?>"><?php echo $row['name'] ?> - <?php echo ($row['email'] !== '--' && $row['email'] !== '') ? $row['email'] : $row['mobile']; ?></option>
                                                        <?php
                                                                }
                                                            }
                                                        ?>
                                                    </select>

                                                    <div class="btn btn-icon bg-white btnCreateItem" data-bs-target="#modal-createItem" data-bs-toggle="modal" class="<?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'customers', 'create', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Currency <span class="text-danger">*</span></label>
                                                <div class="form-control-wrap">
                                                    <select class="js-select in-currency" name="currency" data-search="true" data-remove="true" data-placeholder="Select currency" required onchange="FNcurrency()">
                                                        <?php
                                                            $response_brand = json_decode(getData($db_prefix . 'currency', 'WHERE brand_id ="'.$global_response_brand['response'][0]['brand_id'].'" ORDER BY 1 DESC'), true);
                                                            if ($response_brand['status'] == true) {
                                                                foreach ($response_brand['response'] as $row) {
                                                        ?>
                                                                    <option value="<?php echo $row['code'] ?>" <?php echo ($global_brand_currency_code == $row['code']) ? 'selected' : '';?>><?php echo $row['code'] ?></option>
                                                        <?php
                                                                }
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="username" class="form-label">Due Date</label>
                                                <div class="form-control-wrap">
                                                    <input type="date" class="form-control" name="due_date" placeholder="dd/mm/yyyy"> 
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="email-address" class="form-label">Status <span
                                                        class="text-danger">*</span></label>
                                                <div class="form-control-wrap">
                                                    <select class="js-select" name="status" data-search="true" data-remove="true" data-placeholder="Select a status" required>
                                                        <option value="paid">Paid</option>
                                                        <option value="unpaid" selected>Unpaid</option>
                                                        <option value="refunded">Refunded</option>
                                                        <option value="canceled">Canceled</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--items--> 
                            <!--items--> 
                            <!--items--> 
                            <div class="item-list">

                            </div>
                            <!--items--> 
                            <!--items--> 
                            <!--items--> 

                            <center>
                                <a href="javascript:void(0)" class="btn bg-white text-dark mt-3 mb-3" onclick="add_new_item()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                                    <span>Add New Item</span>
                                </a>
                            </center>

                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Email Note</h3>
                                </div>
                                <div class="card-body p-4">
                                    <textarea class="hugerte-textArea" name="private-note-content"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Total</h3>
                                </div>
                                <div class="card-body p-4 pt-3">
                                    <div class="row g-3">
                                        <div class="col-lg-12">
                                            <label for="username" class="form-label">Shipping</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white currency-code"><?php echo $global_brand_currency_code?></span>
                                                <input type="text" class="form-control invoice-shipping" name="shipping" aria-label="Amount (to the nearest dollar)" value = "0" required>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <label for="username" class="form-label">Discount</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white currency-code"><?php echo $global_brand_currency_code?></span>
                                                <input type="text" class="form-control invoice-discount" name="discount" readonly aria-label="Amount (to the nearest dollar)" value = "0" required>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <label for="username" class="form-label">Vat</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white currency-code"><?php echo $global_brand_currency_code?></span>
                                                <input type="text" class="form-control invoice-vat" name="vat" readonly aria-label="Amount (to the nearest dollar)" value = "0" required>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <label for="username" class="form-label">Total</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white currency-code"><?php echo $global_brand_currency_code?></span>
                                                <input type="text" class="form-control invoice-total" name="total" readonly aria-label="Amount (to the nearest dollar)" value = "0" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <h3 class="card-title">Note</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-lg-12">
                                            <textarea name="note" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-3">
                        <button class="btn btn-primary btn-invoicet-create" type="submit">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<!--extra requirement-->
<!--extra requirement-->
<!--extra requirement-->
<div class="modal modal-blur fade" id="modal-createItem" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">New Customer</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row mb-3 g-3">
              <div class="col-lg-6">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="customer-name" placeholder="Customer name">
              </div>

              <div class="col-lg-6">
                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="customer-email" placeholder="Customer email address">
              </div>

              <div class="col-lg-6">
                <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="customer-mobile" placeholder="Customer mobile number">
              </div>
            </div>

            <input type="radio" name="customer-status" value="active" class="form-selectgroup-input" checked>
          </div>
          <div class="modal-footer">
            <a href="javascript:void(0)" class="btn btn-link link-secondary btn-3" data-bs-dismiss="modal"> Cancel </a>
            <a href="javascript:void(0)" class="btn btn-primary btn-5 ms-auto modal-createItem-btn">Create</a>
          </div>
        </div>
    </div>
</div>
<!--extra requirement-->
<!--extra requirement-->
<!--extra requirement-->

<script data-cfasync="false">
    $('.customers-create-form').submit(function (e) {
        e.preventDefault();

        var btn = document.querySelector(".customers-create-offcanvas-submit").innerHTML;
        document.querySelector(".customers-create-offcanvas-submit").innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            data: formData,
            dataType: 'json',
            success: function (response) {
                const formCreate = document.querySelector('.customers-create-form');

                const formCreatename   = formCreate.querySelector('input[name="name"]').value.trim();
                const formCreateemail  = formCreate.querySelector('input[name="email"]').value.trim();
                const formCreatemobile = formCreate.querySelector('input[name="mobile"]').value.trim();

                document.querySelector(".customers-create-form").reset();
                
                document.querySelectorAll('.offcanvas.show') .forEach(el => bootstrap.Offcanvas.getInstance(el)?.hide());

                document.querySelector(".customers-create-offcanvas-submit").innerHTML = btn;

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

    function FNcurrency(){
        var currency_main = document.querySelector(".in-currency").value;

        document.querySelectorAll('.currency-code').forEach(el => {
            el.innerHTML = currency_main; 
        });
    }
    FNcurrency();

    function FNcalculate(){
        const itemList = document.querySelector('.item-list');
        const shippingInput = document.querySelector('.invoice-shipping');
        const discountInput = document.querySelector('.invoice-discount');
        const vatInput = document.querySelector('.invoice-vat');
        const totalInput = document.querySelector('.invoice-total');

        function calculateTotals() {
            let subtotal = 0;
            let totalDiscount = 0;
            let totalVat = 0;

            // Loop through all item cards
            const itemCards = itemList.querySelectorAll('.card');
            itemCards.forEach(card => {
                const qty = parseFloat(card.querySelector('input[name="item-quantity"]').value) || 0;
                const amount = parseFloat(card.querySelector('input[name="item-amount"]').value) || 0;
                const discount = parseFloat(card.querySelector('input[name="item-discount"]').value) || 0;
                const vat = parseFloat(card.querySelector('input[name="item-vat"]').value) || 0;

                const itemTotal = qty * amount;
                subtotal += itemTotal;
                totalDiscount += discount;
                totalVat += (itemTotal - discount) * (vat / 100);
            });

            const shipping = parseFloat(shippingInput.value) || 0;

            const total = subtotal - totalDiscount + totalVat + shipping;

            discountInput.value = totalDiscount.toFixed(2);
            vatInput.value = totalVat.toFixed(2);
            totalInput.value = total.toFixed(2);
        }

        // Event delegation: listen for changes inside item-list
        itemList.addEventListener('input', function(e) {
            if (['item-quantity', 'item-amount', 'item-discount', 'item-vat'].some(name => e.target.name === name)) {
                calculateTotals();
            }
        });

        // Listen for shipping changes
        shippingInput.addEventListener('input', calculateTotals);

        // Optional: recalc if items are removed
        itemList.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-item')) {
                calculateTotals();
            }
        });
    }

    function add_new_item(){
        const itemList = document.querySelector('.item-list');

        const itemCard = document.createElement('div');
        itemCard.classList.add('card', 'mt-3');
        itemCard.innerHTML = `
            <div class="card-header align-items-center justify-content-between">
                <h3 class="card-title">Item</h3>
                <svg class="remove-item text-danger" style=" cursor: pointer; " xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
            </div>
            <div class="card-body p-4 pt-3">
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" name="item-description" aria-label="" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-label">Quantity <span class="text-danger">*</span></label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" name="item-quantity" value="1" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white currency-code">USD</span>
                                <input type="text" class="form-control" name="item-amount" value="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-label">Discount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white currency-code">USD</span>
                                <input type="text" class="form-control" name="item-discount" value="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <label class="form-label">Vat <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">%</span>
                                <input type="text" class="form-control" name="item-vat" value="0" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Append item card to the list
        itemList.appendChild(itemCard);

        FNcurrency();
        FNcalculate();

        // Add event listener to remove icon
        const removeBtn = itemCard.querySelector('.remove-item');
        removeBtn.addEventListener('click', function() {
            itemCard.remove();
            FNcalculate();
        });
    }
    FNcalculate();


    $('.form-invoice-create').submit(function (e) {
        e.preventDefault();

        var btn = document.querySelector(".btn-invoicet-create").innerHTML;
        document.querySelector(".btn-invoicet-create").innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            data: formData,
            dataType: 'json',
            success: function (response) {
                closeAllBootstrapModals();

                document.querySelector(".btn-invoicet-create").innerHTML = btn;

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
    });


    $('.modal-createItem-btn').click(function () {
        var csrf_token_default = $('input[name="csrf_token_default"]').val();

        // Get modal element
        const modal = document.getElementById("modal-createItem");

        // Set input values by name
        var customer_name = modal.querySelector('input[name="customer-name"]').value;
        var customer_email = modal.querySelector('input[name="customer-email"]').value;
        var customer_mobile = modal.querySelector('input[name="customer-mobile"]').value;

        var statusInput = modal.querySelector('input[name="customer-status"]:checked');
        var customer_status = statusInput ? statusInput.value : ""; // default empty if none selected

        // Textarea (suspend reason)
        var suspend_reason = '';

        if(customer_name == "" || customer_email == "" || customer_mobile == "" || customer_status == ""){
            createToast({
                title: 'Incomplete Information',
                description: 'Please fill in all required fields before proceeding.',
                svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                timeout: 6000,
                top: 70
            });
        }else{
            var btnClass = 'modal-createItem-btn';

            var btn = document.querySelector('.'+btnClass).innerHTML;

            document.querySelector('.'+btnClass).innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

            // Sync all editors
            document.querySelectorAll('.hugerte-textArea').forEach(el => {
                const editor = hugeRTE.get(el);
                if (editor) {
                    // Get content and set it into textarea
                    el.value = editor.getContent({ format: 'html' });
                }
            });

            setTimeout(() => {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo $site_url.$path_admin ?>/dashboard',
                    data: {action: "customers-create", csrf_token: csrf_token_default, name: customer_name, email: customer_email, mobile: customer_mobile, status: customer_status, suspend_reason: suspend_reason},
                    dataType: 'json',
                    success: function (response) {
                        closeAllBootstrapModals();

                        // Get modal element
                        const modal = document.getElementById("modal-createItem");

                        // Reset text inputs
                        modal.querySelectorAll('input[type="text"]').forEach(input => input.value = '');

                        // Reset radios to first option
                        const radios = modal.querySelectorAll('input[name="customer-status"]');
                        if (radios.length > 0) {
                            radios.forEach((r, i) => r.checked = (i === 0)); // check first radio, uncheck others
                        }

                        // Reset textarea
                        const textarea = modal.querySelector('textarea');
                        if (textarea) textarea.value = '';

                        // Hide suspend reason div
                        const suspendDiv = modal.querySelector('#suspend-reason');
                        if (suspendDiv) suspendDiv.classList.add('d-none');

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

                            window.InvoiceCustomerChoices.setChoices([
                                {
                                    value: customer_email,
                                    label: customer_name+' - '+customer_email,
                                    selected: true
                                }
                            ], 'value', 'label', false);
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
            }, 10);
        }
    });
</script>