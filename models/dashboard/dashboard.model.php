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
}
