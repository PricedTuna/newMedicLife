<?php
// controllers/pago.controller.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/pay/pay.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/email/email.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';

// Only administrators and secretaries can access payment functionality
checkUserRole(['A', 'S']);

$paypalModel = new PaymentModel($pdo);
$emailController = new EmailController();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Manejar retorno de PayPal (pago exitoso)
if (isset($_GET['token']) && isset($_GET['PayerID'])) {
    try {
        // Recibir parámetros que pasaste en return_url
        $name = $_GET['name'] ?? 'Cliente';
        $monto = $_GET['amount'] ?? 0;
        $id_cita = $_GET['id_cita'] ?? null;

        // Capturar el pago con token y PayerID
        $status = $paypalModel->captureAndStorePayment($_GET['token'], $_GET['PayerID'], $id_cita);

        // Actualizar cita usando sesión o id_cita recibido
        if ($id_cita) {
            $paypalModel->updateAppointment($id_cita);
        } elseif (isset($_SESSION['patient_appointment'])) {
            $paypalModel->updateAppointment($_SESSION['patient_appointment']);
        }

        $subject = "Confirmación de pago exitoso";
        $message = "Hola $name,\n\nTu pago de $monto MXN por medio de PayPal ha sido recibido con éxito.\nGracias por tu preferencia.\n\nSaludos.";
        $from = 'Medic Life <no-reply@sandbox3e6934d33e59407a9be71bc8778b9998.mailgun.org>';

        // Enviar correo de confirmación
        $email = $_SESSION['patient_email'] ?? 'cliente@correo.com';
        $emailController->sendEmail($email, $subject, $message, $from);

        header('Location: /views/appointment/list/list-appointments.view.php?success=' . urlencode("La cita ha sido pagada con éxito."));
        exit;
    } catch (Exception $e) {
        header('Location: /views/appointment/list/list-appointments.view.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}


// 2. Cancelación de PayPal
if (isset($_GET['paypal_cancel'])) {
    header("Location: /views/appointment/list/list-appointments.view.php?error=" . urlencode("Pago cancelado por el usuario."));
    exit;
}

// 3. Procesar formulario POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $metodoPago = $_POST['metodo_pago'] ?? null;
    $name = $_POST['name'] ?? 'Cliente';
    $email = $_POST['email'] ?? 'cliente@correo.com';
    $monto = isset($_POST['monto']) ? floatval($_POST['monto']) : 0;
    $pagaCon = isset($_POST['paga_con']) ? floatval($_POST['paga_con']) : 0;
    $id_patient = $_POST['id_patient'] ?? '';
    $id_cita = $_POST['id_cita'] ?? '';


    $id_user = $_SESSION['id_user'] ?? 1;

    try {
        if (!$metodoPago) {
            throw new Exception("Debe seleccionar un método de pago.");
        }

        if ($metodoPago === 'efectivo') {
            if (!$monto) {
                throw new Exception("Datos incorrectos para pago en efectivo.");
            }

            $paypalModel->savePay($id_user, $id_patient, $monto, 'cash');
            $paypalModel->updateAppointment($id_cita);

            if (empty($email)) {
                throw new Exception("No se proporcionó correo electrónico.");
            }

            $subject = "Confirmación de pago exitoso";
            $message = "Hola $name,\n\nTu pago de $monto MXN ha sido recibido con éxito.\nGracias por tu preferencia.\n\nSaludos.";
            $from = 'Medic Life <no-reply@sandbox3e6934d33e59407a9be71bc8778b9998.mailgun.org>';

            // Usa el controlador de email si existe
            $result = $emailController->sendEmail($email, $subject, $message, $from);

            // Redirecciona con mensaje de éxito
            header('Location: /views/appointment/list/list-appointments.view.php?success=' . urlencode("La cita ha sido pagada con éxito y se envió correo de confirmación."));
            exit;
        }


        if ($metodoPago === 'paypal') {
            if ($monto <= 0) {
                throw new Exception("Monto inválido para PayPal.");
            }

            // Guardar datos en sesión para usarlos después de redirección de PayPal
            $_SESSION['id_patient'] = $id_patient;
            $_SESSION['id_user'] = 1;
            $_SESSION['patient_appointment'] = $id_cita;
            $_SESSION['patient_email'] = $email;

            $paypalModel->createPayment($name, $email, $monto, "MXN", $id_cita);
            exit;
        }

        throw new Exception("Método de pago no soportado.");
    } catch (Exception $e) {
        header('Location: /views/appointment/list/list-appointments.view.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}
