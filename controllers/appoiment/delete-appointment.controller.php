<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';

header('Content-Type: application/json'); // Indicamos que la respuesta es JSON

// 📌 Registra la solicitud en un log temporal
file_put_contents("debug.log", json_encode($_POST) . PHP_EOL, FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['appointment_id']) && is_numeric($_POST['appointment_id'])) {
        $appointment_id = $_POST['appointment_id'];

        try {
            $stmt = $pdo->prepare("SELECT id FROM appointments WHERE id = :appointment_id");
            $stmt->execute([':appointment_id' => $appointment_id]);
            $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($appointment) {
                //Eliminar la cita
                $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = :appointment_id");
                $stmt->execute([':appointment_id' => $appointment_id]);
                     
            }

            header('Location: /views/appointment/list/list-appointments.view.php?success=' . urlencode("cita eliminad con éxito"));
        } catch (Exception $e) {
            header('Location: /views/appointment/list/list-appointments.view.php?error=' . urlencode("Algo sucedió mal, inténtelo de nuevo en unos minutos o contacte a soporte 1"));
            exit;
        }
    } else {
        header('Location: /views/appointment/list/list-appointments.view.php?error=' . urlencode("Algo sucedió mal, inténtelo de nuevo en unos minutos o contacte a soporte 2"));
        exit;
    }
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
}
