CREATE TABLE medical_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    date_created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    chief_complaint TEXT,
    current_illness TEXT,
    personal_history TEXT,
    family_history TEXT,
    physical_examination TEXT,
    vital_signs JSON,
    diagnosis TEXT,
    treatment_plan TEXT,
    observations TEXT,
    next_appointment DATE,
    doctor_id INT NOT NULL,
    status ENUM('active', 'archived', 'deleted') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (doctor_id) REFERENCES doctors(id)
);

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
);

CREATE TABLE medical_attachments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    medical_history_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(50),
    file_size INT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (medical_history_id) REFERENCES medical_history(id)
);