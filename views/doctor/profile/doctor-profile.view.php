<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';
require $_SERVER['DOCUMENT_ROOT'] . '/models/user.model.php';
require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';

// Ensure $pdo is available
global $pdo;

// Include Smarty
use Smarty\Smarty;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
$smarty = new Smarty();

$smarty->setTemplateDir($_SERVER['DOCUMENT_ROOT'] . '/views/doctor/profile/');
$smarty->setCompileDir($_SERVER['DOCUMENT_ROOT'] . '/views/doctor/profile/templates_c/');

// Check if doctor ID is provided
if (!isset($_GET['id'])) {
    $smarty->assign('error', "ID de doctor no proporcionado");
    $smarty->display('doctor-profile.view.tpl');
    exit;
}

$doctorId = $_GET['id'];

try {
    // Get doctor data from database
    $stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = :id");
    $stmt->execute([':id' => $doctorId]);
    $doctorData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$doctorData) {
        throw new Exception("Doctor no encontrado");
    }

    // Get medical areas assigned to the doctor
    $stmt = $pdo->prepare("
        SELECT ma.name
        FROM doctor_assignments da
        JOIN medical_areas ma ON da.id_medical_area = ma.id
        WHERE da.id_doctor = :id_doctor
    ");
    $stmt->execute([':id_doctor' => $doctorId]);
    $medicalAreas = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Get doctor schedules
    $stmt = $pdo->prepare("
        SELECT id, day, start_time, end_time
        FROM medical_schedules
        WHERE id_doctor = :id_doctor
        ORDER BY CASE
            WHEN day = 'Monday' THEN 1
            WHEN day = 'Tuesday' THEN 2
            WHEN day = 'Wednesday' THEN 3
            WHEN day = 'Thursday' THEN 4
            WHEN day = 'Friday' THEN 5
            WHEN day = 'Saturday' THEN 6
            WHEN day = 'Sunday' THEN 7
        END
    ");
    $stmt->execute([':id_doctor' => $doctorId]);
    $doctorSchedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($doctorData) {
        $doctorData['medical_areas'] = $medicalAreas;
        $doctorData['schedules'] = $doctorSchedules;
    }

    // Check for success or error messages
    if (isset($_GET['success'])) {
        $smarty->assign('success', $_GET['success']);
    }

    if (isset($_GET['error'])) {
        $smarty->assign('error', $_GET['error']);
    }

    // Assign variables to template
    $smarty->assign('doctorData', $doctorData);

    // Display template
    $smarty->display('doctor-profile.view.tpl');
} catch (Exception $e) {
    $smarty->assign('error', $e->getMessage());
    $smarty->display('doctor-profile.view.tpl');
}
?>
