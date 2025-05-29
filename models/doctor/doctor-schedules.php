<?php
// models/schedule/MedicalScheduleModel.php

class MedicalScheduleModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Actualiza un horario existente.
     * @param int $doctorId
     * @param string $day Día (ej. 'Monday')
     * @param string $startTime Hora inicio (HH:MM)
     * @param string $endTime Hora fin (HH:MM)
     */
    public function updateSchedule($doctorId, $day, $startTime, $endTime)
    {
        // Verifica que el horario exista
        $stmt = $this->pdo->prepare("SELECT id FROM medical_schedules WHERE id_doctor = :id_doctor AND day = :day");
        $stmt->execute([':id_doctor' => $doctorId, ':day' => $day]);
        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new Exception("El horario para $day no existe para el doctor con ID $doctorId.");
        }

        $stmt = $this->pdo->prepare("UPDATE medical_schedules SET start_time = :start_time, end_time = :end_time WHERE id_doctor = :id_doctor AND day = :day");
        $stmt->execute([
            ':start_time' => $startTime,
            ':end_time'   => $endTime,
            ':id_doctor'  => $doctorId,
            ':day'        => $day
        ]);
    }

    /**
     * Crea un nuevo horario.
     * @param int $doctorId
     * @param string $day Día (ej. 'Monday')
     * @param string $startTime Hora inicio (HH:MM)
     * @param string $endTime Hora fin (HH:MM)
     * @return int ID del nuevo registro horario.
     */
    public function createSchedule($doctorId, $day, $startTime, $endTime)
    {
        $stmt = $this->pdo->prepare("INSERT INTO medical_schedules (id_doctor, day, start_time, end_time) VALUES (:id_doctor, :day, :start_time, :end_time)");
        $stmt->execute([
            ':id_doctor' => $doctorId,
            ':day'       => $day,
            ':start_time'=> $startTime,
            ':end_time'  => $endTime
        ]);
        return $this->pdo->lastInsertId();
    }

    /**
     * Guarda un horario: crea si no existe, actualiza si ya existe.
     */
    public function saveOrUpdateSchedule($doctorId, $day, $startTime, $endTime)
    {
        $stmt = $this->pdo->prepare("SELECT id FROM medical_schedules WHERE id_doctor = :id_doctor AND day = :day");
        $stmt->execute([':id_doctor' => $doctorId, ':day' => $day]);

        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->updateSchedule($doctorId, $day, $startTime, $endTime);
        } else {
            $this->createSchedule($doctorId, $day, $startTime, $endTime);
        }
    }

    /**
     * Borra un horario dado doctor y día.
     */
    public function deleteSchedule($doctorId, $day)
    {
        $stmt = $this->pdo->prepare("DELETE FROM medical_schedules WHERE id_doctor = :id_doctor AND day = :day");
        $stmt->execute([
            ':id_doctor' => $doctorId,
            ':day' => $day,
        ]);
    }
}
