<?php
if (!defined('Profess0rPay_INIT')) {
    http_response_code(403);
    exit('Direct access not allowed');
}

    if (!canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'system_settings', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }

    if (!hasPermission(json_decode($global_response_permission['response'][0]['permission'], true), 'system_settings', 'manage_update', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }

    require_once __DIR__ . '/../../../pp-include/class-updater.php';

    $updater = new Profess0rPayUpdater();

    $currentVersion = '1.0.0';
    $pdo = connectDatabase();
    $stmt = $pdo->prepare("SELECT `value` FROM `{$db_prefix}env` WHERE `option_name` = 'pp_version'");
    $stmt->execute();
    if($val = $stmt->fetchColumn()) {
        $currentVersion = $val;
    }

    $preflightErrors = $updater->checkPreflight();
    $latestRelease = null;

    try {
        $latestRelease = $updater->getLatestRelease();
    } catch (Exception $e) {
        // API Failed
    }
?>

<div class="page-header d-print-none" aria-label="Page header">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
            <!-- Page pre-title -->
                <div class="page-pretitle">
                    <ol class="breadcrumb breadcrumb-arrow mb-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0)" onclick="load_content('System Settings','<?php echo $site_url.$path_admin ?>/system-settings','nav-item-system-settings')">System Settings</a></li>
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">System Update</a></li>
                    </ol>
                </div>
                <h2 class="page-title">System Update</h2>
            </div>

            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list align-items-center gap-3">
                    <button class="btn btn-dark" onclick="load_content('Update History','<?php echo $site_url.$path_admin ?>/system-update/history','nav-item-system-settings')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 8l0 4l2 2" /><path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5" /></svg>
                        Update History
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        
        <div class="row row-cards">
            <div class="col-md-6">
                <div class="card p-3">
                    <h5 class="card-title text-muted mb-2">Current Version</h5>
                    <h2 class="mb-0 fw-bold text-primary">v<?= htmlspecialchars($currentVersion) ?></h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-3">
                    <h5 class="card-title text-muted mb-2">Latest Version</h5>
                    <?php if ($latestRelease && isset($latestRelease['tag_name'])): ?>
                        <?php $latestVer = str_replace('v', '', $latestRelease['tag_name']); ?>
                        <h2 class="mb-0 fw-bold <?= version_compare($currentVersion, $latestVer, '<') ? 'text-success' : 'text-primary' ?>" id="latest-ver-tag">v<?= htmlspecialchars($latestVer) ?></h2>
                        <?php if (version_compare($currentVersion, $latestVer, '<')): ?>
                            <span class="badge bg-warning mt-2 d-inline-block" style="width: fit-content;">New Update Available!</span>
                        <?php else: ?>
                            <span class="badge bg-success mt-2 d-inline-block" style="width: fit-content;">You are up to date!</span>
                        <?php endif; ?>
                    <?php else: ?>
                        <h2 class="mb-0 fw-bold text-muted">Unknown</h2>
                        <span class="badge bg-danger mt-2 d-inline-block" style="width: fit-content;">Could not fetch release</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (!empty($preflightErrors)): ?>
            <div class="alert alert-danger mt-4">
                <strong>Pre-flight Checks Failed!</strong> Fix the following issues before updating:<br>
                <ul class="mb-0 mt-2">
                    <?php foreach($preflightErrors as $err) echo "<li>$err</li>"; ?>
                </ul>
            </div>
        <?php elseif ($latestRelease && version_compare($currentVersion, str_replace('v', '', $latestRelease['tag_name']), '<')): ?>
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Release Notes (<?= htmlspecialchars($latestRelease['tag_name']) ?>)</h3>
                </div>
                <div class="card-body">
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; max-height: 200px; overflow-y: auto;" class="mb-4">
                        <?= nl2br(htmlspecialchars($latestRelease['body'])) ?>
                    </div>
                    
                    <div id="update-action-container">
                        <button class="btn btn-primary btn-lg" id="btnRunUpdate" onclick="startUpdateSequence()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg>
                            Download & Install Update
                        </button>
                    </div>

                    <div id="update-progress-container" style="display: none;">
                        <div class="d-flex justify-content-between mb-2">
                            <h3 id="update-status-text" class="mb-0 text-primary fw-bold">Initializing...</h3>
                            <span id="update-percent" class="text-muted fw-bold">0%</span>
                        </div>
                        <div class="progress progress-lg mb-3">
                            <div id="update-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width: 0%"></div>
                        </div>
                        <p class="text-muted small" id="update-subtext">Please do not close this window.</p>
                    </div>
                </div>
            </div>

            <script>
                function startUpdateSequence() {
                    let targetVersion = document.getElementById('latest-ver-tag').innerText.replace('v', '').trim();
                    let csrf = document.querySelector('input[name="csrf_token_default"]') ? document.querySelector('input[name="csrf_token_default"]').value : '';
                    
                    document.getElementById('update-action-container').style.display = 'none';
                    document.getElementById('update-progress-container').style.display = 'block';

                    const steps = [
                        { id: 1, text: "Downloading Update...", sub: "Fetching the latest release from GitHub", pct: 25 },
                        { id: 2, text: "Extracting Files...", sub: "Unpacking update files on server", pct: 50 },
                        { id: 3, text: "Installing Update...", sub: "Copying new files and updating database", pct: 80 },
                        { id: 4, text: "Cleaning Up...", sub: "Removing temporary files", pct: 100 }
                    ];

                    let currentStep = 0;

                    function executeNextStep() {
                        if (currentStep >= steps.length) {
                            // Done!
                            document.getElementById('update-status-text').innerText = "Update Successful!";
                            document.getElementById('update-status-text').className = "mb-0 text-success fw-bold";
                            document.getElementById('update-subtext').innerText = "System is up to date.";
                            document.getElementById('update-progress-bar').className = "progress-bar bg-success";
                            
                            createToast({
                                title: 'Update Successful!',
                                description: 'System has been successfully updated to v' + targetVersion,
                                svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#5f38f9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>`,
                                timeout: 6000,
                                top: 70
                            });

                            setTimeout(() => {
                                load_content('System Update','<?php echo $site_url.$path_admin ?>/system-settings/update','nav-item-system-settings');
                            }, 2000);
                            return;
                        }

                        let step = steps[currentStep];
                        document.getElementById('update-status-text').innerText = step.text;
                        document.getElementById('update-subtext').innerText = step.sub;
                        
                        $.ajax({
                            type: 'POST',
                            url: '<?php echo $site_url.$path_admin ?>/dashboard',
                            data: { 
                                action: 'process-system-update', 
                                step: step.id,
                                target_version: targetVersion,
                                csrf_token: csrf 
                            },
                            success: function(text) {
                                try {
                                    let res = typeof text === 'object' ? text : JSON.parse(text.match(/(\{"status":.+\})/s)[1]);
                                    if (res.status === 'success') {
                                        document.getElementById('update-progress-bar').style.width = step.pct + "%";
                                        document.getElementById('update-percent').innerText = step.pct + "%";
                                        currentStep++;
                                        executeNextStep();
                                    } else {
                                        showError(res.message);
                                    }
                                } catch(e) {
                                    console.error("Parse Error:", e, text);
                                    showError("Unexpected response from server on Step " + step.id);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error("Network Error:", error);
                                showError("Network connection lost during Step " + step.id);
                            }
                        });
                    }

                    function showError(msg) {
                        document.getElementById('update-status-text').innerText = "Update Failed!";
                        document.getElementById('update-status-text').className = "mb-0 text-danger fw-bold";
                        document.getElementById('update-subtext').innerText = msg;
                        document.getElementById('update-progress-bar').className = "progress-bar bg-danger";
                        
                        createToast({
                            title: 'Update Failed',
                            description: msg,
                            svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                            timeout: 6000,
                            top: 70
                        });

                        setTimeout(() => {
                            document.getElementById('update-progress-container').style.display = 'none';
                            document.getElementById('update-action-container').style.display = 'block';
                            // Reset bar for retry
                            document.getElementById('update-progress-bar').style.width = "0%";
                            document.getElementById('update-percent').innerText = "0%";
                            document.getElementById('update-status-text').className = "mb-0 text-primary fw-bold";
                            document.getElementById('update-progress-bar').className = "progress-bar progress-bar-striped progress-bar-animated bg-primary";
                        }, 5000);
                    }

                    // Start the chain
                    executeNextStep();
                }
            </script>
        <?php endif; ?>
        
    </div>
</div>