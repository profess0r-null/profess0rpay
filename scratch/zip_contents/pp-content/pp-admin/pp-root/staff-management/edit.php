<?php
    if (!defined('Profess0rPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
    }

    if (!canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'staff_management', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }
 
    if (!hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'staff', 'edit', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }

    $params = json_decode($_POST['params'] ?? '{}', true);

    $staff_id = getParam($params, 'staff');

    if ($staff_id === null) {
        http_response_code(403);
        exit('Invalid staff id');
    }else{
        $staff_id = escape_string($staff_id);

        $response_staff = json_decode(getData($db_prefix.'admin','WHERE a_id = "'.$staff_id.'" AND role = "staff"'),true);
        if($response_staff['status'] == true){
            if($global_user_response['response'][0]['id'] == $staff_id){
                http_response_code(403);
                exit("You can't edit your info");
            }
        }else{
            http_response_code(403);
            exit('Direct access not allowed');
        }
    }
?>

<div class="page-header d-print-none" aria-label="Page header">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
            <!-- Page pre-title -->
                <div class="page-pretitle">
                    <ol class="breadcrumb breadcrumb-arrow mb-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0)" onclick="load_content('Staff Management','<?php echo $site_url.$path_admin ?>/staff-management','nav-item-staff-management')">Staff Management</a></li>
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Edit Staff</a></li>
                    </ol>
                </div>
                <h2 class="page-title">Edit Staff</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row g-gs">
            <div class="col-12 col-xxl-12">
                <form class="form-staff-management-update">
                    <input type="hidden" name="action" value="staff-update">
                    <input type="hidden" name="itemID" value="<?php echo $response_staff['response'][0]['a_id']?>">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">

                    <div class="card p-2">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="full-name" class="form-label">Full name <span
                                                class="text-danger">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="full-name" name="full-name" placeholder="Full Name" value="<?php echo $response_staff['response'][0]['full_name']?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="username" class="form-label">Username <span
                                                class="text-danger">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="<?php echo $response_staff['response'][0]['username']?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="email-address" class="form-label">Email Address <span
                                                class="text-danger">*</span></label>
                                        <div class="form-control-wrap">
                                            <input type="email" class="form-control" id="email-address" name="email-address" placeholder="Email Address" value="<?php echo $response_staff['response'][0]['email']?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="password" class="form-label">New Password</label>
                                        <div class="form-control-wrap">
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end pt-3">
                        <button class="btn btn-primary btn-staff-management-update" type="submit">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script data-cfasync="false">
    $('.form-staff-management-update').submit(function (e) {
        e.preventDefault();

        var btnClass = 'btn-staff-management-update';

        var btn = document.querySelector('.'+btnClass).innerHTML;

        document.querySelector('.'+btnClass).innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url ?>dashboard',
            data: formData,
            dataType: 'json',
            success: function (response) {
                closeAllBootstrapModals();

                document.querySelector('.'+btnClass).innerHTML = btn;

                document.querySelectorAll('input[name="csrf_token"]').forEach(i => {
                    i.value = response.csrf_token;
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