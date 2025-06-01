  <?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/config/database.config.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/models/patient/patient.model.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/controllers/email/email.controller.php";

/**
 * Controlador para la historia médica de pacientes
 * Maneja la búsqueda de pacientes por CURP y la confirmación de identidad
 */
class MedicalStoryController {
    private $patientModel;
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->patientModel = new PatientModel($pdo);
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

        $patient = $this->patientModel->getPatientByCURP($curp);

        if ($patient) {
            $result['success'] = true;
            $result['patient'] = $patient;
        } else {
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
            $emailController = new EmailController();
            $to = $patient['email'];
            $subject = "Historial médico";
            $text = "Esto es un historial médico!!";

            $emailResult = $emailController->sendEmail($to, $subject, $text);

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
    $controller = new MedicalStoryController($pdo);
    $response = ['success' => false, 'message' => ''];

    switch ($_POST['action']) {
        case 'search':
            if (isset($_POST['curp'])) {
                $response = $controller->searchPatientByCURP($_POST['curp']);
            } else {
                $response['message'] = 'Parámetros incompletos';
            }
            break;

        case 'confirm':
            if (isset($_POST['patientId'])) {
                $response = $controller->confirmIdentity($_POST['patientId']);
            } else {
                $response['message'] = 'Parámetros incompletos';
            }
            break;

        default:
            $response['message'] = 'Acción no válida';
            break;
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
