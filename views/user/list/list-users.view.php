<?php
// Include session controller to protect this route
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Smarty\Smarty;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/user.model.php';

$smarty = new Smarty();

$smarty->setTemplateDir(__DIR__);

// Obtener la lista de todos los usuarios
$users = getAllUsers();

$sidebarPath = $_SERVER['DOCUMENT_ROOT'] . '/views/components/sidebar.tpl';
$smarty->assign('error', $_GET['error'] ?? null);
$smarty->assign('success', $_GET['success'] ?? null);
$smarty->assign('sidebarPath', $sidebarPath);
$smarty->assign('users', $users);

$smarty->display('list-users.view.tpl');
?>
