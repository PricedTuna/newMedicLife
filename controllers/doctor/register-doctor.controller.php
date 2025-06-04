<?php
// obtener_doctores.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// controllers/DoctorController.php
require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/doctor/doctor.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/doctor/doctor-assignment.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/doctor/doctor-schedules.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/email/email.controller.php';

// Only administrators and secretaries can manage doctors
checkUserRole(['A', 'S']);

// Ensure we have access to the PDO connection
global $pdo;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recopilación centralizada de datos del formulario
    $doctorId = isset($_POST['id']) && is_numeric($_POST['id']) ? $_POST['id'] : null;
    $data = [
        'names'             => $_POST['name'] ?? '',
        'last_name'         => $_POST['fatherLastName'] ?? '',
        'last_name2'        => $_POST['motherLastName'] ?? '',
        'id_state'          => $_POST['state'] ?? '',
        'id_municipality'   => $_POST['municipality'] ?? '',
        'id_locality'       => $_POST['locality'] ?? '',
        'CP'                => $_POST['postalCode'] ?? '',
        'street'            => $_POST['street'] ?? '',
        'external_number'   => $_POST['extNumber'] ?? '',
        'internal_number'   => (isset($_POST['intNumber']) && $_POST['intNumber'] !== '') ? $_POST['intNumber'] : null,
        'neighborhood'      => $_POST['neighborhood'] ?? '',
        'insurance_number'  => $_POST['affiliationNumber'] ?? '',
        'professional_id'   => $_POST['professionalLicense'] ?? '',
        'birth_date'        => $_POST['birthDate'] ?? '',
        'CURP'              => $_POST['curp'] ?? '',
        'RFC'               => $_POST['rfc'] ?? '',
        'phone'             => $_POST['phoneNumber'] ?? '',
        'email'             => $_POST['email'] ?? '',
        'gender'            => $_POST['gender'] ?? '',
        'medical_area'      => $_POST['medical_area'] ?? ''
    ];

    // Manejo y validación de la foto
    $photoData = null;
    $updatePhoto = false; // Flag to indicate if we should update the photo

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photoTmp = $_FILES['photo']['tmp_name'];
        $photoMime = mime_content_type($photoTmp);
        if (!in_array($photoMime, ['image/jpeg', 'image/png', 'image/gif'])) {
            header('Location: /views/doctor/register/register-doctor.view.php?error=' . urlencode("El archivo debe ser una imagen JPG, PNG o GIF.") . '&id=' . ($doctorId ?? ''));
            exit;
        }
        $photoData = file_get_contents($photoTmp);
        $updatePhoto = true; // New photo uploaded, we should update it
    } elseif (!$doctorId) { // En creación, la foto es obligatoria
        header('Location: /views/doctor/register/register-doctor.view.php?error=' . urlencode("Debes subir una foto.") . '&id=' . ($doctorId ?? ''));
        exit;
    }

    try {
        // Instanciación del modelo de Doctor y validación de datos
        $doctorModel = new DoctorModel($pdo);
        $doctorModel->validateData($data, $doctorId);

        $emailController = new EmailController();

        $name = $data['names'] . " " . $data['last_name'] . " " . $data['last_name2'];
        $email = $data['email'];

        if ($doctorId) {
            // Actualización del doctor
            $doctorModel->updateDoctor($doctorId, $data, $photoData, $updatePhoto);

            // Actualizar la asignación del doctor al área médica
            $assignmentModel = new DoctorAssignmentModel($pdo);
            $assignmentModel->updateMedicalArea($doctorId, $data['medical_area']);

            // Correo de confirmación para actualización
            $subject = "Confirmación de Actualización de datos";
            $message = "Hola $name,\n\nTu Actualización de datos en nuestro sistema de administración medica Medic Life a sido exitoso" . "\nGracias por tu preferencia.\n\nSaludos.";
            $from = 'Medic Life <no-reply@sandbox3e6934d33e59407a9be71bc8778b9998.mailgun.org>';

            $result = $emailController->sendEmail($email, $subject, $message, $from);

            // Guardar horarios (si se envían)
            if (!empty($_POST['schedule']) && is_array($_POST['schedule'])) {
                $scheduleModel = new MedicalScheduleModel($pdo);
                $validDays = []; // Aquí se almacenan los días válidos que se conservarán

                foreach ($_POST['schedule'] as $day => $times) {
                    $startTime = $times['start_time'] ?? null;
                    $endTime = $times['end_time'] ?? null;
                    $isActive = isset($times['active']); // Verifica si el checkbox fue marcado

                    if ($isActive && !empty($startTime) && !empty($endTime)) {
                        $scheduleModel->saveOrUpdateSchedule($doctorId, $day, $startTime, $endTime);
                        $validDays[] = $day;
                    } else {
                        // Si no es válido, no lo añadimos a $validDays
                        // Pero no borramos aquí todavía
                    }
                }

                // Borrar horarios que ya no están activos
                $scheduleModel->deleteMissingSchedules($doctorId, $validDays);
            }

            header('Location: /views/doctor/list/list-doctors.view.php?success=' . urlencode("Doctor actualizado con éxito"));
        } else {
            // Creación de un nuevo doctor
            $newDoctorId = $doctorModel->createDoctor($data, $photoData);

            // Asignación del doctor al área médica
            $assignmentModel = new DoctorAssignmentModel($pdo);
            $assignmentModel->assignMedicalArea($newDoctorId, $data['medical_area']);

            $subject = "Confirmación de Registro de datos";
            $message = "Hola $name,\n\nTu Registro de datos en nuestro sistema de administración medica Medic Life a sido exitoso" . "\nGracias por tu preferencia.\n\nSaludos.";
            $from = 'Medic Life <no-reply@sandbox3e6934d33e59407a9be71bc8778b9998.mailgun.org>';

            $result = $emailController->sendEmail($email, $subject, $message, $from);

            // Guardar horarios (si se envían)
            if (!empty($_POST['schedule']) && is_array($_POST['schedule'])) {
                $scheduleModel = new MedicalScheduleModel($pdo);

                foreach ($_POST['schedule'] as $day => $times) {
                    $startTime = $times['start_time'] ?? null;
                    $endTime = $times['end_time'] ?? null;

                    if (empty($startTime) || empty($endTime)) {
                        // Opcional: borrar horario si los campos están vacíos
                        $scheduleModel->deleteSchedule($newDoctorId, $day);
                        continue;
                    }

                    // Guardar o actualizar horario
                    $scheduleModel->saveOrUpdateSchedule($newDoctorId, $day, $startTime, $endTime);
                }
            }

            // Crear usuario para el doctor si se seleccionó la opción
            if (isset($_POST['create_user']) && $_POST['create_user'] == '1') {
                try {

                    // Generar una contraseña aleatoria
                    $password = bin2hex(random_bytes(4)); // 8 caracteres aleatorios
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    // Insertar el nuevo usuario con rol de doctor
                    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, id_doctor, status) VALUES (:name, :email, :password, 'D', :id_doctor, 'AC')");
                    $stmt->execute([
                        ':name'     => $name,
                        ':email'    => $email,
                        ':password' => $hashedPassword,
                        ':id_doctor'=> $newDoctorId
                    ]);

                    // Enviar correo con la contraseña
                    $userSubject = "Credenciales de acceso a Medic Life";
                    $userMessage = "Hola $name,\n\nSe ha creado una cuenta de usuario para ti en el sistema Medic Life.\n\nTus credenciales de acceso son:\nCorreo: $email\nContraseña: $password\n\nPor favor, cambia tu contraseña después de iniciar sesión por primera vez.\n\nSaludos.";
                    $emailController->sendEmail($email, $userSubject, $userMessage, $from);

                    header('Location: /views/doctor/list/list-doctors.view.php?success=' . urlencode("Doctor creado con éxito y usuario creado con contraseña enviada por correo"));
                    exit;
                } catch (Exception $e) {
                    // Si hay un error al crear el usuario, continuamos con el flujo normal
                    // pero mostramos un mensaje de error
                    header('Location: /views/doctor/list/list-doctors.view.php?success=' . urlencode("Doctor creado con éxito") . '&error=' . urlencode("Error al crear usuario: " . $e->getMessage()));
                    exit;
                }
            }

            header('Location: /views/doctor/list/list-doctors.view.php?success=' . urlencode("Doctor creado con éxito"));
        }
    } catch (Exception $e) {
        header('Location: /views/doctor/register/register-doctor.view.php?error=' . urlencode($e->getMessage()) . '&id=' . ($doctorId ?? ''));
    }
}
