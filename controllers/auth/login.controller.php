<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/models/user.model.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    echo "Email recibido: " . htmlspecialchars($email) . "<br>";
    echo "Contraseña recibida: " . htmlspecialchars($password) . "<br>";

    if (validateUser($email, $password)) {
        $_SESSION['usuario'] = $email;
        header('Location: /views/dashboard/dashboard.view.php');

        exit();
    } else {
        echo "Error: Credenciales incorrectas"; // Depuración
        exit();
    }
} else {
    header('Location: ../index.php');
    exit();
}
?>
