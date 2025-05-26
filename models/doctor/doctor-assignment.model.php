<?php
// models/DoctorAssignmentModel.php
class DoctorAssignmentModel {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Asigna un área médica a un doctor.
     * @param int $doctorId ID del doctor.
     * @param int $medicalArea ID del área médica.
     */
    public function assignMedicalArea($doctorId, $medicalArea) {
        $stmt = $this->pdo->prepare("INSERT INTO doctor_assignments (id_medical_area, id_doctor)
            VALUES (:medical_area, :id_doctor)");
        $stmt->execute([
            ':medical_area' => $medicalArea,
            ':id_doctor'    => $doctorId,
        ]);
    }
}
