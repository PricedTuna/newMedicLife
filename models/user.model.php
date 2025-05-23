<?php
require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';

function validateUser($email, $password) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT password, role FROM users WHERE email = :email AND status = 'AC'");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return ['success' => true, 'role' => $user['role']];
        }

        return ['success' => false];
    } catch (PDOException $e) {
        error_log("Error al validar usuario: " . $e->getMessage());
        return ['success' => false];
    }
}
?>
