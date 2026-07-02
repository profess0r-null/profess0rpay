<?php
if (!defined('Profess0rPay_INIT')) {
    http_response_code(403);
    exit('Direct access not allowed');
}

    if (!canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'staff_management', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }
 
    if (!hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'staff', 'edit_permission', $global_user_response['response'][0]['role'])) {
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

        $response_permission = json_decode(getData($db_prefix.'permission','WHERE id = "'.$staff_id.'"'),true);
        if($response_permission['status'] == true){

            $response_staff = json_decode(getData($db_prefix.'admin','WHERE a_id = "'.$response_permission['response'][0]['a_id'].'" AND role = "staff"'),true);
            if($response_staff['status'] == true){
                if($global_user_response['response'][0]['id'] == $response_staff['response'][0]['id']){
                    http_response_code(403);
                    exit("You can't edit your info");
                }
            }else{
                http_response_code(403);
                exit('Direct access not allowed');
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
                        <li class="breadcrumb-item"><a href="javascript:void(0)" onclick="load_content('Permission List','<?php echo $site_url.$path_admin ?>/staff-management/permissions-list?staff=<?php echo $response_permission['response'][0]['a_id']?>','nav-item-staff-management')">Permission List</a></li>
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Edit Permissions</a></li>
                    </ol>
                </div>
                <h2 class="page-title">Edit Permissions</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row g-gs">
            <div class="col-12 col-xxl-12">
                <form class="form-staff-management-update">
                    <input type="hidden" name="action" value="staff-update-permission">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">
                    <input type="hidden" name="staff_id" value="<?= $staff_id; ?>">

                    <div class="card p-2">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Status <span class="text-danger">*</span></label>
                                        <div class="form-control-wrap">
                                            <select class="js-select" name="status" data-search="true" data-remove="true" data-placeholder="Select Status" required>
                                                <option value="active" <?= $response_permission['response'][0]['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                                <option value="suspend" <?= $response_permission['response'][0]['status'] === 'suspend' ? 'selected' : '' ?>>Suspend</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Select All Permissions</label>
                                        <div class="form-control-wrap">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" value=""id="btnAllPermission">
                                            </div>

                                            <small class="form-hint">Enables or disables all permissions for this staff</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <ul class="nav nav-tabs nav-tabs-s1" style="border-bottom: transparent;">
                                <?php
                                    $i = 0;
                                    $schema = permissionSchema();
                                    $savedPermissions = json_decode($response_permission['response'][0]['permission'], true);

                                    foreach ($schema as $tabKey => $tabData):
                                        $tabId = 'tab-' . $tabKey;

                                        $totalCount = countPermissions($tabKey, $tabData);
                                ?>
                                        <li class="nav-item"> 
                                            <button style=" border-radius: 5px; " class="nav-link <?= $i === 0 ? 'active' : '' ?>" data-bs-toggle="tab" data-bs-target="#<?= $tabId ?>" type="button">
                                                <?= ucfirst(str_replace('_',' ', $tabKey)) ?> 
                                                <span class="badge bg-primary text-primary-fg ms-2 ms-1" style=" font-size: 11px; "><?= $totalCount ?></span>
                                            </button> 
                                        </li>       
                                <?php
                                        $i++;
                                    endforeach;
                                ?>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">

                                <?php
                                    $i = 0;
                                    foreach ($schema as $tabKey => $tabData):
                                        $tabId = 'tab-' . $tabKey;
                                ?>
                                        <div class="tab-pane fade <?= $i === 0 ? 'show active' : '' ?>" id="<?= $tabId ?>">

                                            <!-- ================= RESOURCES TAB ================= -->
                                            <?php if ($tabKey === 'resources'): ?>
                                                <?php foreach ($tabData as $module => $actions): ?>
                                                    <?php
                                                        $rand_id = uniqid();
                                                    ?>

                                                    <div class="border rounded p-3 mb-3">
                                                        <div class="form-label mb-2" style="border-bottom: 1px solid #e5e7eb;margin-left: -1rem;margin-right: -1rem;padding-left: 1rem;padding-right: 1rem;padding-bottom: 10px;font-weight: 600;margin-top: -3px;margin-bottom: 15px !important;display: flex;justify-content: space-between;">
                                                            <?= ucfirst(str_replace(['_', '-'], ' ', $module)) ?>

                                                            <span onclick="select_by_box('<?php echo $rand_id?>')" class="btn-<?php echo $rand_id?> text-primary" style=" cursor: pointer; font-size: 14px; font-weight: 500; ">Select All</span>
                                                        </div>

                                                        <div class="row" style=" margin-bottom: -1rem; ">
                                                            <?php foreach ($actions as $action => $_):
                                                                $checked = $savedPermissions['resources'][$module][$action] ?? false;
                                                            ?>
                                                                <div class="col-md-3 mb-2">
                                                                    <label class="form-check">
                                                                        <input type="checkbox"
                                                                            class="form-check-input perm-checkbox checkbox-<?php echo $rand_id?>"
                                                                            data-type="resources"
                                                                            data-module="<?= $module ?>"
                                                                            data-action="<?= $action ?>"
                                                                            <?= $checked ? 'checked' : '' ?>>

                                                                        <span class="form-check-label" style="font-size: 0.875rem;font-weight: 500;">
                                                                            <?= ucfirst(str_replace(['_', '-'], ' ', $action)) ?> <?= ucfirst(str_replace(['_', '-'], ' ', $module)) ?>
                                                                        </span>
                                                                    </label>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>

                                            <!-- ================= PAGES TAB ================= -->
                                            <?php elseif ($tabKey === 'pages'): ?>
                                                <?php
                                                    $rand_id = uniqid();
                                                ?>

                                                <div class="row" style=" margin-bottom: -1rem; ">
                                                    <div class="col-12 mb-3">
                                                         <span onclick="select_by_box('<?php echo $rand_id?>')" class="btn-<?php echo $rand_id?> text-primary" style=" cursor: pointer; font-size: 14px; font-weight: 500; ">Select All</span>
                                                    </div>

                                                    <?php foreach ($tabData as $page => $_):
                                                        $checked = $savedPermissions['pages'][$page] ?? false;
                                                    ?>
                                                        <div class="col-md-3 mb-2">
                                                            <label class="form-check">
                                                                <input type="checkbox"
                                                                    class="form-check-input perm-checkbox checkbox-<?php echo $rand_id?>"
                                                                    data-type="pages"
                                                                    data-page="<?= $page ?>"
                                                                    <?= $checked ? 'checked' : '' ?>>

                                                                <span class="form-check-label" style="font-size: 0.875rem;font-weight: 500;">
                                                                    View <?= ucfirst(str_replace(['_', '-'], ' ', $page)) ?>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>

                                            <?php endif; ?>

                                        </div>
                                <?php
                                        $i++;
                                    endforeach;
                                ?>

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
    function permissionSwitchCheck(){
        const permissionSwitch = document.getElementById('btnAllPermission');
        permissionSwitch.addEventListener('change', () => {
            const checked = permissionSwitch.checked;
            document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = checked);

            // Update all module buttons
            document.querySelectorAll('[class^="btn-"]').forEach(btn => {
                btn.innerHTML = checked ? 'Deselect All' : 'Select All';
            });
        });

        // Detect manual changes and update button text
        document.querySelectorAll('.perm-checkbox').forEach(cb => {
            cb.addEventListener('change', () => {
                const classes = Array.from(cb.classList).filter(c => c.startsWith('checkbox-'));
                classes.forEach(cls => {
                    const boxid = cls.replace('checkbox-', '');
                    const allCheckboxes = document.querySelectorAll('.' + cls);
                    const btn = document.querySelector('.btn-' + boxid);

                    // If all checkboxes are checked, show 'Deselect All', else 'Select All'
                    btn.innerHTML = Array.from(allCheckboxes).every(c => c.checked) ? 'Deselect All' : 'Select All';
                });

                // Also update global switch
                const allGlobal = Array.from(document.querySelectorAll('.perm-checkbox')).every(c => c.checked);
                permissionSwitch.checked = allGlobal;
            });
        });
    }

    permissionSwitchCheck();

    function select_by_box(boxid) {
        const btn = document.querySelector('.btn-' + boxid);
        const allCheckboxes = document.querySelectorAll('.checkbox-' + boxid);
        const selectAll = btn.innerHTML === 'Select All';

        allCheckboxes.forEach(cb => cb.checked = selectAll);

        const specific = document.getElementById('btnAllPermission');
        if (btn.innerHTML === 'Deselect All' && specific) {
            specific.checked = false;
        }else{
            specific.checked = true;
        }
        
        btn.innerHTML = selectAll ? 'Deselect All' : 'Select All';
    }

    function DefaultSync(){
        const permissionSwitch = document.getElementById('btnAllPermission');

        // Update module Select/Deselect buttons based on CURRENT checkbox state
        document.querySelectorAll('.perm-checkbox').forEach(cb => {
            const classes = Array.from(cb.classList).filter(c => c.startsWith('checkbox-'));
            classes.forEach(cls => {
                const boxid = cls.replace('checkbox-', '');
                const allCheckboxes = document.querySelectorAll('.' + cls);
                const btn = document.querySelector('.btn-' + boxid);

                if (btn) {
                    btn.innerHTML = Array.from(allCheckboxes).every(c => c.checked)
                        ? 'Deselect All'
                        : 'Select All';
                }
            });
        });

        // Sync global switch ONLY (do not touch checkboxes)
        permissionSwitch.checked =
            Array.from(document.querySelectorAll('.perm-checkbox'))
                .every(c => c.checked);
    }

    DefaultSync();
    $('.form-staff-management-update').submit(function (e) {
        e.preventDefault();

        let permissions = {
            resources: {},
            pages: {}
        };

        document.querySelectorAll('.perm-checkbox').forEach(cb => {
            if (cb.dataset.type === 'resources') {
                let module = cb.dataset.module;
                let action = cb.dataset.action;

                if (!permissions.resources[module]) {
                    permissions.resources[module] = {};
                }

                permissions.resources[module][action] = cb.checked;
            }

            if (cb.dataset.type === 'pages') {
                let page = cb.dataset.page;
                permissions.pages[page] = cb.checked;
            }
        });

        var btnClass = 'btn-staff-management-update';

        var btn = document.querySelector('.'+btnClass).innerHTML;

        document.querySelector('.'+btnClass).innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

        var formData = $(this).serialize();

        formData += '&permissions_json=' + encodeURIComponent(JSON.stringify(permissions));

        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            data: formData,
            dataType: 'json',
            success: function (response) {
                closeAllBootstrapModals();

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