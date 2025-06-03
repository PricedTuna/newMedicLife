<?php
// controllers/user/check-email-uniqueness.controller.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/models/user.model.php';

// Verificar que la solicitud sea de tipo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener los parámetros de la solicitud
$email = $_POST['email'] ?? '';
$userId = isset($_POST['userId']) && !empty($_POST['userId']) ? $_POST['userId'] : null;

// Validar que se hayan proporcionado los parámetros necesarios
if (empty($email)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Parámetros incompletos']);
    exit;
}

try {
    // Obtener usuario por email
    $user = getUserByEmail($email);

    // Si se proporciona un ID de usuario, verificar que no sea el mismo usuario
    if ($user && $userId !== null && $user['id'] == $userId) {
        // Es el mismo usuario, por lo que el email es válido para este usuario
        echo json_encode([
            'success' => true,
            'message' => 'El correo electrónico está disponible.'
        ]);
        exit;
    }

    if ($user) {
        // El email ya existe en la base de datos
        echo json_encode([
            'success' => false,
            'message' => 'El correo electrónico ya está registrado en el sistema.'
        ]);
    } else {
        // El email es único
        echo json_encode([
            'success' => true,
            'message' => 'El correo electrónico está disponible.'
        ]);
    }
} catch (PDOException $e) {
    // Error en la base de datos
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}