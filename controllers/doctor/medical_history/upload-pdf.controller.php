<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/medical_history/medical-history.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/utils.php';

// Inicializar conexión a la base de datos
$pdo = getConnection();

// Verificar si hay un error de conexión a la base de datos
if ($pdo === null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Hubo un problema al conectar con la base de datos. Por favor, inténtelo de nuevo más tarde.'
    ]);
    exit;
}

// Verificar que el usuario tenga permisos (debe ser doctor o administrador)
session_start();
if (!isset($_SESSION['usuario']) || ($_SESSION['role'] !== 'D' && $_SESSION['role'] !== 'A')) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'No tiene permisos para realizar esta acción'
    ]);
    exit;
}

// Verificar que se hayan enviado los datos necesarios
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || 
    !isset($_POST['patient-id']) || !is_numeric($_POST['patient-id']) ||
    !isset($_POST['document-title']) || empty($_POST['document-title']) ||
    !isset($_FILES['pdf-file']) || $_FILES['pdf-file']['error'] !== UPLOAD_ERR_OK) {

    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Datos incompletos o inválidos'
    ]);
    exit;
}

try {
    // Obtener ID del doctor actual
    $doctorId = null;

    if ($_SESSION['role'] === 'D') {
        // Si es un doctor, usar su ID
        $stmt = $pdo->prepare("SELECT id_doctor FROM users WHERE email = :email");
        $stmt->execute([':email' => $_SESSION['usuario']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !$user['id_doctor']) {
            throw new Exception('No se pudo determinar el ID del doctor');
        }

        $doctorId = $user['id_doctor'];
    } else {
        // Si es un administrador, verificar si se especificó un doctor
        if (!isset($_POST['doctor-id']) || !is_numeric($_POST['doctor-id'])) {
            throw new Exception('Debe especificar un doctor para el documento');
        }

        $doctorId = (int)$_POST['doctor-id'];
    }

    // Verificar que el archivo sea un PDF
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    $detectedType = finfo_file($fileInfo, $_FILES['pdf-file']['tmp_name']);
    finfo_close($fileInfo);

    if ($detectedType !== 'application/pdf') {
        throw new Exception('El archivo debe ser un PDF válido');
    }

    // Verificar tamaño del archivo (máximo 10MB)
    if ($_FILES['pdf-file']['size'] > 10 * 1024 * 1024) {
        throw new Exception('El archivo es demasiado grande. El tamaño máximo permitido es 10MB');
    }

    // Leer el contenido del archivo
    $fileContent = file_get_contents($_FILES['pdf-file']['tmp_name']);

    if ($fileContent === false) {
        throw new Exception('No se pudo leer el archivo');
    }

    // Preparar datos para guardar
    $data = [
        'patient_id' => (int)$_POST['patient-id'],
        'doctor_id' => $doctorId,
        'title' => $_POST['document-title'],
        'description' => isset($_POST['document-description']) ? $_POST['document-description'] : ''
    ];

    // Instanciar el modelo
    $medicalHistoryModel = new MedicalHistoryModel($pdo);

    // Guardar el documento
    $documentId = $medicalHistoryModel->savePdfDocument($data, $fileContent);

    if (!$documentId) {
        throw new Exception('No se pudo guardar el documento');
    }

    // Devolver respuesta exitosa
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Documento guardado correctamente',
        'document_id' => $documentId
    ]);
} catch (Exception $e) {
    // Registrar el error
    error_log('Error al subir documento PDF: ' . $e->getMessage());

    // Devolver mensaje de error
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error al subir documento: ' . $e->getMessage()
    ]);
}
