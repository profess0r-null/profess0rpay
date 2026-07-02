<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

$install_lock = __DIR__ . '/install.lock';
if (file_exists($install_lock)) {
    die("<html><body style='font-family:sans-serif; text-align:center; padding-top:100px;'><h2>Installation already completed!</h2><p>Please delete the <b>install</b> folder for security reasons.</p></body></html>");
}

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$error = '';
$success = '';

// Helper to check requirements
function checkRequirements() {
    $req = [
        'php' => version_compare(PHP_VERSION, '8.1.0', '>='),
        'pdo' => extension_loaded('pdo_mysql'),
        'curl' => extension_loaded('curl'),
        'mbstring' => extension_loaded('mbstring'),
        'json' => extension_loaded('json'),
    ];
    return $req;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 2) {
        // Step 2: Database Connection Check
        $db_host = $_POST['db_host'] ?? 'localhost';
        $db_name = $_POST['db_name'] ?? '';
        $db_user = $_POST['db_user'] ?? '';
        $db_pass = $_POST['db_pass'] ?? '';

        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Save in session for next step
            $_SESSION['db_host'] = $db_host;
            $_SESSION['db_name'] = $db_name;
            $_SESSION['db_user'] = $db_user;
            $_SESSION['db_pass'] = $db_pass;
            
            header('Location: ?step=3');
            exit;
        } catch (PDOException $e) {
            $error = "Database Connection Failed: " . $e->getMessage();
        }
    } elseif ($step === 3) {
        // Step 3, 4, 5, 6: Import SQL, Create Admin, Generate config, Lock installer
        $admin_name = $_POST['admin_name'] ?? '';
        $admin_email = $_POST['admin_email'] ?? '';
        $admin_pass = $_POST['admin_pass'] ?? '';

        $db_host = $_SESSION['db_host'] ?? '';
        $db_name = $_SESSION['db_name'] ?? '';
        $db_user = $_SESSION['db_user'] ?? '';
        $db_pass = $_SESSION['db_pass'] ?? '';

        if (empty($admin_email) || empty($admin_pass) || empty($db_name)) {
            $error = "All fields are required!";
        } else {
            try {
                $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Step 4: Import SQL
                $sql_file = __DIR__ . '/database.sql';
                if (file_exists($sql_file)) {
                    $sql = file_get_contents($sql_file);
                    // Basic split for queries
                    $queries = explode(';', $sql);
                    foreach ($queries as $query) {
                        $query = trim($query);
                        if (!empty($query)) {
                            $pdo->exec($query);
                        }
                    }
                } else {
                    throw new Exception("database.sql file not found!");
                }

                // Step 5: Admin Account Creation
                $hashed_pass = md5($admin_pass); // Match existing system's hashing
                $stmt = $pdo->prepare("DELETE FROM pp_admin"); // Clear existing admins
                $stmt->execute();

                $stmt = $pdo->prepare("INSERT INTO pp_admin (name, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$admin_name, $admin_email, $hashed_pass]);

                // Update Config File
                $config_file = dirname(__DIR__) . '/pp-config.php';
                $config_content = "<?php\n";
                $config_content .= "    \$db_host = '$db_host';\n";
                $config_content .= "    \$db_port = '3306';\n";
                $config_content .= "    \$db_user = '$db_user';\n";
                $config_content .= "    \$db_pass = '$db_pass';\n";
                $config_content .= "    \$db_name = '$db_name';\n";
                $config_content .= "    \$db_prefix = 'pp_';\n";
                $config_content .= "?>\n";
                file_put_contents($config_file, $config_content);

                // Step 6: Create install.lock
                file_put_contents($install_lock, 'locked');
                
                // Clear session
                session_destroy();
                
                header('Location: ?step=4');
                exit;

            } catch (Exception $e) {
                $error = "Installation Error: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PipraPay Setup Wizard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f4f7fe; }
        .step-active { background: #e2136e; color: white; border-color: #e2136e; }
        .step-inactive { background: white; color: #9ca3af; border-color: #d1d5db; }
        .step-completed { background: #10b981; color: white; border-color: #10b981; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-3xl bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row min-h-[500px]">
        <!-- Sidebar Steps -->
        <div class="w-full md:w-1/3 bg-gray-50 border-r border-gray-100 p-8 flex flex-col">
            <h2 class="text-xl font-bold text-gray-800 mb-8 flex items-center gap-2">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#e2136e" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                PipraPay
            </h2>
            
            <div class="relative flex-1">
                <div class="absolute left-[15px] top-4 bottom-4 w-0.5 bg-gray-200"></div>
                
                <div class="relative z-10 flex flex-col gap-8">
                    <!-- Step 1 -->
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center font-bold text-sm <?= $step > 1 ? 'step-completed' : ($step == 1 ? 'step-active' : 'step-inactive') ?>">
                            <?= $step > 1 ? '✓' : '1' ?>
                        </div>
                        <span class="font-medium <?= $step >= 1 ? 'text-gray-900' : 'text-gray-400' ?>">Requirements</span>
                    </div>
                    <!-- Step 2 -->
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center font-bold text-sm <?= $step > 2 ? 'step-completed' : ($step == 2 ? 'step-active' : 'step-inactive') ?>">
                            <?= $step > 2 ? '✓' : '2' ?>
                        </div>
                        <span class="font-medium <?= $step >= 2 ? 'text-gray-900' : 'text-gray-400' ?>">Database</span>
                    </div>
                    <!-- Step 3 -->
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center font-bold text-sm <?= $step > 3 ? 'step-completed' : ($step == 3 ? 'step-active' : 'step-inactive') ?>">
                            <?= $step > 3 ? '✓' : '3' ?>
                        </div>
                        <span class="font-medium <?= $step >= 3 ? 'text-gray-900' : 'text-gray-400' ?>">Admin Setup</span>
                    </div>
                    <!-- Step 4 -->
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center font-bold text-sm <?= $step == 4 ? 'step-completed' : 'step-inactive' ?>">
                            <?= $step == 4 ? '✓' : '4' ?>
                        </div>
                        <span class="font-medium <?= $step == 4 ? 'text-gray-900' : 'text-gray-400' ?>">Finish</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="w-full md:w-2/3 p-8">
            
            <?php if($error): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm font-medium">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- STEP 1: REQUIREMENTS -->
            <?php if($step == 1): 
                $reqs = checkRequirements();
                $all_passed = !in_array(false, $reqs, true);
            ?>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Server Requirements</h3>
                <p class="text-gray-500 mb-8 text-sm">Please ensure your server meets all the requirements below to proceed.</p>
                
                <div class="space-y-4 mb-8">
                    <div class="flex justify-between items-center p-4 border rounded-lg <?= $reqs['php'] ? 'border-green-100 bg-green-50' : 'border-red-100 bg-red-50' ?>">
                        <span class="font-medium text-gray-700">PHP 8.1+</span>
                        <span class="<?= $reqs['php'] ? 'text-green-600' : 'text-red-600' ?> font-bold"><?= $reqs['php'] ? 'Passed' : 'Failed' ?></span>
                    </div>
                    <div class="flex justify-between items-center p-4 border rounded-lg <?= $reqs['pdo'] ? 'border-green-100 bg-green-50' : 'border-red-100 bg-red-50' ?>">
                        <span class="font-medium text-gray-700">PDO MySQL Extension</span>
                        <span class="<?= $reqs['pdo'] ? 'text-green-600' : 'text-red-600' ?> font-bold"><?= $reqs['pdo'] ? 'Passed' : 'Failed' ?></span>
                    </div>
                    <div class="flex justify-between items-center p-4 border rounded-lg <?= $reqs['curl'] ? 'border-green-100 bg-green-50' : 'border-red-100 bg-red-50' ?>">
                        <span class="font-medium text-gray-700">cURL Extension</span>
                        <span class="<?= $reqs['curl'] ? 'text-green-600' : 'text-red-600' ?> font-bold"><?= $reqs['curl'] ? 'Passed' : 'Failed' ?></span>
                    </div>
                </div>

                <div class="flex justify-end">
                    <?php if($all_passed): ?>
                        <a href="?step=2" class="px-6 py-2.5 bg-[#e2136e] hover:bg-[#c90f5f] text-white font-medium rounded-lg transition-colors">Continue</a>
                    <?php else: ?>
                        <button disabled class="px-6 py-2.5 bg-gray-300 text-gray-500 font-medium rounded-lg cursor-not-allowed">Fix errors to continue</button>
                    <?php endif; ?>
                </div>
            
            <!-- STEP 2: DATABASE -->
            <?php elseif($step == 2): ?>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Database Setup</h3>
                <p class="text-gray-500 mb-8 text-sm">Enter your MySQL database connection details.</p>
                
                <form method="POST" action="?step=2" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Database Host</label>
                        <input type="text" name="db_host" value="localhost" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e2136e] focus:border-[#e2136e] outline-none transition" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Database Name</label>
                        <input type="text" name="db_name" placeholder="e.g. piprapay_db" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e2136e] focus:border-[#e2136e] outline-none transition" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Database User</label>
                            <input type="text" name="db_user" placeholder="e.g. root" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e2136e] focus:border-[#e2136e] outline-none transition" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Database Password</label>
                            <input type="password" name="db_pass" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e2136e] focus:border-[#e2136e] outline-none transition">
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-6">
                        <a href="?step=1" class="text-gray-500 hover:text-gray-700 font-medium text-sm">Back</a>
                        <button type="submit" class="px-6 py-2.5 bg-[#e2136e] hover:bg-[#c90f5f] text-white font-medium rounded-lg transition-colors flex items-center gap-2">
                            Connect Database
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </form>

            <!-- STEP 3: ADMIN SETUP -->
            <?php elseif($step == 3): ?>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Admin Account</h3>
                <p class="text-gray-500 mb-8 text-sm">Create the super admin account to manage your gateway.</p>
                
                <form method="POST" action="?step=3" class="space-y-5" id="adminForm">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Admin Name</label>
                        <input type="text" name="admin_name" value="Admin" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e2136e] focus:border-[#e2136e] outline-none transition" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="admin_email" placeholder="admin@domain.com" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e2136e] focus:border-[#e2136e] outline-none transition" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="admin_pass" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#e2136e] focus:border-[#e2136e] outline-none transition" required minlength="6">
                    </div>

                    <div id="installing-ui" style="display:none;" class="text-center py-4">
                        <svg class="animate-spin h-8 w-8 text-[#e2136e] mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <p class="text-sm text-gray-600 font-medium">Installing Database... Please wait.</p>
                    </div>

                    <div class="flex justify-between items-center pt-6" id="btn-group">
                        <a href="?step=2" class="text-gray-500 hover:text-gray-700 font-medium text-sm">Back</a>
                        <button type="submit" onclick="document.getElementById('installing-ui').style.display='block'; document.getElementById('btn-group').style.display='none';" class="px-6 py-2.5 bg-[#e2136e] hover:bg-[#c90f5f] text-white font-medium rounded-lg transition-colors flex items-center gap-2">
                            Install PipraPay
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </form>

            <!-- STEP 4: SUCCESS -->
            <?php elseif($step == 4): ?>
                <div class="text-center py-8">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-800 mb-2">Installation Successful!</h3>
                    <p class="text-gray-500 mb-8">PipraPay has been successfully installed on your server.</p>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-8 text-sm text-yellow-800 text-left flex gap-3 items-start">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="flex-shrink-0 mt-0.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <p><strong>Security Warning:</strong> For security reasons, please delete the <b>install</b> folder immediately from your server file manager.</p>
                    </div>

                    <a href="../pp-admin/" class="inline-block px-8 py-3 bg-[#e2136e] hover:bg-[#c90f5f] text-white font-bold rounded-lg transition-colors shadow-lg shadow-pink-200">
                        Go to Admin Panel
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </div>

</body>
</html>
