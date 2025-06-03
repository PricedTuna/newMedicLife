<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/config/database.config.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/models/patient/patient.model.php";

/**
 * Controlador para el listado de pacientes
 * Maneja la obtención de datos de pacientes para la vista
 */
class PatientListController {
    private $patientModel;

    public function __construct($pdo) {
        $this->patientModel = new PatientModel($pdo);
    }

    /**
     * Obtiene todos los pacientes activos
     * @return array Lista de pacientes activos
     */
    public function getAllPatients() {
        return $this->patientModel->getAllActivePatients();
    }

    /**
     * Prepara los datos necesarios para la vista de listado de pacientes
     * @return array Datos para la vista
     */
    public function getViewData() {
        $data = [];

        // Obtener pacientes
        $data['patients'] = $this->getAllPatients();

        return $data;
    }
}
