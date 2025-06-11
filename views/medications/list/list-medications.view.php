<?php
// Include session controller to protect this route
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';

// Only allow administrators to access this page
checkUserRole(['A']);

use Smarty\Smarty;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/medications/list-medications.controller.php';

// Inicializar Smarty
$smarty = new Smarty();
$smarty->setTemplateDir(__DIR__);
$smarty->setCompileDir(__DIR__ . '/templates_c');

// Crear directorio de compilación si no existe
if (!file_exists(__DIR__ . '/templates_c')) {
    mkdir(__DIR__ . '/templates_c', 0777, true);
}

// Inicializar controlador
$pdo = getConnection();
$controller = new MedicationsListController($pdo);

// Verificar permisos de administrador
if (!$controller->checkAdminPermission()) {
    header("Location: /");
    exit();
}

// Obtener datos para la vista
$viewData = $controller->getViewData();

// Verifica si vienen mensajes desde GET
$success = isset($_GET['success']) ? $_GET['success'] : null;
$message = isset($_GET['message']) ? $_GET['message'] : null;

// Rutas
$sidebarPath = $_SERVER['DOCUMENT_ROOT'] . '/views/components/sidebar.tpl';

// Asignar variables a Smarty
$smarty->assign('medications', $viewData['medications']);
$smarty->assign('medication_types', $viewData['medication_types']);
$smarty->assign('sidebarPath', $sidebarPath);
$smarty->assign('success', $success);
$smarty->assign('message', $message);
if (isset($viewData['search_term'])) {
    $smarty->assign('search_term', $viewData['search_term']);
}

// Renderizar plantilla
$smarty->display('list-medications.view.tpl');