<?php
// models/schedule/MedicalScheduleModel.php

class MedicalScheduleModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function updateSchedule($doctorId, $day, $startTime, $endTime)
    {
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

    public function deleteSchedule($doctorId, $day)
    {
        $stmt = $this->pdo->prepare("DELETE FROM medical_schedules WHERE id_doctor = :id_doctor AND day = :day");
        $stmt->execute([
            ':id_doctor' => $doctorId,
            ':day' => $day,
        ]);
    }

    /**
     * Elimina todos los horarios que NO estén en el arreglo $scheduleDays.
     * @param int $doctorId
     * @param array $scheduleDays Días válidos como ["Monday", "Wednesday"]
     */
    public function deleteMissingSchedules($doctorId, array $scheduleDays)
    {
        // Si no se proporcionaron días válidos, elimina todos los horarios del doctor
        if (empty($scheduleDays)) {
            $stmt = $this->pdo->prepare("DELETE FROM medical_schedules WHERE id_doctor = :id_doctor");
            $stmt->execute([':id_doctor' => $doctorId]);
            return;
        }

        // Genera placeholders para la cláusula NOT IN
        $placeholders = implode(',', array_fill(0, count($scheduleDays), '?'));

        $sql = "DELETE FROM medical_schedules WHERE id_doctor = ? AND day NOT IN ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $params = array_merge([$doctorId], $scheduleDays);
        $stmt->execute($params);
    }
}