<?php

// models/PatientMOdel.php
class PatientModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Valida que un campo no este vacio y no exceda la longitud maxima
    private function validateField($value, $maxLength, $fieldName)
    {
        if (empty($value)) {
            throw new Exception("EL campo $fieldName es obligatorio.");
        }
        if (strlen($value) > $maxLength) {
            throw new Exception("EL campo $fieldName supera la longitud maxima de $maxLength caracteres.");
        }
    }

    // Valida que un campo cumpla con el patrón especificado
    private function validateRegex($value, $pattern, $fieldName)
    {
        if (!preg_match($pattern, $value)) {
            throw new Exception("El campo $fieldName no tiene el formato correcto.");
        }
    }

    // Verifica la unicidad de un campo, excluyendo opcionalmente un ID
    private function validateUnique($table, $field, $value, $patientId = null, $customMessage = null)
    {
        $query = "SELECT id FROM $table WHERE $field = :value";
        $params = [':value' => $value];
        if ($patientId !== null) {
            $query .= " AND id != :patientId";
            $params[':patientId'] = $patientId;
        }
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new Exception($customMessage ?? "El campo $field ya está en uso.");
        }
    }

    private function validateDecimal($value, $maxLengthI, $maxLengthD, $fieldName)
    {
        $number = (string)$value;

        if (strpos($number, '.') !== false) {
            list($integerPart, $decimalPart) = explode('.', $number);
        } else {
            $integerPart = $number;
            $decimalPart = '';
        }

        $integerDigits = strlen(ltrim($integerPart, '0')) ?: 1;
        $decimalDigits = strlen(rtrim($decimalPart, '0'));

        if ($integerDigits > $maxLengthI) {
            throw new Exception("El campo $fieldName supera la longitud máxima de $maxLengthI caracteres enteros.");
        }
        if ($decimalDigits > $maxLengthD) {
            throw new Exception("El campo $fieldName supera la longitud máxima de $maxLengthD caracteres decimales.");
        }
    }


    /**
     * Valida todos los datos del formulario.
     * @param array $data Datos a validar.
     * @param int|null $doctorId ID del doctor (en actualización).
     */
    public function validateData($data, $patientId = null)
    {
        $this->validateField($data['names'], 40, 'nombre');
        $this->validateField($data['last_name'], 40, 'apellido paterno');
        $this->validateField($data['last_name2'], 40, 'apellido materno');
        $this->validateRegex($data['CP'], '/^\d{5}$/', 'código postal');
        $this->validateField($data['street'], 50, 'calle');
        $this->validateField($data['external_number'], 8, 'número exterior');
        $this->validateField($data['neighborhood'], 50, 'colonia');
        $this->validateField($data['insurance_number'], 20, 'número de afiliación');
        $this->validateRegex($data['birth_date'], '/^\d{4}-\d{2}-\d{2}$/', 'fecha de nacimiento (YYYY-MM-DD)');
        $this->validateField($data['CURP'], 18, 'CURP');
        $this->validateField($data['RFC'], 13, 'RFC');
        $this->validateRegex($data['phone'], '/^\d{10}$/', 'teléfono');
        $this->validateRegex($data['email'], '/^[\w\.\-]+@[\w\.\-]+\.\w{2,4}$/', 'correo electrónico');
        $this->validateField($data['gender'], 2, 'género');
        $this->validateDecimal($data['weight'], 6, 2, 'peso');
        $this->validateDecimal($data['height'], 6, 2, 'altura');

        if (!empty($data['internal_number'])) {
            $this->validateField($data['internal_number'], 8, 'número interior');
        }

        // Validación de unicidad
        $this->validateUnique('patients', 'CURP', $data['CURP'], $patientId, "La CURP que intentas registrar ya existe.");
        // Removed phone uniqueness validation as per requirements
        $this->validateUnique('patients', 'insurance_number', $data['insurance_number'], $patientId, "El número de afiliación que intentas registrar ya existe.");
    }

    /**
     * Actualiza un paciente existente.
     * @param int $doctorId ID del paciente.
     * @param array $data Datos a actualizar.
     * @param string|null $photoData Datos binarios de la foto.
     * @throws Exception Si los datos no son válidos o el paciente no existe
     */
    public function updatePatient($patientId, $data, $photoData)
    {
        // Validar los datos antes de actualizarlos
        $this->validatePatientData($data);

        // Verifica que el paciente exista
        $stmt = $this->pdo->prepare("SELECT id FROM patients WHERE id = :patient_id");
        $stmt->execute([':patient_id' => $patientId]);
        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new Exception("El paciente con ID $patientId no existe.");
        }

        // Si no se proporciona una nueva foto, mantener la existente
        if ($photoData === null) {
            $sql = "UPDATE patients SET
                names = :names, last_name = :last_name, last_name2 = :last_name2,
                id_state = :id_state, id_municipality = :id_municipality, id_locality = :id_locality,
                CP = :CP, street = :street, external_number = :external_number, internal_number = :internal_number,
                neighborhood = :neighborhood, insurance_number = :insurance_number,
                birth_date = :birth_date, CURP = :CURP, RFC = :RFC, phone = :phone,
                email = :email, gender = :gender, weight = :weight, height = :height, blood_type = :blood_type,
                id_emergency_contact = :id_emergency_contact, marital_status = :marital_status, ethnic_group = :ethnic_group, religion = :religion,
                status = :status
                WHERE id = :patient_id";

            $params = [
                ':names'             => $data['names'],
                ':last_name'         => $data['last_name'],
                ':last_name2'        => $data['last_name2'],
                ':id_state'          => $data['id_state'],
                ':id_municipality'   => $data['id_municipality'],
                ':id_locality'       => $data['id_locality'],
                ':CP'                => $data['CP'],
                ':street'            => $data['street'],
                ':external_number'   => $data['external_number'],
                ':internal_number'   => $data['internal_number'],
                ':neighborhood'      => $data['neighborhood'],
                ':insurance_number'  => $data['insurance_number'],
                ':birth_date'        => $data['birth_date'],
                ':CURP'              => $data['CURP'],
                ':RFC'               => $data['RFC'],
                ':phone'             => $data['phone'],
                ':email'             => $data['email'],
                ':gender'            => $data['gender'],
                ':weight'            => $data['weight'],
                ':height'            => $data['height'],
                ':blood_type'        => $data['blood_type'],
                ':id_emergency_contact'  => $data['id_emergency_contact'],
                ':marital_status'    => $data['marital_status'],
                ':ethnic_group'      => $data['ethnic_group'],
                ':religion'          => $data['religion'],
                ':status'            => 'A',
                ':patient_id'        => $patientId
            ];
        } else {
            // Si se proporciona una nueva foto, actualizarla
            $sql = "UPDATE patients SET
                names = :names, last_name = :last_name, last_name2 = :last_name2,
                id_state = :id_state, id_municipality = :id_municipality, id_locality = :id_locality,
                CP = :CP, street = :street, external_number = :external_number, internal_number = :internal_number,
                neighborhood = :neighborhood, insurance_number = :insurance_number,
                birth_date = :birth_date, CURP = :CURP, RFC = :RFC, phone = :phone, photo = :photo,
                email = :email, gender = :gender, weight = :weight, height = :height, blood_type = :blood_type,
                id_emergency_contact = :id_emergency_contact, marital_status = :marital_status, ethnic_group = :ethnic_group, religion = :religion,
                status = :status
                WHERE id = :patient_id";

            $params = [
                ':names'             => $data['names'],
                ':last_name'         => $data['last_name'],
                ':last_name2'        => $data['last_name2'],
                ':id_state'          => $data['id_state'],
                ':id_municipality'   => $data['id_municipality'],
                ':id_locality'       => $data['id_locality'],
                ':CP'                => $data['CP'],
                ':street'            => $data['street'],
                ':external_number'   => $data['external_number'],
                ':internal_number'   => $data['internal_number'],
                ':neighborhood'      => $data['neighborhood'],
                ':insurance_number'  => $data['insurance_number'],
                ':birth_date'        => $data['birth_date'],
                ':CURP'              => $data['CURP'],
                ':RFC'               => $data['RFC'],
                ':phone'             => $data['phone'],
                ':photo'             => $photoData,
                ':email'             => $data['email'],
                ':gender'            => $data['gender'],
                ':weight'            => $data['weight'],
                ':height'            => $data['height'],
                ':blood_type'        => $data['blood_type'],
                ':id_emergency_contact'  => $data['id_emergency_contact'],
                ':marital_status'    => $data['marital_status'],
                ':ethnic_group'      => $data['ethnic_group'],
                ':religion'          => $data['religion'],
                ':status'            => 'A',
                ':patient_id'        => $patientId
            ];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }

    /**
     * Valida los datos del paciente antes de guardarlos
     * @param array $data Datos del paciente a validar
     * @throws Exception Si los datos no son válidos
     */
    private function validatePatientData($data)
    {
        // Validar teléfono (debe tener exactamente 10 dígitos)
        if (isset($data['phone']) && !empty($data['phone'])) {
            if (!preg_match('/^\d{10}$/', $data['phone'])) {
                throw new Exception('El número de teléfono debe tener exactamente 10 dígitos');
            }
        }

        // Validar código postal (debe tener exactamente 5 dígitos)
        if (isset($data['CP']) && !empty($data['CP'])) {
            if (!preg_match('/^\d{5}$/', $data['CP'])) {
                throw new Exception('El código postal debe tener exactamente 5 dígitos');
            }
        }

        // Validar número exterior (debe contener solo letras y números, no caracteres especiales)
        if (isset($data['external_number']) && $data['external_number'] !== '') {
            if (!preg_match('/^[A-Za-z0-9]+$/', $data['external_number'])) {
                throw new Exception('El número exterior debe contener solo letras y números, sin caracteres especiales');
            }
        }

        // Validar número interior (debe contener solo letras y números, no caracteres especiales)
        if (isset($data['internal_number']) && $data['internal_number'] !== '') {
            if (!preg_match('/^[A-Za-z0-9]+$/', $data['internal_number'])) {
                throw new Exception('El número interior debe contener solo letras y números, sin caracteres especiales');
            }
        }
    }

    /**
     * Crea un nuevo registro de pacientes.
     * @param array $data Datos del pacientes.
     * @param string|null $photoData Datos binarios de la foto.
     * @return int ID del nuevo paciente.
     * @throws Exception Si los datos no son válidos
     */
    public function createPatient($data, $photoData)
    {
        // Validar los datos antes de guardarlos
        $this->validatePatientData($data);

        $stmt = $this->pdo->prepare("INSERT INTO patients (
            names, last_name, last_name2, id_state, id_municipality, id_locality,
            CP, street, external_number, internal_number, neighborhood, insurance_number,
            birth_date, CURP, RFC, phone, photo, email, gender, weight, height, blood_type, id_emergency_contact,
            marital_status, ethnic_group, religion, status
        ) VALUES (
            :names, :last_name, :last_name2, :id_state, :id_municipality, :id_locality,
            :CP, :street, :external_number, :internal_number, :neighborhood, :insurance_number,
            :birth_date, :CURP, :RFC, :phone, :photo, :email, :gender, :weight, :height, :blood_type, :id_emergency_contact,
            :marital_status, :ethnic_group, :religion, :status
        )");

        $stmt->execute([
            ':names'                  => $data['names'],
            ':last_name'              => $data['last_name'],
            ':last_name2'             => $data['last_name2'],
            ':id_state'               => $data['id_state'],
            ':id_municipality'        => $data['id_municipality'],
            ':id_locality'            => $data['id_locality'],
            ':CP'                     => $data['CP'],
            ':street'                 => $data['street'],
            ':external_number'        => $data['external_number'],
            ':internal_number'        => $data['internal_number'],
            ':neighborhood'           => $data['neighborhood'],
            ':insurance_number'       => $data['insurance_number'],
            ':birth_date'             => $data['birth_date'],
            ':CURP'                   => $data['CURP'],
            ':RFC'                    => $data['RFC'],
            ':phone'                  => $data['phone'],
            ':photo'                  => $photoData,
            ':email'                  => $data['email'],
            ':gender'                 => $data['gender'],
            ':weight'                 => $data['weight'],
            ':height'                 => $data['height'],
            ':blood_type'             => $data['blood_type'],
            ':id_emergency_contact'  => $data['id_emergency_contact'],
            ':marital_status'         => $data['marital_status'],
            ':ethnic_group'           => $data['ethnic_group'],
            ':religion'               => $data['religion'],
            ':status'                 => 'A',
        ]);

        return $this->pdo->lastInsertId();
    }

    /**
     * Obtiene todos los pacientes activos
     * @return array Lista de pacientes activos
     */
    public function getAllActivePatients()
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM patients WHERE status != 'I'");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener pacientes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca un paciente por su CURP
     * @param string $curp CURP del paciente a buscar
     * @return array|null Datos del paciente o null si no se encuentra
     */
    public function getPatientByCURP($curp)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM patients WHERE CURP = :curp AND status != 'I'");
            $stmt->execute([':curp' => $curp]);
            $patient = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($patient) {
                error_log("Modelo: Paciente encontrado con CURP: $curp, ID: " . $patient['id']);
            } else {
                error_log("Modelo: No se encontró ningún paciente con CURP: $curp");
            }

            return $patient;
        } catch (PDOException $e) {
            error_log("Error al buscar paciente por CURP: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Busca pacientes por CURP o nombre
     * @param string $searchTerm Término de búsqueda (CURP o parte del nombre)
     * @return array Lista de pacientes que coinciden con la búsqueda
     */
    public function searchPatients($searchTerm)
    {
        try {
            // Preparar el término de búsqueda para usar en LIKE
            $searchParam = '%' . $searchTerm . '%';

            // Buscar por CURP, nombres o apellidos
            $sql = "SELECT * FROM patients 
                    WHERE (CURP LIKE :search 
                    OR names LIKE :search 
                    OR last_name LIKE :search 
                    OR last_name2 LIKE :search) 
                    AND status != 'I'
                    ORDER BY names, last_name, last_name2";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':search' => $searchParam]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al buscar pacientes: " . $e->getMessage());
            return [];
        }
    }
}
