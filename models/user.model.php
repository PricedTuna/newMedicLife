<?php
require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';

function validateUser($email, $password) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE email = :email AND status = 'A'");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return true;
        }

        return false;
    } catch (PDOException $e) {
        error_log("Error al validar usuario: " . $e->getMessage());
        return false;
    }
}
?>
