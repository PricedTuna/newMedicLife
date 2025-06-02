<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/email/email.controller.php';


$emailController = new EmailController();

// Only administrators and secretaries can delete doctors
checkUserRole(['A', 'S']);

header('Content-Type: application/json'); // Indicamos que la respuesta es JSON

// 📌 Registra la solicitud en un log temporal
file_put_contents("debug.log", json_encode(utf8ize($_POST)) . PHP_EOL, FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['doctor_id']) && is_numeric($_POST['doctor_id'])) {
        $doctor_id = $_POST['doctor_id'];
        $doctor_names = $_POST['doctor_names'];
        $email = $_POST['doctor_email'];
        try {
            // Verificar existencia del doctor
            $stmt = $pdo->prepare("SELECT id FROM doctors WHERE id = :doctor_id");
            $stmt->execute([':doctor_id' => $doctor_id]);
            $existingDoctor = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingDoctor) {
                // Cambiar el estado a 'inactivo' (puedes usar otro valor si tu DB usa boolean o enum)
                $stmt = $pdo->prepare("UPDATE doctors SET status = 'I' WHERE id = :doctor_id");
                $stmt->execute([':doctor_id' => $doctor_id]);


                $subject = "Aviso de desactivación Medic Life";
                $message = "Hola $doctor_names,\n\nTe informamos que tu estado en nuestro sistema Medic Life a sido desactivado, no podrás hacer uso de nuestros servicios" . "\nEn caso de ayuda favor de contactar a un miembro de nuestro equipo.\n\nSaludos.";
                $from = 'Medic Life <no-reply@sandbox3e6934d33e59407a9be71bc8778b9998.mailgun.org>';

                $result = $emailController->sendEmail($email, $subject, $message, $from);

                header('Location: /views/doctor/list/list-doctors.view.php?success=' . urlencode("Doctor desactivado con éxito"));
            } else {
                header('Location: /views/doctor/list/list-doctors.view.php?error=' . urlencode("Doctor no encontrado"));
            }
        } catch (Exception $e) {
            header('Location: /views/doctor/list/list-doctors.view.php?error=' . urlencode("Algo salió mal. Intente más tarde."));
            exit;
        }
    } else {
        header('Location: /views/doctor/list/list-doctors.view.php?error=' . urlencode("Algo sucedió mal, inténtelo de nuevo en unos minutos o contacte a soporte"));
        exit;
    }
} else {
    echo json_encode(utf8ize(["success" => false, "message" => "Método no permitido."]));
}
