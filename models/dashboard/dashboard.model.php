<?php

class Dashboard
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function updateAppointment($id_cita)
    {
        $stmt = $this->pdo->prepare("UPDATE appointments SET status = :status WHERE id = :id_cita");
        return $stmt->execute([
            'status'     => 'T',
            'id_cita' => $id_cita
        ]);
    }

    public function cancelAppointment($id_cita)
    {
        $stmt = $this->pdo->prepare("UPDATE appointments SET status = :status WHERE id = :id_cita");
        return $stmt->execute([
            'status'     => 'C',
            'id_cita' => $id_cita
        ]);
    }

    /**
     * Obtiene todos los doctores con sus áreas médicas asignadas
     * @return array Lista de doctores con sus áreas médicas
     */
    public function getDoctorsWithMedicalAreas()
    {
        $stmt = $this->pdo->prepare("SELECT
            d.id AS id,
            d.names AS names,
            d.last_name AS last_name,
            d.last_name2 AS last_name2,
            ma.id AS medical_area_id,
            ma.name AS medical_area_name
        FROM doctors d
        INNER JOIN doctor_assignments da ON d.id = da.id_doctor
        INNER JOIN medical_areas ma ON ma.id = da.id_medical_area");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todas las citas con información de pacientes, doctores y áreas médicas
     * @return array Lista de citas con información detallada
     */
    public function getAllAppointments()
    {
        $stmt = $this->pdo->prepare("SELECT
            ap.id AS cita,
            ap.id_patient AS id_patient,
            ap.id_doctor AS id_doctor,
            ap.id_medical_area AS id_medical_area,
            ap.appointment_date AS appointment_date,
            ap.status AS status,
            ma.name AS medical_area,
            p.names AS patient_names,
            p.last_name AS patient_last_name,
            p.last_name2 AS patient_last_name2,
            d.names AS doctor_names,
            d.last_name AS doctor_last_name,
            d.last_name2 AS doctor_last_name2,
            d.email AS doctor_email
        FROM appointments ap
        INNER JOIN doctors d ON d.id = ap.id_doctor
        INNER JOIN patients p ON p.id = ap.id_patient
        INNER JOIN medical_areas ma ON ma.id = ap.id_medical_area
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
