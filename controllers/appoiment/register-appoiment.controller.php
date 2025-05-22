<?php
// obtener_doctores.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// controllers/DoctorController.php
require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/appointments/appointment.model.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recopilación centralizada de datos del formulario
    $appointmentId = isset($_POST['appointment_id']) && is_numeric($_POST['appointment_id']) ? $_POST['appointment_id'] : null;
    $data = [
        'id_patient'             => $_POST['id_patient'] ?? '',
        'id_doctor'              => $_POST['id_doctor'] ?? '',
        'id_receptionist'        => $_POST['id_recepetionist'] ?? '',
        'id_medical_area'        => $_POST['id_medical_area'] ?? '',
        'appointment_date'       => $_POST['appointment_date'] ?? ''
    ];

    try {
        // Instanciación del modelo de Doctor y validación de datos
        $appointmentModel = new AppointmentModel($pdo);

        if ($appointmentId) {
            // Actualización del doctor
            $appointmentModel->updateAppointment($appointmentId, $data);
            header('Location: /views/appointment/list/list-appointments.view.php?success=' . urlencode("Cita actualizada con éxito"));
        } else {
            // Creación de un nuevo doctor
            // echo '<pre>';
            // print_r($data);
            // echo '</pre>';
            // exit;

            $data['id_receptionist'] = 9;
            // echo '<pre>';
            // print_r($data);
            // echo '</pre>';
            // exit;

            $newAppointmentId = $appointmentModel->createAppointment($data);

            header('Location: /views/appointment/list/list-appointments.view.php?success=' . urlencode("Cita creada con éxito"));
        }
    } catch (Exception $e) {
        header('Location: /views/appointment/register/register-appoiment.php?error=' . urlencode($e->getMessage()) . '&id=' . ($doctorId ?? ''));
    }
}
