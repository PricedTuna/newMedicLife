<?php
// models/DoctorModel.php
class AppointmentModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function updateAppointment($appointmentId, $data)
    {
        // Verifica que el doctor exista
        $stmt = $this->pdo->prepare("SELECT id FROM appointments WHERE id = :appointment_id");
        $stmt->execute([':appointment_id' => $appointmentId]);
        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new Exception("La cita con ID $appointmentId no existe.");
        }

        $stmt = $this->pdo->prepare("UPDATE appointments SET id_patients = :id_patients,
        id_doctor = :id_doctor, id_receptionist = :id_receptionist, id_medical_area = :id_medical_area,
        created_at = :created_at, appointment_date = :appointment_date
            WHERE id = :appointment_id");
        $stmt->execute([
            ':id_patients'             => $data['id_patients'],
            ':id_doctor'               => $data['id_doctor'],
            ':id_receptionist'         => $data['id_receptionist'],
            ':id_medical_area'         => $data['id_medical_area'],
            ':appointment_date'        => $data['appointment_date'],
            ':appointment_id'          => $appointmentId
        ]);
    }

    /**
     * Crea un nuevo registro de doctor.
     * @param array $data Datos del doctor.
     * @param string|null $photoData Datos binarios de la foto.
     * @return int ID del nuevo doctor.
     */
    public function createAppointment($data)
    {
        $stmt = $this->pdo->prepare("INSERT INTO appointments (
        id_patient, id_doctor, id_receptionist, id_medical_area, appointment_date
    ) VALUES (
        :id_patient, :id_doctor, :id_receptionist, :id_medical_area, :appointment_date
    )");

        $stmt->execute([
            ':id_patient'       => $data['id_patient'],
            ':id_doctor'        => $data['id_doctor'],
            ':id_receptionist'  => $data['id_receptionist'],
            ':id_medical_area'  => $data['id_medical_area'],
            ':appointment_date' => $data['appointment_date']
        ]);

        return $this->pdo->lastInsertId();
    }
}
