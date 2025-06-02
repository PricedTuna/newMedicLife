<?php



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// controllers
require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/patient/patient.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/emergency_contacts/emergency_contacts.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/email/email.controller.php';

// Only administrators and secretaries can manage patients
checkUserRole(['A', 'S']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recopilación centralizada de datos del formulario
    $patientId = isset($_POST['patient_id']) && is_numeric($_POST['patient_id']) ? $_POST['patient_id'] : null;
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
        'internal_number'   => !empty($_POST['intNumber']) ? $_POST['intNumber'] : null,
        'neighborhood'      => $_POST['neighborhood'] ?? '',
        'insurance_number'  => $_POST['affiliationNumber'] ?? '',
        'birth_date'        => $_POST['birthDate'] ?? '',
        'CURP'              => $_POST['curp'] ?? '',
        'RFC'               => $_POST['rfc'] ?? '',
        'phone'             => $_POST['phoneNumber'] ?? '',
        'email'             => $_POST['email'] ?? '',
        'gender'            => $_POST['gender'] ?? '',
        'medical_area'      => $_POST['medical_area'] ?? '',
        'weight'            => $_POST['weight'] ?? '',
        'height'            => $_POST['height'] ?? '',
        'blood_type'        => $_POST['blood_type'] ?? '',
        'id_emergency_contact' => $_POST['id_emergency_contact'] ?? '',
        'marital_status'    => $_POST['marital_status'] ?? '',
        'ethnic_group'      => $_POST['ethnic_group'] ?? '',
        'religion'          => $_POST['religion'] ?? ''
    ];

    $emergencyContactsId = isset($_POST['emergency_contacts_id']) && is_numeric($_POST['emergency_contacts_id']) ? $_POST['emergency_contacts_id'] : null;
    $dataContact = [
        'names'             => $_POST['ec_name'] ?? '',
        'last_name'         => $_POST['ec_fatherLastName'] ?? '',
        'last_name2'        => $_POST['ec_motherLastName'] ?? '',
        'phone'             => $_POST['ec_phoneNumber'] ?? '',
        'relationship'      => $_POST['ec_relationship'] ?? ''
    ];

    // Manejo y validación de la foto

    $photoData = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photoTmp = $_FILES['photo']['tmp_name'];
        $photoMime = mime_content_type($photoTmp);
        if (!in_array($photoMime, ['image/jpeg', 'image/png', 'image/gif'])) {
            header('Location: /views/patient/register/register-patient.view.php?error=' . urlencode("El archivo debe ser una imagen JPG, PNG o GIF.") . '&id=' . ($doctorId ?? ''));
            exit;
        }
        $photoData = file_get_contents($photoTmp);
    } elseif (!$patientId) { // En creación, la foto es obligatoria
        header('Location: /views/doctor/patient/register-patient.view.php?error=' . urlencode("Debes subir una foto.") . '&id=' . ($doctorId ?? ''));
        exit;
    }

    try {

        $emergencyContactsModel = new EmergencyContactsModel($pdo);
        $emergencyContactsModel->validateData($dataContact, $emergencyContactsId);
        // Instanciación del modelo de Patient y validación de datos
        $patientModel = new PatientModel($pdo);
        $patientModel->validateData($data, $patientId);

        $emailController = new EmailController();

        $name = $data['names'] . " " . $data['last_name'] . " " . $data['last_name2'];
        $email = $data['email'];


        if ($patientId && $emergencyContactsId) {
            // Actualización del paciente
            $emergencyContactsModel->updateEmergencyContact($emergencyContactsId, $dataContact);

            $data['id_emergency_contact'] = $emergencyContactsId;

            $patientModel->updatePatient($patientId, $data, $photoData);

            // Correo de confirmación para actualización
            $subject = "Confirmación de Actualización de datos";
            $message = "Hola $name,\n\nTu Actualización de datos en nuestro sistema de administración medica Medic Life a sido exitoso" . "\nGracias por tu preferencia.\n\nSaludos.";
            $from = 'Medic Life <no-reply@sandbox3e6934d33e59407a9be71bc8778b9998.mailgun.org>';

            $result = $emailController->sendEmail($email, $subject, $message, $from);

            header('Location: /views/patient/list/list-patients.view.php?success=' . urlencode("Paciente actualizado con éxito"));
        } else {
            // Creación de un nuevo paciente
            $newEmergencyContactsID = $emergencyContactsModel->createEmergencyContact($dataContact);

            $data['id_emergency_contact'] = $newEmergencyContactsID;

            $newPatientId = $patientModel->createPatient($data, $photoData);
            // Correo de confirmación para actualizacion
            $subject = "Confirmación de Registro de datos";
            $message = "Hola $name,\n\nTu Registro de datos en nuestro sistema de administración medica Medic Life a sido exitoso" . "\nGracias por tu preferencia.\n\nSaludos.";
            $from = 'Medic Life <no-reply@sandbox3e6934d33e59407a9be71bc8778b9998.mailgun.org>';

            $result = $emailController->sendEmail($email, $subject, $message, $from);

            header('Location: /views/patient/list/list-patients.view.php?success=' . urlencode("Paciente creado con éxito"));
        }
    } catch (Exception $e) {
        header('Location: /views/patient/register/register-patient.view.php?error=' . urlencode($e->getMessage()) . '&id=' . ($patientId ?? ''));
    }
}
