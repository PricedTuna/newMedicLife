<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/medical_history/medical-history.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/utils.php';

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

// Verificar que se hayan enviado los datos necesarios
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['patient-id']) || !is_numeric($_POST['patient-id'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Datos incompletos o inválidos'
    ]);
    exit;
}

try {
    // Debug: Log all POST data
    error_log("POST data: " . print_r($_POST, true));

    // Obtener ID del doctor seleccionado en el formulario (ahora es opcional)
    $doctorId = null;

    // Verificar si se especificó un doctor en el formulario
    if (isset($_POST['doctor-id']) && is_numeric($_POST['doctor-id'])) {
        $doctorId = (int)$_POST['doctor-id'];
        error_log("Doctor ID from form: " . $doctorId);
    } else if ($_SESSION['role'] === 'D') {
        // Si no se especificó un doctor en el formulario, intentar usar el ID del doctor actual (si es un doctor)
        $stmt = $pdo->prepare("SELECT id_doctor FROM users WHERE email = :email");
        $stmt->execute([':email' => $_SESSION['usuario']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Debug: Log user data
        error_log("User data: " . print_r($user, true));

        if ($user && $user['id_doctor']) {
            $doctorId = $user['id_doctor'];
        }
    }

    // Debug: Log doctor ID
    error_log("Doctor ID: " . ($doctorId ? $doctorId : "Not provided"));

    // Preparar datos para guardar
    $data = [
        'patient_id' => (int)$_POST['patient-id'],
        'doctor_id' => $doctorId,
        'date_created' => $_POST['record-date'] . ' ' . date('H:i:s'),
        'chief_complaint' => $_POST['chief-complaint'] ?? null,
        'current_illness' => $_POST['current-illness'] ?? null,
        'personal_history' => $_POST['personal-history'] ?? null,
        'family_history' => $_POST['family-history'] ?? null,
        'physical_examination' => $_POST['physical-examination'] ?? null,
        'diagnosis' => $_POST['diagnosis'] ?? null,
        'treatment_plan' => $_POST['treatment-plan'] ?? null,
        'observations' => $_POST['observations'] ?? null,
        'next_appointment' => !empty($_POST['next-appointment']) ? $_POST['next-appointment'] : null
    ];

    // Preparar datos de signos vitales si se proporcionaron
    if (isset($_POST['temperature']) || isset($_POST['blood-pressure']) || 
        isset($_POST['heart-rate']) || isset($_POST['respiratory-rate']) || 
        isset($_POST['weight']) || isset($_POST['height']) || 
        isset($_POST['oxygen-saturation']) || isset($_POST['glucose-level'])) {

        $data['vital_signs'] = [
            'temperature' => $_POST['temperature'] ?? null,
            'blood_pressure' => $_POST['blood-pressure'] ?? null,
            'heart_rate' => $_POST['heart-rate'] ?? null,
            'respiratory_rate' => $_POST['respiratory-rate'] ?? null,
            'weight' => $_POST['weight'] ?? null,
            'height' => $_POST['height'] ?? null,
            'bmi' => isset($_POST['weight']) && isset($_POST['height']) && !empty($_POST['weight']) && !empty($_POST['height']) ? 
                    round($_POST['weight'] / (($_POST['height']/100) * ($_POST['height']/100)), 2) : null,
            'oxygen_saturation' => $_POST['oxygen-saturation'] ?? null,
            'glucose_level' => $_POST['glucose-level'] ?? null,
            'measured_at' => date('Y-m-d H:i:s')
        ];
    }

    // Validar datos mínimos requeridos
    if (empty($data['date_created'])) {
        throw new Exception('La fecha es obligatoria');
    }

    if (empty($data['diagnosis']) && empty($data['chief_complaint'])) {
        throw new Exception('El diagnóstico o la queja principal son obligatorios');
    }

    // Instanciar el modelo
    $medicalHistoryModel = new MedicalHistoryModel($pdo);

    // Check if the medical_history table exists
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as table_exists 
        FROM information_schema.tables 
        WHERE table_schema = DATABASE() 
        AND table_name = 'medical_history'
    ");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['table_exists'] == 0) {
        error_log("Medical history table does not exist. Attempting to create it...");

        // Try to create the tables
        $sqlFile = $_SERVER['DOCUMENT_ROOT'] . '/models/medical_history/medical-history-tables.sql';
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            $statements = explode(';', $sql);

            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    try {
                        $pdo->exec($statement);
                        error_log("Executed SQL statement: " . substr($statement, 0, 50) . "...");
                    } catch (PDOException $e) {
                        error_log("Error creating table: " . $e->getMessage());
                        throw new Exception('No se pudieron crear las tablas necesarias. Por favor, ejecute el script create-medical-history-tables.php primero.');
                    }
                }
            }

            error_log("Tables created successfully");
        } else {
            error_log("SQL file not found: " . $sqlFile);
            throw new Exception('No se encontró el archivo SQL para crear las tablas. Por favor, contacte al administrador.');
        }
    }

    // Guardar el registro
    $recordId = $medicalHistoryModel->saveHistoryRecord($data);

    if (!$recordId) {
        // Get the last error from the error log
        $errorLogFile = ini_get('error_log');
        $lastError = '';
        if (file_exists($errorLogFile)) {
            $errorLines = file($errorLogFile);
            if (!empty($errorLines)) {
                $lastError = end($errorLines);
            }
        }

        error_log("Failed to save record. Last error: " . $lastError);
        throw new Exception('No se pudo guardar el registro. Verifique que todas las tablas necesarias existan en la base de datos.');
    }

    // Devolver respuesta exitosa
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Registro guardado correctamente',
        'record_id' => $recordId
    ]);
} catch (PDOException $e) {
    // Registrar el error de base de datos
    error_log('Error de base de datos al guardar registro médico: ' . $e->getMessage());
    error_log('SQL state: ' . $e->getCode());

    // Check if it's a table not found error
    if (strpos($e->getMessage(), "Table") !== false && strpos($e->getMessage(), "doesn't exist") !== false) {
        // Return a specific error for table not found
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Las tablas necesarias no existen en la base de datos.',
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'table_error' => true
        ]);
        exit;
    }

    // Check if it's a foreign key constraint error
    if (strpos($e->getMessage(), "foreign key constraint fails") !== false) {
        // Return a specific error for foreign key constraint
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Error de referencia: Asegúrese de que el paciente y el doctor existan en la base de datos.',
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'constraint_error' => true
        ]);
        exit;
    }

    // Devolver mensaje de error detallado
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos al guardar registro',
        'error' => $e->getMessage(),
        'code' => $e->getCode()
    ]);
} catch (Exception $e) {
    // Registrar el error
    error_log('Error al guardar registro médico: ' . $e->getMessage());

    // Devolver mensaje de error
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar registro: ' . $e->getMessage()
    ]);
}
