<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include $_SERVER['DOCUMENT_ROOT'] . '/models/user.model.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = validateUser($email, $password);
    if ($result['success']) {
        $_SESSION['usuario'] = $email;
        $_SESSION['userId'] = $email;
        $_SESSION['role'] = $result['role'];

        // If user is a doctor, get the doctor ID
        if ($result['role'] === 'D') {
            $user = getUserByEmail($email);
            if ($user && isset($user['id_doctor'])) {
                $_SESSION['doctorId'] = $user['id_doctor'];
            }
        }

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
