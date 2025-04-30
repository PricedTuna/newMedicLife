<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Smarty\Smarty;

// Cargar el autoloader de Composer
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

try {
    // Conexión a la base de datos y consultas
    require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';

    // Consulta de Municipios
    $stmt = $pdo->query("SELECT * FROM municipalities");
    $municipalities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consulta de Estados
    $stmt = $pdo->query("SELECT * FROM states");
    $states = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consulta de Localidades
    $stmt = $pdo->query("SELECT * FROM localities");
    $localities = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Log del error
    error_log($e->getMessage());
    echo "Hubo un problema al cargar los datos. Por favor, intente más tarde.";
    exit();
}

// Verificar si las variables contienen datos
if (empty($municipalities) || empty($localities) || empty($states)) {
    // Si alguna de las variables está vacía, mostramos un mensaje de advertencia en el navegador
    echo "<script>alert('Advertencia: Algunos datos no se han cargado correctamente.');</script>";
}

// Inicializar Smarty
$smarty = new Smarty();

$smarty->setTemplateDir(__DIR__);

// Asignar las variables necesarias a Smarty
$smarty->assign('municipalities', $municipalities);
$smarty->assign('localities', $localities);
$smarty->assign('states', $states);
$smarty->assign('success', $_GET['success'] ?? null);

// Mostrar la plantilla
$smarty->display('register-patient-view.tpl');
