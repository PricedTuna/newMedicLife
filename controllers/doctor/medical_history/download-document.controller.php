<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/medical_history/medical-history.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/utils.php';

// Inicializar conexión a la base de datos
$pdo = getConnection();

// Verificar que el usuario tenga permisos (debe ser doctor o administrador)
session_start();
if (!isset($_SESSION['usuario']) || ($_SESSION['role'] !== 'D' && $_SESSION['role'] !== 'A')) {
    header('Content-Type: text/html');
    echo 'No tiene permisos para realizar esta acción';
    exit;
}

// Verificar que se haya enviado el ID del documento
if (!isset($_GET['document_id']) || !is_numeric($_GET['document_id'])) {
    header('Content-Type: text/html');
    echo 'Debe proporcionar un ID de documento válido';
    exit;
}

try {
    $documentId = (int)$_GET['document_id'];

    // Instanciar el modelo
    $medicalHistoryModel = new MedicalHistoryModel($pdo);

    // Obtener el documento
    $document = $medicalHistoryModel->getDocument($documentId);

    if (!$document) {
        header('Content-Type: text/html');
        echo 'Documento no encontrado';
        exit;
    }

    // Configurar cabeceras para descargar el PDF
    $filename = 'Documento_' . $document['title'] . '_' . date('Y-m-d') . '.pdf';
    $filename = str_replace(' ', '_', $filename); // Reemplazar espacios con guiones bajos

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($document['file_content']));

    // Enviar el contenido del archivo
    echo $document['file_content'];

} catch (Exception $e) {
    // Registrar el error
    error_log('Error al descargar documento: ' . $e->getMessage());

    // Mostrar mensaje de error
    header('Content-Type: text/html');
    echo 'Error al descargar el documento: ' . $e->getMessage();
}
