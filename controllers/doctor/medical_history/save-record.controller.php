<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/medical_history/medical-history.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/utils.php';

// Inicializar conexión a la base de datos
$pdo = getConnection();

// Verificar si hay un error de conexión a la base de datos
if ($pdo === null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Hubo un problema al conectar con la base de datos. Por favor, inténtelo de nuevo más tarde.'
    ]);
    exit;
}

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
        'date_created' => $_POST['record-date'] . ' ' . date('H:i:s'),
        'chief_complaint' => $_POST['chief-complaint'] ?? null,
        'current_illness' => $_POST['current-illness'] ?? null,
        'personal_history' => $_POST['personal-history'] ?? null,
        'family_history' => $_POST['family-history'] ?? null,
        'physical_examination' => $_POST['physical-examination'] ?? null,
        'diagnosis' => $_POST['diagnosis'] ?? null,
        'treatment_plan' => $_POST['treatment-plan'] ?? null,
        'observations' => $_POST['observations'] ?? null,
        'next_appointment' => !empty($_POST['next-appointment']) ? $_POST['next-appointment'] : null
    ];

    // Preparar datos de signos vitales si se proporcionaron
    if (isset($_POST['temperature']) || isset($_POST['blood-pressure']) || 
        isset($_POST['heart-rate']) || isset($_POST['respiratory-rate']) || 
        isset($_POST['weight']) || isset($_POST['height']) || 
        isset($_POST['oxygen-saturation']) || isset($_POST['glucose-level'])) {

        $data['vital_signs'] = [
            'temperature' => $_POST['temperature'] ?? null,
            'blood_pressure' => $_POST['blood-pressure'] ?? null,
            'heart_rate' => $_POST['heart-rate'] ?? null,
            'respiratory_rate' => $_POST['respiratory-rate'] ?? null,
            'weight' => $_POST['weight'] ?? null,
            'height' => $_POST['height'] ?? null,
            'bmi' => isset($_POST['weight']) && isset($_POST['height']) && !empty($_POST['weight']) && !empty($_POST['height']) ? 
                    round($_POST['weight'] / (($_POST['height']/100) * ($_POST['height']/100)), 2) : null,
            'oxygen_saturation' => $_POST['oxygen-saturation'] ?? null,
            'glucose_level' => $_POST['glucose-level'] ?? null,
            'measured_at' => date('Y-m-d H:i:s')
        ];
    }

    // Validar datos mínimos requeridos
    if (empty($data['date_created'])) {
        throw new Exception('La fecha es obligatoria');
    }

    if (empty($data['diagnosis']) && empty($data['chief_complaint'])) {
        throw new Exception('El diagnóstico o la queja principal son obligatorios');
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
