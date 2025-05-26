<?php
// Include session controller to protect this route
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';

use Smarty\Smarty;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/doctor/doctor.model.php';

$smarty = new Smarty();

$smarty->setTemplateDir(__DIR__);

// Obtener la lista de doctores activos
$doctorModel = new DoctorModel($pdo);
$doctors = $doctorModel->getAllActiveDoctors();

$sidebarPath = $_SERVER['DOCUMENT_ROOT'] . '/views/components/sidebar.tpl';
$smarty->assign('error', $_GET['error'] ?? null);
$smarty->assign('sidebarPath', $sidebarPath);
$smarty->assign('doctors', $doctors);

$smarty->display('register-user.view.tpl');
?>
