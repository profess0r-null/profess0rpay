<?php
define('Profess0rPay_INIT', true);
require 'pp-config.php';
require 'pp-content/pp-include/pp-functions.php';

$pdo = connectDatabase();

$action = 'mark_read';
$id = 'all';

if ($action === 'mark_read') {
    if ($id === 'all') {
        updateData($db_prefix.'notifications', ['is_read'], [1], "is_read = 0");
    } else {
        updateData($db_prefix.'notifications', ['is_read'], [1], "id = '$id'");
    }
    echo json_encode(['status' => true]);
} 

$notifications = json_decode(getData($db_prefix.'notifications', 'WHERE is_read = 0 ORDER BY id DESC LIMIT 10'), true);
echo "\nUnread count: " . count($notifications['response'] ?? []);
