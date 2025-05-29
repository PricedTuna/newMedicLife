<?php
// Include session controller to protect this route
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';

// Only administrators and secretaries can delete doctors
checkUserRole(['A', 'S']);

use Smarty\Smarty;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/patient/list-patients.controller.php';

// Inicializar Smarty
$smarty = new Smarty();
$smarty->setTemplateDir(__DIR__);
$smarty->setCompileDir(__DIR__ . '/templates_c');

// Inicializar el controlador de listado de pacientes
$patientListController = new PatientListController($pdo);

// Obtener datos para la vista a través del controlador
$viewData = $patientListController->getViewData();

// Verifica si vienen mensajes desde GET
$success = isset($_GET['success']) ? $_GET['success'] : null;
$error = isset($_GET['error']) ? $_GET['error'] : null;

// Rutas
$sidebarPath = $_SERVER['DOCUMENT_ROOT'] . '/views/components/sidebar.tpl';

// Asignar variables a Smarty
$smarty->assign('patients', $viewData['patients']);
$smarty->assign('sidebarPath', $sidebarPath);
$smarty->assign('success', $success);
$smarty->assign('error', $error);

// Renderizar plantilla
$smarty->display('list-patients.view.tpl');
