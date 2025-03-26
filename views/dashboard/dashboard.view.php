<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Smarty\Smarty;

$smarty = new Smarty();

$smarty->setTemplateDir('/var/www/html/views/');
$smarty->setCompileDir('/var/www/html/views/');
$smarty->setCacheDir('/var/www/html/views/');
$smarty->setConfigDir('/var/www/html/views/');


$smarty->assign('base_path', "");

if (isset($_GET['success'])) {
    $smarty->assign('success', $_GET['success']);
}

$smarty->display('dashboard.tpl');
