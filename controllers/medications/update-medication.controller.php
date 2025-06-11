<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/config/database.config.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/models/medications/medications.model.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/controllers/auth/role.controller.php";

/**
 * Controlador para la actualización de medicamentos
 * Maneja la actualización de medicamentos existentes
 */
class UpdateMedicationController {
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
     * Obtiene un medicamento por su ID
     * @param int $id ID del medicamento
     * @return array|null Datos del medicamento o null si no se encuentra
     */
    public function getMedicationById($id) {
        return $this->medicationsModel->getMedicationById($id);
    }

    /**
     * Obtiene todos los tipos de medicamentos
     * @return array Lista de tipos de medicamentos
     */
    public function getAllMedicationTypes() {
        return $this->medicationsModel->getAllMedicationTypes();
    }

    /**
     * Actualiza un medicamento existente
     * @param int $id ID del medicamento
     * @param array $data Datos a actualizar
     * @return bool True si se actualizó correctamente, false en caso contrario
     */
    public function updateMedication($id, $data) {
        try {
            return $this->medicationsModel->updateMedication($id, $data);
        } catch (Exception $e) {
            error_log("Error al actualizar medicamento: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza un tipo de medicamento existente
     * @param int $id ID del tipo de medicamento
     * @param array $data Datos a actualizar
     * @return bool True si se actualizó correctamente, false en caso contrario
     */
    public function updateMedicationType($id, $data) {
        try {
            return $this->medicationsModel->updateMedicationType($id, $data);
        } catch (Exception $e) {
            error_log("Error al actualizar tipo de medicamento: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Procesa el formulario de actualización de medicamento
     * @param int $id ID del medicamento o tipo de medicamento
     * @param string $type Tipo de actualización ('medication' o 'type')
     * @return array Resultado del procesamiento
     */
    public function processForm($id, $type = 'medication') {
        $result = [
            'success' => false,
            'message' => '',
            'errors' => []
        ];

        // Verificar si el usuario tiene permisos
        if (!$this->checkAdminPermission()) {
            $result['message'] = 'No tiene permisos para realizar esta acción';
            return $result;
        }

        if ($type === 'type') {
            // Actualizar tipo de medicamento
            if (empty($_POST['name'])) {
                $result['errors'][] = 'El nombre del tipo de medicamento es obligatorio';
                return $result;
            }

            $typeData = [
                'name' => $_POST['name'],
                'description' => $_POST['description'] ?? '',
                'status' => $_POST['status'] ?? 'A'
            ];

            $success = $this->updateMedicationType($id, $typeData);
            if ($success) {
                $result['success'] = true;
                $result['message'] = 'Tipo de medicamento actualizado correctamente';
            } else {
                $result['message'] = 'Error al actualizar el tipo de medicamento';
            }
        } else {
            // Actualizar medicamento
            if (empty($_POST['name'])) {
                $result['errors'][] = 'El nombre del medicamento es obligatorio';
            }
            if (empty($_POST['id_medicine_type'])) {
                $result['errors'][] = 'El tipo de medicamento es obligatorio';
            }

            if (!empty($result['errors'])) {
                return $result;
            }

            $medicationData = [
                'name' => trim($_POST['name']),
                'id_medicine_type' => intval($_POST['id_medicine_type']),
                'stock' => isset($_POST['stock']) ? intval($_POST['stock']) : 0,
                'price_purchase' => isset($_POST['price_purchase']) ? intval($_POST['price_purchase']) : 0,
                'price_sale' => isset($_POST['price_sale']) ? floatval($_POST['price_sale']) : 0.0,
                'status' => $_POST['status'] ?? 'A'
            ];

            $success = $this->updateMedication($id, $medicationData);
            if ($success) {
                $result['success'] = true;
                $result['message'] = 'Medicamento actualizado correctamente';
            } else {
                $result['message'] = 'Error al actualizar el medicamento';
            }
        }

        return $result;
    }

    /**
     * Prepara los datos necesarios para la vista de actualización de medicamentos
     * @param int $id ID del medicamento o tipo de medicamento
     * @param string $type Tipo de actualización ('medication' o 'type')
     * @return array Datos para la vista
     */
    public function getViewData($id, $type = 'medication') {
        $data = [];

        // Verificar si el usuario tiene permisos
        if (!$this->checkAdminPermission()) {
            $data['error'] = 'No tiene permisos para realizar esta acción';
            return $data;
        }

        // Obtener datos según el tipo
        if ($type === 'type') {
            $data['medication_type'] = $this->medicationsModel->getMedicationTypeById($id);
            if (!$data['medication_type']) {
                $data['error'] = 'El tipo de medicamento no existe';
                return $data;
            }
        } else {
            $data['medication'] = $this->getMedicationById($id);
            if (!$data['medication']) {
                $data['error'] = 'El medicamento no existe';
                return $data;
            }
            $data['medication_types'] = $this->getAllMedicationTypes();
        }

        // Procesar formulario si se ha enviado
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data['form_result'] = $this->processForm($id, $type);

            // Actualizar los datos después de procesar el formulario
            if ($data['form_result']['success']) {
                if ($type === 'type') {
                    $data['medication_type'] = $this->medicationsModel->getMedicationTypeById($id);
                } else {
                    $data['medication'] = $this->getMedicationById($id);
                }
            }
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
        $controller = new UpdateMedicationController($pdo);

        // Obtener el ID y el tipo de la solicitud
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $type = isset($_POST['type']) ? $_POST['type'] : 'medication';

        if ($id <= 0) {
            header("Location: /views/medications/list/list-medications.view.php?success=0&message=" . urlencode("ID de medicamento inválido"));
            exit();
        }

        $result = $controller->processForm($id, $type);

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
        // Si no es POST, redirigir a la página de listado
        header("Location: /views/medications/list/list-medications.view.php");
        exit();
    }
}
