<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/models/user.model.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    echo "Email recibido: " . htmlspecialchars($email) . "<br>";
    echo "Contraseña recibida: " . htmlspecialchars($password) . "<br>";

    $result = validateUser($email, $password);
    if ($result['success']) {
        $_SESSION['usuario'] = $email;
        $_SESSION['role'] = $result['role'];
        header('Location: /views/dashboard/dashboard.view.php');

        exit();
    } else {
        header('Location: /index.php?error=' . urlencode("Credenciales incorrectas"));
        exit();
    }
} else {
    header('Location: ../index.php');
    exit();
}
?>
