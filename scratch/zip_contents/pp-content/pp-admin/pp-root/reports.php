<?php
    if (!defined('Profess0rPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
    }

    if (!canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'reports', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }
?>

<div class="page-header d-print-none" aria-label="Page header">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
            <!-- Page pre-title -->
                <div class="page-pretitle">Reports</div>
                <h2 class="page-title">Reports</h2>
            </div>

            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list d-flex align-items-center flex-nowrap gap-2">
                    <div class="reports-loading"></div>
                    <select class="form-select " id="report-date" data-search="true" data-sort="false" required onchange="load_reports()">
                        <option value="today">
                            Today
                        </option>
                        <option value="yesterday">
                            Yesterday
                        </option>
                        <option value="this_week">
                            This week
                        </option>
                        <option value="last_week">
                            Last week
                        </option>
                        <option value="this_month">
                            This month
                        </option>
                        <option value="last_month">
                            Last month
                        </option>
                        <option value="this_year" selected>
                            This year
                        </option>
                        <option value="previous_year">
                            Previous year
                        </option>
                    </select>

                    <span class="btn-refresh-license" data-bs-toggle="offcanvas" data-bs-target="#custom-date-range-offcanvas">
                        <a href="javascript:void(0)" class="btn btn-primary btn-1 d-none d-sm-inline-block"> 
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-adjustments"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 10a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M6 4v4" /><path d="M6 12v8" /><path d="M10 16a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M12 4v10" /><path d="M12 18v2" /><path d="M16 7a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M18 4v1" /><path d="M18 9v11" /></svg>
                            Custom Range
                        </a>

                        <a href="javascript:void(0)" class="btn btn-primary btn-1 d-sm-none btn-icon"> 
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-adjustments"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 10a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M6 4v4" /><path d="M6 12v8" /><path d="M10 16a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M12 4v10" /><path d="M12 18v2" /><path d="M16 7a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M18 4v1" /><path d="M18 9v11" /></svg>
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
        <div class="card mb-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <p class="text-muted">
                    <strong class="text-primary">Financial Report:</strong>
                    <span id="financial-date-range">--</span>
                </p>
            </div>
        </div>

        <div class="row g-4">

            <!-- Revenue -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <p class="text-muted mb-2">Revenue</p>

                        <h1 class="fw-bold mb-2 text-dark" id="revenue-amount"><?php echo $global_brand_currency_symbol;?>0.00</h1>

                        <div class="d-flex align-items-center text-success">
                            <span id="revenue-count">0 payments completed</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Rate -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <p class="text-muted mb-2">Success Rate</p>

                        <h1 class="fw-bold mb-2 text-dark" id="success-rate">0%</h1>

                        <div class="d-flex align-items-center" id="success-rate-indicator">
                            <span id="success-rate-text">0 total transactions</span>
                            <em class="icon ni ms-1" id="success-rate-icon"></em>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Average Transaction -->
            <div class="col-lg-4 col-md-12">
                <div class="card h-100">
                    <div class="card-body">
                        <p class="text-muted mb-2">Average Transaction</p>

                        <h1 class="fw-bold mb-2 text-dark" id="avg-transaction"><?php echo $global_brand_currency_symbol;?>0.00</h1>

                        <div class="d-flex align-items-center text-info">
                            <span>Average payment amount</span>
                            <svg class="ms-2" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 1c-1.716 0-3.408.106-5.07.31"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>



<div class="offcanvas offcanvas-end" tabindex="-1" id="custom-date-range-offcanvas">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Custom Date Range</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <!-- Body -->
    <div class="offcanvas-body d-flex flex-column p-0">

        <!-- Scrollable content -->
        <div class="flex-grow-1 p-4">
            <div class="card p-2">
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">
                            Start Date <span class="text-danger">*</span>
                        </label>

                        <input placeholder="dd/mm/yyyy" type="date" class="form-control" autocomplete="off" id="custom-date-range-offcanvas-start-date"> 
                    </div>

                    <div class="form-group mt-3">
                        <label class="form-label">
                            End Date <span class="text-danger">*</span>
                        </label>

                        <input placeholder="dd/mm/yyyy" type="date" class="form-control" autocomplete="off" id="custom-date-range-offcanvas-end-date"> 
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom action bar -->
        <div class="border-top p-3 bg-white sticky-bottom">
            <div class="d-flex gap-2">
                <button class="btn btn-light w-50" data-bs-dismiss="offcanvas">
                    Cancel
                </button>
                <button class="btn btn-primary w-50" id="custom-date-range-offcanvas-applyDateFilter">
                    Apply Filter
                </button>
            </div>
        </div>

    </div>
</div>


<script data-cfasync="false">
    function load_reports(){
        var csrf_token_default = $('input[name="csrf_token_default"]').val();
        var date = $('#report-date').val();

        const start = document.getElementById('custom-date-range-offcanvas-start-date').value;
        const end   = document.getElementById('custom-date-range-offcanvas-end-date').value;

        document.querySelector(".reports-loading").innerHTML = '<div class="spinner-border spinner-border-sm text-primary">  <span class="visually-hidden">Loading...</span></div>';
      
        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            data: {action: "reports", csrf_token: csrf_token_default, date: date, start: start, end: end},
            dataType: 'json',
            success: function (res) {
                document.querySelector(".reports-loading").innerHTML = '';

                document.querySelectorAll('input[name="csrf_token"]').forEach(input => {
                    input.value = res.csrf_token;
                });
                document.querySelectorAll('input[name="csrf_token_default"]').forEach(input => {
                    input.value = res.csrf_token;
                });

                document.getElementById('custom-date-range-offcanvas-start-date').value = '';
                document.getElementById('custom-date-range-offcanvas-end-date').value = '';

                if (res.status === 'true') {
                    document.getElementById('financial-date-range').innerText = res.date_range;

                    document.getElementById('revenue-amount').innerText = '<?php echo $global_brand_currency_symbol;?>' + res.revenue;

                    document.getElementById('revenue-count').innerText = res.completed + ' payments completed';

                    document.getElementById('success-rate').innerText = res.success_rate + '%';

                    let indicator = document.getElementById('success-rate-indicator');
                    let text = document.getElementById('success-rate-text');
                    let icon = document.getElementById('success-rate-icon');

                    indicator.classList.remove('text-success','text-danger','text-muted');
                    icon.className = 'icon ni ms-1';

                    if (res.success_trend === 'up') {
                        indicator.classList.add('text-success');
                        icon.classList.add('ni-arrow-up-right');
                        text.innerText = `Improved from ${res.prev_success_rate}%`;
                    }
                    else if (res.success_trend === 'down') {
                        indicator.classList.add('text-danger');
                        icon.classList.add('ni-arrow-down-right');
                        text.innerText = `Dropped from ${res.prev_success_rate}%`;
                    }
                    else {
                        indicator.classList.add('text-muted');
                        icon.classList.add('ni-minus');
                        text.innerText = `No change from ${res.prev_success_rate}%`;
                    }

                    document.getElementById('avg-transaction').innerText = '<?php echo $global_brand_currency_symbol;?>' + res.average;
                } else {
                    createToast({
                        title: res.title,
                        description: res.message,
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

    load_reports();

    $('#custom-date-range-offcanvas-applyDateFilter').click(function () {
        load_reports();
        
        $('#custom-date-range-offcanvas').offcanvas('hide');
    });
</script>