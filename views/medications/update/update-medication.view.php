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
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/medications/update-medication.controller.php';

// Inicializar Smarty
$smarty = new Smarty();
$smarty->setTemplateDir(__DIR__);
$smarty->setCompileDir(__DIR__ . '/templates_c');

// Crear directorio de compilación si no existe
if (!file_exists(__DIR__ . '/templates_c')) {
    mkdir(__DIR__ . '/templates_c', 0777, true);
}

// Verificar que se haya proporcionado un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: /views/medications/list/list-medications.view.php?success=0&message=" . urlencode('ID no válido'));
    exit();
}

$id = (int)$_GET['id'];
$type = isset($_GET['type']) && $_GET['type'] === 'type' ? 'type' : 'medication';

// Inicializar controlador
$pdo = getConnection();
$controller = new UpdateMedicationController($pdo);

// Verificar permisos de administrador
if (!$controller->checkAdminPermission()) {
    header("Location: /");
    exit();
}

// Obtener datos para la vista
$viewData = $controller->getViewData($id, $type);

// Verificar si hay un error
if (isset($viewData['error'])) {
    header("Location: /views/medications/list/list-medications.view.php?success=0&message=" . urlencode($viewData['error']));
    exit();
}

// Rutas
$sidebarPath = $_SERVER['DOCUMENT_ROOT'] . '/views/components/sidebar.tpl';

// Asignar variables a Smarty
$smarty->assign('sidebarPath', $sidebarPath);
$smarty->assign('id', $id);
$smarty->assign('type', $type);

if ($type === 'type') {
    $smarty->assign('medication_type', $viewData['medication_type']);
} else {
    $smarty->assign('medication', $viewData['medication']);
    $smarty->assign('medication_types', $viewData['medication_types']);
}

// Si hay resultados del formulario, asignarlos a Smarty
if (isset($viewData['form_result'])) {
    $smarty->assign('form_result', $viewData['form_result']);
}

// Determinar qué plantilla mostrar
$template = 'update-medication.view.tpl';

// Renderizar plantilla
$smarty->display($template);