<?php
// models/studies/patient-studies.model.php

class PatientStudiesModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Verifica si existe un registro con el mismo scheduled_at y study_type_id
     * Retorna true si existe, false si no
     */
    public function existsByScheduledAtAndStudyType($scheduledAt, $studyTypeId)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM patient_studies WHERE scheduled_at = ? AND study_type_id = ?");
        $stmt->execute([$scheduledAt, $studyTypeId]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Verifica si existe un conflicto de agendamiento para la misma fecha/hora.
     * Retorna true si existe:
     * - otro registro con el mismo study_type_id en la misma fecha/hora, o
     * - otro registro para el mismo patient_id en la misma fecha/hora.
     * Opcionalmente puede excluir un id (útil para reprogramación).
     */
    public function existsConflict($scheduledAt, $patientId, $studyTypeId, $excludeId = null)
    {
        if ($excludeId) {
            $sql = "SELECT COUNT(*) FROM patient_studies WHERE scheduled_at = ? AND (study_type_id = ? OR patient_id = ?) AND id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$scheduledAt, $studyTypeId, $patientId, $excludeId]);
        } else {
            $sql = "SELECT COUNT(*) FROM patient_studies WHERE scheduled_at = ? AND (study_type_id = ? OR patient_id = ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$scheduledAt, $studyTypeId, $patientId]);
        }
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Crea un registro en patient_studies
     * Retorna el id insertado en caso de éxito o false en caso de error
     */
    public function createStudyAppointment($patientId, $studyTypeId, $scheduledAt)
    {
        $stmt = $this->pdo->prepare("INSERT INTO patient_studies (patient_id, study_type_id, scheduled_at) VALUES (?, ?, ?)");
        if ($stmt->execute([$patientId, $studyTypeId, $scheduledAt])) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    /**
     * Obtener estudios agendados de un paciente
     * Retorna array de filas con campos id, patient_id, study_type_id, scheduled_at, status, created_at, updated_at
     */
    public function getByPatient($patientId)
    {
        $stmt = $this->pdo->prepare("SELECT ps.*, st.name AS study_name FROM patient_studies ps LEFT JOIN study_types st ON ps.study_type_id = st.id WHERE ps.patient_id = ? ORDER BY ps.scheduled_at ASC");
        $stmt->execute([$patientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Actualizar el status de un registro (ej. 'Cancelado')
     */
    public function updateStatus($id, $status)
    {
        $allowed = ['Pendiente','Realizado','Cancelado','Reprogramado'];
        if (!in_array($status, $allowed)) return false;
        $stmt = $this->pdo->prepare("UPDATE patient_studies SET status = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    /**
     * Reprogramar (actualizar scheduled_at) y opcionalmente setear status
     */
    public function updateScheduledAt($id, $scheduledAt, $status = null)
    {
        if ($status) {
            $stmt = $this->pdo->prepare("UPDATE patient_studies SET scheduled_at = ?, status = ?, updated_at = NOW() WHERE id = ?");
            return $stmt->execute([$scheduledAt, $status, $id]);
        } else {
            $stmt = $this->pdo->prepare("UPDATE patient_studies SET scheduled_at = ?, updated_at = NOW() WHERE id = ?");
            return $stmt->execute([$scheduledAt, $id]);
        }
    }
}
