<?php
if (!defined('Profess0rPay_INIT')) {
    http_response_code(403);
    exit('Direct access not allowed');
}

// ob_clean clears any HTML output already buffered by pp-adapter.php
// so the response will be clean JSON only
if (ob_get_level()) {
    ob_clean();
}

set_time_limit(300);
ini_set('memory_limit', '512M');

// Catch any PHP fatal errors/warnings and return as JSON
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        if (ob_get_level()) ob_clean();
        echo json_encode(['status' => 'error', 'message' => 'PHP Fatal Error: ' . $error['message'] . ' in ' . basename($error['file']) . ':' . $error['line']]);
    }
});

require_once __DIR__ . '/../../../pp-include/class-updater.php';

$updater = new Profess0rPayUpdater();

$currentVersion = '1.0.0';
$pdo = connectDatabase();
$stmt = $pdo->prepare("SELECT `setting_value` FROM `{$db_prefix}settings` WHERE `setting_key` = 'pp_version'");
$stmt->execute();
if($val = $stmt->fetchColumn()) {
    $currentVersion = $val;
}

try {
    $targetVersion = null;
    $updater->lock();
    file_put_contents(__DIR__ . '/../../../../.maintenance', 'Maintenance mode active.');

    $release = $updater->getLatestRelease();
    if (!$release) throw new \Exception("Could not fetch release from GitHub.");
    
    $manifest = $updater->getManifest($release);
    if (!$manifest) throw new \Exception("update.json manifest missing from release.");

    $targetVersion = $manifest['version'] ?? str_replace('v', '', $release['tag_name']);

    if (version_compare($currentVersion, $targetVersion, '>=')) {
        throw new \Exception("System is already up to date.");
    }

    // Backup
    $updater->createBackup();

    // Download
    $zipUrl = '';
    foreach ($release['assets'] as $asset) {
        if (str_ends_with($asset['name'], '.zip') && $asset['name'] !== 'update.json') {
            $zipUrl = $asset['browser_download_url'];
            break;
        }
    }
    if (!$zipUrl) throw new \Exception("No update.zip found in release.");
    
    $tmpZip = $updater->download($zipUrl);

    // Checksum
    if (isset($manifest['checksum'])) {
        if (!$updater->verifyChecksum($tmpZip, $manifest['checksum'])) {
            throw new \Exception("Checksum verification failed!");
        }
    }

    // Extract & Swap
    $updater->extractAndSwap($tmpZip);

    // Update version in DB
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM `{$db_prefix}env` WHERE `option_name` = 'pp_version'");
    $stmt->execute();
    if($stmt->fetchColumn() > 0) {
        $pdo->prepare("UPDATE `{$db_prefix}env` SET `value` = ? WHERE `option_name` = 'pp_version'")->execute([$targetVersion]);
    } else {
        $pdo->prepare("INSERT INTO `{$db_prefix}env` (`option_name`, `value`) VALUES ('pp_version', ?)")->execute([$targetVersion]);
    }
    
    // Log success
    try {
        $pdo->prepare("INSERT INTO `{$db_prefix}update_logs` (`version`, `status`, `log`) VALUES (?, 'Success', 'Update completed atomically.')")->execute([$targetVersion]);
    } catch (\Throwable $e) {}

    @unlink(__DIR__ . '/../../../../.maintenance');
    $updater->unlock();
    
    if (ob_get_level()) ob_clean();
    echo json_encode(['status' => 'success']);

} catch (\Throwable $e) {
    @unlink(__DIR__ . '/../../../../.maintenance');
    $updater->unlock();
    
    $v = $targetVersion ?? 'unknown';
    $msg = $e->getMessage();
    try {
        $pdo->prepare("INSERT INTO `{$db_prefix}update_logs` (`version`, `status`, `log`) VALUES (?, 'Failed', ?)")->execute([$v, $msg]);
    } catch (\Throwable $ex) {}
    
    if (ob_get_level()) ob_clean();
    echo json_encode(['status' => 'error', 'message' => $msg]);
}
