<?php
// obtener_doctores.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// controllers/DoctorController.php
require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/appointments/appointment.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/email/email.controller.php';

// Only administrators and secretaries can manage appointments
checkUserRole(['A', 'S']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recopilación centralizada de datos del formulario
    $appointmentId = isset($_POST['appointment_id']) && is_numeric($_POST['appointment_id']) ? $_POST['appointment_id'] : null;
    $data = [
        'id_patient'             => $_POST['id_patient'] ?? '',
        'id_doctor'              => $_POST['id_doctor'] ?? '',
        'id_receptionist'        => $_POST['id_recepetionist'] ?? '',
        'id_medical_area'        => $_POST['id_medical_area'] ?? '',
        'appointment_date'       => $_POST['appointment_date'] ?? '',
    ];
    $name        = $_POST['patientName']       ?? '';
    $email       = $_POST['patient_email']     ?? '';
    $doctor      = $_POST['name_doctor']       ?? '';
    $medicalArea = $_POST['name_medical_area'] ?? '';

    //formateo de fecha y hora en español : 
    $fechaOriginal = $data['appointment_date']; // '2025-06-26 08:00'
    $fechaFormateada = formatearFechaEspañol($fechaOriginal);
    $emailController = new EmailController();

    try {
        // Instanciación del modelo de Doctor y validación de datos
        $appointmentModel = new AppointmentModel($pdo);

        if ($appointmentId) {
            // Actualización del doctor
            $data['id_receptionist'] = 9;

            $appointmentModel->updateAppointment($appointmentId, $data);

            //contenido del correo de confirmación de cita:
            $subject = "Confirmación de cita Actualizada";
            $message = "Hola $name,\n\nTu cita a sido agendada para el dia $fechaFormateada con el medico $doctor en la especialidad de $medicalArea" . "\nGracias por tu preferencia.\n\nSaludos.";
            $from = 'Medic Life <no-reply@sandbox3e6934d33e59407a9be71bc8778b9998.mailgun.org>';

            $result = $emailController->sendEmail($email, $subject, $message, $from);

            header('Location: /views/appointment/list/list-appointments.view.php?success=' . urlencode("Cita actualizada con éxito"));
        } else {
            $data['id_receptionist'] = 9;
         
            $newAppointmentId = $appointmentModel->createAppointment($data);

            $subject = "Confirmación de nueva cita";
            $message = "Hola $name,\n\nTu cita a sido agendada para el dia $fechaFormateada con el medico $doctor en la especialidad de $medicalArea" . "\nGracias por tu preferencia.\n\nSaludos.";
            $from = 'Medic Life <no-reply@sandbox3e6934d33e59407a9be71bc8778b9998.mailgun.org>';

            $result = $emailController->sendEmail($email, $subject, $message, $from);

            header('Location: /views/appointment/list/list-appointments.view.php?success=' . urlencode("Cita creada con éxito"));
        }
    } catch (Exception $e) {
        header('Location: /views/appointment/register/register-appoiment.php?error=' . urlencode($e->getMessage()) . '&id=' . ($doctorId ?? ''));
    }
}

// función para formatear correctamente la fecha y la hora din depender de la capacidad del servidor
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
