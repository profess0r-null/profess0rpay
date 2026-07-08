<?php
$zipFile = 'Profess0rPay-v1.2.3.zip';
if (file_exists($zipFile)) {
    unlink($zipFile);
}

$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    die("Failed to create zip");
}

$exclude = [
    '.git',
    '.agents',
    'pp-config.php',
    'hotfix.zip',
    'updater_fix.zip',
    'project_recovery_state.md',
    'scratch',
    'testzip'
];

$dir = __DIR__;
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file) {
    if (!$file->isDir()) {
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($dir) + 1);
        $relativePath = str_replace('\\', '/', $relativePath);

        $skip = false;
        foreach ($exclude as $ex) {
            if (strpos($relativePath, $ex) === 0 || strpos($relativePath, 'chat_history') === 0 || strpos($relativePath, 'Profess0rPay-') === 0 || strpos($relativePath, '.sql') !== false || strpos($relativePath, 'test') === 0 || strpos($relativePath, 'cleanup.php') === 0) {
                $skip = true;
                break;
            }
        }

        if (!$skip) {
            $zip->addFile($filePath, $relativePath);
        }
    }
}

$zip->close();
echo "Profess0rPay-v1.2.3.zip created successfully.";
