<?php
// controllers/doctor/get-doctor-photo.controller.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';

// Verificar que se ha proporcionado un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    exit('ID de doctor no válido');
}

$doctorId = $_GET['id'];

try {
    // Obtener la foto del doctor
    $stmt = $pdo->prepare("SELECT photo FROM doctors WHERE id = :id");
    $stmt->execute([':id' => $doctorId]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$doctor || !$doctor['photo']) {
        header('HTTP/1.1 404 Not Found');
        exit('Foto no encontrada');
    }

    // Establecer las cabeceras para la imagen
    header('Content-Type: image/jpeg');
    header('Content-Length: ' . strlen($doctor['photo']));
    
    // Enviar la imagen
    echo $doctor['photo'];
} catch (PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    exit('Error al obtener la foto: ' . $e->getMessage());
}