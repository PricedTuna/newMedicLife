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

// Include Smarty
use Smarty\Smarty;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
$smarty = new Smarty();

$smarty->setTemplateDir($_SERVER['DOCUMENT_ROOT'] . '/views/user/profile/');
$smarty->setCompileDir($_SERVER['DOCUMENT_ROOT'] . '/views/user/profile/templates_c/');

// Get user data from session
$userEmail = $_SESSION['usuario'];

try {
    // Get user data from database
    $stmt = $pdo->prepare("SELECT id, name, email, role, id_doctor, created_at, status FROM users WHERE email = :email");
    $stmt->execute([':email' => $userEmail]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("Usuario no encontrado");
    }

    // Check if user is a doctor
    $doctorData = null;
    if ($user['role'] === 'D' && $user['id_doctor']) {
        // Get doctor data
        $stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = :id");
        $stmt->execute([':id' => $user['id_doctor']]);
        $doctorData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get medical areas assigned to the doctor
        $stmt = $pdo->prepare("
            SELECT ma.name
            FROM doctor_assignments da
            JOIN medical_areas ma ON da.id_medical_area = ma.id
            WHERE da.id_doctor = :id_doctor
        ");
        $stmt->execute([':id_doctor' => $user['id_doctor']]);
        $medicalAreas = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if ($doctorData) {
            $doctorData['medical_areas'] = $medicalAreas;
        }
    }

    // Check for success or error messages
    if (isset($_GET['success'])) {
        $smarty->assign('success', $_GET['success']);
    }

    if (isset($_GET['error'])) {
        $smarty->assign('error', $_GET['error']);
    }

    // Assign variables to template
    $smarty->assign('user', $user);
    $smarty->assign('doctorData', $doctorData);

    // Display template
    $smarty->display('user-profile.view.tpl');
} catch (Exception $e) {
    $smarty->assign('error', $e->getMessage());
    $smarty->display('user-profile.view.tpl');
}
?>
