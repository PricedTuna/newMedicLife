<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/patient/patient.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/medical_history/medical-history.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/utils.php';

// Inicializar conexión a la base de datos
$pdo = getConnection();

// Verificar que el usuario tenga permisos (debe ser doctor o administrador)
session_start();
if (!isset($_SESSION['usuario']) || ($_SESSION['role'] !== 'D' && $_SESSION['role'] !== 'A')) {
    header('Content-Type: text/html');
    echo 'No tiene permisos para realizar esta acción';
    exit;
}

// Verificar que se haya enviado el ID del paciente
if (!isset($_GET['patient_id']) || !is_numeric($_GET['patient_id'])) {
    header('Content-Type: text/html');
    echo 'Debe proporcionar un ID de paciente válido';
    exit;
}

try {
    $patientId = (int)$_GET['patient_id'];

    // Instanciar los modelos
    $patientModel = new PatientModel($pdo);
    $medicalHistoryModel = new MedicalHistoryModel($pdo);

    // Obtener datos del paciente
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = :id AND status != 'I'");
    $stmt->execute([':id' => $patientId]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        header('Content-Type: text/html');
        echo 'Paciente no encontrado o inactivo';
        exit;
    }

    // Obtener historial médico del paciente
    $history = $medicalHistoryModel->getPatientHistory($patientId);

    // Generar HTML para el PDF
    $html = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Historial Médico - ' . htmlspecialchars($patient['names'] . ' ' . $patient['last_name'] . ' ' . $patient['last_name2']) . '</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
                font-size: 12px;
            }
            h1 {
                color: #333;
                font-size: 18px;
                text-align: center;
                margin-bottom: 20px;
            }
            h2 {
                color: #444;
                font-size: 16px;
                margin-top: 20px;
                margin-bottom: 10px;
            }
            .patient-info {
                margin-bottom: 20px;
                padding: 10px;
                background-color: #f5f5f5;
                border-radius: 5px;
            }
            .patient-info p {
                margin: 5px 0;
            }
            .record {
                margin-bottom: 15px;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 5px;
            }
            .record-header {
                display: flex;
                justify-content: space-between;
                margin-bottom: 10px;
                border-bottom: 1px solid #eee;
                padding-bottom: 5px;
            }
            .record-title {
                font-weight: bold;
            }
            .record-date {
                color: #666;
            }
            .record-content p {
                margin: 5px 0;
            }
            .footer {
                margin-top: 30px;
                text-align: center;
                font-size: 10px;
                color: #666;
            }
            .logo {
                text-align: center;
                margin-bottom: 20px;
            }
            .logo img {
                max-width: 150px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            table, th, td {
                border: 1px solid #ddd;
            }
            th, td {
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;
            }
        </style>
    </head>
    <body>
        <div class="logo">
            <img src="' . $_SERVER['DOCUMENT_ROOT'] . '/views/dashboard/icons/medicLifeLogo.svg" alt="Medic Life Logo">
        </div>

        <h1>Historial Médico</h1>

        <div class="patient-info">
            <h2>Información del Paciente</h2>
            <p><strong>Nombre:</strong> ' . htmlspecialchars($patient['names'] . ' ' . $patient['last_name'] . ' ' . $patient['last_name2']) . '</p>
            <p><strong>CURP:</strong> ' . htmlspecialchars($patient['curp']) . '</p>
            <p><strong>Fecha de Nacimiento:</strong> ' . htmlspecialchars($patient['birth_date']) . '</p>
            <p><strong>Género:</strong> ' . htmlspecialchars($patient['gender'] == 'M' ? 'Masculino' : 'Femenino') . '</p>
            <p><strong>Tipo de Sangre:</strong> ' . htmlspecialchars($patient['blood_type']) . '</p>
            <p><strong>Peso:</strong> ' . htmlspecialchars($patient['weight']) . ' kg</p>
            <p><strong>Altura:</strong> ' . htmlspecialchars($patient['height']) . ' cm</p>
        </div>

        <h2>Registros Médicos</h2>';

    if (empty($history)) {
        $html .= '<p>No hay registros médicos para este paciente.</p>';
    } else {
        foreach ($history as $record) {
            $html .= '
            <div class="record">
                <div class="record-header">
                    <div class="record-title">' . htmlspecialchars($record['diagnosis']) . '</div>
                    <div class="record-date">' . htmlspecialchars($record['record_date']) . '</div>
                </div>
                <div class="record-content">
                    <p><strong>Doctor:</strong> ' . htmlspecialchars($record['doctor_names'] . ' ' . $record['doctor_last_name'] . ' ' . $record['doctor_last_name2']) . '</p>
                    <p><strong>Observaciones:</strong> ' . nl2br(htmlspecialchars($record['observations'])) . '</p>
                    <p><strong>Tratamiento:</strong> ' . nl2br(htmlspecialchars($record['treatment'])) . '</p>
                </div>
            </div>';
        }
    }

    $html .= '
        <div class="footer">
            <p>Este documento es un historial médico generado por Medic Life. Fecha de generación: ' . date('Y-m-d H:i:s') . '</p>
        </div>
    </body>
    </html>';

    // Configurar cabeceras para descargar como PDF
    $filename = 'Historial_Medico_' . $patient['curp'] . '_' . date('Y-m-d') . '.pdf';

    // Nota: En un entorno de producción, se debería usar una biblioteca como TCPDF o MPDF
    // para generar un PDF real. Aquí estamos simplemente devolviendo HTML con cabeceras
    // que sugieren que es un PDF para fines de demostración.

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // En un entorno real, aquí convertiríamos el HTML a PDF
    // Por ahora, simplemente mostramos el HTML
    echo $html;

} catch (Exception $e) {
    // Registrar el error
    error_log('Error al generar PDF del historial: ' . $e->getMessage());

    // Mostrar mensaje de error
    header('Content-Type: text/html');
    echo 'Error al generar el PDF del historial: ' . $e->getMessage();
}
