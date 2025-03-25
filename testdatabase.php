<?php
require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';

try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Error al obtener tablas: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tablas en la base de datos</title>
</head>
<body>
    <h1>Tablas encontradas en la base de datos:</h1>
    <ul>
        <?php foreach ($tables as $table): ?>
            <li><?php echo htmlspecialchars($table); ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
