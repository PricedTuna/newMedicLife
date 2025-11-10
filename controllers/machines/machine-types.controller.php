<?php
// controllers/machines/machine-types.controller.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/machines/machines.model.php';

// Solo administradores y técnicos pueden acceder
checkUserRole(['A', 'T']);

// Inicializar conexión y modelo
$pdo = getConnection();
$model = new MachineModel($pdo);

try {
    // Obtener los tipos de máquina
    $machineTypes = $model->getMachineTypes();

    // Si se pide en formato JSON (AJAX)
    if (isset($_GET['format']) && $_GET['format'] === 'json') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => true, 'data' => $machineTypes]);
        exit;
    }

    // Si se usa desde una vista PHP normal, simplemente los devuelve
    return $machineTypes;

} catch (Exception $e) {
    // Manejo de error
    if (isset($_GET['format']) && $_GET['format'] === 'json') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }

    // Para vistas normales: redirige con mensaje de error
    header('Location: /views/machines/list/list-machines.view.php?error=' . urlencode($e->getMessage()));
    exit;
}
