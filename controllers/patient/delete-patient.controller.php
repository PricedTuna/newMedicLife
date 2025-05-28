<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';

// Only administrators and secretaries can delete patients
checkUserRole(['A', 'S']);

header('Content-Type: application/json'); // Indicamos que la respuesta es JSON

// 📌 Registra la solicitud en un log temporal
file_put_contents("debug.log", json_encode($_POST) . PHP_EOL, FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['patient_id']) && is_numeric($_POST['patient_id'])) {
        $patient_id = $_POST['patient_id'];

        try {
            $stmt = $pdo->prepare("SELECT id_emergency_contact FROM patients WHERE id = :patient_id");
            $stmt->execute([':patient_id' => $patient_id]);
            $patient = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($patient) {
                $emergencyContactId = $patient['id_emergency_contact'];

                //Eliminar al paciente
                $stmt = $pdo->prepare("DELETE FROM patients WHERE id = :patient_id");
                $stmt->execute([':patient_id' => $patient_id]);

                //Eliminar el contacto de emergencia existente
                if ($emergencyContactId) {
                    try {
                        $stmt = $pdo->prepare("DELETE FROM emergency_contacts WHERE id = :id");
                        $stmt->execute([':id' => $emergencyContactId]);
                    } catch (PDOException $e) {
                        echo "Error al eliminar contacto de emergencia: " . $e->getMessage();
                        exit;
                    }
                }
            }

            header('Location: /views/patient/list/list-patients.view.php?success=' . urlencode("Paciente eliminado con éxito"));
        } catch (Exception $e) {
            header('Location: /views/patient/list/list-patients.view.php?error=' . urlencode("Algo sucedió mal, inténtelo de nuevo en unos minutos o contacte a soporte 1"));
            exit;
        }
    } else {
        header('Location: /views/patient/list/list-patients.view.php?error=' . urlencode("Algo sucedió mal, inténtelo de nuevo en unos minutos o contacte a soporte 2"));
        exit;
    }
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
}
