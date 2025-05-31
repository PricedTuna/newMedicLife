<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Smarty\Smarty;

// Corregir la ruta del autoload
require_once __DIR__ . '/../../vendor/autoload.php';

$smarty = new Smarty;

$smarty->setTemplateDir(__DIR__);

if (isset($_GET['error'])) {
    $error = filter_input(INPUT_GET, 'error', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $smarty->assign('error', $error);
}

$smarty->display('login.view.tpl');