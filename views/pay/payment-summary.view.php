<?php
// views/pay/payment-summary.view.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/pay/pay.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';

// Only administrators and secretaries can access payment summary functionality
checkUserRole(['A', 'S']);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize Smarty
use Smarty\Smarty;
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
$smarty = new Smarty;
$smarty->setTemplateDir($_SERVER['DOCUMENT_ROOT'] . '/views/pay');
$smarty->setCompileDir($_SERVER['DOCUMENT_ROOT'] . '/views/pay/templates_c');

// Get date filter from request or use current date
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Initialize the payment model
$paymentModel = new PaymentModel($GLOBALS['pdo']);

// Get payment summary data
$paymentSummary = $paymentModel->getPaymentSummary($date);

// Set up sidebar
$sidebarPath = $_SERVER['DOCUMENT_ROOT'] . '/views/components/sidebar.tpl';
$smarty->assign('sidebarPath', $sidebarPath);

// Assign data to Smarty
$smarty->assign('payments', $paymentSummary['payments']);
$smarty->assign('totals', $paymentSummary['totals']);
$smarty->assign('date', $paymentSummary['date']);

// Display the template
$smarty->display('payment-summary.view.tpl');
