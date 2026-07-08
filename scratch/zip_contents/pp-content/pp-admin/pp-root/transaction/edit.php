<?php
if (!defined('Profess0rPay_INIT')) {
    http_response_code(403);
    exit('Direct access not allowed');
}

    if (!canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'transaction', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }

    if (!hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'transaction', 'edit', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }

    $params = json_decode($_POST['params'] ?? '{}', true);

    $ref = getParam($params, 't_id');

    if ($ref === null) {
        http_response_code(403);
        exit('Invalid transaction id');
    }else{
        $ref = escape_string($ref);

        $response_transaction = json_decode(getData($db_prefix.'transaction','WHERE ref = "'.$ref.'" AND brand_id = "'.$global_response_brand['response'][0]['brand_id'].'" AND status NOT IN ("initiated")'),true);
        if($response_transaction['status'] == true){
            $response_gateway = json_decode(getData($db_prefix.'gateways',' WHERE brand_id ="'.$global_response_brand['response'][0]['brand_id'].'" AND gateway_id = "'.$response_transaction['response'][0]['gateway_id'].'"'),true);

            $gateway_name = $response_gateway['response'][0]['name'] ?? 'Unknow';

            $customer_info = json_decode($response_transaction['response'][0]['customer_info'], true) ?: [];
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
                        <li class="breadcrumb-item"><a href="javascript:void(0)" onclick="load_content('Transaction','<?php echo $site_url.$path_admin ?>/transaction','nav-item-transaction')">Transaction</a></li>
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">View Transaction</a></li>
                    </ol>
                </div>
                <h2 class="page-title">View Transaction</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <div data-bs-toggle="modal" data-bs-target="#model-bulkAction" class="btn btn-primary <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'transaction', 'edit', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415" /><path d="M16 5l3 3" /></svg> Edit</div>
                    <button class="btn btn-success btnIpnItem-<?php echo $ref;?> <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'transaction', 'send_ipn', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" onclick="ipnItem('<?php echo $ref;?>')"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-sitemap"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M2 16.667a2.667 2.667 0 0 1 2.667 -2.667h2.666a2.667 2.667 0 0 1 2.667 2.667v2.666a2.667 2.667 0 0 1 -2.667 2.667h-2.666a2.667 2.667 0 0 1 -2.667 -2.667z" /><path d="M14 16.667a2.667 2.667 0 0 1 2.667 -2.667h2.666a2.667 2.667 0 0 1 2.667 2.667v2.666a2.667 2.667 0 0 1 -2.667 2.667h-2.666a2.667 2.667 0 0 1 -2.667 -2.667z" /><path d="M8 4.667a2.667 2.667 0 0 1 2.667 -2.667h2.666a2.667 2.667 0 0 1 2.667 2.667v2.666a2.667 2.667 0 0 1 -2.667 2.667h-2.666a2.667 2.667 0 0 1 -2.667 -2.667z" /><path d="M12 8a1 1 0 0 0 -1 1v2h-3c-1.645 0 -3 1.355 -3 3v1a1 1 0 0 0 1 1a1 1 0 0 0 1 -1v-1c0 -.564 .436 -1 1 -1h8c.564 0 1 .436 1 1v1a1 1 0 0 0 1 1a1 1 0 0 0 1 -1v-1c0 -1.645 -1.355 -3 -3 -3h-3v-2a1 1 0 0 0 -1 -1z" /></svg> Send IPN</button>
                    <button class="btn btn-danger btnDeleteItem-<?php echo $ref;?> <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'transaction', 'delete', $global_user_response['response'][0]['role']) ? '' : 'd-none' ?>" onclick="deleteItem('<?php echo $ref;?>')"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg> Delete</button>
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
                <div class="row g-3">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Transaction Status</h3>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-lg-4">
                                        <label for="username" class="form-label">Payment ID</label>
                                        <p class="m-0 text-dark form-label"><?php echo $response_transaction['response'][0]['ref']?></p>
                                    </div>

                                    <div class="col-lg-4">
                                        <label for="username" class="form-label">Date</label>
                                        <p class="m-0 text-dark form-label"><?php echo convertUTCtoUserTZ($response_transaction['response'][0]['created_date'], ($global_response_brand['response'][0]['timezone'] === '--' || $global_response_brand['response'][0]['timezone'] === '') ? 'Asia/Dhaka' : $global_response_brand['response'][0]['timezone'], "M d, Y h:i A")?></p>
                                    </div>

                                    <div class="col-lg-4">
                                        <label for="username" class="form-label">Status</label>
                                        <?php
                                            // Example status (this could come from your database)
                                            $status = $response_transaction['response'][0]['status']; // e.g., 'completed', 'pending', 'refunded', 'canceled'

                                            // Determine badge class and text
                                            $badgeClass = '';
                                            $badgeText = ucfirst($status); // Capitalize first letter for display

                                            switch ($status) {
                                                case 'completed':
                                                    $badgeClass = 'primary';
                                                    break;
                                                case 'pending':
                                                case 'refunded':
                                                    $badgeClass = 'warning';
                                                    break;
                                                case 'canceled':
                                                    $badgeClass = 'danger';
                                                    break;
                                                default:
                                                    $badgeClass = 'secondary'; // fallback
                                                    break;
                                            }
                                        ?>

                                        <p class="m-0"><span class="badge bg-<?php echo $badgeClass; ?> text-<?php echo $badgeClass; ?>-fg"></span> <?php echo $badgeText; ?></p>
                                    </div>
                                </div>

                            </div>
                        </div>


                        <div class="card mt-3">
                            <div class="card-header">
                                <ul class="nav nav-tabs nav-tabs-s1" style="border-bottom: transparent;">
                                    <li class="nav-item"> 
                                        <button style=" border-radius: 5px; " class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-transaction-details" type="button"><svg style="margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-receipt"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2m4 -14h6m-6 4h6m-2 4h2"></path></svg> Transaction Details</button> 
                                    </li>
                                    <li class="nav-item"> 
                                        <button style=" border-radius: 5px; " class="nav-link " data-bs-toggle="tab" data-bs-target="#tab-customer" type="button"><svg style="margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg> Customer</button> 
                                    </li>
                                    <li class="nav-item btn-tab-more"> 
                                        <button style=" border-radius: 5px; " class="nav-link " data-bs-toggle="tab" data-bs-target="#tab-more" type="button"><svg style="margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-info-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 9h.01" /><path d="M11 12h1v4h1" /></svg> More Info</button> 
                                    </li>
                                    <li class="nav-item btn-tab-metadata"> 
                                        <button style=" border-radius: 5px; " class="nav-link " data-bs-toggle="tab" data-bs-target="#tab-metadata" type="button"><svg style="margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-database"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 12.75a4 1.75 0 1 0 8 0a4 1.75 0 1 0 -8 0" /><path d="M8 12.5v3.75c0 .966 1.79 1.75 4 1.75s4 -.784 4 -1.75v-3.75" /><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2" /></svg> Metadata</button> 
                                    </li>
                                    <li class="nav-item btn-tab-endpoint"> 
                                        <button style=" border-radius: 5px; " class="nav-link " data-bs-toggle="tab" data-bs-target="#tab-endpoint" type="button"><svg style="margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-transform-point-bottom-left"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 4a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1l0 -2"></path><path d="M3 18a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1l0 -2" fill="currentColor"></path><path d="M17 4a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1l0 -2"></path><path d="M17 18a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1l0 -2"></path><path d="M11 5h2"></path><path d="M5 11v2"></path><path d="M19 11v2"></path><path d="M11 19h2"></path></svg> Endpoint</button> 
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="tab-transaction-details">
                                        <div class="border rounded p-4">
                                            <div class="form-label mb-2" style="border-bottom: 1px solid #e5e7eb;margin-left: -1.5rem !important;margin-right: -1.5rem !important;padding-left: 1.5rem !important;padding-right: 1.5rem !important;padding-bottom: 1rem !important;font-weight: 600;margin-top: -3px;margin-bottom: 15px !important;display: flex;justify-content: space-between;">
                                                Gateway Information
                                            </div>
                                            <div class="row g-3" style=" margin-bottom: -1rem; ">
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Gateway</label>

                                                    <p class="m-0 text-dark form-label"><?php echo $gateway_name?></p>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Currency</label>

                                                    <p class="m-0 text-dark form-label"><?php echo $response_transaction['response'][0]['local_currency']?></p>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Sender</label>

                                                    <p class="m-0 text-dark form-label"><?php echo $response_transaction['response'][0]['sender']?></p>
                                                </div>

                                                <?php
                                                    if($response_transaction['response'][0]['trx_slip'] !== '--'){
                                                ?>
                                                        <div class="col-md-4 mb-2">
                                                            <label class="form-label">Payment Slip</label>

                                                            <p class="m-0 text-dark form-label"><a href="<?php echo $response_transaction['response'][0]['trx_slip']?>" target="blank">View</a></p>
                                                        </div>
                                                <?php
                                                    }else{
                                                ?>
                                                        <div class="col-md-4 mb-2">
                                                            <label class="form-label">Transaction Id</label>

                                                            <p class="m-0 text-dark form-label"><?php echo $response_transaction['response'][0]['trx_id']?></p>
                                                        </div>
                                                <?php
                                                    }
                                                ?>
                                            </div>
                                        </div>

                                        <div class="border rounded p-4 mt-4">
                                            <div class="form-label mb-2" style="border-bottom: 1px solid #e5e7eb;margin-left: -1.5rem !important;margin-right: -1.5rem !important;padding-left: 1.5rem !important;padding-right: 1.5rem !important;padding-bottom: 1rem !important;font-weight: 600;margin-top: -3px;margin-bottom: 15px !important;display: flex;justify-content: space-between;">
                                                Transaction Information
                                            </div>
                                            <div class="row g-3" style=" margin-bottom: -1rem; ">
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Currency</label>

                                                    <p class="m-0 text-dark form-label"><?php echo $response_transaction['response'][0]['currency']?></p>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Amount</label>

                                                    <p class="m-0 text-dark form-label"><?php echo $response_transaction['response'][0]['currency'].' '.money_round($response_transaction['response'][0]['amount'], 2)?></p>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Processing Fee</label>

                                                    <p class="m-0 text-dark form-label"><?php echo $response_transaction['response'][0]['currency'].' '.money_round($response_transaction['response'][0]['processing_fee'], 2)?></p>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Discount amount</label>

                                                    <p class="m-0 text-dark form-label"><?php echo $response_transaction['response'][0]['currency'].' '.money_round($response_transaction['response'][0]['discount_amount'], 2)?></p>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Net Amount</label>

                                                    <p class="m-0 text-dark form-label"><?php echo $response_transaction['response'][0]['currency'].' '.money_round($response_transaction['response'][0]['amount']+$response_transaction['response'][0]['processing_fee']-$response_transaction['response'][0]['discount_amount'], 2)?></p>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Net Local Amount</label>

                                                    <p class="m-0 text-dark form-label"><?php echo $response_transaction['response'][0]['local_currency'].' '.money_round($response_transaction['response'][0]['local_net_amount'], 2)?></p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="tab-pane fade " id="tab-customer">
                                        <div class="border rounded p-4">
                                            <div class="form-label mb-2" style="border-bottom: 1px solid #e5e7eb;margin-left: -1.5rem !important;margin-right: -1.5rem !important;padding-left: 1.5rem !important;padding-right: 1.5rem !important;padding-bottom: 1rem !important;font-weight: 600;margin-top: -3px;margin-bottom: 15px !important;display: flex;justify-content: space-between;">
                                                Customer Information
                                            </div>
                                            <div class="row g-3" style=" margin-bottom: -1rem; ">
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Full Name</label>

                                                    <p class="m-0 text-dark form-label"><?php echo $customer_info['name'] ?? '' ?></p>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Email Address</label>

                                                    <p class="m-0 text-dark form-label"><?php echo $customer_info['email'] ?? '' ?></p>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Mobile Number</label>

                                                    <p class="m-0 text-dark form-label"><?php echo $customer_info['mobile'] ?? '' ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade " id="tab-metadata">
                                        <div class="border rounded p-4">
                                            <div class="form-label mb-2" style="border-bottom: 1px solid #e5e7eb;margin-left: -1.5rem !important;margin-right: -1.5rem !important;padding-left: 1.5rem !important;padding-right: 1.5rem !important;padding-bottom: 1rem !important;font-weight: 600;margin-top: -3px;margin-bottom: 15px !important;display: flex;justify-content: space-between;">
                                                Metadata Information
                                            </div>
                                            <div class="row g-3" style=" margin-bottom: -1rem; ">
                                                <?php
                                                    $metadata = json_decode($response_transaction['response'][0]['metadata'], true) ?: [];
                                                    if(!empty($metadata)){
                                                        foreach($metadata as $key => $value){
                                                ?>
                                                            <div class="col-md-4 mb-2">
                                                                <label class="form-label"><?php echo $key?></label>

                                                                <p class="m-0 text-dark form-label"><?php echo $value ?? '' ?></p>
                                                            </div>
                                                <?php
                                                        }
                                                    }else{
                                                ?>
                                                      <style>
                                                           .btn-tab-metadata{
                                                              display: none;
                                                           }
                                                      </style>
                                                <?php
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="tab-pane fade " id="tab-more">
                                        <div class="border rounded p-4">
                                            <div class="form-label mb-2" style="border-bottom: 1px solid #e5e7eb;margin-left: -1.5rem !important;margin-right: -1.5rem !important;padding-left: 1.5rem !important;padding-right: 1.5rem !important;padding-bottom: 1rem !important;font-weight: 600;margin-top: -3px;margin-bottom: 15px !important;display: flex;justify-content: space-between;">
                                                More Information
                                            </div>
                                            <div class="row g-3" style=" margin-bottom: -1rem; ">
                                                <?php
                                                    $source_info = json_decode($response_transaction['response'][0]['source_info'], true) ?: [];
                                                    if(!empty($source_info)){
                                                        foreach ($source_info as $item) {
                                                            $title = $item['label'] ?? '';
                                                            $description = $item['value'] ?? '';
                                                ?>
                                                            <div class="col-md-4 mb-2">
                                                                <label class="form-label"><?php echo htmlspecialchars($title); ?></label>
                                                                <p class="m-0 text-dark form-label">
                                                                    <?php
                                                                        // Auto-link if value is a URL
                                                                        if (filter_var($description, FILTER_VALIDATE_URL)) {
                                                                            echo '<a href="'.htmlspecialchars($description).'" target="_blank">View</a>';
                                                                        } else {
                                                                            echo htmlspecialchars($description);
                                                                        }
                                                                    ?>
                                                                </p>
                                                            </div>
                                                <?php
                                                        }
                                                    }else{
                                                ?>
                                                      <style>
                                                           .btn-tab-more{
                                                              display: none;
                                                           }
                                                      </style>
                                                <?php
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade " id="tab-endpoint">
                                        <div class="border rounded p-4">
                                            <div class="form-label mb-2" style="border-bottom: 1px solid #e5e7eb;margin-left: -1.5rem !important;margin-right: -1.5rem !important;padding-left: 1.5rem !important;padding-right: 1.5rem !important;padding-bottom: 1rem !important;font-weight: 600;margin-top: -3px;margin-bottom: 15px !important;display: flex;justify-content: space-between;">
                                                Endpoint
                                            </div>
                                            <div class="row g-3" style=" margin-bottom: -1rem; ">
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Return URL</label>
                                                    <p class="m-0 text-dark form-label"><?php echo ($response_transaction['response'][0]['return_url'] == "--" || $response_transaction['response'][0]['return_url'] == "") ? '' : $response_transaction['response'][0]['return_url']; ?></p>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Webhook URL</label>
                                                    <p class="m-0 text-dark form-label"><?php echo ($response_transaction['response'][0]['webhook_url'] == "--" || $response_transaction['response'][0]['webhook_url'] == "") ? '' : $response_transaction['response'][0]['webhook_url']; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                            <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'transaction', 'approve', $global_user_response['response'][0]['role']) ? '<option value="approved">Approve</option>' : '' ?>
                            <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'transaction', 'cancel', $global_user_response['response'][0]['role']) ? '<option value="canceled">Cancel</option>' : '' ?>
                            <?= hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'transaction', 'refund', $global_user_response['response'][0]['role']) ? '<option value="refunded">Refund</option>' : '' ?>
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
            const selectedRows = ['<?php echo $ref ?>'];

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

                            load_content('Edit Transaction','<?php echo $site_url.$path_admin ?>/transaction/edit?t_id=<?php echo $ref?>','nav-item-transaction');
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

    function ipnItem(ItemID){
        var my_action_confirmation_btn = document.querySelector("#my-action-confirmation-btn").value;
        var csrf_token_default = $('input[name="csrf_token_default"]').val();

        var btnClass = 'btnIpnItem-'+ItemID;

        if(my_action_confirmation_btn !== ""){
            var btn = document.querySelector('#model-my-action-confirmation-btn').innerHTML;

            document.querySelector('#model-my-action-confirmation-btn').innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

            $.ajax({
                type: 'POST',
                url: '<?php echo $site_url.$path_admin ?>/dashboard',
                data: {action: "transaction-ipn", csrf_token: csrf_token_default, ItemID: ItemID},
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
            show_action_confirmation_tab(btnClass, 'Send Transaction IPN', 'Confirm', 'btn-success');
        }
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

                        load_content('Transaction','<?php echo $site_url.$path_admin ?>/transaction','nav-item-transaction');
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


</script>