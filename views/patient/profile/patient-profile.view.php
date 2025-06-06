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

$smarty->setTemplateDir($_SERVER['DOCUMENT_ROOT'] . '/views/patient/profile/');
$smarty->setCompileDir($_SERVER['DOCUMENT_ROOT'] . '/views/patient/profile/templates_c/');

// Check if patient ID is provided
if (!isset($_GET['id'])) {
    $smarty->assign('error', "ID de paciente no proporcionado");
    $smarty->display('patient-profile.view.tpl');
    exit;
}

$patientId = $_GET['id'];

try {
    // Get patient data from database
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = :id");
    $stmt->execute([':id' => $patientId]);
    $patientData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patientData) {
        throw new Exception("Paciente no encontrado");
    }

    // Get emergency contact for the patient
    if ($patientData && isset($patientData['id_emergency_contact']) && $patientData['id_emergency_contact']) {
        $stmt = $pdo->prepare("
            SELECT * FROM emergency_contacts
            WHERE id = :id_emergency_contact
        ");
        $stmt->execute([':id_emergency_contact' => $patientData['id_emergency_contact']]);
        $emergencyContact = $stmt->fetch(PDO::FETCH_ASSOC);

        // Format the emergency contact data to match the template expectations
        if ($emergencyContact) {
            // Combine names, last_name, and last_name2 into a single name field for display
            $emergencyContact['name'] = $emergencyContact['names'] . ' ' . 
                                        $emergencyContact['last_name'] . ' ' . 
                                        $emergencyContact['last_name2'];

            // Put the emergency contact in an array to maintain compatibility with the template
            $emergencyContacts = [$emergencyContact];
        } else {
            $emergencyContacts = [];
        }
    } else {
        $emergencyContacts = [];
    }

    if ($patientData) {
        $patientData['emergency_contacts'] = $emergencyContacts;
    }

    // Check for success or error messages
    if (isset($_GET['success'])) {
        $smarty->assign('success', $_GET['success']);
    }

    if (isset($_GET['error'])) {
        $smarty->assign('error', $_GET['error']);
    }

    // Assign variables to template
    $smarty->assign('patientData', $patientData);

    // Display template
    $smarty->display('patient-profile.view.tpl');
} catch (Exception $e) {
    $smarty->assign('error', $e->getMessage());
    $smarty->display('patient-profile.view.tpl');
}
?>
