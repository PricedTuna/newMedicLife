<?php
// Include session controller to protect this route
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';

use Smarty\Smarty;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';

// Inicializar Smarty
$smarty = new Smarty();
$smarty->setTemplateDir(__DIR__);
$smarty->setCompileDir(__DIR__ . '/templates_c');

// Crear directorio de compilación si no existe
if (!is_dir(__DIR__ . '/templates_c')) {
    mkdir(__DIR__ . '/templates_c', 0755, true);
}

// Verifica si vienen mensajes desde GET
$success = isset($_GET['success']) ? $_GET['success'] : null;
$error = isset($_GET['error']) ? $_GET['error'] : null;

// Asignar variables a Smarty
$smarty->assign('success', $success);
$smarty->assign('error', $error);

// Renderizar plantilla
$smarty->display('medical-story.view.tpl');
