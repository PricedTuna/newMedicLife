<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Smarty\Smarty;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

$smarty = new Smarty;

$smarty->setTemplateDir(__DIR__);
$smarty->display('register-appoiment.tpl');
