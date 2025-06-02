<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/patient/patient.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/medical_history/medical-history.model.php';
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

// Verificar que se haya enviado el ID del paciente
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['patient_id']) || !is_numeric($_GET['patient_id'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Debe proporcionar un ID de paciente válido'
    ]);
    exit;
}

try {
    $patientId = (int)$_GET['patient_id'];

    // Instanciar los modelos
    $patientModel = new PatientModel($pdo);
    $medicalHistoryModel = new MedicalHistoryModel($pdo);

    // Obtener datos del paciente
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = :id AND status != 'I'");
    $stmt->execute([':id' => $patientId]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Paciente no encontrado o inactivo'
        ]);
        exit;
    }

    // Obtener historial médico del paciente
    $history = $medicalHistoryModel->getPatientHistory($patientId);

    // Obtener citas del paciente
    $appointments = $medicalHistoryModel->getPatientAppointments($patientId);

    // Obtener documentos PDF del paciente
    $documents = $medicalHistoryModel->getPatientDocuments($patientId);

    // Devolver resultados
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'patient' => $patient,
        'history' => $history,
        'appointments' => $appointments,
        'documents' => $documents
    ]);
} catch (Exception $e) {
    // Registrar el error
    error_log('Error al obtener historial del paciente: ' . $e->getMessage());

    // Devolver mensaje de error
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener historial del paciente: ' . $e->getMessage()
    ]);
}
