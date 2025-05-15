<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// controllers
require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/patient/patient.model.php';

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
        'marital_status'    => $_POST['marital_status'] ?? '',
        'ethnic_group'      => $_POST['ethnic_group'] ?? '',
        'religion'          => $_POST['religion'] ?? ''
    ];

    try {
        // Instanciación del modelo de Patient y validación de datos
        $patientModel = new PatientModel($pdo);
        $patientModel->validateData($data, $patientId);
        
        if ($patientId) {
            // Actualización del paciente
            $patientModel->updatePatient($patientId, $data);
            header('Location: /views/patient/list/list-patients.view.php?success=' . urlencode("Paciente actualizado con éxito"));
        } else {
            // Creación de un nuevo doctor
            $newPatientId = $patientModel->createPatient($data);
            
            header('Location: /views/patient/list/list-patients.view.php?success=' . urlencode("Paciente creado con éxito"));
        }
    } catch (Exception $e) {
        header('Location: /views/patient/register/register-patient.view.php?error=' . urlencode($e->getMessage()) . '&id=' . ($patientId ?? ''));
    }
}