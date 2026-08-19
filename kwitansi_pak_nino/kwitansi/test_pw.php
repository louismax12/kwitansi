<?php
$pwds = ['', 'root', '1234', 'admin', '123456', 'password'];
foreach($pwds as $p) {
    try {
        $db = new PDO('mysql:host=localhost;port=3306', 'root', $p);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "SUCCESS WITH PASSWORD: '$p'\n";
        exit;
    } catch(Exception $e) {
    }
}
echo "FAILED TO FIND PASSWORD\n";
