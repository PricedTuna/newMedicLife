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
        $stmt = $pdo->prepare("SELECT id, name, email, role, id_doctor, created_at, status FROM users WHERE status = 'AC' ORDER BY id ASC");
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

function updateUserProfile($userId, $data) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
        $stmt->execute([
            ':id' => $userId,
            ':name' => $data['name'],
            ':email' => $data['email']
        ]);

        return ['success' => true, 'message' => 'Perfil actualizado correctamente'];
    } catch (PDOException $e) {
        error_log("Error al actualizar perfil de usuario: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error al actualizar el perfil: ' . $e->getMessage()];
    }
}

function changeUserPassword($userId, $currentPassword, $newPassword) {
    global $pdo;

    try {
        // First, verify the current password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ['success' => false, 'message' => 'Usuario no encontrado'];
        }

        // Verify that the current password is correct
        if (!password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'message' => 'La contraseña actual es incorrecta'];
        }

        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the password in the database
        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->execute([
            ':id' => $userId,
            ':password' => $hashedPassword
        ]);

        return ['success' => true, 'message' => 'Contraseña actualizada correctamente'];
    } catch (PDOException $e) {
        error_log("Error al cambiar contraseña: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error al cambiar la contraseña: ' . $e->getMessage()];
    }
}
?>
