<?php
require_once __DIR__ . '/app_common.php';

header('Content-Type: application/json');

session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode(array('status' => 'error', 'message' => 'Anda belum login.'));
    exit;
}

$db = connectDb();
$result = saveKwitansi($db, $_POST);

echo json_encode($result);
