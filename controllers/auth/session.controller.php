<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['usuario'])) {
    // User is not logged in, redirect to login page
    header('Location: /index.php');
    exit();
}
?>
