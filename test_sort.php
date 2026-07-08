<?php
$pdo = new PDO('mysql:host=localhost;dbname=profess0rpay_db;charset=utf8mb4', 'root', '');
$stmt = $pdo->query("SELECT id, slug, display, sort_order FROM pp_gateways WHERE slug LIKE '%bkash%' AND status='active' ORDER BY sort_order ASC, id ASC");
$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($res);
