<?php
    declare(strict_types=1);

    if (!defined('Profess0rPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
    }

    if ($global_user_login != true) {
        http_response_code(401);
        exit(json_encode(['status' => false, 'message' => 'Unauthorized']));
    }

    header('Content-Type: application/json');

    $action = $_POST['action'] ?? $_GET['action'] ?? '';

    if ($action === 'get_unread') {
        // Device Offline Check
        $devices = json_decode(getData($db_prefix.'device', ''), true);
        if (isset($devices['status']) && $devices['status'] == true) {
            foreach ($devices['response'] as $device) {
                $last_active = strtotime($device['updated_date']);
                $now = time();
                if (($now - $last_active) > 3600) {
                    // check if we already notified recently (in the last 24 hours) for this device
                    $recentAlerts = json_decode(getData($db_prefix.'notifications', "WHERE type='warning' AND title='Device Offline' AND message LIKE '%" . addslashes($device['device_name']) . "%' AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)"), true);
                    if (isset($recentAlerts['status']) && $recentAlerts['status'] == false && function_exists('addNotification')) {
                        addNotification("Device Offline", "Device '" . $device['device_name'] . "' has been offline for over an hour. Check battery or internet!", "warning");
                    }
                }
            }
        }

        // System Update Check
        $updateLockFile = realpath(__DIR__ . '/../../pp-content/tmp') . '/last_update_check.txt';
        if (!file_exists($updateLockFile) || (time() - filemtime($updateLockFile)) > 43200) {
            @file_put_contents($updateLockFile, time());
            $updaterClass = realpath(__DIR__ . '/../../pp-include/class-updater.php');
            if ($updaterClass && file_exists($updaterClass)) {
                require_once $updaterClass;
                try {
                    $updater = new Profess0rPayUpdater();
                    $latestRelease = $updater->getLatestRelease();
                    if ($latestRelease && isset($latestRelease['tag_name'])) {
                        $currentVersion = '1.0.0';
                        $pdo = connectDatabase();
                        $stmt = $pdo->prepare("SELECT `value` FROM `{$db_prefix}env` WHERE `option_name` = 'pp_version'");
                        $stmt->execute();
                        if($val = $stmt->fetchColumn()) {
                            $currentVersion = $val;
                        }
                        if (version_compare($currentVersion, ltrim($latestRelease['tag_name'], 'v'), '<')) {
                            $recentUpdateAlerts = json_decode(getData($db_prefix.'notifications', "WHERE type='info' AND title='System Update Available' AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)"), true);
                            if (isset($recentUpdateAlerts['status']) && $recentUpdateAlerts['status'] == false && function_exists('addNotification')) {
                                addNotification("System Update Available", "New version " . $latestRelease['tag_name'] . " is available. Go to System Settings to update!", "info");
                            }
                        }
                    }
                } catch (Exception $e) {}
            }
        }

        $notifications = json_decode(getData($db_prefix.'notifications', 'ORDER BY id DESC LIMIT 10'), true);
        
        $unread_count_data = json_decode(getData($db_prefix.'notifications', 'WHERE is_read = 0'), true);
        $unread_count = ($unread_count_data['status'] == true) ? count($unread_count_data['response']) : 0;
        
        if ($notifications['status'] == true) {
            $tz = (isset($global_response_brand['response'][0]['timezone']) && $global_response_brand['response'][0]['timezone'] !== '--' && $global_response_brand['response'][0]['timezone'] !== '') ? $global_response_brand['response'][0]['timezone'] : 'Asia/Dhaka';
            foreach ($notifications['response'] as &$n) {
                if (function_exists('convertUTCtoUserTZ')) {
                    $n['time_formatted'] = convertUTCtoUserTZ($n['created_at'], $tz, 'h:i A');
                } else {
                    $n['time_formatted'] = date('h:i A', strtotime($n['created_at']));
                }
            }
            echo json_encode(['status' => true, 'count' => $unread_count, 'data' => $notifications['response']]);
        } else {
            echo json_encode(['status' => true, 'count' => 0, 'data' => []]);
        }
    } 
    elseif ($action === 'mark_read') {
        $id = $_POST['id'] ?? $_GET['id'] ?? '';
        if ($id === 'all') {
            updateData($db_prefix.'notifications', ['is_read'], [1], "is_read = 0");
        } else {
            updateData($db_prefix.'notifications', ['is_read'], [1], "id = '$id'");
        }
        echo json_encode(['status' => true]);
    }
    elseif ($action === 'clear_all') {
        deleteData($db_prefix.'notifications', "id > 0");
        echo json_encode(['status' => true]);
    } 
    else {
        echo json_encode(['status' => false, 'message' => 'Invalid action']);
    }
    exit;
?>
