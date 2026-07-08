<?php
if (!defined('Profess0rPay_INIT')) {
    http_response_code(403);
    exit('Direct access not allowed');
}

    if (!canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'invoice', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }

    if (!hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'invoice', 'edit', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }


    $params = json_decode($_POST['params'] ?? '{}', true);

    $i_id = getParam($params, 'i_id');

    if ($i_id === null) {
        http_response_code(403);
        exit('Invalid invoice id');
    }else{
        $i_id = escape_string($i_id);

        $response_invoice = json_decode(getData($db_prefix.'invoice','WHERE ref = "'.$i_id.'" AND brand_id = "'.$global_response_brand['response'][0]['brand_id'].'"'),true);
        if($response_invoice['status'] == true){

        }else{
            http_response_code(403);
            exit('Direct access not allowed');
        }
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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Edit Invoice</a></li>
                    </ol>
                </div>
                <h2 class="page-title">Edit Invoice</h2>
            </div>

            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <div class="btn btn-primary" onclick="copyContent('<?php echo $site_url.$path_invoice ?>/<?php echo $i_id;?>', 'Copied!', 'Invoice URL copied successfully.')"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-copy"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 9.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667l0 -8.666" /><path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" /></svg> Copy Link</div>
                    <button class="btn btn-danger btnDeleteItem-<?php echo $i_id;?> <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'invoice', 'delete', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" onclick="deleteItem('<?php echo $i_id;?>')"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg> Delete</button>
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
                <form class="form-invoice-edit">
                    <input type="hidden" name="action" value="invoice-edit">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">
                    <input type="hidden" name="invoiceID" value="<?php echo $response_invoice['response'][0]['ref']?>">
                    <input type="hidden" name="deleted_items" id="deleted_items" value="">

                    <div class="row g-3">
                        <div class="col-lg-8">
                            <div class="card p-2">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-lg-12">
                                            <div class="form-group customer-list">
                                                <label for="customers" class="form-label">Customer <span class="text-danger">*</span></label>
                                                <div class="d-flex">
                                                    <select class="js-select" data-search="false" data-remove="false" data-placeholder="Select customer" required>
                                                        <?php
                                                                $customer_info = json_decode($response_invoice['response'][0]['customer_info'], true);
                                                        ?>
                                                        <option value="<?php echo $customer_info['id']?>" selected><?php echo $customer_info['name']?> - <?php echo $customer_info['email']?></option>
                                                    </select>
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
                                                                    <option value="<?php echo $row['code'] ?>" <?php echo ($response_invoice['response'][0]['currency'] == $row['code']) ? 'selected' : '';?>><?php echo $row['code'] ?></option>
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
                                                    <input type="date" class="form-control" name="due_date" placeholder="dd/mm/yyyy" value="<?php echo ($response_invoice['response'][0]['due_date'] === '--') ? '' : $response_invoice['response'][0]['due_date'];?>" > 
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="email-address" class="form-label">Status <span
                                                        class="text-danger">*</span></label>
                                                <div class="form-control-wrap">
                                                    <select class="js-select" name="status" data-search="true" data-remove="true" data-placeholder="Select a status" required>
                                                        <option value="paid"     <?= ($response_invoice['response'][0]['status'] === 'paid') ? 'selected' : '' ?>>Paid</option>
                                                        <option value="unpaid"   <?= ($response_invoice['response'][0]['status'] === 'unpaid') ? 'selected' : '' ?>>Unpaid</option>
                                                        <option value="refunded" <?= ($response_invoice['response'][0]['status'] === 'refunded') ? 'selected' : '' ?>>Refunded</option>
                                                        <option value="canceled" <?= ($response_invoice['response'][0]['status'] === 'canceled') ? 'selected' : '' ?>>Canceled</option>
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
                                <?php
                                    $response = json_decode(getData($db_prefix.'invoice_items','WHERE brand_id ="'.$global_response_brand['response'][0]['brand_id'].'" AND invoice_id ="'.$i_id.'"'),true);
                                    foreach($response['response'] as $row){
                                ?>
                                        <div class="card mt-3 item-<?php echo $row['id']?>">
                                            <div class="card-header align-items-center justify-content-between">
                                                <h3 class="card-title">Item</h3>
                                                <svg class="remove-item text-danger" style=" cursor: pointer; " onclick="delete_item('<?php echo $row['id']?>')" xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                            </div>
                                            <div class="card-body p-4 pt-3">
                                                <div class="row g-3">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="form-label">Description <span class="text-danger">*</span></label>
                                                            <div class="form-control-wrap">
                                                                <input type="hidden"name="item-id" value="<?php echo $row['id']?>">
                                                                <input type="text" class="form-control" name="item-description" value="<?php echo $row['description']?>" aria-label="" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control" name="item-quantity" value="<?php echo money_round($row['quantity'])?>" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-white currency-code">USD</span>
                                                                <input type="text" class="form-control" name="item-amount" value="<?php echo money_round($row['amount'])?>" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label class="form-label">Discount <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-white currency-code">USD</span>
                                                                <input type="text" class="form-control" name="item-discount" value="<?php echo money_round($row['discount'])?>" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label class="form-label">Vat <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-white">%</span>
                                                                <input type="text" class="form-control" name="item-vat" value="<?php echo money_round($row['vat'])?>" required>
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
                                    <span>Add New Item</span>
                                </a>
                            </center>

                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Email Note</h3>
                                </div>
                                <div class="card-body p-4">
                                    <textarea class="hugerte-textArea" name="private-note-content"><?php echo ($response_invoice['response'][0]['private_note'] === '--') ? '' : $response_invoice['response'][0]['private_note'];?></textarea>
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
                                                <input type="text" class="form-control invoice-shipping" name="shipping" aria-label="Amount (to the nearest dollar)" value = "<?php echo money_round($response_invoice['response'][0]['shipping'])?>" required>
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
                                            <textarea name="note" class="form-control"><?php if($response_invoice['response'][0]['note'] !== "--"){ echo $response_invoice['response'][0]['note']; } ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-3">
                        <button class="btn btn-primary btn-invoicet-edit" type="submit">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script data-cfasync="false">
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

        window.calculateTotals = calculateTotals;

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
            if (e.target.classList.contains('ni-trash-empty')) {
                calculateTotals();
            }
        });

        calculateTotals();
    }

    function delete_item(divID) {
        var item = document.querySelector(".item-" + divID);
        var deletedInput = document.getElementById('deleted_items');

        if (item) {
            item.remove();
        }

        // Add ID to hidden input (comma-separated)
        if (deletedInput) {
            let current = deletedInput.value ? deletedInput.value.split(',') : [];

            if (!current.includes(String(divID))) {
                current.push(divID);
            }

            deletedInput.value = current.join(',');
        }

        calculateTotals();
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


    $('.form-invoice-edit').submit(function (e) {
        e.preventDefault();

        var btn = document.querySelector(".btn-invoicet-edit").innerHTML;
        document.querySelector(".btn-invoicet-edit").innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

        // Sync all editors
        document.querySelectorAll('.hugerte-textArea').forEach(el => {
            const editor = hugeRTE.get(el);
            if (editor) {
                // Get content and set it into textarea
                el.value = editor.getContent({ format: 'html' });
            }
        });

        setTimeout(() => {
            var formData = $(this).serialize();

            $.ajax({
                type: 'POST',
                url: '<?php echo $site_url.$path_admin ?>/dashboard',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    closeAllBootstrapModals();

                    document.querySelector(".btn-invoicet-edit").innerHTML = btn;

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
        }, 10);
    });

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
                data: {action: "invoice-delete", csrf_token: csrf_token_default, ItemID: ItemID},
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
            show_action_confirmation_tab(btnClass, 'Delete Invoice', 'Delete', 'btn-danger');
        }
    }
</script>