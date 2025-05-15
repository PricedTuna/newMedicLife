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

try {
    // Obtener los datos del paciente si se pasa un ID
    $patient = null;
    if (isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = :id");
        $stmt->execute([':id' => $_GET['id']]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si el paciente tiene un id_emergency_contacts, consulta ese contacto
        if ($patient && !empty($patient['id_emergency_contact'])) {
            $stmt = $pdo->prepare("SELECT * FROM emergency_contacts WHERE id = :id");
            $stmt->execute([':id' => $patient['id_emergency_contact']]);
            $emergencyContacts = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
} catch (\Throwable $th) {  
    print_r($th);
    exit;
} catch (PDOException $e) {
    die("Error al obtener tablas: " . $e->getMessage());
}

// Inicializar Smarty
$smarty = new Smarty();

$smarty->setTemplateDir(__DIR__);

// Asignar las variables necesarias a Smarty
$smarty->assign('emergencyContacts', $emergencyContacts ?? null);
$smarty->assign('patient', $patient);
$smarty->assign('municipalities', $municipalities);
$smarty->assign('localities', $localities);
$smarty->assign('states', $states);
$smarty->assign('success', $_GET['success'] ?? null);

// Mostrar la plantilla
$smarty->display('register-patient-view.tpl');
