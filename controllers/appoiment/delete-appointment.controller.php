<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/email/email.controller.php';

$emailController = new EmailController();

// Only administrators and secretaries can delete appointments
checkUserRole(['A', 'S']);

header('Content-Type: application/json'); // Indicamos que la respuesta es JSON

// 📌 Registra la solicitud en un log temporal
file_put_contents("debug.log", json_encode(utf8ize($_POST)) . PHP_EOL, FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['appointment_id']) && is_numeric($_POST['appointment_id'])) {
        $appointment_id = $_POST['appointment_id'];
        $patient_name = $_POST['patient_name'];
        $email = $_POST['patient_email'];
        $fecha = $_POST['appointment_date'];
        $fechaOriginal = $fecha; // '2025-06-26 08:00'
        $fechaFormateada = formatearFechaEspañol($fechaOriginal);

        try {
            $stmt = $pdo->prepare("SELECT id FROM appointments WHERE id = :appointment_id");
            $stmt->execute([':appointment_id' => $appointment_id]);
            $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($appointment) {
                //Eliminar la cita
                $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = :appointment_id");
                $stmt->execute([':appointment_id' => $appointment_id]);
            }

            // Notificación de eliminación de cita
            $subject = "Cancelación de cita Medica Medic Life";
            $message = "Hola $patient_name,\n\nTe informamos que tu cita para el dia $fechaFormateada Fue cancelada de nuestro sistema" . "\nEn caso de ayuda favor de contactar a un miembro de nuestro equipo.\n\nSaludos.";
            $from = 'Medic Life <no-reply@sandbox3e6934d33e59407a9be71bc8778b9998.mailgun.org>';

            $result = $emailController->sendEmail($email, $subject, $message, $from);

            header('Location: /views/appointment/list/list-appointments.view.php?success=' . urlencode("cita eliminad con éxito"));
        } catch (Exception $e) {
            header('Location: /views/appointment/list/list-appointments.view.php?error=' . urlencode("Algo sucedió mal, inténtelo de nuevo en unos minutos o contacte a soporte"));
            exit;
        }
    } else {
        header('Location: /views/appointment/list/list-appointments.view.php?error=' . urlencode("Algo sucedió mal, inténtelo de nuevo en unos minutos o contacte a soporte 2"));
        exit;
    }
} else {
    echo json_encode(utf8ize(["success" => false, "message" => "Método no permitido."]));
}

function formatearFechaEspañol($fecha)
{
    $dias = [
        'Sunday' => 'domingo',
        'Monday' => 'lunes',
        'Tuesday' => 'martes',
        'Wednesday' => 'miércoles',
        'Thursday' => 'jueves',
        'Friday' => 'viernes',
        'Saturday' => 'sábado'
    ];

    $meses = [
        'January' => 'enero',
        'February' => 'febrero',
        'March' => 'marzo',
        'April' => 'abril',
        'May' => 'mayo',
        'June' => 'junio',
        'July' => 'julio',
        'August' => 'agosto',
        'September' => 'septiembre',
        'October' => 'octubre',
        'November' => 'noviembre',
        'December' => 'diciembre'
    ];

    $dt = new DateTime($fecha);
    $diaSemana = $dias[$dt->format('l')];
    $dia = $dt->format('d');
    $mes = $meses[$dt->format('F')];
    $año = $dt->format('Y');
    $hora = $dt->format('H:i');

    return "$diaSemana, $dia de $mes de $año a las $hora";
}
