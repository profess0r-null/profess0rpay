<?php
require 'pp-config.php';
$pdo = new PDO('mysql:host='.$databaseHost.';dbname='.$databaseName, $databaseUsername, $databasePassword);
$stmt = $pdo->query('SELECT ref, return_url FROM mhs_transaction ORDER BY id DESC LIMIT 1');
print_r($stmt->fetch(PDO::FETCH_ASSOC));
