<?php
// Include session controller to protect this route
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';

use Smarty\Smarty;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
$smarty = new Smarty();

try {
    // Obtener los datos del doctor si se pasa un ID
    $doctor = null;
    $medical_schedules = null;
    $doctorAssignments = null;
    if (isset($_GET['id'])) {
        // Seleccionar doctor con su id
        $stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = :id");
        $stmt->execute([':id' => $_GET['id']]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

        // seleccionar horarios del doctor
        $stmt = $pdo->prepare("SELECT * FROM medical_schedules WHERE id_doctor = :id_doctor");
        $stmt->execute(['id_doctor' => $_GET['id']]);
        $medical_schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $smarty->assign('medical_schedules', $medical_schedules);
        $schedules = [];
        foreach ($medical_schedules as $schedule) {
            $day = $schedule['day'];
            $schedules[$day] = [
                'start_time' => $schedule['start_time'],
                'end_time' => $schedule['end_time']
            ];
        }
        $smarty->assign('schedules', $schedules);


        // seleccionar area medica del doctor
        $stmt = $pdo->prepare("SELECT * FROM doctor_assignments WHERE id_doctor = :id_doctor");
        $stmt->execute(['id_doctor' => $_GET['id']]);
        $doctorAssignments = $stmt->fetch(PDO::FETCH_ASSOC);
        $smarty->assign('doctorAssignments', $doctorAssignments);
    }


    // Consultar municipios, estados, localidades y áreas médicas
    $stmt = $pdo->query("SELECT id_state, id, name FROM municipalities");
    $municipalities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->query("SELECT * FROM states");
    $states = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->query("SELECT id_state, id_municipality, id, name FROM localities");
    $localities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->query("SELECT id, name AS area_name FROM medical_areas");
    $medical_areas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (\Throwable $th) {
    print_r($th);
    exit;
} catch (PDOException $e) {
    die("Error al obtener tablas: " . $e->getMessage());
}



$smarty->setTemplateDir(__DIR__);

$sidebarPath = $_SERVER['DOCUMENT_ROOT'] . '/views/components/sidebar.tpl';
$smarty->assign('error', $_GET['error'] ?? null);
$smarty->assign('sidebarPath', $sidebarPath);
$smarty->assign('doctor', $doctor);
$smarty->assign('municipalities', $municipalities);
$smarty->assign('states', $states);
$smarty->assign('localities', $localities);
$smarty->assign('medical_areas', $medical_areas);

$smarty->display('register-doctor.view.tpl');
