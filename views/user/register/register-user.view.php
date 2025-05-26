<?php
// Include session controller to protect this route
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';

use Smarty\Smarty;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/doctor/doctor.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/user.model.php';

$smarty = new Smarty();

$smarty->setTemplateDir(__DIR__);

// Obtener la lista de doctores activos
$doctorModel = new DoctorModel($pdo);
$doctors = $doctorModel->getAllActiveDoctors();

// Obtener la lista de todos los usuarios
$users = getAllUsers();

// Verificar si estamos en modo edición o cambio de contraseña
$editMode = false;
$passwordChangeMode = false;
$userData = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = $_GET['id'];
    $userData = getUserById($userId);
    if ($userData) {
        $editMode = true;

        // Verificar si estamos en modo de cambio de contraseña
        if (isset($_GET['password_change']) && $_GET['password_change'] == '1') {
            $passwordChangeMode = true;
        }
    }
}

$sidebarPath = $_SERVER['DOCUMENT_ROOT'] . '/views/components/sidebar.tpl';
$smarty->assign('error', $_GET['error'] ?? null);
$smarty->assign('success', $_GET['success'] ?? null);
$smarty->assign('sidebarPath', $sidebarPath);
$smarty->assign('doctors', $doctors);
$smarty->assign('users', $users);
$smarty->assign('editMode', $editMode);
$smarty->assign('passwordChangeMode', $passwordChangeMode);
$smarty->assign('userData', $userData);

$smarty->display('register-user.view.tpl');
?>
