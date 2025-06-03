<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/patient/patient.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/medical_history/medical-history.model.php';
use Smarty\Smarty;
// Inicializar conexión a la base de datos
$pdo = getConnection();

// Verificar si hay un error de conexión a la base de datos
if ($pdo === null || isset($_SESSION['db_error'])) {
    $error = isset($_SESSION['db_error']) ? $_SESSION['db_error'] : "Hubo un problema al conectar con la base de datos. Por favor, inténtelo de nuevo más tarde.";

    // Limpiar el mensaje de error para que no se muestre en futuras peticiones
    unset($_SESSION['db_error']);

    // Redirigir con mensaje de error
    header('Location: /views/dashboard/dashboard.view.php?error=' . urlencode($error));
    exit;
}

// Inicializar Smarty
$smarty = new Smarty();

$smarty->setTemplateDir($_SERVER['DOCUMENT_ROOT'] . '/views/');
$smarty->setCompileDir($_SERVER['DOCUMENT_ROOT'] . '/templates_c/');

// Verificar que el usuario tenga permisos (debe ser doctor o administrador)
if ($_SESSION['role'] !== 'D' && $_SESSION['role'] !== 'A') {
    header('Location: /views/dashboard/dashboard.view.php?error=No tiene permisos para acceder a esta página');
    exit;
}

// Verificar si se proporcionó un ID de paciente
$patientId = null;
$patient = null;
$history = [];
$appointments = [];
$documents = [];

if (isset($_GET['patient_id']) && is_numeric($_GET['patient_id'])) {
    $patientId = (int)$_GET['patient_id'];

    // Obtener datos del paciente
    $patientModel = new PatientModel($pdo);
    $medicalHistoryModel = new MedicalHistoryModel($pdo);

    try {
        $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = :id AND status != 'I'");
        $stmt->execute([':id' => $patientId]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($patient) {
            // Ensure CURP is available in both uppercase and lowercase
            if (isset($patient['curp']) && !isset($patient['CURP'])) {
                $patient['CURP'] = $patient['curp'];
            } elseif (isset($patient['CURP']) && !isset($patient['curp'])) {
                $patient['curp'] = $patient['CURP'];
            }

            // Get patient history
            try {
                $history = $medicalHistoryModel->getPatientHistory($patientId);
            } catch (Exception $e) {
                error_log("Error getting patient history: " . $e->getMessage());
                $history = [];
            }

            // Get patient appointments
            try {
                $appointments = $medicalHistoryModel->getPatientAppointments($patientId);
            } catch (Exception $e) {
                error_log("Error getting patient appointments: " . $e->getMessage());
                $appointments = [];
            }

            // Get patient documents
            try {
                $documents = $medicalHistoryModel->getPatientDocuments($patientId);
            } catch (Exception $e) {
                error_log("Error getting patient documents: " . $e->getMessage());
                $documents = [];
            }

            // Pasar datos del paciente a la plantilla
            $smarty->assign('patient', $patient);
            $smarty->assign('patientHistory', $history);
            $smarty->assign('patientAppointments', $appointments);
            $smarty->assign('patientDocuments', $documents);
            $smarty->assign('showPatientHistory', true);
        }
    } catch (Exception $e) {
        error_log("Error fetching patient data: " . $e->getMessage());
        $smarty->assign('error', "Error al obtener datos del paciente: " . $e->getMessage());
    }
}

// Obtener ID del doctor actual
$doctorId = null;
if ($_SESSION['role'] === 'D') {
    $stmt = $pdo->prepare("SELECT id_doctor FROM users WHERE email = :email");
    $stmt->execute([':email' => $_SESSION['usuario']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['id_doctor']) {
        $doctorId = $user['id_doctor'];
    }
}

// Obtener áreas médicas
$medical_areas = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM medical_areas");
    $stmt->execute();
    $medical_areas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error fetching medical areas: " . $e->getMessage());
}

// Obtener lista de doctores activos
$doctors = [];
try {
    $stmt = $pdo->query("SELECT 
        d.id AS doctor_id, 
        d.names AS doctor_name, 
        d.last_name, 
        d.last_name2, 
        d.specialty,
        ma.id AS medical_area_id,
        ma.name AS medical_area_name
    FROM doctors d
    LEFT JOIN doctor_assignments da ON d.id = da.id_doctor
    LEFT JOIN medical_areas ma ON ma.id = da.id_medical_area
    WHERE d.status != 'I' 
    ORDER BY d.names");

    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debug: Log the number of doctors found
    error_log("Number of doctors found: " . count($doctors));
} catch (Exception $e) {
    error_log("Error fetching doctors: " . $e->getMessage());
}

// Pasar datos a la plantilla
$smarty->assign('doctorId', $doctorId);
$smarty->assign('doctors', $doctors);
$smarty->assign('medical_areas', $medical_areas);
$smarty->assign('role', $_SESSION['role']);
$smarty->assign('user', $_SESSION['usuario']);
$smarty->assign('isDoctor', $_SESSION['role'] === 'D');

// Mostrar la plantilla
$smarty->display('doctor/medical_history/medical-history.view.tpl');
