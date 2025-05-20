<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Smarty\Smarty;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';

$medical_areas = null;
$stmt = $pdo->prepare("SELECT * FROM medical_areas");
$stmt->execute();
$medical_areas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$doctors = null;
$stmt = $pdo->prepare(" SELECT 
        d.id AS doctor_id,
        d.names AS doctor_name,
        ma.id AS medical_area_id,
        ma.name AS medical_area_name
    FROM doctors d
    INNER JOIN doctor_assignments da ON d.id = da.id_doctor
    INNER JOIN medical_areas ma ON ma.id = da.id_medical_area");
$stmt->execute();
$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

$patients = null;
$stmt = $pdo->prepare("SELECT * FROM patients");
$stmt->execute();
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

$smarty = new Smarty();

$smarty->setTemplateDir(__DIR__);

$smarty->assign('medical_areas', $medical_areas);
$smarty->assign('doctors', $doctors);
$smarty->assign('patients', $patients);

$smarty->display('register-appoiment.tpl');
