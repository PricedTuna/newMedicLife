<?php
require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';

try {
    $stmt = $pdo->query("SELECT id, password FROM users");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Verifica si la contraseña ya está encriptada
    if (password_needs_rehash($row['password'], PASSWORD_DEFAULT)) {
        $hashedPassword = password_hash($row['password'], PASSWORD_DEFAULT);

        $updateStmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
        $updateStmt->execute([
            ':password' => $hashedPassword,
            ':id' => $row['id']
        ]);
    }
}
} catch (\Throwable $th) {
    echo var_dump($th);
    exit;
}


echo "✅ Contraseñas actualizadas correctamente.";
?>
