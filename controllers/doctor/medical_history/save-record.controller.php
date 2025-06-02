<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
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

// Verificar que se hayan enviado los datos necesarios
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['patient-id']) || !is_numeric($_POST['patient-id'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Datos incompletos o inválidos'
    ]);
    exit;
}

try {
    // Obtener ID del doctor actual
    $doctorId = null;

    if ($_SESSION['role'] === 'D') {
        // Si es un doctor, usar su ID
        $stmt = $pdo->prepare("SELECT id_doctor FROM users WHERE email = :email");
        $stmt->execute([':email' => $_SESSION['usuario']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !$user['id_doctor']) {
            throw new Exception('No se pudo determinar el ID del doctor');
        }

        $doctorId = $user['id_doctor'];
    } else {
        // Si es un administrador, verificar si se especificó un doctor
        if (!isset($_POST['doctor-id']) || !is_numeric($_POST['doctor-id'])) {
            throw new Exception('Debe especificar un doctor para el registro');
        }

        $doctorId = (int)$_POST['doctor-id'];
    }

    // Preparar datos para guardar
    $data = [
        'patient_id' => (int)$_POST['patient-id'],
        'doctor_id' => $doctorId,
        'appointment_id' => isset($_POST['appointment-id']) && !empty($_POST['appointment-id']) ? (int)$_POST['appointment-id'] : null,
        'record_date' => $_POST['record-date'],
        'diagnosis' => $_POST['diagnosis'],
        'observations' => $_POST['observations'],
        'treatment' => $_POST['treatment']
    ];

    // Validar datos
    if (empty($data['record_date'])) {
        throw new Exception('La fecha es obligatoria');
    }

    if (empty($data['diagnosis'])) {
        throw new Exception('El diagnóstico es obligatorio');
    }

    if (empty($data['observations'])) {
        throw new Exception('Las observaciones son obligatorias');
    }

    if (empty($data['treatment'])) {
        throw new Exception('El tratamiento es obligatorio');
    }

    // Instanciar el modelo
    $medicalHistoryModel = new MedicalHistoryModel($pdo);

    // Guardar el registro
    $recordId = $medicalHistoryModel->saveHistoryRecord($data);

    if (!$recordId) {
        throw new Exception('No se pudo guardar el registro');
    }

    // Devolver respuesta exitosa
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Registro guardado correctamente',
        'record_id' => $recordId
    ]);
} catch (Exception $e) {
    // Registrar el error
    error_log('Error al guardar registro médico: ' . $e->getMessage());

    // Devolver mensaje de error
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar registro: ' . $e->getMessage()
    ]);
}
