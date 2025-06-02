<?php
// config/database.php

$host     = "localhost:3306";
$dbname   = "medic_life";
$username = "root";
$password = "root";

// Variable global para la conexión
$GLOBALS['pdo'] = null;

/**
 * Función para obtener la conexión a la base de datos
 * @return PDO Objeto de conexión PDO
 */
if (!function_exists('getConnection')) {
    function getConnection() {
        global $host, $dbname, $username, $password;

        // Si ya existe una conexión, la devolvemos
        if ($GLOBALS['pdo'] !== null) {
            return $GLOBALS['pdo'];
        }

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            // Configuramos PDO para que lance excepciones en caso de error
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Guardamos la conexión en la variable global
            $GLOBALS['pdo'] = $pdo;

            return $pdo;
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
            exit;
        }
    }
}

// Para mantener compatibilidad con código existente
try {
    $pdo = getConnection();
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>
