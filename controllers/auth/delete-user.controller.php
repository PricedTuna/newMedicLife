<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';

// Only administrators can delete users
isAdmin();

// Get database connection
$pdo = getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['user_id']) && is_numeric($_POST['user_id'])) {
        $user_id = $_POST['user_id'];

        try {
            // Verificar si el usuario existe
            $stmt = $pdo->prepare("SELECT id, email FROM users WHERE id = :user_id");
            $stmt->execute([':user_id' => $user_id]);
            $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$existingUser) {
                header('Location: /views/user/register/register-user.view.php?error=' . urlencode("Usuario no encontrado"));
                exit;
            }

            // Evitar eliminar el usuario con el que se inició sesión
            if (isset($_SESSION['usuario']) && $existingUser['email'] === $_SESSION['usuario']) {
                header('Location: /views/user/list/list-users.view.php?error=' . urlencode("No puedes eliminar el usuario con el que has iniciado sesión"));
                exit;
            }

            // En lugar de eliminar físicamente, cambiamos el estado a inactivo
            $stmt = $pdo->prepare("UPDATE users SET status = 'IN' WHERE id = :user_id");
            $stmt->execute([':user_id' => $user_id]);

            header('Location: /views/user/list/list-users.view.php?success=' . urlencode("Usuario eliminado con éxito"));
            exit;
        } catch (Exception $e) {
            header('Location: /views/user/list/list-users.view.php?error=' . urlencode("Error al eliminar el usuario: " . $e->getMessage()));
            exit;
        }
    } else {
        header('Location: /views/user/list/list-users.view.php?error=' . urlencode("ID de usuario inválido"));
        exit;
    }
} else {
    header('Location: /views/user/list/list-users.view.php');
    exit;
}
?>
