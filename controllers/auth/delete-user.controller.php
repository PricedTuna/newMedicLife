<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['user_id']) && is_numeric($_POST['user_id'])) {
        $user_id = $_POST['user_id'];

        try {
            // Verificar si el usuario existe
            $stmt = $pdo->prepare("SELECT id FROM users WHERE id = :user_id");
            $stmt->execute([':user_id' => $user_id]);
            $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$existingUser) {
                header('Location: /views/user/register/register-user.view.php?error=' . urlencode("Usuario no encontrado"));
                exit;
            }

            // En lugar de eliminar físicamente, cambiamos el estado a inactivo
            $stmt = $pdo->prepare("UPDATE users SET status = 'IN' WHERE id = :user_id");
            $stmt->execute([':user_id' => $user_id]);

            header('Location: /views/user/register/register-user.view.php?success=' . urlencode("Usuario eliminado con éxito"));
            exit;
        } catch (Exception $e) {
            header('Location: /views/user/register/register-user.view.php?error=' . urlencode("Error al eliminar el usuario: " . $e->getMessage()));
            exit;
        }
    } else {
        header('Location: /views/user/register/register-user.view.php?error=' . urlencode("ID de usuario inválido"));
        exit;
    }
} else {
    header('Location: /views/user/register/register-user.view.php');
    exit;
}
?>
