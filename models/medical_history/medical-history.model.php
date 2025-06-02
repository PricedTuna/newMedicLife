<?php

class MedicalHistoryModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Obtiene el historial médico de un paciente
     * @param int $patientId ID del paciente
     * @return array Registros del historial médico
     */
    public function getPatientHistory($patientId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT mh.*, a.appointment_date, a.medical_area, 
                       d.names as doctor_names, d.last_name as doctor_last_name, d.last_name2 as doctor_last_name2
                FROM medical_history mh
                LEFT JOIN appointments a ON mh.appointment_id = a.id
                LEFT JOIN doctors d ON a.id_doctor = d.id
                WHERE mh.patient_id = :patient_id
                ORDER BY mh.record_date DESC
            ");
            $stmt->execute([':patient_id' => $patientId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener historial médico: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene un registro específico del historial médico
     * @param int $recordId ID del registro
     * @return array|null Datos del registro o null si no se encuentra
     */
    public function getHistoryRecord($recordId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT mh.*, a.appointment_date, a.medical_area, 
                       d.names as doctor_names, d.last_name as doctor_last_name, d.last_name2 as doctor_last_name2,
                       p.names as patient_names, p.last_name as patient_last_name, p.last_name2 as patient_last_name2,
                       p.birth_date, p.gender, p.curp
                FROM medical_history mh
                LEFT JOIN appointments a ON mh.appointment_id = a.id
                LEFT JOIN doctors d ON a.id_doctor = d.id
                LEFT JOIN patients p ON mh.patient_id = p.id
                WHERE mh.id = :record_id
            ");
            $stmt->execute([':record_id' => $recordId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener registro de historial: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Guarda un nuevo registro en el historial médico
     * @param array $data Datos del registro
     * @return int|false ID del nuevo registro o false en caso de error
     */
    public function saveHistoryRecord($data) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO medical_history (
                    patient_id, doctor_id, appointment_id, record_date, 
                    diagnosis, observations, treatment, created_at
                ) VALUES (
                    :patient_id, :doctor_id, :appointment_id, :record_date,
                    :diagnosis, :observations, :treatment, NOW()
                )
            ");
            
            $params = [
                ':patient_id' => $data['patient_id'],
                ':doctor_id' => $data['doctor_id'],
                ':appointment_id' => !empty($data['appointment_id']) ? $data['appointment_id'] : null,
                ':record_date' => $data['record_date'],
                ':diagnosis' => $data['diagnosis'],
                ':observations' => $data['observations'],
                ':treatment' => $data['treatment']
            ];
            
            $stmt->execute($params);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error al guardar registro de historial: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene las citas de un paciente para asociarlas al historial
     * @param int $patientId ID del paciente
     * @return array Lista de citas del paciente
     */
    public function getPatientAppointments($patientId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT a.*, d.names as doctor_names, d.last_name as doctor_last_name, d.last_name2 as doctor_last_name2
                FROM appointments a
                JOIN doctors d ON a.id_doctor = d.id
                WHERE a.id_patient = :patient_id
                ORDER BY a.appointment_date DESC
            ");
            $stmt->execute([':patient_id' => $patientId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener citas del paciente: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Guarda un documento PDF en el historial médico
     * @param array $data Datos del documento
     * @param string $fileContent Contenido binario del archivo PDF
     * @return int|false ID del nuevo documento o false en caso de error
     */
    public function savePdfDocument($data, $fileContent) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO medical_documents (
                    patient_id, doctor_id, title, description, file_content, 
                    file_type, created_at
                ) VALUES (
                    :patient_id, :doctor_id, :title, :description, :file_content,
                    'application/pdf', NOW()
                )
            ");
            
            $params = [
                ':patient_id' => $data['patient_id'],
                ':doctor_id' => $data['doctor_id'],
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':file_content' => $fileContent
            ];
            
            $stmt->execute($params);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error al guardar documento PDF: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene los documentos PDF de un paciente
     * @param int $patientId ID del paciente
     * @return array Lista de documentos del paciente
     */
    public function getPatientDocuments($patientId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT md.id, md.patient_id, md.doctor_id, md.title, md.description, 
                       md.created_at, d.names as doctor_names, d.last_name as doctor_last_name, 
                       d.last_name2 as doctor_last_name2
                FROM medical_documents md
                JOIN doctors d ON md.doctor_id = d.id
                WHERE md.patient_id = :patient_id
                ORDER BY md.created_at DESC
            ");
            $stmt->execute([':patient_id' => $patientId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener documentos del paciente: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene un documento PDF específico
     * @param int $documentId ID del documento
     * @return array|null Datos del documento o null si no se encuentra
     */
    public function getDocument($documentId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM medical_documents
                WHERE id = :document_id
            ");
            $stmt->execute([':document_id' => $documentId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener documento: " . $e->getMessage());
            return null;
        }
    }
}