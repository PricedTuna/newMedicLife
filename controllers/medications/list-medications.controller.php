<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/config/database.config.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/models/medications/medications.model.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/controllers/auth/role.controller.php";

/**
 * Controlador para el listado de medicamentos
 * Maneja la obtención de datos de medicamentos para la vista
 */
class MedicationsListController {
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
     * Obtiene todos los medicamentos activos
     * @return array Lista de medicamentos activos
     */
    public function getAllMedications() {
        return $this->medicationsModel->getAllMedications();
    }

    /**
     * Obtiene todos los tipos de medicamentos
     * @return array Lista de tipos de medicamentos
     */
    public function getAllMedicationTypes() {
        return $this->medicationsModel->getAllMedicationTypes();
    }

    /**
     * Busca medicamentos por nombre
     * @param string $searchTerm Término de búsqueda
     * @return array Lista de medicamentos que coinciden con la búsqueda
     */
    public function searchMedications($searchTerm) {
        return $this->medicationsModel->searchMedications($searchTerm);
    }

    /**
     * Prepara los datos necesarios para la vista de listado de medicamentos
     * @return array Datos para la vista
     */
    public function getViewData() {
        $data = [];

        // Verificar si hay un término de búsqueda
        $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

        // Obtener medicamentos (filtrados si hay búsqueda)
        if (!empty($searchTerm)) {
            $data['medications'] = $this->searchMedications($searchTerm);
            $data['search_term'] = $searchTerm;
        } else {
            $data['medications'] = $this->getAllMedications();
        }

        // Obtener tipos de medicamentos
        $data['medication_types'] = $this->getAllMedicationTypes();

        return $data;
    }
}

// Si se accede directamente a este archivo, redirigir a la página de listado
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header("Location: /views/medications/list");
    exit();
}