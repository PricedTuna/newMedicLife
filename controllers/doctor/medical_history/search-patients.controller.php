<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/patient/patient.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/utils.php';

// Inicializar conexión a la base de datos
$pdo = getConnection();

// Verificar que el usuario tenga permisos (debe ser doctor o administrador)
session_start();
if (!isset($_SESSION['usuario']) || ($_SESSION['role'] !== 'D' && $_SESSION['role'] !== 'A')) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'No tiene permisos para realizar esta acción'
    ]);
    exit;
}

// Verificar que se haya enviado un término de búsqueda
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['search_term']) || empty($_POST['search_term'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Debe proporcionar un término de búsqueda'
    ]);
    exit;
}

try {
    $searchTerm = $_POST['search_term'];

    // Instanciar el modelo de pacientes
    $patientModel = new PatientModel($pdo);

    // Buscar pacientes por CURP o nombre
    $patients = $patientModel->searchPatients($searchTerm);

    // Devolver resultados
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'patients' => $patients
    ]);
} catch (Exception $e) {
    // Registrar el error
    error_log('Error al buscar pacientes: ' . $e->getMessage());

    // Devolver mensaje de error
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error al buscar pacientes: ' . $e->getMessage()
    ]);
}
