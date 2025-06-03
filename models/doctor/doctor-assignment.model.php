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

    /**
     * Actualiza el área médica de un doctor.
     * @param int $doctorId ID del doctor.
     * @param int $medicalArea ID del área médica.
     */
    public function updateMedicalArea($doctorId, $medicalArea) {
        // Verificar si ya existe una asignación para este doctor
        $stmt = $this->pdo->prepare("SELECT id_doctor FROM doctor_assignments WHERE id_doctor = :id_doctor");
        $stmt->execute([':id_doctor' => $doctorId]);
        $assignment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($assignment) {
            // Actualizar la asignación existente
            $stmt = $this->pdo->prepare("UPDATE doctor_assignments SET id_medical_area = :medical_area
                WHERE id_doctor = :id_doctor");
            $stmt->execute([
                ':medical_area' => $medicalArea,
                ':id_doctor'    => $doctorId,
            ]);
        } else {
            // Crear una nueva asignación
            $this->assignMedicalArea($doctorId, $medicalArea);
        }
    }
}
