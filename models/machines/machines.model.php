<?php

class MachineModel
{
    public $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // =============================
    // 🔍 VALIDACIÓN DE DATOS
    // =============================
    public function validateMachineData($data, $isUpdate = false)
    {
        $errors = [];

        // Nombre obligatorio y longitud
        if (empty($data['name']) || strlen(trim($data['name'])) < 3) {
            $errors[] = 'El nombre de la máquina es obligatorio y debe tener al menos 3 caracteres.';
        }

        if (empty($data['id_machine_type']) || !$this->existsInTable('machine_types', $data['id_machine_type'], 'id')) {
            $errors[] = 'El tipo de máquina especificado no existe.';
        }

        if (empty($data['id_medical_area']) || !$this->existsInTable('medical_areas', $data['id_medical_area'], 'id')) {
            $errors[] = 'El área médica especificada no existe.';
        }



        // Estado operativo válido
        $validStatuses = ['Operativa', 'Mantenimiento', 'Fuera de servicio'];
        if (empty($data['operational_status']) || !in_array($data['operational_status'], $validStatuses)) {
            $errors[] = 'El estado operativo debe ser: Operativa, Mantenimiento o Fuera de servicio.';
        }

        // Buffer válido (entero positivo)
        if (!isset($data['buffer_minutes']) || !is_numeric($data['buffer_minutes']) || $data['buffer_minutes'] < 0) {
            $errors[] = 'El buffer debe ser un número positivo.';
        }

        // Validar formato de ventana operativa
        if (!empty($data['operational_windows']) && is_array($data['operational_windows'])) {
            foreach ($data['operational_windows'] as $window) {
                if (empty($window['day_of_week']) || empty($window['start_time']) || empty($window['end_time'])) {
                    $errors[] = 'Cada ventana operativa debe incluir día, hora de inicio y hora de fin.';
                }
                if (
                    !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $window['start_time']) ||
                    !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $window['end_time'])
                ) {
                    $errors[] = 'Las horas deben tener formato HH:MM o HH:MM:SS.';
                }
            }
        }

        // En caso de actualización, el ID debe existir
        if ($isUpdate && (!isset($data['id']) || !$this->existsInTable('machines', $data['id']))) {
            $errors[] = 'La máquina que se intenta actualizar no existe.';
        }

        return $errors;
    }

    public function existsInTable($table, $id, $idColumn = 'id')
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$table} WHERE {$idColumn} = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }


    public function getActiveMachines()
    {
        try {
            $sql = "
            SELECT 
                m.id,
                m.name,
                mt.name AS machine_type,
                ma.name AS medical_area,
                m.location,
                m.operational_status,
                m.buffer_minutes,
                m.assignment_rules,
                m.created_at,
                m.updated_at
            FROM machines m
            INNER JOIN machine_types mt ON m.id_machine_type = mt.id
            INNER JOIN medical_areas ma ON m.id_medical_area = ma.id
            WHERE m.operational_status = 'Operativa'
            ORDER BY m.name ASC
        ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener máquinas activas: " . $e->getMessage());
            return [];
        }
    }


    // =============================
    // ➕ CREATE
    // =============================
    // =============================
