<?php
if (!defined('Profess0rPay_INIT')) {
    http_response_code(403);
    exit('Direct access not allowed');
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
                <div class="page-pretitle">Activities</div>
                <h2 class="page-title">Activities</h2>
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
                                                <option value="expired">Expired</option>
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
                            <div class="col-lg-6 col-md-6 text-end">
                                <div class="ms-auto text-secondary">
                                    Search:<div class="ms-2 d-inline-block"><input type="text" class="form-control form-control-sm search_input" aria-label="Search"></div>
                                </div>
                            </div>
                        </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-selectable card-table table-vcenter text-nowrap datatable">
                      <thead>
                        <tr>
                          <th>Browser</th>
                          <th>Device</th>
                          <th>IP</th>
                          <th>Date</th>
                          <th>Status</th>
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



<script data-cfasync="false">
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
            data: {action: "activities-list", csrf_token: csrf_token_default, search_input: search_input, show_limit: show_limit, page: page, filter_status: filter_status, filter_start: filter_start, filter_end: filter_end},
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
                        let bgColor = '';

                        if (item.status === 'active') badge = 'primary';
                        if (item.status === 'expired') badge = 'danger';
                        
                        if (item.isequal === 'matched') bgColor = 'style=" border-left: 2px solid #5f38f9; background-color: #5f38f90d; "';

                        html += `
                            <tr ${bgColor}>
                                <td><span class="text-secondary">${item.browser}</span></td>
                                <td>${item.device}</td>
                                <td>${item.ip}</td>
                                <td>${item.created_date}</td>
                                <td><span class="badge bg-${badge} me-1"></span> ${item.status.charAt(0).toUpperCase() + item.status.slice(1)}</td>
                            </tr>
                        `;
                    });

                    $(".table-data-list").html(html);
                    document.querySelector(".table-data-list-entries").innerHTML = res.datatableInfo;

                    $(".table-data-list-pagination").html(res.pagination);
                } else {
                    html = `<td colspan="5" class="text-center text-muted"> <div style="margin: 50px;"> <center> <svg xmlns="http://www.w3.org/2000/svg" style=" width: 40px; height: 40px; " viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-mood-cry"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 10l.01 0" /><path d="M15 10l.01 0" /><path d="M9.5 15.25a3.5 3.5 0 0 1 5 0" /><path d="M17.566 17.606a2 2 0 1 0 2.897 .03l-1.463 -1.636l-1.434 1.606z" /><path d="M20.865 13.517a8.937 8.937 0 0 0 .135 -1.517a9 9 0 1 0 -9 9c.69 0 1.36 -.076 2 -.222" /></svg> <p style=" font-weight: 600; font-size: 16px; margin-top: 7px; margin-bottom: 3px; ">`+res.title+`</p> <p style=" margin: 0; ">`+res.message+`</p> </center> </div> </td>`;
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
</script>