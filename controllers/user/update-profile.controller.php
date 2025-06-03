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
    // Get form data
    $userId = $_POST['user_id'] ?? null;
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    

    $errors = [];
    
    if (empty($userId)) {
        $errors[] = "ID de usuario no válido";
    }
    
    if (empty($name)) {
        $errors[] = "El nombre es obligatorio";
    }
    
    if (empty($email)) {
        $errors[] = "El correo electrónico es obligatorio";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El correo electrónico no es válido";
    }
    

    if (!empty($email)) {
        $existingUser = getUserByEmail($email);
        if ($existingUser && $existingUser['id'] != $userId) {
            $errors[] = "El correo electrónico ya está en uso por otro usuario";
        }
    }
    

    if (empty($errors)) {
        $data = [
            'name' => $name,
            'email' => $email
        ];
        
        $result = updateUserProfile($userId, $data);
        
        if ($result['success']) {
            // Update session email if it was changed
            if ($_SESSION['usuario'] !== $email) {
                $_SESSION['usuario'] = $email;
            }
            

            header('Location: /views/user/profile/user-profile.view.php?success=' . urlencode($result['message']));
            exit;
        } else {

            header('Location: /views/user/profile/user-profile.view.php?error=' . urlencode($result['message']));
            exit;
        }
    } else {

        $errorMessage = implode('. ', $errors);
        header('Location: /views/user/profile/user-profile.view.php?error=' . urlencode($errorMessage));
        exit;
    }
} else {

    header('Location: /views/user/profile/user-profile.view.php');
    exit;
}
?>