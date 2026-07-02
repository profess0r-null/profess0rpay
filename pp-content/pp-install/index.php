<?php
    if (!defined('Profess0rPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
    }

    if(isset($_POST['test_databse_request'])){
        $host        = $_POST['dbHost'] ?? '';
        $port        = $_POST['dbPort'] ?? '3306';
        $dbname      = $_POST['dbName'] ?? '';
        $username    = $_POST['dbUsername'] ?? '';
        $password    = $_POST['dbPassword'] ?? '';
        $tablePrefix = $_POST['tablePrefix'] ?? '';

        if (!$host || !$dbname || !$username) {
            echo json_encode(['status' => 'false', 'message' => 'Please fill in all required fields.']);
            exit;
        }

        if (!in_array('mysql', PDO::getAvailableDrivers())) {
            echo json_encode(['status' => 'false', 'message' => 'PDO MySQL driver is not enabled on this server.']);
            exit;
        }

        if($requriemntnoneedchecked == false){
            echo json_encode([
                'status'  => 'false',
                'title'   => 'Server Requirements Not Met',
                'message' => 'Your server does not meet the minimum requirements. Please enable the required PHP extensions and try again.'
            ]);
            exit;
        }

        try {
            $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_AUTOCOMMIT => false
            ]);

            // Read SQL file
            $sqlContent = file_get_contents(__DIR__ . '/db.sql');
            if ($sqlContent === false) {
                throw new Exception("SQL file not found or empty.");
            }

            if (!empty($tablePrefix) && $tablePrefix !== 'pp_') {
                $sqlContent = str_replace('pp_', $tablePrefix, $sqlContent);
            }

            $queries = array_filter(array_map('trim', explode(";\n", $sqlContent)));

            // Start transaction AFTER SQL is prepared
            $pdo->beginTransaction();

            foreach ($queries as $query) {
                if ($query !== '') {
                    $pdo->exec($query);
                }
            }

            if ($pdo->inTransaction()) {
                $pdo->commit();
            }

            // Write config file
            $app_secret = bin2hex(random_bytes(32));
            $configContent = "<?php
    \$db_host = '".addslashes($host). "';
    \$db_port = '" . addslashes($port) . "'; 
    \$db_user = '".addslashes($username)."';
    \$db_pass = '".addslashes($password)."';
    \$db_name = '".addslashes($dbname)."';
    \$db_prefix = '".addslashes($tablePrefix)."';
    \$app_secret = '".$app_secret."';
?>";

            file_put_contents(__DIR__ . '/../../pp-temp-config.php', $configContent);

            echo json_encode(['status' => 'true', 'title' => 'Imported successfully', 'message' => 'Database connection verified and imported successfully.']);
        } catch (Throwable $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }

            echo json_encode(['status' => 'false', 'title' => 'Database Error', 'message' => $e->getMessage()]);
        }
        exit;
    }

        
    if(isset($_POST['adminName'])){
        $adminName = $_POST['adminName'];
        $adminEmail = $_POST['adminEmail'];
        $adminUsername = $_POST['adminUsername'];
        $adminPassword = $_POST['adminPassword'];
        $confirmPassword = $_POST['confirmPassword'];

        if($requriemntnoneedchecked == false){
            echo json_encode([
                'status'  => 'false',
                'title'   => 'Server Requirements Not Met',
                'message' => 'Your server does not meet the minimum requirements. Please enable the required PHP extensions and try again.'
            ]);
            exit;
        }

        if($adminName == "" || $adminEmail == "" || $adminUsername == "" || $adminPassword == "" || $confirmPassword == ""){
            echo json_encode(['status' => "false", 'message' => 'Enter all info before process.']);
        }else{
            if($adminPassword == $confirmPassword){
                if (filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
                    $new_temp_password = generateStrongPassword(8);

                    $hashedPass = password_hash($adminPassword, PASSWORD_BCRYPT);
                    $temp_password = password_hash($new_temp_password, PASSWORD_BCRYPT);

                    $a_id = generateItemID();
                    $brand_id = generateItemID();

                    $columns = ['a_id', 'full_name', 'username', 'email', 'password', 'temp_password', 'created_date', 'updated_date'];
                    $values = [$a_id, $adminName, $adminUsername, $adminEmail, $hashedPass, $temp_password, getCurrentDatetime('Y-m-d H:i:s'), getCurrentDatetime('Y-m-d H:i:s')];

                    insertData($db_prefix.'admin', $columns, $values);

                    $columns = ['brand_id', 'a_id', 'permission', 'created_date', 'updated_date'];
                    $values = [$brand_id, $a_id, json_encode(permissionSchema()), getCurrentDatetime('Y-m-d H:i:s'), getCurrentDatetime('Y-m-d H:i:s')];

                    insertData($db_prefix.'permission', $columns, $values);

                    $columns = ['brand_id', 'created_date', 'updated_date'];
                    $values = [$brand_id, getCurrentDatetime('Y-m-d H:i:s'), getCurrentDatetime('Y-m-d H:i:s')];

                    insertData($db_prefix.'brands', $columns, $values);

                    $columns = ['brand_id', 'code', 'symbol', 'created_date', 'updated_date'];
                    $values = [$brand_id, 'BDT', '৳', getCurrentDatetime('Y-m-d H:i:s'), getCurrentDatetime('Y-m-d H:i:s')];

                    insertData($db_prefix.'currency', $columns, $values);

                    $tempFile  = __DIR__ . '/../../pp-temp-config.php';
                    $finalFile = __DIR__ . '/../../pp-config.php';

                    if (!file_exists($tempFile)) {
                        echo json_encode(['status' => 'false', 'message' => 'Temp config file not found.']);
                        exit;
                    }

                    // Read temp content
                    $configContent = file_get_contents($tempFile);
                    if ($configContent === false) {
                        echo json_encode(['status' => 'false', 'message' => 'Failed to read temp config file.']);
                        exit;
                    }

                    // Write final config
                    if (file_put_contents($finalFile, $configContent) === false) {
                        echo json_encode(['status' => 'false', 'message' => 'Failed to create final config file.']);
                        exit;
                    }

                    unlink($tempFile);

                    echo json_encode(['status' => "true", 'message' => 'Install Completed.']);
                }else{
                    echo json_encode(['status' => "false", 'message' => 'Invalid email address.']);
                }
            }else{
                echo json_encode(['status' => "false", 'message' => 'Password and Confirm Password must be the same.']);
            }
        }

        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="author" content="QubePlug Bangladesh">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Installer - Profess0rPay</title>
    <link rel="shortcut icon" href="<?= $professorpay_favicon ?? '' ?>">

    <link rel="stylesheet" href="<?php echo $site_url ?>assets/css/tabler.min.css?v=1.5" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler-vendors.min.css" />

    <style>
      @import url("<?php echo $site_url ?>assets/css/inter.css");
    </style>
    <style>
        :root{
            --tblr-font-monospace: Monaco, Consolas, Liberation Mono, Courier New, monospace;
            --tblr-font-sans-serif: Inter Var, Inter, -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
            --tblr-font-serif: Georgia, Times New Roman, times, serif;
            --tblr-font-comic: Comic Sans MS, Comic Sans, Chalkboard SE, Comic Neue, sans-serif, cursive;
        }
    </style>

    <style>
        .all-pages .card{
            display: none;
        }
        .all-pages .card.active{
            display: block;
        }
    </style>
</head>
<body>
    <div class="container p-2 p-sm-4">
        <div class="text-center mb-5">
            <div class="brand-logo mb-1">
                <a href="#" class="logo-link">
                    <div class="logo-wrap">
                        <img src="<?= $professorpay_logo_light ?? '' ?>" alt="" style=" height: 40px; ">
                    </div>
                </a>
            </div>
        </div>

        <div class="col-lg-5 mx-auto">
            <ul class="steps steps-primary steps-counter p-0 m-0 mb-5 border-0">
                <li class="step-item active" data-step="1">Requirements</li>
                <li class="step-item" data-step="2">Database</li>
                <li class="step-item" data-step="3">Admin Setup</li>
                <li class="step-item" data-step="4">Complete</li>
            </ul>
        </div>

        <div class="col-lg-5 mx-auto all-pages">
            <!-- Page 1: Requirements Check -->
            <div class="card active" id="page1">
                <div class="card-header d-grid">
                    <h3 class="card-title mb-1">System Requirements Check</h3>
                    <p class="card-subtitle">Please wait while we check your server requirements.</p>
                </div>

                <div class="card-body">
                    <div class="requirements-grid">
                        <div class="requirement-groups">
                            <div id="phpRequirements">
                                <?php
                                    $satisfied_btn = true;

                                    foreach ($requirements as $req) {

                                        if (!$req['check']) {
                                            $satisfied_btn = false;
                                        }

                                        // Set status classes and icons
                                        $statusClass = $req['check'] ? 'text-success' : 'text-danger';
                                        $statusIcon  = $req['check'] ? 'bi-check-circle-fill' : 'bi-x-circle-fill'; // using Bootstrap Icons
                                        $statusText  = $req['check'] ? 'Passed' : 'Failed';

                                    ?>
                                        <div class="d-flex justify-content-between align-items-center border rounded p-3 mb-2">
                                            <div>
                                                <strong><?= $req['name'] ?></strong>
                                                <div class="small text-muted">
                                                    Required: <?= $req['required'] ?> | Current: <?= $req['current'] ?>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="<?= $statusIcon ?> <?= $statusClass ?>" style="font-size: 1.25rem;"></i>
                                                <span class="<?= $statusClass ?> fw-bold"><?= $statusText ?></span>
                                            </div>
                                        </div>
                                <?php
                                    }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-light" disabled>Previous</button>

                                <?php
                                    if($satisfied_btn == false){
                                ?>
                                        <button class="btn btn-danger" onclick="location.reload()">
                                            <span class="btn-text">Check Again</span>
                                        </button>
                                <?php
                                    }else{
                                ?>
                                        <button class="btn btn-primary" id="btnCheckRequirements">
                                            <span class="btn-text">Continue</span>
                                        </button>
                                <?php
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page 2: Database Configuration -->
            <div class="card" id="page2">
                <div class="card-header d-grid">
                    <h3 class="card-title mb-1">Database Configuration</h3>
                    <p class="card-subtitle">Enter your database connection details.</p>
                </div>

                <div class="card-body">
                    <form class="database-config-extra" id="dbForm">
                        <div class="row gy-3">
                            <!-- Database Driver -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label d-block mb-2">Database Driver</label>

                                    <div class="row gy-3">
                                        <div class="col-6">
                                            <div class="form-control-wrap p-2 border rounded" style="height: 40px;">
                                                <div class="form-check form-check-inline" style="margin-top: -2px;">
                                                    <input class="form-check-input" type="checkbox" id="driverMysql" name="dbDriver" value="mysql" checked>
                                                    <label class="form-check-label" for="driverMysql">MySQL / MariaDB</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dbHost" class="form-label">Database Host</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="dbHost" 
                                                placeholder="localhost" value="localhost" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dbPort" class="form-label">Database Port</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="dbPort" 
                                                placeholder="3306" value="3306" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dbName" class="form-label">Database Name</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="dbName" 
                                                placeholder="Enter database name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dbUsername" class="form-label">Database Username</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="dbUsername" 
                                                placeholder="Enter username" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="dbPassword" class="form-label">Database Password</label>
                                    <div class="form-control-wrap">
                                        <input type="password" class="form-control" id="dbPassword" 
                                                placeholder="Enter password">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="tablePrefix" class="form-label">Table Prefix (Optional)</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="tablePrefix" 
                                                placeholder="pp_" value="pp_">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="button" class="btn btn-outline-primary w-100 test-connection-btn" id="btnTestConnection">Check & Import</button>
                            </div>
                        </div>
                    </form>


                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-light" id="btnPrevToRequirements">Previous</button>
                                <button class="btn btn-primary" id="btnNextToAdmin" disabled>
                                    Continue
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page 3: Admin Setup -->
            <div class="card" id="page3">
                <div class="card-header d-grid">
                    <h3 class="card-title mb-1">Administrator Account Setup</h3>
                    <p class="card-subtitle">Create your administrator account.</p>
                </div>

                <div class="card-body">
                    <form id="adminForm" action="">
                        <div class="row gy-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="adminName" class="form-label">Full Name</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="adminName" name="adminName"
                                                placeholder="Enter your full name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="adminUsername" class="form-label">Username</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="adminUsername" name="adminUsername"
                                                placeholder="Enter username" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="adminEmail" class="form-label">Email Address</label>
                                    <div class="form-control-wrap">
                                        <input type="email" class="form-control" id="adminEmail" name="adminEmail"
                                                placeholder="Enter email address" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="adminPassword" class="form-label">Password</label>
                                    <div class="form-control-wrap">
                                        <input type="password" class="form-control" id="adminPassword" name="adminPassword"
                                                placeholder="Enter password" required>
                                        <div class="password-strength mt-1">
                                            <small class="text-muted">Password strength: <strong><span id="passwordStrength">None</span></strong></small>
                                            <div class="strength-meter mt-2">
                                                <div class="strength-fill" id="passwordStrengthMeter" style="height: 2px; border-radius: 5px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                                    <div class="form-control-wrap">
                                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword"
                                                placeholder="Confirm password" required>
                                        <div class="mt-1" id="passwordMatch"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-light d-none" id="btnPrevToDatabase">Previous</button>
                                    <div class="w-100"></div>
                                    <button class="btn btn-primary" id="btnCompleteInstall">
                                        Finish
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Page 4: Installation Complete -->
            <div class="card card-gutter-lg rounded-4 card-auth installer-page" id="page4">
                <div class="card-body text-center mt-2">
                    <div class="m-2">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 50px; height: 50px;" viewBox="0 0 24 24" fill="none" stroke="#5f38f9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>
                    </div>
                    <h3 class="nk-block-title mb-2">Installation Complete!</h3>
                    <p class="mb-4">Profess0rPay has been successfully installed and configured.</p>
                    
                    <div class="installation-log mb-4" id="installationLog">
                        <!-- Installation log will appear here -->
                    </div>

                    <div class="alert alert-warning mb-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0"><em class="icon ni ni-alert-fill"></em></div>
                            <div class="flex-grow-1 ms-2">
                                <h4 class="mb-1">Important Security Notice</h4>
                                <p class="mb-0">For security reasons, please delete or rename the <code>pp-install</code> directory after installation.</p>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="login" class="btn btn-primary" id="btnGoToDashboard">
                            <em class="icon ni ni-dashboard me-1"></em>
                            Go to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="<?php echo $site_url ?>assets/js/tabler.min.js"></script>
    <script src="<?php echo $site_url ?>assets/js/jquery-3.6.4.min.js"></script>
    <script src="<?php echo $site_url ?>assets/js/custom-toast.js?v=1.2"></script>

    <script data-cfasync="false">
        let currentStep = 1;
        const totalSteps = 4;

        function showStep(step) {
            // Remove active from all pages inside .all-pages
            document.querySelectorAll('.all-pages').forEach(card => {
                card.querySelectorAll('.active').forEach(child => {
                    child.classList.remove('active');
                });
            });

            // Activate current page
            const page = document.getElementById('page' + step);
            if (page) {
                page.classList.add('active');
            }

            // Update step indicators
            document.querySelectorAll('.steps .step-item').forEach(item => {
                item.classList.remove('active', 'completed');

                const itemStep = parseInt(item.dataset.step);

                if (itemStep < step) {
                    item.classList.add('completed');
                } else if (itemStep === step) {
                    item.classList.add('active');
                }
            });

            currentStep = step;
        }

        document.getElementById('btnCheckRequirements')?.addEventListener('click', () => {
            showStep(2);
        });

        document.getElementById('btnPrevToRequirements')?.addEventListener('click', () => {
            showStep(1);
        });

        document.getElementById('btnNextToAdmin')?.addEventListener('click', () => {
            showStep(3);
        });

        document.getElementById('btnPrevToDatabase')?.addEventListener('click', () => {
            showStep(2);
        });

        <?php
           if(file_exists(__DIR__ . '/../../pp-temp-config.php')){
        ?>
              showStep(3);
        <?php
           }
        ?>

        $(document).ready(function() {
            $('.database-config-extra input[name="dbDriver"]').on('change', function () {
                $('.database-config-extra input[name="dbDriver"]').not(this).prop('checked', false);
            });

            $('#btnTestConnection').on('click', function () {

                let btn = $('.test-connection-btn');

                let driver = $('input[name="dbDriver"]:checked').val();
                if (!driver) {
                    createToast({
                        title: 'Action Required',
                        description: 'Please select a database driver.',
                        svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                        timeout: 6000,
                        top: 20
                    });
                    return;
                }

                let data = {
                    test_databse_request: true,
                    dbDriver: driver,
                    dbHost: $('#dbHost').val(),
                    dbPort: $('#dbPort').val(),
                    dbName: $('#dbName').val(),
                    dbUsername: $('#dbUsername').val(),
                    dbPassword: $('#dbPassword').val(),
                    tablePrefix: $('#tablePrefix').val()
                };

                btn.html('<span class="spinner-border spinner-border-sm"></span>');

                $.ajax({
                    url: 'install',
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function (response) {
                        btn.text('Check & Import');

                        if (response.status === true || response.status === 'true') {
                            $('#btnNextToAdmin').prop('disabled', false);

                            createToast({
                                title: `${response.title}`,
                                description: `${response.message}`,
                                svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#5f38f9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>`,
                                timeout: 6000,
                                top: 20
                            });
                        } else {
                            createToast({
                                title: 'Action Required',
                                description: `${response.message}`,
                                svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                                timeout: 6000,
                                top: 20
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log('AJAX Error:');
                        console.log('Status:', status);
                        console.log('Error:', error);            
                        console.log('Response Text:', xhr.responseText); 
                        console.log('XHR Object:', xhr);          

                        btn.text('Check & Import');

                        createToast({
                            title: 'Action Required',
                            description: 'Something went wrong.',
                            svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                            timeout: 6000,
                            top: 20
                        });
                    }
                });
            });

            const passwordInput = document.getElementById('adminPassword');
            const strengthText = document.getElementById('passwordStrength');
            const strengthMeter = document.getElementById('passwordStrengthMeter');

            passwordInput.addEventListener('input', function() {
                const value = passwordInput.value;
                let score = 0;

                if (value.length >= 8) score++; // length check
                if (/[A-Z]/.test(value)) score++; // uppercase
                if (/[a-z]/.test(value)) score++; // lowercase
                if (/[0-9]/.test(value)) score++; // number
                if (/[\W]/.test(value)) score++; // special character

                let strength = 'None';
                let color = 'red';
                let width = (score / 5) * 100 + '%';

                switch(score) {
                    case 1: strength = 'Very Weak'; color = 'red'; break;
                    case 2: strength = 'Weak'; color = 'orange'; break;
                    case 3: strength = 'Medium'; color = 'yellow'; break;
                    case 4: strength = 'Strong'; color = 'lightgreen'; break;
                    case 5: strength = 'Very Strong'; color = 'green'; break;
                    default: strength = 'None'; color = 'red';
                }

                strengthText.textContent = strength;
                strengthText.style.color = color;
                strengthMeter.style.width = width;
                strengthMeter.style.background = color;
            });

            $('#adminForm').submit(function(e) {
                e.preventDefault(); 

                var btn = document.querySelector("#btnCompleteInstall").innerHTML;

                document.querySelector("#btnCompleteInstall").innerHTML = '<div class="spinner-border spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span> </div>';

                var formData = $(this).serialize();

                $.ajax({
                    type: 'POST',
                    url: 'install', 
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        document.querySelector("#btnCompleteInstall").innerHTML = btn;

                        if (response.status == 'true') {
                            showStep(4);
                        } else {
                            createToast({
                                title: 'Action Required',
                                description: `${response.message}`,
                                svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                                timeout: 6000,
                                top: 20
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        createToast({
                            title: 'Action Required',
                            description: 'Something went wrong.',
                            svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                            timeout: 6000,
                            top: 20
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>