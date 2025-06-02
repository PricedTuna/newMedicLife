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
                SELECT mh.*, d.names as doctor_names, d.last_name as doctor_last_name, d.last_name2 as doctor_last_name2
                FROM medical_history mh
                LEFT JOIN doctors d ON mh.doctor_id = d.id
                WHERE mh.patient_id = :patient_id AND mh.status != 'deleted'
                ORDER BY mh.date_created DESC
            ");
            $stmt->execute([':patient_id' => $patientId]);
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get vital signs for each record
            foreach ($records as &$record) {
                $vitalSigns = $this->getVitalSigns($record['id']);
                if (!empty($vitalSigns)) {
                    $record['vital_signs_data'] = $vitalSigns;
                }

                // Get attachments for each record
                $attachments = $this->getRecordAttachments($record['id']);
                if (!empty($attachments)) {
                    $record['attachments'] = $attachments;
                }
            }

            return $records;
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
                SELECT mh.*, 
                       d.names as doctor_names, d.last_name as doctor_last_name, d.last_name2 as doctor_last_name2,
                       p.names as patient_names, p.last_name as patient_last_name, p.last_name2 as patient_last_name2,
                       p.birth_date, p.gender, p.CURP
                FROM medical_history mh
                LEFT JOIN doctors d ON mh.doctor_id = d.id
                LEFT JOIN patients p ON mh.patient_id = p.id
                WHERE mh.id = :record_id AND mh.status != 'deleted'
            ");
            $stmt->execute([':record_id' => $recordId]);
            $record = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($record) {
                // Get vital signs for the record
                $vitalSigns = $this->getVitalSigns($record['id']);
                if (!empty($vitalSigns)) {
                    $record['vital_signs_data'] = $vitalSigns;
                }

                // Get attachments for the record
                $attachments = $this->getRecordAttachments($record['id']);
                if (!empty($attachments)) {
                    $record['attachments'] = $attachments;
                }
            }

            return $record;
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
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("
                INSERT INTO medical_history (
                    patient_id, doctor_id, date_created, 
                    chief_complaint, current_illness, personal_history, family_history,
                    physical_examination, diagnosis, treatment_plan, observations,
                    next_appointment, status, created_at
                ) VALUES (
                    :patient_id, :doctor_id, :date_created, 
                    :chief_complaint, :current_illness, :personal_history, :family_history,
                    :physical_examination, :diagnosis, :treatment_plan, :observations,
                    :next_appointment, 'active', NOW()
                )
            ");

            $params = [
                ':patient_id' => $data['patient_id'],
                ':doctor_id' => $data['doctor_id'],
                ':date_created' => $data['date_created'] ?? date('Y-m-d H:i:s'),
                ':chief_complaint' => $data['chief_complaint'] ?? null,
                ':current_illness' => $data['current_illness'] ?? null,
                ':personal_history' => $data['personal_history'] ?? null,
                ':family_history' => $data['family_history'] ?? null,
                ':physical_examination' => $data['physical_examination'] ?? null,
                ':diagnosis' => $data['diagnosis'] ?? null,
                ':treatment_plan' => $data['treatment_plan'] ?? null,
                ':observations' => $data['observations'] ?? null,
                ':next_appointment' => $data['next_appointment'] ?? null
            ];

            $stmt->execute($params);
            $recordId = $this->pdo->lastInsertId();

            // Save vital signs if provided
            if (!empty($data['vital_signs'])) {
                $this->saveVitalSigns($recordId, $data['vital_signs']);
            }

            $this->pdo->commit();
            return $recordId;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
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
     * Obtiene los signos vitales de un registro médico
     * @param int $medicalHistoryId ID del registro médico
     * @return array Datos de los signos vitales
     */
    public function getVitalSigns($medicalHistoryId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM vital_signs_history
                WHERE medical_history_id = :medical_history_id
                ORDER BY measured_at DESC
            ");
            $stmt->execute([':medical_history_id' => $medicalHistoryId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener signos vitales: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Guarda los signos vitales de un registro médico
     * @param int $medicalHistoryId ID del registro médico
     * @param array $vitalSigns Datos de los signos vitales
     * @return int|false ID del nuevo registro de signos vitales o false en caso de error
     */
    public function saveVitalSigns($medicalHistoryId, $vitalSigns) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO vital_signs_history (
                    medical_history_id, temperature, blood_pressure, heart_rate,
                    respiratory_rate, weight, height, bmi, oxygen_saturation,
                    glucose_level, measured_at
                ) VALUES (
                    :medical_history_id, :temperature, :blood_pressure, :heart_rate,
                    :respiratory_rate, :weight, :height, :bmi, :oxygen_saturation,
                    :glucose_level, :measured_at
                )
            ");

            $params = [
                ':medical_history_id' => $medicalHistoryId,
                ':temperature' => $vitalSigns['temperature'] ?? null,
                ':blood_pressure' => $vitalSigns['blood_pressure'] ?? null,
                ':heart_rate' => $vitalSigns['heart_rate'] ?? null,
                ':respiratory_rate' => $vitalSigns['respiratory_rate'] ?? null,
                ':weight' => $vitalSigns['weight'] ?? null,
                ':height' => $vitalSigns['height'] ?? null,
                ':bmi' => $vitalSigns['bmi'] ?? null,
                ':oxygen_saturation' => $vitalSigns['oxygen_saturation'] ?? null,
                ':glucose_level' => $vitalSigns['glucose_level'] ?? null,
                ':measured_at' => $vitalSigns['measured_at'] ?? date('Y-m-d H:i:s')
            ];

            $stmt->execute($params);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error al guardar signos vitales: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene los archivos adjuntos de un registro médico
     * @param int $medicalHistoryId ID del registro médico
     * @return array Lista de archivos adjuntos
     */
    public function getRecordAttachments($medicalHistoryId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, medical_history_id, file_name, file_type, file_size, uploaded_at
                FROM medical_attachments
                WHERE medical_history_id = :medical_history_id
                ORDER BY uploaded_at DESC
            ");
            $stmt->execute([':medical_history_id' => $medicalHistoryId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener archivos adjuntos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Guarda un archivo adjunto para un registro médico
     * @param int $medicalHistoryId ID del registro médico
     * @param string $fileName Nombre del archivo
     * @param string $filePath Ruta del archivo
     * @param string $fileType Tipo MIME del archivo
     * @param int $fileSize Tamaño del archivo en bytes
     * @return int|false ID del nuevo archivo adjunto o false en caso de error
     */
    public function saveAttachment($medicalHistoryId, $fileName, $filePath, $fileType, $fileSize) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO medical_attachments (
                    medical_history_id, file_name, file_path, file_type, file_size, uploaded_at
                ) VALUES (
                    :medical_history_id, :file_name, :file_path, :file_type, :file_size, NOW()
                )
            ");

            $params = [
                ':medical_history_id' => $medicalHistoryId,
                ':file_name' => $fileName,
                ':file_path' => $filePath,
                ':file_type' => $fileType,
                ':file_size' => $fileSize
            ];

            $stmt->execute($params);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error al guardar archivo adjunto: " . $e->getMessage());
            return false;
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
            // First, create a medical history record if it doesn't exist
            $medicalHistoryId = null;

            if (!empty($data['medical_history_id'])) {
                $medicalHistoryId = $data['medical_history_id'];
            } else {
                // Create a basic medical history record
                $historyData = [
                    'patient_id' => $data['patient_id'],
                    'doctor_id' => $data['doctor_id'],
                    'diagnosis' => $data['title'],
                    'observations' => $data['description'] ?? 'Documento PDF subido',
                    'date_created' => date('Y-m-d H:i:s')
                ];

                $medicalHistoryId = $this->saveHistoryRecord($historyData);

                if (!$medicalHistoryId) {
                    throw new Exception('No se pudo crear el registro de historial médico');
                }
            }

            // Save the file to disk
            $fileName = $data['title'] . '_' . date('Y-m-d_H-i-s') . '.pdf';
            $filePath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/medical_attachments/';

            // Create directory if it doesn't exist
            if (!file_exists($filePath)) {
                mkdir($filePath, 0777, true);
            }

            $fullPath = $filePath . $fileName;
            file_put_contents($fullPath, $fileContent);

            // Save attachment record
            return $this->saveAttachment(
                $medicalHistoryId,
                $fileName,
                $fullPath,
                'application/pdf',
                strlen($fileContent)
            );
        } catch (Exception $e) {
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
                SELECT ma.id, ma.medical_history_id, ma.file_name, ma.file_type, ma.file_size, ma.uploaded_at,
                       mh.patient_id, mh.doctor_id, mh.diagnosis as title, mh.observations as description,
                       d.names as doctor_names, d.last_name as doctor_last_name, d.last_name2 as doctor_last_name2
                FROM medical_attachments ma
                JOIN medical_history mh ON ma.medical_history_id = mh.id
                JOIN doctors d ON mh.doctor_id = d.id
                WHERE mh.patient_id = :patient_id AND mh.status != 'deleted'
                ORDER BY ma.uploaded_at DESC
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
     * @param int $attachmentId ID del adjunto
     * @return array|null Datos del documento o null si no se encuentra
     */
    public function getDocument($attachmentId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT ma.*, mh.patient_id, mh.doctor_id, mh.diagnosis as title, mh.observations as description
                FROM medical_attachments ma
                JOIN medical_history mh ON ma.medical_history_id = mh.id
                WHERE ma.id = :attachment_id
            ");
            $stmt->execute([':attachment_id' => $attachmentId]);
            $attachment = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($attachment && file_exists($attachment['file_path'])) {
                $attachment['file_content'] = file_get_contents($attachment['file_path']);
            }

            return $attachment;
        } catch (PDOException $e) {
            error_log("Error al obtener documento: " . $e->getMessage());
            return null;
        }
    }
}
