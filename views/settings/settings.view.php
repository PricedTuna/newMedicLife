<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';
require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';

// Include Smarty
use Smarty\Smarty;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
$smarty = new Smarty();

$smarty->setTemplateDir($_SERVER['DOCUMENT_ROOT'] . '/views/settings/');
$smarty->setCompileDir($_SERVER['DOCUMENT_ROOT'] . '/views/settings/templates_c/');

try {
    // Check for success or error messages
    if (isset($_GET['success'])) {
        $smarty->assign('success', $_GET['success']);
    }

    if (isset($_GET['error'])) {
        $smarty->assign('error', $_GET['error']);
    }

    // Assign the sidebar path
    $sidebarPath = $_SERVER['DOCUMENT_ROOT'] . '/views/components/sidebar.tpl';
    $smarty->assign('sidebarPath', $sidebarPath);

    // Check if user is admin and load transfer configuration
    $isAdmin = isAdmin(false);
    $smarty->assign('isAdmin', $isAdmin);

    if ($isAdmin) {
        // Include transfer configuration controller
        $transferConfig = include $_SERVER['DOCUMENT_ROOT'] . '/controllers/pay/transfer_config.controller.php';
        $smarty->assign('transferConfig', $transferConfig);
    }

    // Display template
    $smarty->display('settings.view.tpl');
} catch (Exception $e) {
    $smarty->assign('error', $e->getMessage());
    $smarty->display('settings.view.tpl');
}
?>
