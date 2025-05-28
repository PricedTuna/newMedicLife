<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';
require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/models/doctor/doctor.model.php';

// Verificar que se haya enviado un archivo
if (!isset($_FILES['doctor_photo']) || $_FILES['doctor_photo']['error'] !== UPLOAD_ERR_OK) {
    $error = "Error al subir la imagen. Por favor, inténtelo de nuevo.";
    header("Location: /views/user/profile/user-profile.view.php?error=" . urlencode($error));
    exit;
}

// Verificar que se haya enviado el ID del doctor
if (!isset($_POST['doctor_id']) || !is_numeric($_POST['doctor_id'])) {
    $error = "ID de doctor inválido.";
    header("Location: /views/user/profile/user-profile.view.php?error=" . urlencode($error));
    exit;
}

$doctorId = (int)$_POST['doctor_id'];

// Verificar que el usuario actual sea el doctor o un administrador
$userEmail = $_SESSION['usuario'];
$stmt = $pdo->prepare("SELECT id, role, id_doctor FROM users WHERE email = :email");
$stmt->execute([':email' => $userEmail]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $error = "Usuario no encontrado.";
    header("Location: /views/user/profile/user-profile.view.php?error=" . urlencode($error));
    exit;
}

// Solo permitir actualizar la foto si el usuario es el doctor o un administrador
if ($user['role'] !== 'A' && ($user['role'] !== 'D' || $user['id_doctor'] != $doctorId)) {
    $error = "No tiene permisos para actualizar esta foto.";
    header("Location: /views/user/profile/user-profile.view.php?error=" . urlencode($error));
    exit;
}

// Verificar el tipo de archivo
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
$fileInfo = finfo_open(FILEINFO_MIME_TYPE);
$detectedType = finfo_file($fileInfo, $_FILES['doctor_photo']['tmp_name']);
finfo_close($fileInfo);

if (!in_array($detectedType, $allowedTypes)) {
    $error = "Tipo de archivo no permitido. Solo se permiten imágenes JPG, PNG y GIF.";
    header("Location: /views/user/profile/user-profile.view.php?error=" . urlencode($error));
    exit;
}

// Leer el contenido del archivo
$photoData = file_get_contents($_FILES['doctor_photo']['tmp_name']);

try {
    // Crear instancia del modelo de doctor
    $doctorModel = new DoctorModel($pdo);

    // Obtener datos actuales del doctor para mantener los demás campos sin cambios
    $stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = :id");
    $stmt->execute([':id' => $doctorId]);
    $doctorData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$doctorData) {
        throw new Exception("Doctor no encontrado.");
    }

    // Actualizar solo la foto del doctor
    $doctorModel->updateDoctor($doctorId, $doctorData, $photoData);

    // Redirigir con mensaje de éxito
    header("Location: /views/user/profile/user-profile.view.php?success=" . urlencode("Foto actualizada correctamente."));
    exit;
} catch (Exception $e) {
    $error = "Error al actualizar la foto: " . $e->getMessage();
    header("Location: /views/user/profile/user-profile.view.php?error=" . urlencode($error));
    exit;
}
