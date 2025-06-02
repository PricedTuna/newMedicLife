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

        // Check if there's a redirect URL
        if (isset($_POST['redirect']) && !empty($_POST['redirect'])) {
            $redirect = urldecode($_POST['redirect']);
            // Make sure the redirect URL is within our site (security measure)
            if (strpos($redirect, '/') === 0) {
                header('Location: ' . $redirect);
                exit();
            }
        }

        // Default redirect to dashboard
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
