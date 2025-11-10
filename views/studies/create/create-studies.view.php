<?php
// views/studies/create-study.view.php

// =============================
// 🧩 Protección de ruta
// =============================
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';
checkUserRole(['A', 'T']); // Solo administradores y técnicos

// =============================
// ⚙️ Configuración general
// =============================
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/studies/studies.model.php';

// Controlador
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/studies/studies.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/machines/list-machine.controller.php';

use Smarty\Smarty;

// Mostrar errores (solo en desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// =============================
// 🧠 Inicialización de Smarty
// =============================
$smarty = new Smarty();
$smarty->setTemplateDir(__DIR__);
$smarty->setCompileDir(__DIR__ . '/templates_c');

if (!file_exists(__DIR__ . '/templates_c')) {
    mkdir(__DIR__ . '/templates_c', 0777, true);
}

// =============================
// 🗄️ Inicializar conexión y modelo
// =============================
$pdo = getConnection();
$model = new StudyModel($pdo);
$machinesController = new MachinesListController($pdo);
$machinesData = $machinesController->getViewData();
// =============================
// 📋 Obtener datos para la vista
// =============================
$viewData = getStudyViewData($model);

// =============================
// 📦 Asignar variables a Smarty
// =============================
$sidebarPath = $_SERVER['DOCUMENT_ROOT'] . '/views/components/sidebar.tpl';

$smarty->assign([
    'sidebarPath' => $sidebarPath,
    'success' => $_GET['success'] ?? null,
    'error' => $_GET['error'] ?? null,
    'studyData' => $viewData['studyData'], // datos para edición
    'allStudies' => $viewData['allStudies'],
    'machines' => $machinesData['machines'], // lista de máquinas
]);

// =============================
// 🧾 Renderizar plantilla
// =============================
$smarty->display('create-studies.view.tpl');
