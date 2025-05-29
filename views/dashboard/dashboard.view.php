<?php
// Include session controller to protect this route
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Smarty\Smarty;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/dashboard/dashboard.controller.php';

// Inicializar Smarty
$smarty = new Smarty;
$smarty->setTemplateDir(__DIR__);

// Inicializar el controlador del dashboard
$dashboardController = new DashboardController($pdo);

// Obtener datos del dashboard a través del controlador
$dashboardData = $dashboardController->getDashboardData();

// Obtener mensajes de error o éxito de la URL
$error = isset($_GET['error']) ? $_GET['error'] : null;
$success = isset($_GET['success']) ? $_GET['success'] : null;

// Asignar variables a la plantilla
$smarty->assign('doctors', $dashboardData['doctors']);
$smarty->assign('appointments', $dashboardData['appointments']);
$smarty->assign('error', $error);
$smarty->assign('success', $success);
$smarty->assign('isDoctor', $_SESSION['role'] === 'D');
$smarty->assign('userEmail', $_SESSION['usuario']);

// Mostrar la plantilla
$smarty->display('dashboard.view.tpl');
