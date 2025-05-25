<?php
// controllers/pago.controller.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/pay/pay.model.php';

$paypalModel = new PaymentModel($pdo);

// 1. Manejar retorno de PayPal (pago exitoso)
if (isset($_GET['token']) && isset($_GET['PayerID'])) {
    try {
        $status = $paypalModel->captureAndStorePayment($_GET['token'], $_GET['PayerID']);
        // Pago exitoso, mostrar mensaje o redirigir
        header('Location: /views/pay/success.php?status=' . urlencode($status));
        exit;
    } catch (Exception $e) {
        header('Location: /views/pay/pay.view.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}

// 2. Manejar cancelación de PayPal
if (isset($_GET['paypal_cancel'])) {
    header("Location: /views/pay/pay.view.php?error=" . urlencode("Pago cancelado por el usuario."));
    exit;
}

// 3. Manejar envío del formulario (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recopilación de datos
    $metodoPago = $_POST['metodo_pago'] ?? null;
    $name = $_POST['name'] ?? 'Cliente';
    $email = $_POST['email'] ?? 'cliente@correo.com';
    $monto = isset($_POST['monto']) ? floatval($_POST['monto']) : 0;
    $pagaCon = isset($_POST['paga_con']) ? floatval($_POST['paga_con']) : 0;

    try {
        if (!$metodoPago) {
            throw new Exception("Debe seleccionar un método de pago.");
        }

        if ($metodoPago === 'paypal') {
            if ($monto <= 0) {
                throw new Exception("Monto inválido para PayPal.");
            }

            // Crear y redirigir a PayPal
            $paypalModel->createPayment($name, $email, $monto);

            // Nota: createPayment debe redirigir con header y exit
        }

        elseif ($metodoPago === 'efectivo') {
            if ($monto <= 0 || $pagaCon < $monto) {
                throw new Exception("Datos incorrectos para pago en efectivo.");
            }

            $restante = $pagaCon - $monto;

            // Puedes guardar en base de datos si lo deseas

            header('Location: /views/pay/pay.view.php?restante=' . urlencode($restante));
            exit;
        }

        else {
            throw new Exception("Método de pago no soportado.");
        }

    } catch (Exception $e) {
        header('Location: /views/pay/pay.view.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}
