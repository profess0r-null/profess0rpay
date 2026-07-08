<?php
define('Profess0rPay_INIT', true);
require 'pp-config.php';
require 'pp-content/pp-include/pp-adapter.php'; // this sets up pdo and getData
$db_prefix = 'pp_';

try {
    $pdo = connectDatabase();
    $stmt = $pdo->query("SHOW COLUMNS FROM `{$db_prefix}device`");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    
    $stmt2 = $pdo->query("SHOW COLUMNS FROM `{$db_prefix}sms_data`");
    print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
} catch(\Throwable $e) {
    echo $e->getMessage();
}
