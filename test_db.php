<?php
require_once 'pp-config.php';
$pdo = new PDO("mysql:host=".$db_host.";dbname=".$db_name, $db_user, $db_pass);
$stmt = $pdo->query("SELECT * FROM ".$db_prefix."env WHERE option_name = 'dynamicNumericRoute'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