// ➕ CREATE
// =============================
    public function createMachine($data)
    {
        // Validar datos
        $errors = $this->validateMachineData($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            $this->pdo->beginTransaction();

            // Insertar la máquina con model y description
            $stmt = $this->pdo->prepare("
            INSERT INTO machines (
                name,
                model,
                description,
                id_machine_type,
                id_medical_area,
                location,
                operational_status,
                buffer_minutes,
                assignment_rules
            ) VALUES (
                :name,
                :model,
                :description,
                :id_machine_type,
                :id_medical_area,
                :location,
                :operational_status,
                :buffer_minutes,
                :assignment_rules
            )
        ");

            $stmt->execute([
                ':name' => trim($data['name']),
                ':model' => trim($data['model'] ?? ''),
                ':description' => trim($data['description'] ?? ''),
                ':id_machine_type' => (int) $data['id_machine_type'],
                ':id_medical_area' => (int) $data['id_medical_area'],
                ':location' => trim($data['location']),
                ':operational_status' => $data['operational_status'] ?? 'Operativa',
                ':buffer_minutes' => $data['buffer_minutes'] ?? 0,
                ':assignment_rules' => $data['assignment_rules'] ?? null
            ]);

            $machineId = $this->pdo->lastInsertId();

            // Insertar ventanas operativas si existen
            if (!empty($data['operational_windows']) && is_array($data['operational_windows'])) {
                $stmtWin = $this->pdo->prepare("
                INSERT INTO machine_operational_windows (
                    id_machine,
                    day_of_week,
                    start_time,
                    end_time
                ) VALUES (
                    :id_machine,
                    :day_of_week,
                    :start_time,
                    :end_time
                )
            ");
                foreach ($data['operational_windows'] as $window) {
                    $stmtWin->execute([
                        ':id_machine' => $machineId,
                        ':day_of_week' => $window['day_of_week'],
                        ':start_time' => $window['start_time'],
                        ':end_time' => $window['end_time']
                    ]);
                }
            }

            $this->pdo->commit();
            return [
                'success' => true,
                'message' => 'Máquina creada exitosamente.',
                'machine_id' => $machineId
            ];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Error al crear la máquina: ' . $e->getMessage()
            ];
        }
    }


    public function deleteMachine($id)
    {
        try {
            // Verificar que la máquina exista
            if (!$this->existsInTable('machines', $id)) {
                return [
                    'success' => false,
                    'message' => 'No se encontró ninguna máquina con el ID especificado.'
                ];
            }

            // Inicia una transacción
            $this->pdo->beginTransaction();

            // 1️⃣ Eliminar ventanas operativas asociadas usando el método existente
            $windowsResult = $this->deleteOperationalWindows($id);
            if (!$windowsResult['success']) {
                $this->pdo->rollBack();
                return [
                    'success' => false,
                    'message' => 'Error al eliminar ventanas operativas: ' . $windowsResult['message']
                ];
            }

            // 2️⃣ Eliminar la máquina principal
            $stmt = $this->pdo->prepare("
            DELETE FROM machines
            WHERE id = ?
        ");
            $stmt->execute([$id]);

            // Verifica si realmente se eliminó la máquina
            if ($stmt->rowCount() === 0) {
                $this->pdo->rollBack();
                return [
                    'success' => false,
                    'message' => 'No se pudo eliminar la máquina.'
                ];
            }

            // 3️⃣ Confirmar la transacción
            $this->pdo->commit();

            return [
                'success' => true,
                'message' => 'Máquina y sus ventanas operativas eliminadas correctamente.'
            ];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Error al eliminar la máquina: ' . $e->getMessage()
            ];
        }
    }


    // =============================
// ✏️ UPDATE
// =============================
    public function updateMachine($id, $data)
    {
        $data['id'] = $id;
        $errors = $this->validateMachineData($data, true);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            $this->pdo->beginTransaction();

            // Actualizar datos principales incluyendo model y description
            $stmt = $this->pdo->prepare("
            UPDATE machines
            SET
                name = ?,
                model = ?,
                description = ?,
                id_machine_type = ?,
                id_medical_area = ?,
                location = ?,
                operational_status = ?,
                buffer_minutes = ?,
                assignment_rules = ?,
                updated_at = NOW()
            WHERE id = ?
        ");

            $stmt->execute([
                $data['name'],
                $data['model'] ?? '',
                $data['description'] ?? '',
                $data['id_machine_type'],
                $data['id_medical_area'],
                $data['location'],
                $data['operational_status'],
                $data['buffer_minutes'],
                $data['assignment_rules'],
                $id
            ]);

            // Actualizar ventanas operativas si se proporcionan
            if (!empty($data['operational_windows']) && is_array($data['operational_windows'])) {
                // Eliminar anteriores
                $stmtDel = $this->pdo->prepare("
                DELETE FROM machine_operational_windows
                WHERE id_machine = ?
            ");
                $stmtDel->execute([$id]);

                // Insertar nuevas
                $stmtWin = $this->pdo->prepare("
                INSERT INTO machine_operational_windows (
                    id_machine,
                    day_of_week,
                    start_time,
                    end_time
                ) VALUES (?, ?, ?, ?)
            ");
                foreach ($data['operational_windows'] as $window) {
                    $stmtWin->execute([
                        $id,
                        $window['day_of_week'],
                        $window['start_time'],
                        $window['end_time']
                    ]);
                }
            }

            $this->pdo->commit();
            return ['success' => true, 'message' => 'Máquina actualizada correctamente.'];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => 'Error al actualizar la máquina: ' . $e->getMessage()];
        }
    }

    public function createMachineType($data)
    {
        if (empty($data['name']) || strlen(trim($data['name'])) < 3) {
            return ['success' => false, 'message' => 'El nombre del tipo de máquina es obligatorio y debe tener al menos 3 caracteres.'];
        }

        try {
            $stmt = $this->pdo->prepare("
            INSERT INTO machine_types (name, description)
            VALUES (?, ?)
        ");
            $stmt->execute([
                $data['name'],
                $data['description'] ?? null
            ]);

            return ['success' => true, 'message' => 'Tipo de máquina creado correctamente.'];

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al crear el tipo de máquina: ' . $e->getMessage()];
        }
    }

    // 🔍 Obtener todos los tipos de máquina
    public function getMachineTypes(): array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT id, name, description FROM machine_types ORDER BY name ASC');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function getMedicalAreas(): array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT id, name FROM medical_areas ORDER BY name ASC');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error al obtener las áreas médicas: ' . $e->getMessage());
            return [];
        }
    }

    public function getMachineById($id)
    {
        try {
            $stmt = $this->pdo->prepare("
            SELECT id, name, model, description, id_machine_type, id_medical_area,
                   location, operational_status, buffer_minutes, assignment_rules,
                   created_at, updated_at
            FROM machines
            WHERE id = ?
            LIMIT 1
        ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Exception $e) {
            error_log("Error al obtener la máquina por ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Eliminar un tipo de máquina
     * @param int $id Tipo de máquina a eliminar
     * @return array Resultado de la operación
     */
    public function deleteMachineType($id)
    {
        try {
            // Verificar si el tipo de máquina existe
            $stmtCheck = $this->pdo->prepare("SELECT COUNT(*) FROM machine_types WHERE id = ?");
            $stmtCheck->execute([$id]);
            if ($stmtCheck->fetchColumn() == 0) {
                return [
                    'success' => false,
                    'message' => 'No se encontró el tipo de máquina especificado.'
                ];
            }

            // Verificar si existen máquinas asociadas
            $stmtMachines = $this->pdo->prepare("SELECT COUNT(*) FROM machines WHERE id_machine_type = ?");
            $stmtMachines->execute([$id]);
            if ($stmtMachines->fetchColumn() > 0) {
                return [
                    'success' => false,
                    'message' => 'No se puede eliminar este tipo de máquina porque existen máquinas asociadas.'
                ];
            }

            // Eliminar el tipo de máquina
            $stmtDelete = $this->pdo->prepare("DELETE FROM machine_types WHERE id = ?");
            $stmtDelete->execute([$id]);

            if ($stmtDelete->rowCount() === 0) {
                return [
                    'success' => false,
                    'message' => 'No se pudo eliminar el tipo de máquina.'
                ];
            }

            return [
                'success' => true,
                'message' => 'Tipo de máquina eliminado correctamente.'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al eliminar el tipo de máquina: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Crea ventanas operativas para una máquina existente
     * 
     * @param int $machineId ID de la máquina
     * @param array $windows Array de ventanas operativas con estructura:
     * [
     *   ['day_of_week' => 'Lunes', 'start_time' => '08:00', 'end_time' => '12:00'],
     *   ...
     * ]
     * 
     * @return array Resultado con 'success' y 'message'
     */
    public function createOperationalWindows($machineId, array $windows)
    {
        // Verificar que la máquina exista
        if (!$this->existsInTable('machines', $machineId)) {
            return [
                'success' => false,
                'message' => 'La máquina especificada no existe.'
            ];
        }

        // Validar cada ventana
        $validDays = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        foreach ($windows as $index => $window) {
            if (
                empty($window['day_of_week']) ||
                !in_array($window['day_of_week'], $validDays)
            ) {
                return [
                    'success' => false,
                    'message' => "El día de la semana en la fila {$index} no es válido."
                ];
            }

            if (
                empty($window['start_time']) ||
                !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $window['start_time'])
            ) {
                return [
                    'success' => false,
                    'message' => "El horario de inicio en la fila {$index} no tiene formato válido (HH:MM o HH:MM:SS)."
                ];
            }

            if (
                empty($window['end_time']) ||
                !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $window['end_time'])
            ) {
                return [
                    'success' => false,
                    'message' => "El horario de fin en la fila {$index} no tiene formato válido (HH:MM o HH:MM:SS)."
                ];
            }

            // Validar que start_time < end_time
            if (strtotime($window['start_time']) >= strtotime($window['end_time'])) {
                return [
                    'success' => false,
                    'message' => "El horario de inicio debe ser menor al horario de fin en la fila {$index}."
                ];
            }
        }

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("
            INSERT INTO machine_operational_windows (id_machine, day_of_week, start_time, end_time)
            VALUES (:id_machine, :day_of_week, :start_time, :end_time)
        ");

            foreach ($windows as $window) {
                $stmt->execute([
                    ':id_machine' => $machineId,
                    ':day_of_week' => $window['day_of_week'],
                    ':start_time' => $window['start_time'],
                    ':end_time' => $window['end_time']
                ]);
            }

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => 'Ventanas operativas creadas correctamente.'
            ];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Error al crear ventanas operativas: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Actualiza las ventanas operativas de una máquina
     *
     * @param int $machineId ID de la máquina
     * @param array $windows Array de ventanas operativas:
     * [
     *   ['day_of_week' => 'Lunes', 'start_time' => '08:00', 'end_time' => '12:00'],
     *   ...
     * ]
     *
     * @return array Resultado con 'success' y 'message'
     */
    public function updateOperationalWindows($machineId, array $windows)
    {
        // Verificar que la máquina exista
        if (!$this->existsInTable('machines', $machineId)) {
            return [
                'success' => false,
                'message' => 'La máquina especificada no existe.'
            ];
        }

        // Validar cada ventana
        $validDays = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        foreach ($windows as $index => $window) {
            if (empty($window['day_of_week']) || !in_array($window['day_of_week'], $validDays)) {
                return [
                    'success' => false,
                    'message' => "El día de la semana en la fila {$index} no es válido."
                ];
            }

            if (empty($window['start_time']) || !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $window['start_time'])) {
                return [
                    'success' => false,
                    'message' => "El horario de inicio en la fila {$index} no tiene formato válido (HH:MM o HH:MM:SS)."
                ];
            }

            if (empty($window['end_time']) || !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $window['end_time'])) {
                return [
                    'success' => false,
                    'message' => "El horario de fin en la fila {$index} no tiene formato válido (HH:MM o HH:MM:SS)."
                ];
            }

            if (strtotime($window['start_time']) >= strtotime($window['end_time'])) {
                return [
                    'success' => false,
                    'message' => "El horario de inicio debe ser menor al horario de fin en la fila {$index}."
                ];
            }
        }

        try {
            $this->pdo->beginTransaction();

            // Eliminar ventanas existentes
            $stmtDel = $this->pdo->prepare("
            DELETE FROM machine_operational_windows
            WHERE id_machine = ?
        ");
            $stmtDel->execute([$machineId]);

            // Insertar nuevas ventanas
            $stmt = $this->pdo->prepare("
            INSERT INTO machine_operational_windows (id_machine, day_of_week, start_time, end_time)
            VALUES (:id_machine, :day_of_week, :start_time, :end_time)
        ");

            foreach ($windows as $window) {
                $stmt->execute([
                    ':id_machine' => $machineId,
                    ':day_of_week' => $window['day_of_week'],
                    ':start_time' => $window['start_time'],
                    ':end_time' => $window['end_time']
                ]);
            }

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => 'Ventanas operativas actualizadas correctamente.'
            ];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Error al actualizar ventanas operativas: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Elimina todas las ventanas operativas de una máquina
     *
     * @param int $machineId ID de la máquina
     * @return array Resultado con 'success' y 'message'
     */
    public function deleteOperationalWindows($machineId)
    {
        // Verificar que la máquina exista
        if (!$this->existsInTable('machines', $machineId)) {
            return [
                'success' => false,
                'message' => 'La máquina especificada no existe.'
            ];
        }

        try {
            $stmt = $this->pdo->prepare("
            DELETE FROM machine_operational_windows
            WHERE id_machine = ?
        ");
            $stmt->execute([$machineId]);

            return [
                'success' => true,
                'message' => 'Todas las ventanas operativas de la máquina han sido eliminadas.'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al eliminar ventanas operativas: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener todas las ventanas operativas de una máquina
     *
     * @param int $id_machine ID de la máquina
     * @return array Lista de ventanas operativas
     */
    public function getOperationalWindows($id_machine): array
    {
        try {
            $stmt = $this->pdo->prepare("
            SELECT id, day_of_week, start_time, end_time
            FROM machine_operational_windows
            WHERE id_machine = ?
            ORDER BY 
                FIELD(day_of_week, 'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'),
                start_time ASC
        ");
            $stmt->execute([$id_machine]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al obtener ventanas operativas para la máquina {$id_machine}: " . $e->getMessage());
            return [];
        }
    }

    public function getMachineTypeById($id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT id, name, description FROM machine_types WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Exception $e) {
            error_log("Error al obtener tipo de máquina por ID: " . $e->getMessage());
            return null;
        }
    }

    public function updateMachineType($id, $data)
    {
        if (empty($data['name']) || strlen(trim($data['name'])) < 3) {
            return ['success' => false, 'message' => 'El nombre del tipo de máquina es obligatorio y debe tener al menos 3 caracteres.'];
        }

        try {
            $stmt = $this->pdo->prepare("UPDATE machine_types SET name = ?, description = ? WHERE id = ?");
            $stmt->execute([trim($data['name']), $data['description'] ?? null, $id]);
            return ['success' => true, 'message' => 'Tipo de máquina actualizado correctamente.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al actualizar el tipo de máquina: ' . $e->getMessage()];
        }
    }

    // Obtener todas las máquinas (activas y no activas)
public function getAllMachines(): array
{
    try {
        $sql = "
        SELECT 
            m.id,
            m.name,
            m.model,
            m.description,
            mt.name AS machine_type,
            ma.name AS medical_area,
            m.location,
            m.operational_status,
            m.buffer_minutes,
            m.assignment_rules,
            m.created_at,
            m.updated_at
        FROM machines m
        INNER JOIN machine_types mt ON m.id_machine_type = mt.id
        INNER JOIN medical_areas ma ON m.id_medical_area = ma.id
        ORDER BY m.name ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Error al obtener todas las máquinas: " . $e->getMessage());
        return [];
    }
}


}