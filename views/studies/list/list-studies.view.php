<?php
// controllers/auth/session.controller.php protege la ruta
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';

// Solo administradores o técnicos pueden acceder
checkUserRole(['A', 'T']);

use Smarty\Smarty;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/studies/studies.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/studies/studies.model.php';

// Inicializar Smarty
$smarty = new Smarty();
$smarty->setTemplateDir(__DIR__);
$smarty->setCompileDir(__DIR__ . '/templates_c');

// Crear directorio de compilación si no existe
if (!file_exists(__DIR__ . '/templates_c')) {
    mkdir(__DIR__ . '/templates_c', 0777, true);
}

// Inicializar modelo y cargar datos
$pdo = getConnection();
$model = new StudyModel($pdo);
$viewData = getStudyViewData($model);

// Rutas
$sidebarPath = $_SERVER['DOCUMENT_ROOT'] . '/views/components/sidebar.tpl';

// Asignar variables a Smarty
$smarty->assign([
    'sidebarPath'   => $sidebarPath,
    'allStudies'    => $viewData['allStudies'],
    'studyData'     => $viewData['studyData'] ?? null,
    'success'       => $_GET['success'] ?? null,
    'error'         => $_GET['error'] ?? null
]);

// Renderizar plantilla
$smarty->display('list-studies.view.tpl');
