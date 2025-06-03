<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';
require $_SERVER['DOCUMENT_ROOT'] . '/models/user.model.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $userId = $_POST['user_id'] ?? null;
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    

    $errors = [];
    
    if (empty($userId)) {
        $errors[] = "ID de usuario no válido";
    }
    
    if (empty($currentPassword)) {
        $errors[] = "La contraseña actual es obligatoria";
    }
    
    if (empty($newPassword)) {
        $errors[] = "La nueva contraseña es obligatoria";
    } elseif (strlen($newPassword) < 8) {
        $errors[] = "La nueva contraseña debe tener al menos 8 caracteres";
    }
    
    if ($newPassword !== $confirmPassword) {
        $errors[] = "Las contraseñas no coinciden";
    }
    
    // If there are no errors, update the password
    if (empty($errors)) {
        $result = changeUserPassword($userId, $currentPassword, $newPassword);
        
        if ($result['success']) {
            // Redirect to profile page with success message
            header('Location: /views/user/profile/user-profile.view.php?success=' . urlencode($result['message']));
            exit;
        } else {
            // Redirect to profile page with error message
            header('Location: /views/user/profile/user-profile.view.php?error=' . urlencode($result['message']));
            exit;
        }
    } else {
        // Redirect to profile page with error messages
        $errorMessage = implode('. ', $errors);
        header('Location: /views/user/profile/user-profile.view.php?error=' . urlencode($errorMessage));
        exit;
    }
} else {
    // Redirect to profile page if accessed directly
    header('Location: /views/user/profile/user-profile.view.php');
    exit;
}
?>