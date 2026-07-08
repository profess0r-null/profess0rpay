<?php
define('Profess0rPay_INIT', true);
$_SERVER['REQUEST_URI'] = '/payment/493626008542478350368174879?gateway=2474258794';
$_GET['gateway'] = '2474258794';
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'c:\xampp\htdocs\Profess0rPay\pp-config.php';
require 'c:\xampp\htdocs\Profess0rPay\pp-content\pp-include\pp-functions.php';

$data = [
    'brand' => ['id' => 'some_brand'],
    'lang' => ['pay_now' => 'Pay Now'],
    'options' => ['watermark_text' => 'watermark']
];

try {
    include 'c:\xampp\htdocs\Profess0rPay\pp-content\pp-modules\pp-themes\twenty-six\gateway.php';
} catch (Throwable $e) {
    echo "Caught Error: " . $e->getMessage() . " at " . $e->getFile() . " on line " . $e->getLine();
}
