<?php
// index.php - Front Controller Router
session_start();
date_default_timezone_set('Asia/Jakarta'); // Fix PHP Timezone warning
require_once 'models/Database.php';

$controller = isset($_GET['c']) ? $_GET['c'] : 'dashboard';
$action = isset($_GET['a']) ? $_GET['a'] : 'index';

// Authentication Check
if (!isset($_SESSION['id_user']) && $controller !== 'auth') {
    header('Location: index.php?c=auth&a=login');
    exit;
}

// Simple Router
if ($controller == 'auth') {
    require_once 'controllers/Auth.php';
    $ctrl = new Auth();
} elseif ($controller == 'dashboard') {
    require_once 'controllers/Dashboard.php';
    $ctrl = new Dashboard();
} elseif ($controller == 'sales') {
    require_once 'controllers/Sales.php';
    $ctrl = new Sales();
} elseif ($controller == 'riwayat') {
    require_once 'controllers/Riwayat.php';
    $ctrl = new Riwayat();
} elseif ($controller == 'barang') {
    // Admin Only
    if($_SESSION['role'] !== 'admin') { die("Akses Ditolak."); }
    require_once 'controllers/Barang.php';
    $ctrl = new Barang();
} elseif ($controller == 'user') {
    // Admin Only
    if($_SESSION['role'] !== 'admin') { die("Akses Ditolak."); }
    require_once 'controllers/User.php';
    $ctrl = new User();
} elseif ($controller == 'laporan') {
    // Admin Only
    if($_SESSION['role'] !== 'admin') { die("Akses Ditolak."); }
    require_once 'controllers/Laporan.php';
    $ctrl = new Laporan();
} else {
    die("Controller not found.");
}

// Execute Action
if (method_exists($ctrl, $action)) {
    $ctrl->$action();
} else {
    die("Action not found.");
}
?>
