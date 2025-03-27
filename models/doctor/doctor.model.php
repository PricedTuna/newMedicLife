<?php
// models/DoctorModel.php
class DoctorModel {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Valida que un campo no esté vacío y no exceda la longitud máxima
    private function validateField($value, $maxLength, $fieldName) {
        if (empty($value)) {
            throw new Exception("El campo $fieldName es obligatorio.");
        }
        if (strlen($value) > $maxLength) {
            throw new Exception("El campo $fieldName supera la longitud máxima de $maxLength caracteres.");
        }
    }
    
    // Valida que un campo cumpla con el patrón especificado
    private function validateRegex($value, $pattern, $fieldName) {
        if (!preg_match($pattern, $value)) {
            throw new Exception("El campo $fieldName no tiene el formato correcto.");
        }
    }
    
    // Verifica la unicidad de un campo, excluyendo opcionalmente un ID
    private function validateUnique($table, $field, $value, $doctorId = null, $customMessage = null) {
        $query = "SELECT id FROM $table WHERE $field = :value";
        $params = [':value' => $value];
        if ($doctorId !== null) {
            $query .= " AND id != :doctorId";
            $params[':doctorId'] = $doctorId;
        }
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new Exception($customMessage ?? "El campo $field ya está en uso.");
        }
    }
    
    /**
     * Valida todos los datos del formulario.
     * @param array $data Datos a validar.
     * @param int|null $doctorId ID del doctor (en actualización).
     */
    public function validateData($data, $doctorId = null) {
        $this->validateField($data['names'], 40, 'nombre');
        $this->validateField($data['last_name'], 40, 'apellido paterno');
        $this->validateField($data['last_name2'], 40, 'apellido materno');
        $this->validateRegex($data['CP'], '/^\d{5}$/', 'código postal');
        $this->validateField($data['street'], 50, 'calle');
        $this->validateField($data['external_number'], 8, 'número exterior');
        $this->validateField($data['neighborhood'], 50, 'colonia');
        $this->validateField($data['insurance_number'], 20, 'número de afiliación');
        $this->validateField($data['professional_id'], 15, 'cédula profesional');
        $this->validateRegex($data['birth_date'], '/^\d{4}-\d{2}-\d{2}$/', 'fecha de nacimiento (YYYY-MM-DD)');
        $this->validateField($data['CURP'], 18, 'CURP');
        $this->validateField($data['RFC'], 13, 'RFC');
        $this->validateRegex($data['phone'], '/^\d{10}$/', 'teléfono');
        $this->validateRegex($data['email'], '/^[\w\.\-]+@[\w\.\-]+\.\w{2,4}$/', 'correo electrónico');
        $this->validateField($data['gender'], 2, 'género');
        
        if (!empty($data['internal_number'])) {
            $this->validateField($data['internal_number'], 8, 'número interior');
        }
        
        // Validación de unicidad
        $this->validateUnique('doctors', 'CURP', $data['CURP'], $doctorId, "La CURP que intentas registrar ya existe.");
        $this->validateUnique('doctors', 'phone', $data['phone'], $doctorId, "El número de teléfono que intentas registrar ya existe.");
        $this->validateUnique('doctors', 'insurance_number', $data['insurance_number'], $doctorId, "El número de afiliación que intentas registrar ya existe.");
    }
    
    /**
     * Actualiza un doctor existente.
     * @param int $doctorId ID del doctor.
     * @param array $data Datos a actualizar.
     * @param string|null $photoData Datos binarios de la foto.
     */
    public function updateDoctor($doctorId, $data, $photoData) {
        // Verifica que el doctor exista
        $stmt = $this->pdo->prepare("SELECT id FROM doctors WHERE id = :doctor_id");
        $stmt->execute([':doctor_id' => $doctorId]);
        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new Exception("El doctor con ID $doctorId no existe.");
        }
        
        $stmt = $this->pdo->prepare("UPDATE doctors SET
            names = :names, last_name = :last_name, last_name2 = :last_name2,
            id_state = :id_state, id_municipality = :id_municipality, id_locality = :id_locality,
            CP = :CP, street = :street, external_number = :external_number, internal_number = :internal_number,
            neighborhood = :neighborhood, insurance_number = :insurance_number, professional_id = :professional_id,
            birth_date = :birth_date, CURP = :CURP, RFC = :RFC, phone = :phone, gender = :gender,
            email = :email, photo = :photo, status = :status
            WHERE id = :doctor_id");
        $stmt->execute([
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
            ':professional_id'   => $data['professional_id'],
            ':birth_date'        => $data['birth_date'],
            ':CURP'              => $data['CURP'],
            ':RFC'               => $data['RFC'],
            ':phone'             => $data['phone'],
            ':gender'            => $data['gender'],
            ':email'             => $data['email'],
            ':photo'             => $photoData,
            ':status'            => 'A',
            ':doctor_id'         => $doctorId
        ]);
    }
    
    /**
     * Crea un nuevo registro de doctor.
     * @param array $data Datos del doctor.
     * @param string|null $photoData Datos binarios de la foto.
     * @return int ID del nuevo doctor.
     */
    public function createDoctor($data, $photoData) {
        $stmt = $this->pdo->prepare("INSERT INTO doctors (
            names, last_name, last_name2, id_state, id_municipality, id_locality,
            CP, street, external_number, internal_number, neighborhood, insurance_number,
            professional_id, birth_date, CURP, RFC, phone, gender, email, photo, status
        ) VALUES (
            :names, :last_name, :last_name2, :id_state, :id_municipality, :id_locality,
            :CP, :street, :external_number, :internal_number, :neighborhood, :insurance_number,
            :professional_id, :birth_date, :CURP, :RFC, :phone, :gender, :email, :photo, :status
        )");
        $stmt->execute([
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
            ':professional_id'   => $data['professional_id'],
            ':birth_date'        => $data['birth_date'],
            ':CURP'              => $data['CURP'],
            ':RFC'               => $data['RFC'],
            ':phone'             => $data['phone'],
            ':gender'            => $data['gender'],
            ':email'             => $data['email'],
            ':photo'             => $photoData,
            ':status'            => 'A'
        ]);
        return $this->pdo->lastInsertId();
    }
}
