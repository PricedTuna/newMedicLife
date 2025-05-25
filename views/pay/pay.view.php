<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Smarty\Smarty;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
// Pacientes obtenidos de la base de datos
$patients = null;

//EJecutar la consola
$stmt = $pdo->prepare("SELECT id, names, last_name, last_name2, CURP FROM patients");
$stmt->execute();

//Obtener todos los datos como array asociativo
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
$smarty = new Smarty();

// Obtener Citas
if (isset($_GET['id'])) {
    $appointmentId = $_GET['id'];

    // Puedes validar o limpiar el valor
    $appointmentId = (int)$appointmentId; // solo si es numérico

    // Luego puedes usarlo en una consulta, por ejemplo:
    $stmt = $pdo->prepare("SELECT
        ap.id AS id,
        p.names AS patient_name,
        p.curp AS curp,
        ap.appointment_date
    FROM appointments ap
    INNER JOIN patients p ON ap.id_patient = p.id
    WHERE ap.id = :id");

    $stmt->execute(['id' => $appointmentId]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si usas Smarty, lo asignas:
    $smarty->assign('appointment', $appointment);

}

$smarty->setTemplateDir(__DIR__);

$sidebarPath = $_SERVER['DOCUMENT_ROOT'] . '/views/components/sidebar.tpl';
$smarty->assign('sidebarPath', $sidebarPath);
$smarty->assign('patients', $patients);

$smarty->display('pay.view.tpl');
