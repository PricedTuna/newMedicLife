<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/config/database.config.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/models/medications/medications.model.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/controllers/auth/role.controller.php";

/**
 * Controlador para la eliminación de medicamentos
 * Maneja la eliminación de medicamentos existentes
 */
class DeleteMedicationController {
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
     * Elimina un medicamento
     * @param int $id ID del medicamento
     * @return bool True si se eliminó correctamente, false en caso contrario
     */
    public function deleteMedication($id) {
        try {
            return $this->medicationsModel->deleteMedication($id);
        } catch (Exception $e) {
            error_log("Error al eliminar medicamento: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un tipo de medicamento
     * @param int $id ID del tipo de medicamento
     * @return bool True si se eliminó correctamente, false en caso contrario
     */
    public function deleteMedicationType($id) {
        try {
            return $this->medicationsModel->deleteMedicationType($id);
        } catch (Exception $e) {
            error_log("Error al eliminar tipo de medicamento: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Procesa la solicitud de eliminación
     * @param int $id ID del medicamento o tipo de medicamento
     * @param string $type Tipo de eliminación ('medication' o 'type')
     * @return array Resultado del procesamiento
     */
    public function processRequest($id, $type = 'medication') {
        $result = [
            'success' => false,
            'message' => '',
            'redirect' => '/views/medications/list/list-medications.view.php'
        ];

        // Verificar si el usuario tiene permisos
        if (!$this->checkAdminPermission()) {
            $result['message'] = 'No tiene permisos para realizar esta acción';
            return $result;
        }

        // Verificar que el ID sea válido
        if (empty($id) || !is_numeric($id)) {
            $result['message'] = 'ID no válido';
            return $result;
        }

        // Eliminar según el tipo
        if ($type === 'type') {
            $success = $this->deleteMedicationType($id);
            if ($success) {
                $result['success'] = true;
                $result['message'] = 'Tipo de medicamento eliminado correctamente';
            } else {
                $result['message'] = 'Error al eliminar el tipo de medicamento. Es posible que esté siendo utilizado por medicamentos.';
            }
        } else {
            $success = $this->deleteMedication($id);
            if ($success) {
                $result['success'] = true;
                $result['message'] = 'Medicamento eliminado correctamente';
            } else {
                $result['message'] = 'Error al eliminar el medicamento. Es posible que esté siendo utilizado en prescripciones.';
            }
        }

        return $result;
    }
}

// Si se accede directamente a este archivo mediante GET, procesar la solicitud
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    // Iniciar sesión para verificar permisos
    session_start();

    // Verificar que el usuario esté autenticado
    if (!isset($_SESSION['usuario'])) {
        header("Location: /views/login");
        exit();
    }

    // Obtener conexión a la base de datos
    $pdo = getConnection();
    if ($pdo === null) {
        die("Error de conexión a la base de datos");
    }

    // Crear controlador
    $controller = new DeleteMedicationController($pdo);

    // Verificar permisos
    if (!$controller->checkAdminPermission()) {
        header("Location: /");
        exit();
    }

    // Obtener parámetros
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $type = isset($_GET['type']) && $_GET['type'] === 'type' ? 'type' : 'medication';

    // Procesar solicitud
    $result = $controller->processRequest($id, $type);

    // Redirigir con mensaje
    $redirectUrl = $result['redirect'];
    if (strpos($redirectUrl, '?') !== false) {
        $redirectUrl .= '&';
    } else {
        $redirectUrl .= '?';
    }
    $redirectUrl .= 'success=' . ($result['success'] ? '1' : '0') . '&message=' . urlencode($result['message']);

    header("Location: $redirectUrl");
    exit();
}
