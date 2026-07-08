<?php
$zipFile = 'Profess0rPay-v1.2.2-fixed.zip';
if (file_exists($zipFile)) {
    unlink($zipFile);
}

$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    die("Failed to create zip\n");
}

$basePath = realpath(__DIR__);
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($basePath, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

$ignoreDirs = ['.git', '.agents', '.github', 'scratch', 'check_zip'];
$ignoreFiles = ['Profess0rPay-v1.2.2.zip', 'Profess0rPay-v1.2.1.zip', 'Profess0rPay-v1.2.0.zip', 'updater_fix.zip', 'export_chat.php', 'test.php', 'test.txt', 'chat_history_backup.md', 'project_recovery_state.md', 'check_zip.php', 'pp-config.php', 'cleanup.php', $zipFile];

foreach ($iterator as $item) {
    $subPath = $iterator->getSubPathName();
    
    // Check ignore list
    $skip = false;
    foreach ($ignoreDirs as $ign) {
        if (str_starts_with($subPath, $ign . DIRECTORY_SEPARATOR) || $subPath === $ign) {
            $skip = true;
            break;
        }
    }
    if ($skip) continue;
    
    if (!$item->isDir() && in_array(basename($subPath), $ignoreFiles)) {
        continue;
    }
    
    if (!$item->isDir() && str_ends_with($subPath, '.sql')) {
        continue; // ignore DB backup
    }

    // Convert backslash to forward slash for Linux compatibility
    $localPath = str_replace('\\', '/', $subPath);
    
    if ($item->isDir()) {
        $zip->addEmptyDir($localPath);
    } else {
        $zip->addFile($item->getPathname(), $localPath);
    }
}

$zip->close();
echo "Fixed zip created successfully: " . $zipFile . "\n";
