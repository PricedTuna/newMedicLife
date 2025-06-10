<?php
// controllers/pay/payment-summary.controller.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/pay/pay.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';

// Only administrators and secretaries can access payment summary functionality
checkUserRole(['A', 'S']);

// Initialize the payment model
$paymentModel = new PaymentModel($GLOBALS['pdo']);

// Get date filter from request or use current date
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Get payment summary data
$paymentSummary = $paymentModel->getPaymentSummary($date);

// Return JSON if requested
if (isset($_GET['format']) && $_GET['format'] === 'json') {
    header('Content-Type: application/json');
    echo json_encode($paymentSummary);
    exit;
}

// Otherwise, redirect to the view
header('Location: /views/pay/payment-summary.view.php?date=' . urlencode($date));
exit;
