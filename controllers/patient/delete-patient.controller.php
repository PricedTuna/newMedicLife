<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/email/email.controller.php';

$emailController = new EmailController();

// Only administrators and secretaries can delete patients
checkUserRole(['A', 'S']);

header('Content-Type: application/json'); // Indicamos que la respuesta es JSON

// 📌 Registra la solicitud en un log temporal
file_put_contents("debug.log", json_encode(utf8ize($_POST)) . PHP_EOL, FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['patient_id']) && is_numeric($_POST['patient_id'])) {
        $patient_id   = $_POST['patient_id'];
        $patient_name = $_POST['patient_names'];
        $email        = $_POST['patient_email'];

        try {
            $stmt = $pdo->prepare("UPDATE patients SET status = 'I' WHERE id = :patient_id");
            $stmt->execute([':patient_id' => $patient_id]);

            $subject = "Aviso de desactivación Medic Life";
            $message = "Hola $patient_name,\n\nTe informamos que tu estado en nuestro sistema Medic Life a sido desactivado, no podrás hacer uso de nuestros servicios" . "\nEn caso de ayuda favor de contactar a un miembro de nuestro equipo.\n\nSaludos.";
            $from = 'Medic Life <no-reply@sandbox3e6934d33e59407a9be71bc8778b9998.mailgun.org>';

            $result = $emailController->sendEmail($email, $subject, $message, $from);


            header('Location: /views/patient/list/list-patients.view.php?success=' . urlencode("Paciente eliminado con éxito"));
        } catch (Exception $e) {
            echo var_dump($e);
            exit;
            header('Location: /views/patient/list/list-patients.view.php?error=' . urlencode("Algoooooo sucedió mal, inténtelo de nuevo en unos minutos o contacte a soporte"));
            exit;
        }
    } else {
        header('Location: /views/patient/list/list-patients.view.php?error=' . urlencode("Algo sucedió mal, inténtelo de nuevo en unos minutos o contacte a soporte 2"));
        exit;
    }
} else {
    echo json_encode(utf8ize(["success" => false, "message" => "Método no permitido."]));
}
