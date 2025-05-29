<?php
// obtener_doctores.php
// Include session controller to protect this route
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';

// Only administrators and secretaries can delete doctors
checkUserRole(['A', 'S']);

use Smarty\Smarty;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';

$smarty = new Smarty();
$smarty->setTemplateDir(__DIR__);
$smarty->setCompileDir(__DIR__ . '/templates_c');

// Obtener pacientes
try {
    $stmt = $pdo->query("SELECT * FROM patients WHERE status != 'I'");
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener pacientes: " . $e->getMessage());
}

// Verifica si vienen mensajes desde GET
$success = isset($_GET['success']) ? $_GET['success'] : null;
$error = isset($_GET['error']) ? $_GET['error'] : null;

// Rutas
$sidebarPath = $_SERVER['DOCUMENT_ROOT'] . '/views/components/sidebar.tpl';

// Asignar variables a Smarty
$smarty->assign('patients', $patients);
$smarty->assign('sidebarPath', $sidebarPath);
$smarty->assign('success', $success);
$smarty->assign('error', $error);

// Renderizar plantilla
$smarty->display('list-patients.view.tpl');
