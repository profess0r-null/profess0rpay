<?php
$zipFile = 'hotfix.zip';
if (file_exists($zipFile)) {
    unlink($zipFile);
}

$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    die("Failed to create zip");
}

$files = [
    'pp-content/pp-include/pp-adapter.php',
    'pp-content/pp-include/pp-functions.php',
    'pp-content/pp-admin/index.php',
    'pp-content/pp-modules/pp-themes/twenty-six/gateway.php',
    'pp-content/pp-modules/pp-themes/twenty-six/checkout.php',
    'pp-content/pp-modules/pp-themes/twenty-six/checkout-status.php',
    'pp-content/pp-admin/pp-root/gateways/edit.php',
    'assets/images/bkash_merchant.jpg',
    'assets/images/bkash_agent.jpg',
    'assets/images/nagad_merchant.jpg',
    'assets/images/nagad_agent.jpg',
    'assets/images/rocket_merchant.jpg',
    'assets/images/rocket_agent.jpg',
    'assets/images/upay_merchant.jpg',
    'assets/images/upay_agent.jpg',
    'assets/images/cellfin_merchant.jpg',
    'assets/images/cellfin_agent.jpg'
];

foreach ($files as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        $zip->addFile(__DIR__ . '/' . $file, $file);
    }
}

$zip->close();
echo "hotfix.zip created successfully.";
