<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/machines/machines.model.php';

class MachinesListController {
    private $model;

    public function __construct($pdo) {
        $this->model = new MachineModel($pdo);
    }

    public function getViewData() {
        // Obtener máquinas
        $machines = method_exists($this->model, 'getAllMachines')
            ? $this->model->getAllMachines()
            : $this->model->getActiveMachines();

        // Obtener tipos de máquina y áreas médicas
        $machine_types = method_exists($this->model, 'getMachineTypes') 
            ? $this->model->getMachineTypes() 
            : [];

        $medical_areas = method_exists($this->model, 'getMedicalAreas') 
            ? $this->model->getMedicalAreas() 
            : [];

        return [
            'machines' => $machines,
            'machine_types' => $machine_types,
            'medical_areas' => $medical_areas
        ];
    }
}
