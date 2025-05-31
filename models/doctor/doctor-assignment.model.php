<?php
class DoctorAssignmentModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Asigna un área médica a un doctor.
     * @param int $doctorId ID del doctor.
     * @param int $medicalArea ID del área médica.
     * @param string $action 'create' o 'update'
     */
    public function assignMedicalArea($doctorId, $medicalArea, $action)
    {
        if (trim($action) === 'create') {
            $stmt = $this->pdo->prepare("
                INSERT INTO doctor_assignments (id_medical_area, id_doctor)
                VALUES (:medical_area, :id_doctor)
            ");
            $stmt->execute([
                ':medical_area' => $medicalArea,
                ':id_doctor'    => $doctorId,
            ]);
        } elseif (trim($action) === 'update') {
            $stmt = $this->pdo->prepare("
                UPDATE doctor_assignments 
                SET id_medical_area = :medical_area 
                WHERE id_doctor = :id_doctor
            ");
            $stmt->execute([
                ':medical_area' => $medicalArea,
                ':id_doctor'    => $doctorId,
            ]);
        }
    }
}
