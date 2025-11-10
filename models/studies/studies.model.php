<?php
// models/studies/studies.model.php

class StudyModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // =========================
    // Crear un nuevo tipo de estudio
    // =========================
    // =========================
    // Crear un nuevo estudio
    // =========================
    public function createStudy(array $data): array
    {
        try {
            $this->pdo->beginTransaction();

            // Insertar estudio
            $stmt = $this->pdo->prepare("
                INSERT INTO study_types
                    (code, name, description, specialty, default_duration, preparation, sla_days, requires_technician, requires_machine, result_type, price, active)
                VALUES
                    (:code, :name, :description, :specialty, :default_duration, :preparation, :sla_days, :requires_technician, :requires_machine, :result_type, :price, :active)
            ");
            $stmt->execute([
                ':code' => $data['code'],
                ':name' => $data['name'],
                ':description' => $data['description'] ?? null,
                ':specialty' => $data['specialty'] ?? null,
                ':default_duration' => $data['default_duration'] ?? 0,
                ':preparation' => $data['preparation'] ?? null,
                ':sla_days' => $data['sla_days'] ?? 0,
                ':requires_technician' => $data['requires_technician'] ?? false,
                ':requires_machine' => $data['requires_machine'] ?? false,
                ':result_type' => $data['result_type'] ?? 'texto',
                ':price' => $data['price'] ?? 0.00,
                ':active' => $data['active'] ?? true
            ]);

            $studyId = (int) $this->pdo->lastInsertId();

            // Crear relación con máquina si aplica
            if (!empty($data['requires_machine']) && !empty($data['machine_id'])) {
                $this->createOrUpdateMachineCompatibility($studyId, $data['machine_id'], $data['default_duration'], $data['price']);
            }

            $this->pdo->commit();

            return ['success' => true, 'id' => $studyId];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // =========================
    // Crear o actualizar relación estudio-maquina
    // =========================
    public function createOrUpdateMachineCompatibility(int $studyId, int $machineId, ?int $duration = null, ?float $price = null): array
    {
        try {
            // Verificar si ya existe
            $stmt = $this->pdo->prepare("
                SELECT id FROM machine_study_compatibility
                WHERE study_type_id = :study_id AND machine_id = :machine_id
            ");
            $stmt->execute([':study_id' => $studyId, ':machine_id' => $machineId]);
            $exists = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($exists) {
                // Actualizar
                $stmt = $this->pdo->prepare("
                    UPDATE machine_study_compatibility
                    SET override_duration = :duration,
                        override_price = :price
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':duration' => $duration,
                    ':price' => $price,
                    ':id' => $exists['id']
                ]);
            } else {
                // Crear nuevo
                $stmt = $this->pdo->prepare("
                    INSERT INTO machine_study_compatibility
                        (study_type_id, machine_id, override_duration, override_price)
                    VALUES
                        (:study_id, :machine_id, :duration, :price)
                ");
                $stmt->execute([
                    ':study_id' => $studyId,
                    ':machine_id' => $machineId,
                    ':duration' => $duration,
                    ':price' => $price
                ]);
            }

            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // =========================
    // Obtener compatibilidad de un estudio con máquinas
    // =========================
    public function getMachineCompatibility(int $studyId): array
    {
        $stmt = $this->pdo->prepare("
        SELECT 
            msc.*,
            m.name AS machine_name,
            m.model AS machine_model,
            mt.name AS machine_type_name,
            ma.name AS medical_area_name,
            m.location,
            m.operational_status,
            m.buffer_minutes
        FROM machine_study_compatibility msc
        INNER JOIN machines m ON m.id = msc.machine_id
        INNER JOIN machine_types mt ON mt.id = m.id_machine_type
        INNER JOIN medical_areas ma ON ma.id = m.id_medical_area
        WHERE msc.study_type_id = :study_id
    ");
        $stmt->execute([':study_id' => $studyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // =========================
    // Eliminar relación estudio-maquina
    // =========================
    public function deleteMachineCompatibility(int $studyId, int $machineId): array
    {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM machine_study_compatibility
                WHERE study_type_id = :study_id AND machine_id = :machine_id
            ");
            $stmt->execute([':study_id' => $studyId, ':machine_id' => $machineId]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // =========================
    // Actualizar estudio y compatibilidad máquina
    // =========================
    // =========================
// Actualizar estudio y compatibilidad máquina
// =========================
    public function updateStudyWithMachine(int $id, array $data): array
    {
        try {
            $this->pdo->beginTransaction();

            // Actualizar estudio
            $this->updateStudy($id, $data);

            // Eliminar todas las relaciones anteriores
            $stmt = $this->pdo->prepare("DELETE FROM machine_study_compatibility WHERE study_type_id = :study_id");
            $stmt->execute([':study_id' => $id]);

            // Crear la nueva relación si requiere máquina
            if (!empty($data['requires_machine']) && !empty($data['machine_id'])) {
                $this->createOrUpdateMachineCompatibility(
                    $id,
                    $data['machine_id'],
                    $data['default_duration'],
                    $data['price']
                );
            }

            $this->pdo->commit();
            return ['success' => true];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    // =========================
    // Obtener todos los estudios
    // =========================
    public function getAllStudies(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM study_types ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================
    // Obtener un estudio por ID
    // =========================
    public function getStudyById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM study_types WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $study = $stmt->fetch(PDO::FETCH_ASSOC);
        return $study ?: null;
    }

    // =========================
    // Actualizar un estudio
    // =========================
    public function updateStudy(int $id, array $data): array
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE study_types SET
                    code = :code,
                    name = :name,
                    description = :description,
                    specialty = :specialty,
                    default_duration = :default_duration,
                    preparation = :preparation,
                    sla_days = :sla_days,
                    requires_technician = :requires_technician,
                    requires_machine = :requires_machine,
                    result_type = :result_type,
                    price = :price,
                    active = :active,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
            ");

            $stmt->execute([
                ':code' => $data['code'],
                ':name' => $data['name'],
                ':description' => $data['description'] ?? null,
                ':specialty' => $data['specialty'] ?? null,
                ':default_duration' => $data['default_duration'] ?? 0,
                ':preparation' => $data['preparation'] ?? null,
                ':sla_days' => $data['sla_days'] ?? 0,
                ':requires_technician' => $data['requires_technician'] ?? false,
                ':requires_machine' => $data['requires_machine'] ?? false,
                ':result_type' => $data['result_type'] ?? 'texto',
                ':price' => $data['price'] ?? 0.00,
                ':active' => $data['active'] ?? true,
                ':id' => $id
            ]);

            return ['success' => true];

        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // =========================
    // Eliminar un estudio
    // =========================
    public function deleteStudy(int $id): array
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM study_types WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
