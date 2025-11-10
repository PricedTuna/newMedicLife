<?php
// controllers/machines/get-machine-windows.controller.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Protege la ruta (solo Admin y Técnicos)
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';
checkUserRole(['A', 'T']);

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/machines/machines.model.php';

// Inicializar conexión y modelo
$pdo = getConnection();
$model = new MachineModel($pdo);

// Validar que se reciba el id_machine
$id_machine = isset($_GET['id_machine']) && is_numeric($_GET['id_machine']) ? (int) $_GET['id_machine'] : null;

if (!$id_machine) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Se requiere un ID de máquina válido.'
    ]);
    exit;
}

// Obtener ventanas operativas
$windows = $model->getOperationalWindows($id_machine);

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'data' => $windows
]);
exit;
