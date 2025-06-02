<?php
use Smarty\Smarty;

// No session validation required for this page

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';

// Inicializar Smarty
$smarty = new Smarty();
$smarty->setTemplateDir($_SERVER['DOCUMENT_ROOT'] . '/views/');
$smarty->setCompileDir($_SERVER['DOCUMENT_ROOT'] . '/templates_c/');

// Verificar si hay mensajes de error o éxito
$error = isset($_GET['error']) ? $_GET['error'] : null;
$success = isset($_GET['success']) ? $_GET['success'] : null;

// Asignar variables a la plantilla
$smarty->assign('error', $error);
$smarty->assign('success', $success);

// Mostrar la plantilla
$smarty->display('public/medical_history/public-medical-history.view.tpl');
?>