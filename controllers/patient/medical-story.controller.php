  <?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/config/database.config.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/models/patient/patient.model.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/models/email/email.model.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/utils/utils.php";

/**
 * Controlador para la historia médica de pacientes
 * Maneja la búsqueda de pacientes por CURP y la confirmación de identidad
 */
class MedicalStoryController {
    private $patientModel;
    private $pdo;
    private $emailModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->patientModel = new PatientModel($pdo);
        $this->emailModel = new EmailModel();
    }

    /**
     * Busca un paciente por su CURP
     * @param string $curp CURP del paciente a buscar
     * @return array Resultado de la búsqueda
     */
    public function searchPatientByCURP($curp) {
        $result = [
            'success' => false,
            'patient' => null,
            'message' => ''
        ];

        if (empty($curp)) {
            $result['message'] = 'Por favor, ingrese un CURP para buscar';
            return $result;
        }

        // Log the search attempt
        error_log("Buscando paciente con CURP: $curp");

        $patient = $this->patientModel->getPatientByCURP($curp);

        if ($patient) {
            error_log("Paciente encontrado: ID " . $patient['id']);
            $result['success'] = true;
            $result['patient'] = $patient;
        } else {
            error_log("No se encontró ningún paciente con el CURP: $curp");
            $result['message'] = 'No se encontró ningún paciente con el CURP proporcionado';
        }

        return $result;
    }

    /**
     * Confirma la identidad del paciente y registra la aceptación del historial médico
     * @param int $patientId ID del paciente
     * @return array Resultado de la confirmación
     */
    public function confirmIdentity($patientId) {
        $result = [
            'success' => false,
            'message' => '',
            'emailSent' => false
        ];

        try {
            // Obtener información del paciente
            $stmt = $this->pdo->prepare("SELECT * FROM patients WHERE id = :id");
            $stmt->execute([':id' => $patientId]);
            $patient = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$patient) {
                $result['message'] = 'No se encontró al paciente';
                return $result;
            }

            // Enviar correo electrónico
            $to = $patient['email'];
            $subject = "Historial médico";
            $text = "Esto es un historial médico!!";

            // Log the email parameters for debugging
            error_log("Sending email - To: $to, Subject: $subject, Text: $text");

            // Validate email parameters before sending
            if (empty($to)) {
                $result['message'] = 'El paciente no tiene un correo electrónico registrado';
                $result['success'] = true; // Still consider it a success for the medical story
                return $result;
            }

            $this->emailModel->sendEmail($to, $subject, $text);

            if (strpos($emailResult, "Correo enviado correctamente") === 0) {
                $result['emailSent'] = true;
                $result['success'] = true;
                $result['message'] = 'Se aceptó tu historial médico y se envió un correo a ' . $to;
            } else {
                // El correo no se envió, pero aún así consideramos exitosa la confirmación
                $result['success'] = true;
                $result['message'] = 'Se aceptó tu historial médico, pero hubo un problema al enviar el correo: ' . $emailResult;
            }
        } catch (Exception $e) {
            $result['message'] = 'Error al procesar la solicitud: ' . $e->getMessage();
        }

        return $result;
    }
}


// Procesar solicitudes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    error_log("Procesando solicitud AJAX: " . $_POST['action']);

    $controller = new MedicalStoryController($pdo);
    $response = ['success' => false, 'message' => ''];

    switch ($_POST['action']) {
        case 'search':
            if (isset($_POST['curp'])) {
                error_log("Buscando paciente con CURP desde AJAX: " . $_POST['curp']);
                $response = $controller->searchPatientByCURP($_POST['curp']);
                error_log("Respuesta de búsqueda: " . ($response['success'] ? 'Éxito' : 'Fallo') . " - " . $response['message']);
            } else {
                error_log("Error: Parámetros incompletos para búsqueda");
                $response['message'] = 'Parámetros incompletos';
            }
            break;

        case 'confirm':
            if (isset($_POST['patientId'])) {
                error_log("Confirmando identidad del paciente: " . $_POST['patientId']);
                $response = $controller->confirmIdentity($_POST['patientId']);
                error_log("Respuesta de confirmación: " . ($response['success'] ? 'Éxito' : 'Fallo') . " - " . $response['message']);
            } else {
                error_log("Error: Parámetros incompletos para confirmación");
                $response['message'] = 'Parámetros incompletos';
            }
            break;

        default:
            error_log("Error: Acción no válida - " . $_POST['action']);
            $response['message'] = 'Acción no válida';
            break;
    }

    header('Content-Type: application/json');
    echo json_encode(utf8ize($response));
    exit;
}
