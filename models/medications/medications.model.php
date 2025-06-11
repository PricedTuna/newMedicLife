<?php

// models/medications/medications.model.php
class MedicationsModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Valida que un campo no este vacio y no exceda la longitud maxima
     * @param string $value Valor a validar
     * @param int $maxLength Longitud máxima permitida
     * @param string $fieldName Nombre del campo para el mensaje de error
     * @throws Exception Si el campo está vacío o excede la longitud máxima
     */
    private function validateField($value, $maxLength, $fieldName)
    {
        if (empty($value)) {
            throw new Exception("El campo $fieldName es obligatorio.");
        }
        if (strlen($value) > $maxLength) {
            throw new Exception("El campo $fieldName supera la longitud maxima de $maxLength caracteres.");
        }
    }

    /**
     * Verifica la unicidad de un campo, excluyendo opcionalmente un ID
     * @param string $table Nombre de la tabla
     * @param string $field Nombre del campo
     * @param string $value Valor a verificar
     * @param int|null $id ID a excluir (para actualizaciones)
     * @param string|null $customMessage Mensaje personalizado de error
     * @throws Exception Si el valor ya existe en la tabla
     */
    private function validateUnique($table, $field, $value, $id = null, $customMessage = null)
    {
        $query = "SELECT id FROM $table WHERE $field = :value";
        $params = [':value' => $value];
        if ($id !== null) {
            $query .= " AND id != :id";
            $params[':id'] = $id;
        }
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new Exception($customMessage ?? "El campo $field ya está en uso.");
        }
    }

    /**
     * Valida los datos del medicamento
     * @param array $data Datos a validar
     * @param int|null $id ID del medicamento (en actualización)
     * @throws Exception Si los datos no son válidos
     */
    public function validateMedicationData($data, $id = null)
    {
        // Validar campos obligatorios
        if (!isset($data['name']) || empty($data['name'])) {
            throw new Exception("El nombre del medicamento es obligatorio.");
        }

        if (!isset($data['id_medicine_type']) || empty($data['id_medicine_type'])) {
            throw new Exception("El tipo de medicamento es obligatorio.");
        }

        // Validar longitud del nombre
        $this->validateField($data['name'], 50, 'nombre');

        // Validar unicidad del nombre
        $this->validateUnique('medications', 'name', $data['name'], $id, "El nombre del medicamento ya existe.");

        // Validar que el tipo de medicamento exista
        $stmt = $this->pdo->prepare("SELECT id FROM medications_types WHERE id = :id");
        $stmt->execute([':id' => $data['id_medicine_type']]);
        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new Exception("El tipo de medicamento seleccionado no existe.");
        }

        // Validar que los campos numéricos sean números válidos
        if (isset($data['stock']) && (!is_numeric($data['stock']) || $data['stock'] < 0)) {
            throw new Exception("El stock debe ser un número positivo.");
        }

        if (isset($data['price_purchase']) && (!is_numeric($data['price_purchase']) || $data['price_purchase'] < 0)) {
            throw new Exception("El precio de compra debe ser un número positivo.");
        }

        if (isset($data['price_sale']) && (!is_numeric($data['price_sale']) || $data['price_sale'] < 0)) {
            throw new Exception("El precio de venta debe ser un número positivo.");
        }
    }

    /**
     * Crea un nuevo medicamento
     * @param array $data Datos del medicamento
     * @return int ID del nuevo medicamento
     * @throws Exception Si los datos no son válidos
     */
    public function createMedication($data)
    {
        try {
            // Log the data being received
            error_log("Intentando crear medicamento con datos: " . json_encode($data));

            // Validar los datos
            $this->validateMedicationData($data);

            // Verificar si la tabla medications existe
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as table_exists 
                FROM information_schema.tables 
                WHERE table_schema = DATABASE() 
                AND table_name = 'medications'
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['table_exists'] == 0) {
                // Crear la tabla medications si no existe
                error_log("La tabla medications no existe, creándola...");
                $this->createMedicationsTable();
            }

            // Preparar los datos para la inserción
            $stock = isset($data['stock']) ? intval($data['stock']) : 0;
            $price_purchase = isset($data['price_purchase']) ? intval($data['price_purchase']) : 0;
            $price_sale = isset($data['price_sale']) ? floatval($data['price_sale']) : 0.0;
            $status = isset($data['status']) && !empty($data['status']) ? $data['status'] : 'A';

            // Check if the status column exists in the medications table
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as column_exists 
                FROM information_schema.columns 
                WHERE table_schema = DATABASE() 
                AND table_name = 'medications' 
                AND column_name = 'status'
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $hasStatusColumn = $result['column_exists'] > 0;

            error_log("Status column exists in medications table: " . ($hasStatusColumn ? 'Yes' : 'No'));

            // Log the prepared data
            error_log("Datos preparados para inserción: name={$data['name']}, id_medicine_type={$data['id_medicine_type']}, stock={$stock}, price_purchase={$price_purchase}, price_sale={$price_sale}, status={$status}");

            // Insertar el medicamento - Prepare SQL based on column existence
            if ($hasStatusColumn) {
                $sql = "
                    INSERT INTO medications (
                        name, id_medicine_type, stock, price_purchase, price_sale, status
                    ) VALUES (
                        :name, :id_medicine_type, :stock, :price_purchase, :price_sale, :status
                    )
                ";
                $params = [
                    ':name' => $data['name'],
                    ':id_medicine_type' => $data['id_medicine_type'],
                    ':stock' => $stock,
                    ':price_purchase' => $price_purchase,
                    ':price_sale' => $price_sale,
                    ':status' => $status
                ];
            } else {
                $sql = "
                    INSERT INTO medications (
                        name, id_medicine_type, stock, price_purchase, price_sale
                    ) VALUES (
                        :name, :id_medicine_type, :stock, :price_purchase, :price_sale
                    )
                ";
                $params = [
                    ':name' => $data['name'],
                    ':id_medicine_type' => $data['id_medicine_type'],
                    ':stock' => $stock,
                    ':price_purchase' => $price_purchase,
                    ':price_sale' => $price_sale
                ];
            }

            error_log("SQL para inserción: " . $sql);
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute($params);

            $newId = $this->pdo->lastInsertId();
            error_log("Medicamento creado exitosamente con ID: " . $newId);

            return $newId;
        } catch (Exception $e) {
            error_log("Error al crear medicamento: " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Actualiza un medicamento existente
     * @param int $id ID del medicamento
     * @param array $data Datos a actualizar
     * @throws Exception Si los datos no son válidos o el medicamento no existe
     */
    public function updateMedication($id, $data)
    {
        try {
            // Validar los datos
            $this->validateMedicationData($data, $id);

            // Verificar que el medicamento exista
            $stmt = $this->pdo->prepare("SELECT id FROM medications WHERE id = :id");
            $stmt->execute([':id' => $id]);
            if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                throw new Exception("El medicamento con ID $id no existe.");
            }

            // Preparar los datos para la actualización
            $stock = isset($data['stock']) ? intval($data['stock']) : 0;
            $price_purchase = isset($data['price_purchase']) ? intval($data['price_purchase']) : 0;
            $price_sale = isset($data['price_sale']) ? floatval($data['price_sale']) : 0.0;
            $status = isset($data['status']) && !empty($data['status']) ? $data['status'] : 'A';

            // Check if the status column exists in the medications table
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as column_exists 
                FROM information_schema.columns 
                WHERE table_schema = DATABASE() 
                AND table_name = 'medications' 
                AND column_name = 'status'
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $hasStatusColumn = $result['column_exists'] > 0;

            error_log("Status column exists in medications table (update): " . ($hasStatusColumn ? 'Yes' : 'No'));

            // Actualizar el medicamento - Prepare SQL based on column existence
            if ($hasStatusColumn) {
                $sql = "
                    UPDATE medications SET
                        name = :name,
                        id_medicine_type = :id_medicine_type,
                        stock = :stock,
                        price_purchase = :price_purchase,
                        price_sale = :price_sale,
                        status = :status
                    WHERE id = :id
                ";
                $params = [
                    ':name' => $data['name'],
                    ':id_medicine_type' => $data['id_medicine_type'],
                    ':stock' => $stock,
                    ':price_purchase' => $price_purchase,
                    ':price_sale' => $price_sale,
                    ':status' => $status,
                    ':id' => $id
                ];
            } else {
                $sql = "
                    UPDATE medications SET
                        name = :name,
                        id_medicine_type = :id_medicine_type,
                        stock = :stock,
                        price_purchase = :price_purchase,
                        price_sale = :price_sale
                    WHERE id = :id
                ";
                $params = [
                    ':name' => $data['name'],
                    ':id_medicine_type' => $data['id_medicine_type'],
                    ':stock' => $stock,
                    ':price_purchase' => $price_purchase,
                    ':price_sale' => $price_sale,
                    ':id' => $id
                ];
            }

            error_log("SQL para actualización: " . $sql);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return true;
        } catch (Exception $e) {
            error_log("Error al actualizar medicamento: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Elimina un medicamento (cambio de estado a inactivo)
     * @param int $id ID del medicamento
     * @throws Exception Si el medicamento no existe o no se puede eliminar
     */
    public function deleteMedication($id)
    {
        try {
            // Verificar que el medicamento exista
            $stmt = $this->pdo->prepare("SELECT id FROM medications WHERE id = :id");
            $stmt->execute([':id' => $id]);
            if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                throw new Exception("El medicamento con ID $id no existe.");
            }

            // Check if the status column exists in the medications table
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as column_exists 
                FROM information_schema.columns 
                WHERE table_schema = DATABASE() 
                AND table_name = 'medications' 
                AND column_name = 'status'
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $hasStatusColumn = $result['column_exists'] > 0;

            error_log("Status column exists in medications table (delete): " . ($hasStatusColumn ? 'Yes' : 'No'));

            // Verificar si el medicamento está siendo usado en prescripciones
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count FROM prescription_medications 
                WHERE id_medicine = :id
            ");
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] > 0 && $hasStatusColumn) {
                // Si está siendo usado y existe la columna status, cambiar el estado a inactivo
                error_log("Medicamento en uso, cambiando estado a inactivo");
                $stmt = $this->pdo->prepare("
                    UPDATE medications SET status = 'I' WHERE id = :id
                ");
                $stmt->execute([':id' => $id]);
            } else {
                // Si no está siendo usado o no existe la columna status, eliminar el registro
                error_log("Eliminando medicamento con ID: " . $id);
                $stmt = $this->pdo->prepare("
                    DELETE FROM medications WHERE id = :id
                ");
                $stmt->execute([':id' => $id]);
            }

            return true;
        } catch (Exception $e) {
            error_log("Error al eliminar medicamento: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtiene un medicamento por su ID
     * @param int $id ID del medicamento
     * @return array|null Datos del medicamento o null si no se encuentra
     */
    public function getMedicationById($id)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT m.*, mt.name as medicine_type_name
                FROM medications m
                JOIN medications_types mt ON m.id_medicine_type = mt.id
                WHERE m.id = :id
            ");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener medicamento: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene todos los medicamentos activos
     * @return array Lista de medicamentos
     */
    public function getAllMedications()
    {
        try {
            // Check if the status column exists in the medications table
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as column_exists 
                FROM information_schema.columns 
                WHERE table_schema = DATABASE() 
                AND table_name = 'medications' 
                AND column_name = 'status'
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $hasStatusColumn = $result['column_exists'] > 0;

            error_log("Status column exists in medications table (getAllMedications): " . ($hasStatusColumn ? 'Yes' : 'No'));

            if ($hasStatusColumn) {
                $sql = "
                    SELECT m.*, mt.name as medicine_type_name
                    FROM medications m
                    JOIN medications_types mt ON m.id_medicine_type = mt.id
                    WHERE m.status != 'I'
                    ORDER BY m.name
                ";
            } else {
                $sql = "
                    SELECT m.*, mt.name as medicine_type_name
                    FROM medications m
                    JOIN medications_types mt ON m.id_medicine_type = mt.id
                    ORDER BY m.name
                ";
            }

            error_log("SQL para obtener medicamentos: " . $sql);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $medications = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Número de medicamentos obtenidos: " . count($medications));

            return $medications;
        } catch (PDOException $e) {
            error_log("Error al obtener medicamentos: " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            return [];
        }
    }

    /**
     * Busca medicamentos por nombre
     * @param string $searchTerm Término de búsqueda
     * @return array Lista de medicamentos que coinciden con la búsqueda
     */
    public function searchMedications($searchTerm)
    {
        try {
            // Check if the status column exists in the medications table
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as column_exists 
                FROM information_schema.columns 
                WHERE table_schema = DATABASE() 
                AND table_name = 'medications' 
                AND column_name = 'status'
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $hasStatusColumn = $result['column_exists'] > 0;

            error_log("Status column exists in medications table (searchMedications): " . ($hasStatusColumn ? 'Yes' : 'No'));

            $searchParam = '%' . $searchTerm . '%';

            if ($hasStatusColumn) {
                $sql = "
                    SELECT m.*, mt.name as medicine_type_name
                    FROM medications m
                    JOIN medications_types mt ON m.id_medicine_type = mt.id
                    WHERE m.name LIKE :search AND m.status != 'I'
                    ORDER BY m.name
                ";
            } else {
                $sql = "
                    SELECT m.*, mt.name as medicine_type_name
                    FROM medications m
                    JOIN medications_types mt ON m.id_medicine_type = mt.id
                    WHERE m.name LIKE :search
                    ORDER BY m.name
                ";
            }

            error_log("SQL para buscar medicamentos: " . $sql . " con término: " . $searchTerm);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':search' => $searchParam]);
            $medications = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Número de medicamentos encontrados: " . count($medications));

            return $medications;
        } catch (PDOException $e) {
            error_log("Error al buscar medicamentos: " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            return [];
        }
    }

    /**
     * Obtiene todos los tipos de medicamentos
     * @return array Lista de tipos de medicamentos
     */
    public function getAllMedicationTypes()
    {
        try {
            // Verificar si la tabla medications_types existe y tiene la estructura correcta
            $this->createMedicationTypesTable();

            // Check if the status column exists in the medications_types table
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as column_exists 
                FROM information_schema.columns 
                WHERE table_schema = DATABASE() 
                AND table_name = 'medications_types' 
                AND column_name = 'status'
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $hasStatusColumn = $result['column_exists'] > 0;

            if ($hasStatusColumn) {
                $stmt = $this->pdo->prepare("
                    SELECT * FROM medications_types
                    WHERE status = 'A'
                    ORDER BY name
                ");
            } else {
                $stmt = $this->pdo->prepare("
                    SELECT * FROM medications_types
                    ORDER BY name
                ");
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener tipos de medicamentos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene un tipo de medicamento por su ID
     * @param int $id ID del tipo de medicamento
     * @return array|null Datos del tipo de medicamento o null si no se encuentra
     */
    public function getMedicationTypeById($id)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM medications_types
                WHERE id = :id
            ");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener tipo de medicamento: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Crea un nuevo tipo de medicamento
     * @param array $data Datos del tipo de medicamento
     * @return int ID del nuevo tipo de medicamento
     * @throws Exception Si los datos no son válidos
     */
    public function createMedicationType($data)
    {
        try {
            // Validar los datos
            $this->validateField($data['name'], 50, 'nombre');
            $this->validateUnique('medications_types', 'name', $data['name'], null, "El nombre del tipo de medicamento ya existe.");

            // Verificar si la tabla medications_types existe
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as table_exists 
                FROM information_schema.tables 
                WHERE table_schema = DATABASE() 
                AND table_name = 'medications_types'
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['table_exists'] == 0) {
                // Crear la tabla medications_types si no existe
                $this->createMedicationTypesTable();
            }

            // Insertar el tipo de medicamento
            $stmt = $this->pdo->prepare("
                INSERT INTO medications_types (
                    name, description, status
                ) VALUES (
                    :name, :description, :status
                )
            ");

            $stmt->execute([
                ':name' => $data['name'],
                ':description' => $data['description'] ?? '',
                ':status' => 'A'
            ]);

            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            error_log("Error al crear tipo de medicamento: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Actualiza un tipo de medicamento existente
     * @param int $id ID del tipo de medicamento
     * @param array $data Datos a actualizar
     * @throws Exception Si los datos no son válidos o el tipo de medicamento no existe
     */
    public function updateMedicationType($id, $data)
    {
        try {
            // Validar los datos
            $this->validateField($data['name'], 50, 'nombre');
            $this->validateUnique('medications_types', 'name', $data['name'], $id, "El nombre del tipo de medicamento ya existe.");

            // Verificar que el tipo de medicamento exista
            $stmt = $this->pdo->prepare("SELECT id FROM medications_types WHERE id = :id");
            $stmt->execute([':id' => $id]);
            if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                throw new Exception("El tipo de medicamento con ID $id no existe.");
            }

            // Actualizar el tipo de medicamento
            $stmt = $this->pdo->prepare("
                UPDATE medications_types SET
                    name = :name,
                    description = :description,
                    status = :status
                WHERE id = :id
            ");

            $stmt->execute([
                ':name' => $data['name'],
                ':description' => $data['description'] ?? '',
                ':status' => $data['status'] ?? 'A',
                ':id' => $id
            ]);

            return true;
        } catch (Exception $e) {
            error_log("Error al actualizar tipo de medicamento: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Elimina un tipo de medicamento (cambio de estado a inactivo)
     * @param int $id ID del tipo de medicamento
     * @throws Exception Si el tipo de medicamento no existe o no se puede eliminar
     */
    public function deleteMedicationType($id)
    {
        try {
            // Verificar que el tipo de medicamento exista
            $stmt = $this->pdo->prepare("SELECT id FROM medications_types WHERE id = :id");
            $stmt->execute([':id' => $id]);
            if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                throw new Exception("El tipo de medicamento con ID $id no existe.");
            }

            // Verificar si el tipo de medicamento está siendo usado en medicamentos
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count FROM medications 
                WHERE id_medicine_type = :id
            ");
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                // Si está siendo usado, solo cambiar el estado a inactivo
                $stmt = $this->pdo->prepare("
                    UPDATE medications_types SET status = 'I' WHERE id = :id
                ");
                $stmt->execute([':id' => $id]);
            } else {
                // Si no está siendo usado, eliminar el registro
                $stmt = $this->pdo->prepare("
                    DELETE FROM medications_types WHERE id = :id
                ");
                $stmt->execute([':id' => $id]);
            }

            return true;
        } catch (Exception $e) {
            error_log("Error al eliminar tipo de medicamento: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Crea la tabla medications si no existe
     */
    private function createMedicationsTable()
    {
        try {
            $sql = "
                CREATE TABLE IF NOT EXISTS `medications` (
                  `id` int NOT NULL AUTO_INCREMENT,
                  `id_medicine_type` int NOT NULL,
                  `name` varchar(50) NOT NULL,
                  `stock` int DEFAULT NULL,
                  `price_purchase` int DEFAULT NULL,
                  `price_sale` double DEFAULT NULL,
                  `status` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  KEY `id_medicine_type` (`id_medicine_type`),
                  CONSTRAINT `medications_ibfk_1` FOREIGN KEY (`id_medicine_type`) REFERENCES `medications_types` (`id`)
                )
            ";
            $this->pdo->exec($sql);
            error_log("Tabla medications creada exitosamente");
        } catch (PDOException $e) {
            error_log("Error al crear tabla medications: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Crea la tabla medications_types si no existe
     */
    private function createMedicationTypesTable()
    {
        try {
            // First check if the table exists
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as table_exists 
                FROM information_schema.tables 
                WHERE table_schema = DATABASE() 
                AND table_name = 'medications_types'
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $tableExists = $result['table_exists'] > 0;

            if (!$tableExists) {
                // Create the table if it doesn't exist
                $sql = "
                    CREATE TABLE IF NOT EXISTS `medications_types` (
                      `id` int NOT NULL AUTO_INCREMENT,
                      `name` varchar(50) NOT NULL,
                      `description` text,
                      `status` char(1) NOT NULL DEFAULT 'A',
                      PRIMARY KEY (`id`)
                    )
                ";
                $this->pdo->exec($sql);
                error_log("Tabla medications_types creada exitosamente");
            } else {
                // Check if the description column exists
                $stmt = $this->pdo->prepare("
                    SELECT COUNT(*) as column_exists 
                    FROM information_schema.columns 
                    WHERE table_schema = DATABASE() 
                    AND table_name = 'medications_types' 
                    AND column_name = 'description'
                ");
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $hasDescriptionColumn = $result['column_exists'] > 0;

                // Add the description column if it doesn't exist
                if (!$hasDescriptionColumn) {
                    $sql = "ALTER TABLE `medications_types` ADD COLUMN `description` text";
                    $this->pdo->exec($sql);
                    error_log("Columna description agregada a la tabla medications_types");
                }

                // Check if the status column exists
                $stmt = $this->pdo->prepare("
                    SELECT COUNT(*) as column_exists 
                    FROM information_schema.columns 
                    WHERE table_schema = DATABASE() 
                    AND table_name = 'medications_types' 
                    AND column_name = 'status'
                ");
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $hasStatusColumn = $result['column_exists'] > 0;

                // Add the status column if it doesn't exist
                if (!$hasStatusColumn) {
                    $sql = "ALTER TABLE `medications_types` ADD COLUMN `status` char(1) NOT NULL DEFAULT 'A'";
                    $this->pdo->exec($sql);
                    error_log("Columna status agregada a la tabla medications_types");
                }
            }

            error_log("Tabla medications_types verificada y actualizada si era necesario");

            // Insertar algunos tipos de medicamentos por defecto
            $defaultTypes = [
                ['name' => 'Analgésico', 'description' => 'Medicamentos para aliviar el dolor'],
                ['name' => 'Antibiótico', 'description' => 'Medicamentos para combatir infecciones bacterianas'],
                ['name' => 'Antiinflamatorio', 'description' => 'Medicamentos para reducir la inflamación'],
                ['name' => 'Antipirético', 'description' => 'Medicamentos para reducir la fiebre'],
                ['name' => 'Antialérgico', 'description' => 'Medicamentos para tratar alergias']
            ];

            foreach ($defaultTypes as $type) {
                $stmt = $this->pdo->prepare("
                    INSERT INTO medications_types (name, description, status)
                    VALUES (:name, :description, 'A')
                ");
                $stmt->execute([
                    ':name' => $type['name'],
                    ':description' => $type['description']
                ]);
            }
        } catch (PDOException $e) {
            error_log("Error al crear tabla medications_types: " . $e->getMessage());
            throw $e;
        }
    }
}
