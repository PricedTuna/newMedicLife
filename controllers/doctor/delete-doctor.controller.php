<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';

// Only administrators and secretaries can delete doctors
checkUserRole(['A', 'S']);

header('Content-Type: application/json'); // Indicamos que la respuesta es JSON

// 📌 Registra la solicitud en un log temporal
file_put_contents("debug.log", json_encode($_POST) . PHP_EOL, FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['doctor_id']) && is_numeric($_POST['doctor_id'])) {
        $doctor_id = $_POST['doctor_id'];

        try {
            // Verificar existencia del doctor
            $stmt = $pdo->prepare("SELECT id FROM doctors WHERE id = :doctor_id");
            $stmt->execute([':doctor_id' => $doctor_id]);
            $existingDoctor = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingDoctor) {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE id_doctor = :id_doctor AND status = :status");
                $stmt->execute(['id_doctor' => $doctor_id,
                                'status' => 'A']);
                $citasPendientes = $stmt->fetchColumn();

                if ($citasPendientes == 0) {
                    // No tiene citas activas, se puede desactivar
                    $stmt = $pdo->prepare("UPDATE doctors SET status = 'I' WHERE id = :doctor_id");
                    $stmt->execute([':doctor_id' => $doctor_id]);

                    header('Location: /views/doctor/list/list-doctors.view.php?success=' . urlencode("Doctor desactivado con éxito"));
                } else {
                    // Tiene citas activas, no se puede eliminar
                    header('Location: /views/doctor/list/list-doctors.view.php?error=' . urlencode("El Doctor cuenta con citas, No se puede eliminar"));
                }
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
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
}
