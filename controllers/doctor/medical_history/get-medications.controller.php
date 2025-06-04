<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';

// Inicializar conexión a la base de datos
$pdo = getConnection();

// Verificar si hay un error de conexión a la base de datos
if ($pdo === null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Hubo un problema al conectar con la base de datos. Por favor, inténtelo de nuevo más tarde.'
    ]);
    exit;
}

// Verificar que el usuario tenga permisos (debe ser doctor o administrador)
session_start();
if (!isset($_SESSION['usuario']) || ($_SESSION['role'] !== 'D' && $_SESSION['role'] !== 'A')) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'No tiene permisos para realizar esta acción'
    ]);
    exit;
}

// Verificar que se haya enviado el ID del tipo de medicamento
if (!isset($_GET['type_id']) || !is_numeric($_GET['type_id'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Debe proporcionar un ID de tipo de medicamento válido'
    ]);
    exit;
}

$typeId = (int)$_GET['type_id'];

try {
    // Obtener medicamentos del tipo seleccionado
    $stmt = $pdo->prepare("
        SELECT * FROM medications 
        WHERE id_medicine_type = :type_id 
        ORDER BY name
    ");
    $stmt->execute([':type_id' => $typeId]);
    $medications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver resultados
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'medications' => $medications
    ]);
} catch (PDOException $e) {
    // Registrar el error
    error_log('Error al obtener medicamentos: ' . $e->getMessage());

    // Devolver mensaje de error
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener medicamentos: ' . $e->getMessage()
    ]);
}