<?php
// index.php
// Simple Front Controller
date_default_timezone_set('Asia/Jakarta');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);

// Include configuration
require_once __DIR__ . '/config/database.php';

// Simple Router
$controller = isset($_GET['c']) ? $_GET['c'] : 'dashboard';
$action = isset($_GET['a']) ? $_GET['a'] : 'index';

// Note: In PHP 5.4 we don't have ucfirst directly if it's complex, but ucfirst() is available.
$controllerName = ucfirst($controller) . 'Controller';
$controllerFile = __DIR__ . '/controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    if (class_exists($controllerName)) {
        $ctrl = new $controllerName($conn);
        if (method_exists($ctrl, $action)) {
            $ctrl->$action();
        } else {
            die("Action '$action' not found in $controllerName.");
        }
    } else {
        die("Class '$controllerName' not found.");
    }
} else {
    die("Controller file '$controllerFile' not found.");
}
