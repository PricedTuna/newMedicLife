<?php
require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    exit('ID inválido.');
}

$doctor_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT photo FROM patients WHERE id = :id");
$stmt->execute([':id' => $doctor_id]);
$doctor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doctor || !$doctor['photo']) {
    http_response_code(404);
    exit('Imagen no encontrada.');
}

// Suponemos que las imágenes son JPG por defecto, puedes guardar el MIME real si quieres más precisión
header('Content-Type: image/jpeg');
echo $doctor['photo'];
