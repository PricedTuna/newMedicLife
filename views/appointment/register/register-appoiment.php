<?php
// Include session controller to protect this route
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Smarty\Smarty;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';

$smarty = new Smarty();

// Obtener áreas médicas
$medical_areas = null;
$stmt = $pdo->prepare("SELECT * FROM medical_areas");
$stmt->execute();
$medical_areas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener doctores
$doctors = null;

// Determinar si el usuario es un doctor
$isDoctor = isset($_SESSION['role']) && $_SESSION['role'] === 'D';
$doctorId = $isDoctor && isset($_SESSION['doctorId']) ? $_SESSION['doctorId'] : null;

$query = "SELECT
        d.id AS doctor_id,
        d.names AS doctor_name,
        ma.id AS medical_area_id,
        ma.name AS medical_area_name
    FROM doctors d
    INNER JOIN doctor_assignments da ON d.id = da.id_doctor
    INNER JOIN medical_areas ma ON ma.id = da.id_medical_area
    WHERE d.status != 'I'";

$params = [];

// Si el usuario es un doctor, solo mostrar ese doctor
if ($isDoctor && $doctorId) {
    $query .= " AND d.id = :doctorId";
    $params[':doctorId'] = $doctorId;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pasar la información de si es doctor a la plantilla
$smarty->assign('isDoctor', $isDoctor);

// Obtener horarios
$schedules = null;
$stmt = $pdo->prepare("SELECT * FROM medical_schedules");
$stmt->execute();
$schedules = $stmt->fetchALL(PDO::FETCH_ASSOC);

// Obtener pacientes
$patients = null;
$stmt = $pdo->prepare("SELECT id, names, last_name, last_name2, CURP FROM patients");
$stmt->execute();
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inicializar variables correctamente
$appointment[] = null;
$allAppointments = [];

// Manejo de citas
if (isset($_GET['id'])) {
    $appointmentId = (int)$_GET['id'];

    $stmt = $pdo->prepare("SELECT
        ap.id AS id,
        ap.id_patient as id_patient,
        ap.id_doctor as id_doctor,
        ap.id_medical_area AS id_medical_area,
        d.names as name_doctor,
        p.names AS patient_name,
        p.last_name as last_name,
        p.last_name2 as last_name2,
        p.curp AS curp,
        ma.name AS name_medical_area,
        ap.appointment_date
    FROM appointments ap
    INNER JOIN patients p ON ap.id_patient = p.id
    INNER JOIN doctors d ON ap.id_doctor = d.id
    INNER JOIN medical_areas ma ON ma.id = ap.id_medical_area
    WHERE ap.id = :id");

    $stmt->execute(['id' => $appointmentId]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

    // Construir el nombre completo evitando espacios extras
    if ($appointment) {
        $parts = array_filter([
            $appointment['patient_name'] ?? '',
            $appointment['last_name'] ?? '',
            $appointment['last_name2'] ?? '',
        ], fn($v) => !empty($v));

        $appointment['full_name'] = implode(' ', $parts);
    }

    $smarty->assign('appointment', $appointment);
} else {
    $smarty->assign('appointment', $appointment); // Vacío si no hay ID
}

try {
    $stmt = $pdo->prepare("SELECT * FROM appointments");
    $stmt->execute();
    $allAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener todas las citas: " . $e->getMessage());
}

$smarty->assign('allAppointments', $allAppointments);


// Configuración de Smarty
$smarty->setTemplateDir(__DIR__);

$sidebarPath = $_SERVER['DOCUMENT_ROOT'] . '/views/components/sidebar.tpl';
$smarty->assign('sidebarPath', $sidebarPath);
$smarty->assign('medical_areas', $medical_areas);
$smarty->assign('doctors', $doctors);
$smarty->assign('patients', $patients);
$smarty->assign('schedules', $schedules);

// Agrega esto JUSTO ANTES de $smarty->display()
$smarty->clearCompiledTemplate();
$smarty->clearAllCache();
$smarty->display('register-appoiment.tpl');
