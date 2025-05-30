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

function getAllUsers() {
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT id, name, email, role, id_doctor, created_at, status FROM users ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener usuarios: " . $e->getMessage());
        return [];
    }
}

function getUserById($id) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT id, name, email, role, id_doctor, status FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener usuario por ID: " . $e->getMessage());
        return null;
    }
}

function getUserByEmail($email) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT id, name, email, role, id_doctor, status FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener usuario por email: " . $e->getMessage());
        return null;
    }
}
?>
