<?php

// models/PatientMOdel.php
class EmergencyContactsModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    private function validateField($value, $maxLength, $fieldName)
    {
        if (empty($value)) {
            throw new Exception("El campo $fieldName es obligatorio.");
        }
        if (strlen($value) > $maxLength) {
            throw new Exception("El campo $fieldName supera la longitud máxima de $maxLength caracteres.");
        }
    }

    private function validateRegex($value, $pattern, $fieldName)
    {
        if (!preg_match($pattern, $value)) {
            throw new Exception("El campo $fieldName no tiene el formato correcto.");
        }
    }

    public function validateData($data = null)
    {
        $this->validateField($data['names'], 40, 'nombre');
        $this->validateField($data['last_name'], 40, 'apellido paterno');
        $this->validateField($data['last_name2'], 40, 'apellido materno');
        $this->validateRegex($data['phone'], '/^\d{10}$/', 'teléfono');
        $this->validateField($data['relationship'], 20, 'relación');
    }

    public function updateEmergencyContact($emergencyContactId, $data)
    {
        // Verifica que el contacto exista
        $stmt = $this->pdo->prepare("SELECT id FROM emergency_contacts WHERE id = :emergency_contacts_id");
        $stmt->execute([':emergency_contacts_id' => $emergencyContactId]);
        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new Exception("El contacto de emergencia con ID $emergencyContactId no existe.");
        }

        $stmt = $this->pdo->prepare("UPDATE emergency_contacts SET
            names = :names, last_name = :last_name, last_name2 = :last_name2, phone = :phone,
            relationship = :relationship
            WHERE id = :emergency_contacts_id");

        $stmt->execute([
            ':names'               => $data['names'],
            ':last_name'           => $data['last_name'],
            ':last_name2'          => $data['last_name2'],
            ':phone'               => $data['phone'],
            ':relationship'        => $data['relationship'],
            ':emergency_contacts_id' => $emergencyContactId
        ]);
        return $emergencyContactId;
    }

    public function createEmergencyContact($data)
    {
        $stmt = $this->pdo->prepare("INSERT INTO emergency_contacts (
            names, last_name, last_name2, phone, relationship
        ) VALUES (
            :names, :last_name, :last_name2, :phone, :relationship
        )");

        $stmt->execute([
            ':names'        => $data['names'],
            ':last_name'    => $data['last_name'],
            ':last_name2'   => $data['last_name2'],
            ':phone'        => $data['phone'],
            ':relationship' => $data['relationship']
        ]);

        return $this->pdo->lastInsertId();
    }
}
