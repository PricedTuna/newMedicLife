<?php
header('Content-Type: application/json');
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/studies/patient-studies.model.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$patientId = $_GET['patient_id'] ?? null;
if (!$patientId || !is_numeric($patientId)) {
    echo json_encode(['success' => false, 'message' => 'patient_id inválido']);
    exit;
}

$pdo = getConnection();
if ($pdo === null) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit;
}

$model = new PatientStudiesModel($pdo);
try {
    $rows = $model->getByPatient((int)$patientId);
    // Debug: log number of rows returned
    error_log('[get-patient-studies] patient_id=' . $patientId . ' rows=' . count($rows));
    echo json_encode(['success' => true, 'studies' => $rows]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
