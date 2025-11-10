<?php
// views/machines/create/create-machine.view.php

// =============================
// 🧩 Protección de ruta
// =============================
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';
checkUserRole(['A']); // Solo administradores

// =============================
// ⚙️ Configuración general
// =============================
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/machines/machines.model.php';

// Controladores
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/machines/create-machine.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/machines/get-medical-areas.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/machines/machine-types.controller.php';

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
$model = new MachineModel($pdo);

// =============================
// 📋 Obtener datos para la vista
// =============================
$viewData = [
    'machine_types' => method_exists($model, 'getMachineTypes') ? $model->getMachineTypes() : [],
    'medical_areas' => method_exists($model, 'getMedicalAreas') ? $model->getMedicalAreas() : []
];

// =============================
// 🔎 Si se recibe un ID, buscar datos de la máquina
// =============================
$machineData = null;

$operationalWindows = []; // inicializa el array
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $machineId = (int) $_GET['id'];
    if (method_exists($model, 'getMachineById')) {
        $machineData = $model->getMachineById($machineId);
    }

    // ✅ Obtener ventanas operativas usando el método del modelo
    if (method_exists($model, 'getOperationalWindows')) {
        $operationalWindows = $model->getOperationalWindows($machineId);
    }
}

$typeData = null;
if (isset($_GET['id_type']) && !empty($_GET['id_type'])) {
    $typeId = (int) $_GET['id_type'];
    if (method_exists($model, 'getMachineTypeById')) {
        $typeData = $model->getMachineTypeById($typeId);
    }
}

// Determinar qué formulario mostrar
$formType = $_GET['form'] ?? 'machine';

// =============================
// 📦 Asignar variables a Smarty
// =============================
$sidebarPath = $_SERVER['DOCUMENT_ROOT'] . '/views/components/sidebar.tpl';

$smarty->assign([
    'machine_types' => $viewData['machine_types'],
    'medical_areas' => $viewData['medical_areas'],
    'sidebarPath' => $sidebarPath,
    'success' => $_GET['success'] ?? null,
    'error' => $_GET['error'] ?? null,
    'machineData' => $machineData,        // datos para edición
    'operationalWindows' => $operationalWindows,  // ventanas operativas
    'typeData' => $typeData,  // datos del tipo de máquina para edición
    'formType' => $formType,
]);

// =============================
// 🧾 Renderizar plantilla
// =============================
$smarty->display('create-machine.view.tpl');
