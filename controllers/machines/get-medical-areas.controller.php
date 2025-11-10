<?php
// controllers/machines/get-medical-areas.controller.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 🔒 Protección de sesión y roles
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';

// Solo administradores o técnicos pueden acceder
checkUserRole(['A', 'T']);

// Configuración de base de datos y modelo
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/machines/machines.model.php';

// Crear conexión y modelo
$pdo = getConnection();
$model = new MachineModel($pdo);

try {
    // Obtener todas las áreas médicas
    $medicalAreas = $model->getMedicalAreas();

    // Si se accede mediante AJAX o API (por ejemplo, para un select dinámico)
    if (isset($_GET['json'])) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($medicalAreas);
        exit;
    }

    // Si se incluye desde una vista (Smarty, PHP puro, etc.)
    return $medicalAreas;

} catch (Exception $e) {
    if (isset($_GET['json'])) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => $e->getMessage()]);
    } else {
        echo 'Error al obtener las áreas médicas: ' . htmlspecialchars($e->getMessage());
    }
    exit;
}
