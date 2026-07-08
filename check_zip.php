<?php
$z = new ZipArchive();
if ($z->open('Profess0rPay-v1.2.2.zip') === true) {
    for($i=0; $i<$z->numFiles; $i++) {
        echo $z->getNameIndex($i) . "\n";
        if($i>10) break;
    }
}
