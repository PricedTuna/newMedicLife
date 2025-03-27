<?php
// obtener_doctores.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// controllers/DoctorController.php
require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/doctor/doctor.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/doctor/doctor-assignment.model.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recopilación centralizada de datos del formulario
    $doctorId = isset($_POST['doctor_id']) && is_numeric($_POST['doctor_id']) ? $_POST['doctor_id'] : null;
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
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photoTmp = $_FILES['photo']['tmp_name'];
        $photoMime = mime_content_type($photoTmp);
        if (!in_array($photoMime, ['image/jpeg', 'image/png', 'image/gif'])) {
            header('Location: /views/doctor/register/register-doctor.view.php?error=' . urlencode("El archivo debe ser una imagen JPG, PNG o GIF.") . '&id=' . ($doctorId ?? ''));
            exit;
        }
        $photoData = file_get_contents($photoTmp);
    } elseif (!$doctorId) { // En creación, la foto es obligatoria
        header('Location: /views/doctor/register/register-doctor.view.php?error=' . urlencode("Debes subir una foto.") . '&id=' . ($doctorId ?? ''));
        exit;
    }
    
    try {
        // Instanciación del modelo de Doctor y validación de datos
        $doctorModel = new DoctorModel($pdo);
        $doctorModel->validateData($data, $doctorId);
        
        if ($doctorId) {
            // Actualización del doctor
            $doctorModel->updateDoctor($doctorId, $data, $photoData);
            header('Location: /views/doctor/list/list-doctors.view.php?success=' . urlencode("Doctor actualizado con éxito"));
        } else {
            // Creación de un nuevo doctor
            $newDoctorId = $doctorModel->createDoctor($data, $photoData);
            
            // Asignación del doctor al área médica
            $assignmentModel = new DoctorAssignmentModel($pdo);
            $assignmentModel->assignMedicalArea($newDoctorId, $data['medical_area']);
            
            header('Location: /views/doctor/list/list-doctors.view.php?success=' . urlencode("Doctor creado con éxito"));
        }
    } catch (Exception $e) {
        header('Location: /views/doctor/register/register-doctor.view.php?error=' . urlencode($e->getMessage()) . '&id=' . ($doctorId ?? ''));
    }
}
