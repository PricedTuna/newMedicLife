<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/config/database.config.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/models/medications/medications.model.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/controllers/auth/role.controller.php";

/**
 * Controlador para la creación de medicamentos
 * Maneja la creación de nuevos medicamentos
 */
class CreateMedicationController {
    private $medicationsModel;
    private $roleController;

    public function __construct($pdo) {
        $this->medicationsModel = new MedicationsModel($pdo);
        $this->roleController = new RoleController();
    }

    /**
     * Verifica que el usuario tenga permisos de administrador
     * @return bool True si el usuario tiene permisos, false en caso contrario
     */
    public function checkAdminPermission() {
        return $this->roleController->isUserAdmin();
    }

    /**
     * Obtiene todos los tipos de medicamentos
     * @return array Lista de tipos de medicamentos
     */
    public function getAllMedicationTypes() {
        return $this->medicationsModel->getAllMedicationTypes();
    }

    /**
     * Crea un nuevo medicamento
     * @param array $data Datos del medicamento
     * @return int|false ID del nuevo medicamento o false en caso de error
     */
    public function createMedication($data) {
        try {
            // Log the data being received by the controller
            error_log("Controller recibiendo datos para crear medicamento: " . json_encode($data));

            // Validate required fields
            if (empty($data['name'])) {
                throw new Exception("El nombre del medicamento es obligatorio");
            }
            if (empty($data['id_medicine_type'])) {
                throw new Exception("El tipo de medicamento es obligatorio");
            }

            // Ensure numeric fields are properly formatted
            if (isset($data['stock'])) {
                $data['stock'] = filter_var($data['stock'], FILTER_VALIDATE_INT);
                if ($data['stock'] === false || $data['stock'] < 0) {
                    throw new Exception("El stock debe ser un número entero positivo");
                }
            }

            if (isset($data['price_purchase'])) {
                $data['price_purchase'] = filter_var($data['price_purchase'], FILTER_VALIDATE_INT);
                if ($data['price_purchase'] === false || $data['price_purchase'] < 0) {
                    throw new Exception("El precio de compra debe ser un número entero positivo");
                }
            }

            if (isset($data['price_sale'])) {
                $data['price_sale'] = filter_var($data['price_sale'], FILTER_VALIDATE_FLOAT);
                if ($data['price_sale'] === false || $data['price_sale'] < 0) {
                    throw new Exception("El precio de venta debe ser un número positivo");
                }
            }

            // Ensure status is valid
            if (isset($data['status']) && !in_array($data['status'], ['A', 'I'])) {
                $data['status'] = 'A'; // Default to active if invalid
            }

            // Call the model to create the medication
            return $this->medicationsModel->createMedication($data);
        } catch (Exception $e) {
            error_log("Error al crear medicamento en el controlador: " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Crea un nuevo tipo de medicamento
     * @param array $data Datos del tipo de medicamento
     * @return int|false ID del nuevo tipo de medicamento o false en caso de error
     */
    public function createMedicationType($data) {
        try {
            return $this->medicationsModel->createMedicationType($data);
        } catch (Exception $e) {
            error_log("Error al crear tipo de medicamento: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Procesa el formulario de creación de medicamento
     * @return array Resultado del procesamiento
     */
    public function processForm() {
        $result = [
            'success' => false,
            'message' => '',
            'errors' => []
        ];

        try {
            // Log the form submission
            error_log("Procesando formulario: " . json_encode($_POST));

            // Verificar si el usuario tiene permisos
            if (!$this->checkAdminPermission()) {
                $result['message'] = 'No tiene permisos para realizar esta acción';
                return $result;
            }

            // Verificar si se está creando un medicamento o un tipo de medicamento
            if (isset($_POST['action']) && $_POST['action'] === 'create_type') {
                // Crear tipo de medicamento
                if (empty($_POST['name'])) {
                    $result['errors'][] = 'El nombre del tipo de medicamento es obligatorio';
                    return $result;
                }

                $typeData = [
                    'name' => trim($_POST['name']),
                    'description' => isset($_POST['description']) ? trim($_POST['description']) : ''
                ];

                error_log("Intentando crear tipo de medicamento: " . json_encode($typeData));

                $typeId = $this->createMedicationType($typeData);
                if ($typeId) {
                    $result['success'] = true;
                    $result['message'] = 'Tipo de medicamento creado correctamente';
                    $result['type_id'] = $typeId;
                    error_log("Tipo de medicamento creado con ID: " . $typeId);
                } else {
                    $result['message'] = 'Error al crear el tipo de medicamento';
                    error_log("Error al crear tipo de medicamento");
                }
            } else {
                // Crear medicamento - Validar campos obligatorios
                $requiredFields = [
                    'name' => 'El nombre del medicamento es obligatorio',
                    'id_medicine_type' => 'El tipo de medicamento es obligatorio',
                    'stock' => 'El stock del medicamento es obligatorio',
                    'price_purchase' => 'El precio de compra del medicamento es obligatorio',
                    'price_sale' => 'El precio de venta del medicamento es obligatorio',
                    'status' => 'El estado del medicamento es obligatorio'
                ];

                foreach ($requiredFields as $field => $errorMessage) {
                    if (!isset($_POST[$field]) || $_POST[$field] === '') {
                        $result['errors'][] = $errorMessage;
                    }
                }

                if (!empty($result['errors'])) {
                    error_log("Errores de validación en el formulario: " . json_encode($result['errors']));
                    return $result;
                }

                // Validar campos numéricos
                if (isset($_POST['stock']) && (!is_numeric($_POST['stock']) || intval($_POST['stock']) < 0)) {
                    $result['errors'][] = 'El stock debe ser un número entero positivo';
                }

                if (isset($_POST['price_purchase']) && (!is_numeric($_POST['price_purchase']) || intval($_POST['price_purchase']) < 0)) {
                    $result['errors'][] = 'El precio de compra debe ser un número entero positivo';
                }

                if (isset($_POST['price_sale']) && (!is_numeric($_POST['price_sale']) || floatval($_POST['price_sale']) < 0)) {
                    $result['errors'][] = 'El precio de venta debe ser un número positivo';
                }

                if (!empty($result['errors'])) {
                    error_log("Errores de validación en campos numéricos: " . json_encode($result['errors']));
                    return $result;
                }

                // Preparar datos del medicamento
                $medicationData = [
                    'name' => trim($_POST['name']),
                    'id_medicine_type' => intval($_POST['id_medicine_type']),
                    'stock' => intval($_POST['stock']),
                    'price_purchase' => intval($_POST['price_purchase']),
                    'price_sale' => floatval($_POST['price_sale']),
                    'status' => $_POST['status']
                ];

                error_log("Intentando crear medicamento: " . json_encode($medicationData));

                $medicationId = $this->createMedication($medicationData);
                if ($medicationId) {
                    $result['success'] = true;
                    $result['message'] = 'Medicamento creado correctamente';
                    $result['medication_id'] = $medicationId;
                    error_log("Medicamento creado con ID: " . $medicationId);
                } else {
                    $result['message'] = 'Error al crear el medicamento';
                    error_log("Error al crear medicamento");
                }
            }
        } catch (Exception $e) {
            $result['success'] = false;
            $result['message'] = 'Error al procesar el formulario: ' . $e->getMessage();
            $result['errors'][] = $e->getMessage();
            error_log("Exception en processForm: " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
        }

        return $result;
    }

    /**
     * Prepara los datos necesarios para la vista de creación de medicamentos
     * @return array Datos para la vista
     */
    public function getViewData() {
        $data = [];

        // Obtener tipos de medicamentos
        $data['medication_types'] = $this->getAllMedicationTypes();

        // Procesar formulario si se ha enviado
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data['form_result'] = $this->processForm();
        }

        return $data;
    }
}

// Si se accede directamente a este archivo, procesar el formulario y redirigir
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    // Si es una solicitud POST, procesar el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once $_SERVER["DOCUMENT_ROOT"] . "/config/database.config.php";
        $pdo = getConnection();
        $controller = new CreateMedicationController($pdo);
        $result = $controller->processForm();

        // Redirigir a la lista de medicamentos con un mensaje
        $redirectUrl = '/views/medications/list/list-medications.view.php';
        if ($result['success']) {
            $redirectUrl .= '?success=1&message=' . urlencode($result['message']);
        } else {
            $redirectUrl .= '?success=0&message=' . urlencode($result['message']);
        }
        header("Location: " . $redirectUrl);
        exit();
    } else {
        // Si no es POST, redirigir a la página de creación
        header("Location: /views/medications/create/create-medication.view.php");
        exit();
    }
}
