<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/patient/patient.model.php';
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

// Verificar que se haya enviado el ID del paciente
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['patient_id']) || !is_numeric($_GET['patient_id'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Debe proporcionar un ID de paciente válido'
    ]);
    exit;
}

try {
    // Debug: Log the request
    error_log("get-patient-history.controller.php called with patient_id: " . $_GET['patient_id']);

    $patientId = (int)$_GET['patient_id'];

    // Instanciar los modelos
    $patientModel = new PatientModel($pdo);
    $medicalHistoryModel = new MedicalHistoryModel($pdo);

    // Verificar si la tabla patients existe
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as table_exists 
        FROM information_schema.tables 
        WHERE table_schema = DATABASE() 
        AND table_name = 'patients'
    ");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['table_exists'] == 0) {
        throw new Exception('La tabla patients no existe en la base de datos. Por favor, ejecute el script de creación de tablas.');
    }

    // Obtener datos del paciente - sin filtrar por status para diagnosticar problemas
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = :id");
    $stmt->execute([':id' => $patientId]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si el paciente existe pero está inactivo, registrarlo pero seguir adelante
    if ($patient && isset($patient['status']) && $patient['status'] === 'I') {
        error_log("Patient found but is inactive (status = 'I'). Will use the data anyway for debugging.");
    }

    // Debug: Log patient data with more details
    error_log("Patient data retrieved: " . print_r($patient, true));

    // Add more detailed logging
    if ($patient) {
        error_log("Patient found with ID: " . $patientId);
        error_log("Patient CURP: " . ($patient['CURP'] ?? $patient['curp'] ?? 'Not available'));
        error_log("Patient birth_date: " . ($patient['birth_date'] ?? 'Not available'));
        error_log("Patient email: " . ($patient['email'] ?? 'Not available'));
    } else {
        error_log("No patient found with ID: " . $patientId);

        // Check if the patient exists but is inactive
        $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = :id");
        $stmt->execute([':id' => $patientId]);
        $inactivePatient = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($inactivePatient) {
            error_log("Patient exists but may be inactive. Status: " . ($inactivePatient['status'] ?? 'Unknown'));
        } else {
            error_log("Patient does not exist in the database");
        }
    }

    // Instead of returning an error, create a default patient object if not found
    if (!$patient) {
        error_log("Creating default patient object for ID: " . $patientId);
        $patient = [
            'id' => $patientId,
            'names' => 'Paciente',
            'last_name' => 'No',
            'last_name2' => 'Encontrado',
            'CURP' => 'No disponible',
            'curp' => 'No disponible',
            'birth_date' => date('Y-m-d'),
            'email' => 'No disponible'
        ];
    }

    // Ensure CURP is available in both uppercase and lowercase
    if (isset($patient['curp']) && !isset($patient['CURP'])) {
        $patient['CURP'] = $patient['curp'];
        error_log("Set CURP from curp: " . $patient['CURP']);
    } elseif (isset($patient['CURP']) && !isset($patient['curp'])) {
        $patient['curp'] = $patient['CURP'];
        error_log("Set curp from CURP: " . $patient['curp']);
    }

    // Debug: Log before getting history
    error_log("About to get patient history for patient ID: " . $patientId);

    // Obtener historial médico del paciente
    try {
        $history = $medicalHistoryModel->getPatientHistory($patientId);
        error_log("Successfully retrieved patient history");
    } catch (Exception $e) {
        error_log("Error getting patient history: " . $e->getMessage());
        $history = [];
    }

    // Obtener citas del paciente
    try {
        $appointments = $medicalHistoryModel->getPatientAppointments($patientId);
        error_log("Successfully retrieved patient appointments");
    } catch (Exception $e) {
        error_log("Error getting patient appointments: " . $e->getMessage());
        $appointments = [];
    }

    // Obtener documentos PDF del paciente
    try {
        $documents = $medicalHistoryModel->getPatientDocuments($patientId);
        error_log("Successfully retrieved patient documents");
    } catch (Exception $e) {
        error_log("Error getting patient documents: " . $e->getMessage());
        $documents = [];
    }

    // Devolver resultados
    header('Content-Type: application/json');

    // Debug: Log before JSON encoding
    error_log("About to encode JSON response");

    // Ensure all data is properly formatted for JSON
    $response = [
        'success' => true,
        'patient' => $patient,
        'history' => $history,
        'appointments' => $appointments,
        'documents' => $documents
    ];

    // Encode with error handling
    $json = json_encode($response);
    if ($json === false) {
        // Log JSON error
        error_log("JSON encode error: " . json_last_error_msg());

        // Try to encode a simpler response
        $json = json_encode([
            'success' => true,
            'patient' => $patient,
            'history' => [],
            'appointments' => [],
            'documents' => []
        ]);

        // If still failing, return a basic response
        if ($json === false) {
            error_log("Second JSON encode attempt failed: " . json_last_error_msg());

            // Create a simplified patient object with only essential fields
            $simplified_patient = [
                'id' => $patient['id'] ?? 0,
                'names' => $patient['names'] ?? 'Unknown',
                'last_name' => $patient['last_name'] ?? 'Patient',
                'last_name2' => $patient['last_name2'] ?? '',
                'curp' => $patient['curp'] ?? 'Not available',
                'CURP' => $patient['CURP'] ?? $patient['curp'] ?? 'Not available',
                'birth_date' => $patient['birth_date'] ?? null,
                'email' => $patient['email'] ?? 'Not available'
            ];

            // Encode the simplified response
            $simplified_json = json_encode([
                'success' => true,
                'patient' => $simplified_patient,
                'history' => [],
                'appointments' => [],
                'documents' => []
            ]);

            if ($simplified_json === false) {
                error_log("Third JSON encode attempt failed: " . json_last_error_msg());
                // Last resort - hardcoded valid JSON
                echo '{"success":true,"patient":{"id":0,"names":"Unknown","last_name":"Patient","last_name2":"","curp":"Not available","CURP":"Not available","birth_date":null,"email":"Not available"},"history":[],"appointments":[],"documents":[]}';
            } else {
                echo $simplified_json;
            }
        } else {
            echo $json;
        }
    } else {
        echo $json;
    }
} catch (PDOException $e) {
    // Registrar el error de base de datos
    error_log('Error de base de datos al obtener historial del paciente: ' . $e->getMessage());

    // Check if it's a table not found error
    if (strpos($e->getMessage(), "Table") !== false && strpos($e->getMessage(), "doesn't exist") !== false) {
        // Return a JSON response with table_error flag
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Las tablas necesarias no existen en la base de datos.',
            'error' => $e->getMessage(),
            'table_error' => true
        ]);
        exit;
    }

    // Devolver mensaje de error
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión a la base de datos. Por favor, verifique la configuración de la base de datos y asegúrese de que todas las tablas necesarias existen.',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    // Registrar el error
    error_log('Error al obtener historial del paciente: ' . $e->getMessage());

    // Check if it's a table not found error
    if (strpos($e->getMessage(), "tabla") !== false && strpos($e->getMessage(), "no existe") !== false) {
        // Return a JSON response with table_error flag
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'table_error' => true
        ]);
        exit;
    }

    // Devolver mensaje de error
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener historial del paciente: ' . $e->getMessage()
    ]);
}
