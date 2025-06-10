<?php
// obtener_doctores.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';

// Allow administrators, secretaries, and doctors to access appointments
checkUserRole(['A', 'S', 'D']);
use Smarty\Smarty;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';

$smarty = new Smarty();
$smarty->setTemplateDir(__DIR__);
$smarty->setCompileDir(__DIR__ . '/templates_c');

global $pdo;

// Check if the user is a doctor
$isDoctor = isset($_SESSION['role']) && $_SESSION['role'] === 'D';
$doctorId = $isDoctor && isset($_SESSION['doctorId']) ? $_SESSION['doctorId'] : null;

// Base SQL query
$sql = "SELECT
    ap.id as cita,
    p.names AS patient_name,
    p.last_name AS last_name,
    p.last_name2 AS last_name2,
    p.email AS patient_email,
    ma.name AS medical_area,
    d.names AS doctor_name,
    ap.appointment_date AS appointment_date,
    ap.status AS status
FROM appointments ap
INNER JOIN patients p on ap.id_patient = p.id
INNER JOIN doctors d on ap.id_doctor = d.id
INNER JOIN medical_areas ma on ma.id = ap.id_medical_area";

// If user is a doctor, only show their appointments
if ($isDoctor && $doctorId) {
    $sql .= " WHERE ap.id_doctor = :doctorId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':doctorId' => $doctorId]);
} else {
    $stmt = $pdo->query($sql);
}

$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Sort paid appointments (status 'F') by most recent date first
usort($appointments, function($a, $b) {
    // Only sort if both are paid appointments
    if ($a['status'] === 'F' && $b['status'] === 'F') {
        return strtotime($b['appointment_date']) - strtotime($a['appointment_date']);
    }
    return 0;
});

// Verifica si vienen mensajes desde GET
$success = isset($_GET['success']) ? $_GET['success'] : null;
$error = isset($_GET['error']) ? $_GET['error'] : null;

// Rutas
$sidebarPath = $_SERVER['DOCUMENT_ROOT'] . '/views/components/sidebar.tpl';

// Asignar variables a Smarty
$smarty->assign('appointments', $appointments);
$smarty->assign('sidebarPath', $sidebarPath);
$smarty->assign('success', $success);
$smarty->assign('error', $error);

// Renderizar plantilla
$smarty->display('list-appointments.view.tpl');
