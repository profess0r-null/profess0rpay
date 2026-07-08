<?php
require 'c:\xampp\htdocs\Profess0rPay\pp-content\pp-config\database.php';
require 'c:\xampp\htdocs\Profess0rPay\pp-content\pp-include\pp-functions.php';

$trx_res = json_decode(getData($db_prefix.'transaction', "WHERE ref = '868757763844407069295267587'"), true);
echo "With single quotes:\n";
print_r($trx_res['response'][0]['return_url'] ?? 'not found');
echo "\n";

$trx_res_2 = json_decode(getData($db_prefix.'transaction', "WHERE ref = \"868757763844407069295267587\""), true);
echo "With double quotes:\n";
print_r($trx_res_2['response'][0]['return_url'] ?? 'not found');
echo "\n";
