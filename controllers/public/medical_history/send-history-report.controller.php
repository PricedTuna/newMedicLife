<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/patient/patient.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/medical_history/medical-history.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/email/email.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/utils.php';

// Inicializar conexión a la base de datos
$pdo = getConnection();

// Verificar que se haya enviado el CURP (ya sea por POST o GET)
$curp = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['curp']) && !empty($_POST['curp'])) {
    $curp = trim($_POST['curp']);
} elseif (isset($_GET['curp']) && !empty($_GET['curp'])) {
    $curp = trim($_GET['curp']);
} else {
    // Redirigir a la página adecuada según de dónde vino la solicitud
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

    if (strpos($referer, 'view-history.view.php') !== false) {
        header('Location: /views/public/medical_history/view-history.view.php?error=' . urlencode('Debe proporcionar un CURP válido'));
    } else {
        header('Location: /views/public/medical_history/public-medical-history.view.php?error=' . urlencode('Debe proporcionar un CURP válido'));
    }
    exit;
}

try {
    // Instanciar los modelos
    $patientModel = new PatientModel($pdo);
    $medicalHistoryModel = new MedicalHistoryModel($pdo);

    // Buscar al paciente por CURP
    $patient = $patientModel->getPatientByCURP($curp);

    // Determinar la página de redirección basada en el referer
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    $redirectPage = (strpos($referer, 'view-history.view.php') !== false) 
        ? '/views/public/medical_history/view-history.view.php' 
        : '/views/public/medical_history/public-medical-history.view.php';

    if (!$patient) {
        header('Location: ' . $redirectPage . '?error=' . urlencode('No se encontró ningún paciente con el CURP proporcionado'));
        exit;
    }

    // Verificar que el paciente tenga un correo electrónico
    if (empty($patient['email'])) {
        header('Location: ' . $redirectPage . '?error=' . urlencode('El paciente no tiene un correo electrónico registrado'));
        exit;
    }

    // Obtener historial médico del paciente
    $history = $medicalHistoryModel->getPatientHistory($patient['id']);

    // Generar HTML para el reporte
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
            <p><strong>CURP:</strong> ' . htmlspecialchars($patient['CURP']) . '</p>
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

    // Enviar el reporte por correo electrónico
    $emailController = new EmailController();
    $to = $patient['email'];
    $subject = 'Su Historial Médico - Medic Life';
    $message = "Estimado/a " . $patient['names'] . ",\n\n";
    $message .= "Adjunto encontrará su historial médico solicitado a través de nuestro sistema.\n\n";
    $message .= "Si usted no solicitó este reporte, por favor ignore este correo.\n\n";
    $message .= "Atentamente,\nEquipo Medic Life";

    // En un entorno real, aquí convertiríamos el HTML a PDF y lo adjuntaríamos al correo
    // Por ahora, simplemente enviamos el HTML como texto
    $message .= "\n\n" . $html;

    $result = $emailController->sendEmail($to, $subject, $message);

    if (strpos($result, 'Error') === 0) {
        // Si hay un error al enviar el correo
        header('Location: ' . $redirectPage . '?error=' . urlencode('Error al enviar el reporte: ' . $result));
    } else {
        // Si el correo se envió correctamente
        header('Location: ' . $redirectPage . '?success=' . urlencode('El reporte de historial médico ha sido enviado a su correo electrónico'));
    }

} catch (Exception $e) {
    // Registrar el error
    error_log('Error al generar reporte de historial médico: ' . $e->getMessage());

    // Determinar la página de redirección basada en el referer si no se ha definido antes
    if (!isset($redirectPage)) {
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $redirectPage = (strpos($referer, 'view-history.view.php') !== false) 
            ? '/views/public/medical_history/view-history.view.php' 
            : '/views/public/medical_history/public-medical-history.view.php';
    }

    // Redirigir con mensaje de error
    header('Location: ' . $redirectPage . '?error=' . urlencode('Error al procesar la solicitud: ' . $e->getMessage()));
}
?>
