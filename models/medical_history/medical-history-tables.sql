-- Tabla para almacenar registros de historial médico
CREATE TABLE IF NOT EXISTS `medical_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `record_date` date NOT NULL,
  `diagnosis` varchar(255) NOT NULL,
  `observations` text NOT NULL,
  `treatment` text NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `appointment_id` (`appointment_id`),
  CONSTRAINT `medical_history_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  CONSTRAINT `medical_history_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`),
  CONSTRAINT `medical_history_ibfk_3` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla para almacenar documentos PDF
CREATE TABLE IF NOT EXISTS `medical_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `file_content` mediumblob NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `doctor_id` (`doctor_id`),
  CONSTRAINT `medical_documents_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  CONSTRAINT `medical_documents_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;