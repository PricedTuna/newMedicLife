<?php
// config/database.php

$host     = "localhost:3306";
$dbname   = "medic_life_new";
$username = "root";
$password = "Root";

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
            // Log the error instead of displaying it directly
            error_log("Error de conexión a la base de datos: " . $e->getMessage());

            // Return null to indicate connection failure
            return null;
        }
    }
}

// Para mantener compatibilidad con código existente
try {
    $pdo = getConnection();

    // Verificar si la conexión fue exitosa
    if ($pdo === null) {
        // Log the error
        error_log("No se pudo establecer la conexión a la base de datos");

        // Set a user-friendly error message
        $_SESSION['db_error'] = "Hubo un problema al conectar con la base de datos. Por favor, inténtelo de nuevo más tarde.";
    }
} catch (PDOException $e) {
    // Log the error
    error_log("Error de conexión: " . $e->getMessage());

    // Set a user-friendly error message
    $_SESSION['db_error'] = "Hubo un problema al conectar con la base de datos. Por favor, inténtelo de nuevo más tarde.";
}
?>
