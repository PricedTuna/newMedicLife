<?php
// controllers/patient/check-field-uniqueness.controller.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';

// Verificar que la solicitud sea de tipo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener los parámetros de la solicitud
$field = $_POST['field'] ?? '';
$value = $_POST['value'] ?? '';
$patientId = isset($_POST['patientId']) && !empty($_POST['patientId']) ? $_POST['patientId'] : null;

// Validar que se hayan proporcionado los parámetros necesarios
if (empty($field) || empty($value)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Parámetros incompletos']);
    exit;
}

// Validar que el campo sea uno de los permitidos
$allowedFields = ['email', 'CURP', 'RFC', 'phone', 'insurance_number'];
if (!in_array($field, $allowedFields)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Campo no válido']);
    exit;
}

try {
    // Preparar la consulta SQL para verificar la unicidad del campo
    $query = "SELECT id FROM patients WHERE $field = :value";
    $params = [':value' => $value];

    // Si se proporciona un ID de paciente, excluirlo de la búsqueda (para actualizaciones)
    if ($patientId !== null) {
        $query .= " AND id != :patientId";
        $params[':patientId'] = $patientId;
    }

    // Obtener la conexión a la base de datos
    $pdo = $GLOBALS['pdo'];
    if ($pdo === null) {
        $pdo = getConnection();
    }

    if ($pdo === null) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['success' => false, 'message' => 'No se pudo establecer la conexión a la base de datos']);
        exit;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Determinar el mensaje según el campo
    $fieldMessages = [
        'email' => 'El correo electrónico',
        'CURP' => 'La CURP',
        'RFC' => 'El RFC',
        'phone' => 'El número de teléfono',
        'insurance_number' => 'El número de afiliación'
    ];

    if ($result) {
        // El campo ya existe en la base de datos
        echo json_encode([
            'success' => false,
            'message' => $fieldMessages[$field] . ' ya está registrado en el sistema.'
        ]);
    } else {
        // El campo es único
        echo json_encode([
            'success' => true,
            'message' => $fieldMessages[$field] . ' está disponible.'
        ]);
    }
} catch (PDOException $e) {
    // Error en la base de datos
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}
