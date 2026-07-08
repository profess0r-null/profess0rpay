<?php
require 'pp-config.php';
$pdo = new PDO('mysql:host='.$db_host.';dbname='.$db_name, $db_user, $db_pass);
$stmt = $pdo->query('SELECT name, slug, logo FROM '.$db_prefix.'gateways');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
