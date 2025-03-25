<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';

header('Content-Type: application/json'); // Indicamos que la respuesta es JSON

// 📌 Registra la solicitud en un log temporal
file_put_contents("debug.log", json_encode($_POST) . PHP_EOL, FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['doctor_id']) && is_numeric($_POST['doctor_id'])) {
        $doctor_id = $_POST['doctor_id'];

        try {
            $stmt = $pdo->prepare("SELECT id FROM doctors WHERE id = :doctor_id");
            $stmt->execute([':doctor_id' => $doctor_id]);
            $existingDoctor = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$existingDoctor) {
                echo json_encode(["success" => false, "message" => "El doctor con ID $doctor_id no existe."]);
                exit;
            }

            // Eliminar doctor
            $stmt = $pdo->prepare("DELETE FROM doctors WHERE id = :doctor_id");
            $stmt->execute([':doctor_id' => $doctor_id]);

            header('Location: /views/doctor/list/list-doctors.view.php?success=' . urlencode("Doctor eliminado con éxito"));
            exit;

        } catch (Exception $e) {
            header('Location: /views/doctor/list/list-doctors.view.php?error=' . urlencode("Algo sucedió mal, inténtelo de nuevo en unos minutos o contacte a soporte"));
            exit;
        }
    } else {
        header('Location: /views/doctor/list/list-doctors.view.php?error=' . urlencode("Algo sucedió mal, inténtelo de nuevo en unos minutos o contacte a soporte"));
        exit;
    }
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
}
?>
