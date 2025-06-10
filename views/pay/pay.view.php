<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Smarty\Smarty;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/pay/transfer_config.model.php';

// Pacientes obtenidos de la base de datos
$patients = null;

// Obtener la configuración de transferencia bancaria
$transferConfigModel = new TransferConfigModel($GLOBALS['pdo']);
$transferConfig = $transferConfigModel->getConfig();

//EJecutar la consola
$stmt = $GLOBALS['pdo']->prepare("SELECT id, names, last_name, last_name2, CURP FROM patients");
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
    $stmt = $GLOBALS['pdo']->prepare("SELECT
        ap.id AS id,
        ap.id_patient AS id_patient,
        p.email AS email,
        p.names AS patient_name,
        p.last_name AS last_name,
        p.last_name2 AS last_name2,
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
$smarty->assign('transferConfig', $transferConfig);

$smarty->display('pay.view.tpl');
