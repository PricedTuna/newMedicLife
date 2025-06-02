<?php
use Smarty\Smarty;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/patient/patient.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/medical_history/medical-history.model.php';

// Inicializar conexión a la base de datos
$pdo = getConnection();

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
if (isset($_GET['patient_id']) && is_numeric($_GET['patient_id'])) {
    $patientId = (int)$_GET['patient_id'];

    // Obtener datos del paciente
    $patientModel = new PatientModel($pdo);
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = :id AND status != 'I'");
    $stmt->execute([':id' => $patientId]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($patient) {
        // Pasar datos del paciente a la plantilla
        $smarty->assign('patient', $patient);
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

// Pasar datos a la plantilla
$smarty->assign('doctorId', $doctorId);
$smarty->assign('role', $_SESSION['role']);
$smarty->assign('user', $_SESSION['usuario']);

// Mostrar la plantilla
$smarty->display('doctor/medical_history/medical-history.view.tpl');
