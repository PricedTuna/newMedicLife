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
            // First check if the medical_history table exists
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as table_exists 
                FROM information_schema.tables 
                WHERE table_schema = DATABASE() 
                AND table_name = 'medical_history'
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['table_exists'] == 0) {
                error_log("La tabla medical_history no existe en la base de datos");
                throw new PDOException("La tabla medical_history no existe en la base de datos");
            }

            // Check the structure of the medical_history table
            $stmt = $this->pdo->prepare("DESCRIBE medical_history");
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // Determine if we're using the new or old schema
            $hasStatusColumn = in_array('status', $columns);

            if ($hasStatusColumn) {
                $stmt = $this->pdo->prepare("
                    SELECT mh.*, d.names as doctor_names, d.last_name as doctor_last_name, d.last_name2 as doctor_last_name2
                    FROM medical_history mh
                    LEFT JOIN appointments a ON mh.appointment_id = a.id
                    LEFT JOIN doctors d ON a.id_doctor = d.id
                    WHERE mh.patient_id = :patient_id AND mh.status != 'deleted'
                    ORDER BY mh.record_date DESC
                ");
            } else {
                $stmt = $this->pdo->prepare("
                    SELECT mh.*, d.names as doctor_names, d.last_name as doctor_last_name, d.last_name2 as doctor_last_name2
                    FROM medical_history mh
                    LEFT JOIN appointments a ON mh.appointment_id = a.id
                    LEFT JOIN doctors d ON a.id_doctor = d.id
                    WHERE mh.patient_id = :patient_id
                    ORDER BY mh.created_at DESC
                ");
            }

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
            throw $e;
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
                LEFT JOIN appointments a ON mh.appointment_id = a.id
                LEFT JOIN doctors d ON a.id_doctor = d.id
                LEFT JOIN patients p ON mh.patient_id = p.id
                WHERE mh.id = :record_id
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
            // Debug: Log input data
            error_log("saveHistoryRecord input data: " . print_r($data, true));

            // Verify required fields
            if (empty($data['patient_id'])) {
                error_log("Error: patient_id is empty");
                throw new PDOException("El ID del paciente es obligatorio");
            }

            if (empty($data['date_created'])) {
                error_log("Error: date_created is empty");
                throw new PDOException("La fecha es obligatoria");
            }

            $this->pdo->beginTransaction();

            // Check if the medical_history table exists
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as table_exists 
                FROM information_schema.tables 
                WHERE table_schema = DATABASE() 
                AND table_name = 'medical_history'
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['table_exists'] == 0) {
                error_log("La tabla medical_history no existe, intentando crearla");

                // Try to create the table
                $createTableSQL = "
                CREATE TABLE IF NOT EXISTS `medical_history` (
                  `id` int NOT NULL AUTO_INCREMENT,
                  `patient_id` int NOT NULL,
                  `appointment_id` int DEFAULT NULL,
                  `record_date` date NOT NULL,
                  `diagnosis` varchar(255) NOT NULL,
                  `observations` text NOT NULL,
                  `treatment` text NOT NULL,
                  `created_at` datetime NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `patient_id` (`patient_id`),
                  KEY `appointment_id` (`appointment_id`),
                  CONSTRAINT `medical_history_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
                  CONSTRAINT `medical_history_ibfk_3` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL
                )";

                $this->pdo->exec($createTableSQL);
                error_log("Tabla medical_history creada exitosamente");

                // Also create the vital_signs_history table
                $createVitalSignsTableSQL = "
                CREATE TABLE vital_signs_history (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    medical_history_id INT NOT NULL,
                    temperature DECIMAL(3,1),
                    blood_pressure VARCHAR(20),
                    heart_rate INT,
                    respiratory_rate INT,
                    weight DECIMAL(5,2),
                    height DECIMAL(5,2),
                    bmi DECIMAL(4,2),
                    oxygen_saturation INT,
                    glucose_level INT,
                    measured_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (medical_history_id) REFERENCES medical_history(id)
                )";

                $this->pdo->exec($createVitalSignsTableSQL);
                error_log("Tabla vital_signs_history creada exitosamente");
            }

            // Prepare SQL based on whether appointment_id is provided
            if (isset($data['appointment_id']) && !empty($data['appointment_id'])) {
                $stmt = $this->pdo->prepare("
                    INSERT INTO medical_history (
                        patient_id, appointment_id, record_date, 
                        diagnosis, observations, treatment, 
                        created_at
                    ) VALUES (
                        :patient_id, :appointment_id, :record_date, 
                        :diagnosis, :observations, :treatment, 
                        NOW()
                    )
                ");

                $params = [
                    ':patient_id' => $data['patient_id'],
                    ':appointment_id' => $data['appointment_id'],
                    ':record_date' => $data['date_created'] ?? date('Y-m-d'),
                    ':diagnosis' => $data['diagnosis'] ?? null,
                    ':observations' => $data['observations'] ?? null,
                    ':treatment' => $data['treatment_plan'] ?? null
                ];
            } else {
                $stmt = $this->pdo->prepare("
                    INSERT INTO medical_history (
                        patient_id, record_date, 
                        diagnosis, observations, treatment, 
                        created_at
                    ) VALUES (
                        :patient_id, :record_date, 
                        :diagnosis, :observations, :treatment, 
                        NOW()
                    )
                ");

                $params = [
                    ':patient_id' => $data['patient_id'],
                    ':record_date' => $data['date_created'] ?? date('Y-m-d'),
                    ':diagnosis' => $data['diagnosis'] ?? null,
                    ':observations' => $data['observations'] ?? null,
                    ':treatment' => $data['treatment_plan'] ?? null
                ];
            }

            // Debug: Log SQL parameters
            error_log("SQL parameters: " . print_r($params, true));

            $stmt->execute($params);
            $recordId = $this->pdo->lastInsertId();

            error_log("Record saved with ID: " . $recordId);

            // Save vital signs if provided
            if (!empty($data['vital_signs'])) {
                error_log("Saving vital signs: " . print_r($data['vital_signs'], true));
                $this->saveVitalSigns($recordId, $data['vital_signs']);
            }

            $this->pdo->commit();
            return $recordId;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error al guardar registro de historial: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Obtiene las citas activas de un paciente para asociarlas al historial
     * @param int $patientId ID del paciente
     * @return array Lista de citas activas del paciente
     */
    public function getPatientAppointments($patientId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT a.*, d.names as doctor_names, d.last_name as doctor_last_name, d.last_name2 as doctor_last_name2,
                DATE_FORMAT(a.appointment_date, '%H:%i:%s') as appointment_time
                FROM appointments a
                JOIN doctors d ON a.id_doctor = d.id
                WHERE a.id_patient = :patient_id AND a.status = 'A'
                ORDER BY a.appointment_date DESC
            ");
            $stmt->execute([':patient_id' => $patientId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener citas del paciente: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtiene todas las citas de un paciente (activas, finalizadas, etc.)
     * @param int $patientId ID del paciente
     * @return array Lista de todas las citas del paciente
     */
    public function getAllPatientAppointments($patientId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT a.*, d.names as doctor_names, d.last_name as doctor_last_name, d.last_name2 as doctor_last_name2,
                DATE_FORMAT(a.appointment_date, '%H:%i:%s') as appointment_time
                FROM appointments a
                JOIN doctors d ON a.id_doctor = d.id
                WHERE a.id_patient = :patient_id
                ORDER BY a.appointment_date DESC
            ");
            $stmt->execute([':patient_id' => $patientId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener todas las citas del paciente: " . $e->getMessage());
            throw $e;
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
            // Check if the medical_attachments table exists
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as table_exists 
                FROM information_schema.tables 
                WHERE table_schema = DATABASE() 
                AND table_name = 'medical_attachments'
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['table_exists'] == 0) {
                error_log("La tabla medical_attachments no existe, intentando crearla");

                // Try to create the table
                $createTableSQL = "
                CREATE TABLE medical_attachments (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    medical_history_id INT NOT NULL,
                    file_name VARCHAR(255) NOT NULL,
                    file_path VARCHAR(255) NOT NULL,
                    file_type VARCHAR(50),
                    file_size INT,
                    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (medical_history_id) REFERENCES medical_history(id)
                )";

                $this->pdo->exec($createTableSQL);
                error_log("Tabla medical_attachments creada exitosamente");
            }

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
            error_log("Recomendación: Ejecute el script create-medical-history-tables.php para crear las tablas necesarias");
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
            // Check if the medical_history table exists
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as table_exists 
                FROM information_schema.tables 
                WHERE table_schema = DATABASE() 
                AND table_name = 'medical_history'
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['table_exists'] == 0) {
                error_log("La tabla medical_history no existe, intentando crearla");

                // Try to create the table
                $createTableSQL = "
                CREATE TABLE IF NOT EXISTS `medical_history` (
                  `id` int NOT NULL AUTO_INCREMENT,
                  `patient_id` int NOT NULL,
                  `appointment_id` int DEFAULT NULL,
                  `record_date` date NOT NULL,
                  `diagnosis` varchar(255) NOT NULL,
                  `observations` text NOT NULL,
                  `treatment` text NOT NULL,
                  `created_at` datetime NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `patient_id` (`patient_id`),
                  KEY `appointment_id` (`appointment_id`),
                  CONSTRAINT `medical_history_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
                  CONSTRAINT `medical_history_ibfk_3` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL
                )";

                $this->pdo->exec($createTableSQL);
                error_log("Tabla medical_history creada exitosamente");
            }

            // First, create a medical history record if it doesn't exist
            $medicalHistoryId = null;

            if (!empty($data['medical_history_id'])) {
                $medicalHistoryId = $data['medical_history_id'];
            } else {
                // Create a basic medical history record
                $historyData = [
                    'patient_id' => $data['patient_id'],
                    'diagnosis' => $data['title'],
                    'observations' => $data['description'] ?? 'Documento PDF subido',
                    'treatment' => 'Ver documento adjunto',
                    'date_created' => date('Y-m-d')
                ];

                // Add appointment_id if provided
                if (!empty($data['appointment_id'])) {
                    $historyData['appointment_id'] = $data['appointment_id'];
                }

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
                try {
                    if (!mkdir($filePath, 0777, true)) {
                        error_log("Failed to create directory: " . $filePath);
                        throw new Exception('No se pudo crear el directorio para guardar archivos');
                    }
                } catch (Exception $e) {
                    error_log("Exception creating directory: " . $e->getMessage());
                    // Try to create a directory in a different location as fallback
                    $filePath = $_SERVER['DOCUMENT_ROOT'] . '/assets/uploads/';
                    if (!file_exists($filePath)) {
                        if (!mkdir($filePath, 0777, true)) {
                            error_log("Failed to create fallback directory: " . $filePath);
                            throw new Exception('No se pudo crear el directorio para guardar archivos');
                        }
                    }
                }
            }

            // Check if directory is writable
            if (!is_writable($filePath)) {
                error_log("Directory is not writable: " . $filePath);
                chmod($filePath, 0777); // Try to make it writable
                if (!is_writable($filePath)) {
                    throw new Exception('El directorio para guardar archivos no tiene permisos de escritura');
                }
            }

            $fullPath = $filePath . $fileName;
            if (file_put_contents($fullPath, $fileContent) === false) {
                error_log("Failed to write file: " . $fullPath);
                throw new Exception('No se pudo guardar el archivo en el servidor');
            }

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
            // First check if the medical_attachments table exists
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as table_exists 
                FROM information_schema.tables 
                WHERE table_schema = DATABASE() 
                AND table_name = 'medical_attachments'
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['table_exists'] == 0) {
                error_log("La tabla medical_attachments no existe en la base de datos");
                throw new PDOException("La tabla medical_attachments no existe en la base de datos");
            }

            // Check the structure of the medical_history table
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as table_exists 
                FROM information_schema.tables 
                WHERE table_schema = DATABASE() 
                AND table_name = 'medical_history'
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['table_exists'] == 0) {
                error_log("La tabla medical_history no existe en la base de datos");
                throw new PDOException("La tabla medical_history no existe en la base de datos");
            }

            // Check if medical_history has a status column
            $stmt = $this->pdo->prepare("DESCRIBE medical_history");
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // Determine if we're using the new or old schema
            $hasStatusColumn = in_array('status', $columns);

            if ($hasStatusColumn) {
                $stmt = $this->pdo->prepare("
                    SELECT ma.id, ma.medical_history_id, ma.file_name, ma.file_type, ma.file_size, ma.uploaded_at,
                           mh.patient_id, mh.appointment_id, mh.diagnosis as title, mh.observations as description,
                           d.names as doctor_names, d.last_name as doctor_last_name, d.last_name2 as doctor_last_name2
                    FROM medical_attachments ma
                    JOIN medical_history mh ON ma.medical_history_id = mh.id
                    LEFT JOIN appointments a ON mh.appointment_id = a.id
                    LEFT JOIN doctors d ON a.id_doctor = d.id
                    WHERE mh.patient_id = :patient_id AND mh.status != 'deleted'
                    ORDER BY ma.uploaded_at DESC
                ");
            } else {
                $stmt = $this->pdo->prepare("
                    SELECT ma.id, ma.medical_history_id, ma.file_name, ma.file_type, ma.file_size, ma.uploaded_at,
                           mh.patient_id, mh.appointment_id, mh.diagnosis as title, mh.observations as description,
                           d.names as doctor_names, d.last_name as doctor_last_name, d.last_name2 as doctor_last_name2
                    FROM medical_attachments ma
                    JOIN medical_history mh ON ma.medical_history_id = mh.id
                    LEFT JOIN appointments a ON mh.appointment_id = a.id
                    LEFT JOIN doctors d ON a.id_doctor = d.id
                    WHERE mh.patient_id = :patient_id
                    ORDER BY ma.uploaded_at DESC
                ");
            }

            $stmt->execute([':patient_id' => $patientId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener documentos del paciente: " . $e->getMessage());
            throw $e;
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
                SELECT ma.*, mh.patient_id, mh.appointment_id, mh.diagnosis as title, mh.observations as description
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

    /**
     * Guarda una prescripción médica
     * @param int $appointmentId ID de la cita
     * @param array $prescriptionData Datos de la prescripción
     * @return int|false ID de la nueva prescripción o false en caso de error
     */
    public function savePrescription($appointmentId, $prescriptionData) {
        try {
            // Verificar si la tabla prescription_medications existe
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as table_exists 
                FROM information_schema.tables 
                WHERE table_schema = DATABASE() 
                AND table_name = 'prescription_medications'
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['table_exists'] == 0) {
                error_log("La tabla prescription_medications no existe en la base de datos");
                throw new PDOException("La tabla prescription_medications no existe en la base de datos");
            }

            // Insertar la prescripción
            $stmt = $this->pdo->prepare("
                INSERT INTO prescription_medications (
                    id_medicine, id_appointment, dose, frequency, duration
                ) VALUES (
                    :id_medicine, :id_appointment, :dose, :frequency, :duration
                )
            ");

            $stmt->execute([
                ':id_medicine' => $prescriptionData['medication_id'],
                ':id_appointment' => $appointmentId,
                ':dose' => $prescriptionData['dose'],
                ':frequency' => $prescriptionData['frequency'],
                ':duration' => $prescriptionData['duration']
            ]);

            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error al guardar prescripción: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene las prescripciones médicas para un registro de historial médico
     * @param int $medicalHistoryId ID del registro de historial médico
     * @return array Prescripciones médicas
     */
    public function getPrescriptionsForMedicalHistory($medicalHistoryId) {
        try {
            // Verificar si la tabla prescription_medications existe
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as table_exists 
                FROM information_schema.tables 
                WHERE table_schema = DATABASE() 
                AND table_name = 'prescription_medications'
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['table_exists'] == 0) {
                error_log("La tabla prescription_medications no existe en la base de datos");
                return [];
            }

            // Obtener el ID de la cita asociada al registro de historial médico
            $stmt = $this->pdo->prepare("
                SELECT appointment_id FROM medical_history WHERE id = :medical_history_id
            ");
            $stmt->execute([':medical_history_id' => $medicalHistoryId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result || !$result['appointment_id']) {
                return [];
            }

            $appointmentId = $result['appointment_id'];

            // Obtener las prescripciones para la cita
            $stmt = $this->pdo->prepare("
                SELECT pm.*, m.name as medication_name, mt.name as medication_type_name
                FROM prescription_medications pm
                JOIN medications m ON pm.id_medicine = m.id
                JOIN medications_types mt ON m.id_medicine_type = mt.id
                WHERE pm.id_appointment = :appointment_id
            ");
            $stmt->execute([':appointment_id' => $appointmentId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener prescripciones: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene las prescripciones médicas para un paciente
     * @param int $patientId ID del paciente
     * @return array Prescripciones médicas agrupadas por registro de historial médico
     */
    public function getPatientPrescriptions($patientId) {
        try {
            // Verificar si la tabla prescription_medications existe
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as table_exists 
                FROM information_schema.tables 
                WHERE table_schema = DATABASE() 
                AND table_name = 'prescription_medications'
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['table_exists'] == 0) {
                error_log("La tabla prescription_medications no existe en la base de datos");
                return [];
            }

            // Obtener todas las prescripciones para el paciente
            $stmt = $this->pdo->prepare("
                SELECT pm.*, m.name as medication_name, mt.name as medication_type_name,
                       mh.id as medical_history_id, mh.diagnosis, mh.record_date
                FROM prescription_medications pm
                JOIN medications m ON pm.id_medicine = m.id
                JOIN medications_types mt ON m.id_medicine_type = mt.id
                JOIN appointments a ON pm.id_appointment = a.id
                JOIN medical_history mh ON a.id = mh.appointment_id
                WHERE mh.patient_id = :patient_id
                ORDER BY mh.record_date DESC, mt.name, m.name
            ");
            $stmt->execute([':patient_id' => $patientId]);

            $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Agrupar prescripciones por registro de historial médico
            $result = [];
            foreach ($prescriptions as $prescription) {
                $medicalHistoryId = $prescription['medical_history_id'];
                if (!isset($result[$medicalHistoryId])) {
                    $result[$medicalHistoryId] = [
                        'medical_history_id' => $medicalHistoryId,
                        'diagnosis' => $prescription['diagnosis'],
                        'record_date' => $prescription['record_date'],
                        'prescriptions' => []
                    ];
                }

                unset($prescription['medical_history_id']);
                unset($prescription['diagnosis']);
                unset($prescription['record_date']);

                $result[$medicalHistoryId]['prescriptions'][] = $prescription;
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Error al obtener prescripciones del paciente: " . $e->getMessage());
            return [];
        }
    }
}
