<?php
use Smarty\Smarty;

// No session validation required for this page

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

// Verificar si hay mensajes de error o éxito
$error = isset($_GET['error']) ? $_GET['error'] : null;
$success = isset($_GET['success']) ? $_GET['success'] : null;

// Verificar si se proporcionó un CURP
$curp = isset($_GET['curp']) ? trim($_GET['curp']) : null;
$patient = null;
$history = null;

if ($curp) {
    try {
        // Instanciar los modelos
        $patientModel = new PatientModel($pdo);
        $medicalHistoryModel = new MedicalHistoryModel($pdo);
        
        // Buscar al paciente por CURP
        $patient = $patientModel->getPatientByCURP($curp);
        
        if ($patient) {
            // Obtener historial médico del paciente
            $history = $medicalHistoryModel->getPatientHistory($patient['id']);
        } else {
            $error = 'No se encontró ningún paciente con el CURP proporcionado';
        }
    } catch (Exception $e) {
        $error = 'Error al procesar la solicitud: ' . $e->getMessage();
        error_log('Error al buscar historial médico: ' . $e->getMessage());
    }
}

// Asignar variables a la plantilla
$smarty->assign('error', $error);
$smarty->assign('success', $success);
$smarty->assign('curp', $curp);
$smarty->assign('patient', $patient);
$smarty->assign('history', $history);

// Mostrar la plantilla
$smarty->display('public/medical_history/view-history.view.tpl');
?>