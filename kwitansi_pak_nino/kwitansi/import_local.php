<?php
try {
    $db = new PDO('mysql:host=localhost;port=3306', 'root', '123456789');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = file_get_contents('kwitansi.sql');
    $db->exec($sql);
    echo "SUCCESS\n";
} catch(Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
