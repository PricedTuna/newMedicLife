<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/patient/patient.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/medical_history/medical-history.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/utils.php';

// Inicializar conexión a la base de datos
$pdo = getConnection();

// Verificar que se haya enviado el CURP
if (!isset($_GET['curp']) || empty($_GET['curp'])) {
    header('Location: /views/public/medical_history/view-history.view.php?error=' . urlencode('Debe proporcionar un CURP válido'));
    exit;
}

/**
 * Renderiza los signos vitales en formato HTML
 * @param array $vitalSigns Datos de los signos vitales
 * @return string HTML con los signos vitales
 */
function renderVitalSigns($vitalSigns) {
    if (empty($vitalSigns)) {
        return '';
    }

    $html = '<div class="vital-signs">
        <h3>Signos Vitales</h3>
        <table>
            <tr>
                <th>Parámetro</th>
                <th>Valor</th>
            </tr>';

    if (!empty($vitalSigns['temperature'])) {
        $html .= '<tr>
            <td>Temperatura</td>
            <td>' . htmlspecialchars($vitalSigns['temperature']) . ' °C</td>
        </tr>';
    }

    if (!empty($vitalSigns['blood_pressure'])) {
        $html .= '<tr>
            <td>Presión Arterial</td>
            <td>' . htmlspecialchars($vitalSigns['blood_pressure']) . ' mmHg</td>
        </tr>';
    }

    if (!empty($vitalSigns['heart_rate'])) {
        $html .= '<tr>
            <td>Frecuencia Cardíaca</td>
            <td>' . htmlspecialchars($vitalSigns['heart_rate']) . ' lpm</td>
        </tr>';
    }

    if (!empty($vitalSigns['respiratory_rate'])) {
        $html .= '<tr>
            <td>Frecuencia Respiratoria</td>
            <td>' . htmlspecialchars($vitalSigns['respiratory_rate']) . ' rpm</td>
        </tr>';
    }

    if (!empty($vitalSigns['weight'])) {
        $html .= '<tr>
            <td>Peso</td>
            <td>' . htmlspecialchars($vitalSigns['weight']) . ' kg</td>
        </tr>';
    }

    if (!empty($vitalSigns['height'])) {
        $html .= '<tr>
            <td>Altura</td>
            <td>' . htmlspecialchars($vitalSigns['height']) . ' cm</td>
        </tr>';
    }

    if (!empty($vitalSigns['bmi'])) {
        $html .= '<tr>
            <td>IMC</td>
            <td>' . htmlspecialchars($vitalSigns['bmi']) . ' kg/m²</td>
        </tr>';
    }

    if (!empty($vitalSigns['oxygen_saturation'])) {
        $html .= '<tr>
            <td>Saturación de Oxígeno</td>
            <td>' . htmlspecialchars($vitalSigns['oxygen_saturation']) . ' %</td>
        </tr>';
    }

    if (!empty($vitalSigns['glucose_level'])) {
        $html .= '<tr>
            <td>Nivel de Glucosa</td>
            <td>' . htmlspecialchars($vitalSigns['glucose_level']) . ' mg/dL</td>
        </tr>';
    }

    $html .= '</table>
    </div>';

    return $html;
}

/**
 * Renderiza las prescripciones médicas en formato HTML
 * @param array $prescriptions Datos de las prescripciones
 * @return string HTML con las prescripciones
 */
function renderPrescriptions($prescriptions) {
    if (empty($prescriptions)) {
        return '';
    }

    $html = '<div class="prescriptions">
        <h3>Prescripciones Médicas</h3>
        <table>
            <tr>
                <th>Tipo de Medicamento</th>
                <th>Medicamento</th>
                <th>Dosis</th>
                <th>Frecuencia</th>
                <th>Duración</th>
            </tr>';

    foreach ($prescriptions as $prescription) {
        $html .= '<tr>
            <td>' . htmlspecialchars($prescription['medication_type_name']) . '</td>
            <td>' . htmlspecialchars($prescription['medication_name']) . '</td>
            <td>' . htmlspecialchars($prescription['dose']) . '</td>
            <td>' . htmlspecialchars($prescription['frequency']) . '</td>
            <td>' . htmlspecialchars($prescription['duration']) . '</td>
        </tr>';
    }

    $html .= '</table>
    </div>';

    return $html;
}

try {
    $curp = trim($_GET['curp']);

    // Instanciar los modelos
    $patientModel = new PatientModel($pdo);
    $medicalHistoryModel = new MedicalHistoryModel($pdo);

    // Buscar al paciente por CURP
    $patient = $patientModel->getPatientByCURP($curp);

    if (!$patient) {
        header('Location: /views/public/medical_history/view-history.view.php?error=' . urlencode('No se encontró ningún paciente con el CURP proporcionado'));
        exit;
    }

    // Obtener historial médico del paciente
    $history = $medicalHistoryModel->getPatientHistory($patient['id']);

    // Obtener todas las citas del paciente
    $appointments = $medicalHistoryModel->getAllPatientAppointments($patient['id']);

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
            h3 {
                color: #555;
                font-size: 14px;
                margin-top: 15px;
                margin-bottom: 8px;
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
                margin-bottom: 15px;
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
            .vital-signs, .prescriptions {
                margin-bottom: 15px;
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
            // Obtener prescripciones para este registro
            $prescriptions = $medicalHistoryModel->getPrescriptionsForMedicalHistory($record['id']);

            $html .= '
            <div class="record">
                <div class="record-header">
                    <div class="record-title">' . htmlspecialchars($record['diagnosis']) . '</div>
                    <div class="record-date"><strong>Fecha:</strong> ' . htmlspecialchars($record['record_date']) . '</div>
                </div>
                <div class="record-content">
                    <p><strong>Doctor:</strong> ' . htmlspecialchars($record['doctor_names'] . ' ' . $record['doctor_last_name'] . ' ' . $record['doctor_last_name2']) . '</p>
                    ' . (!empty($record['chief_complaint']) ? '<p><strong>Motivo de Consulta:</strong> ' . nl2br(htmlspecialchars($record['chief_complaint'])) . '</p>' : '') . '
                    ' . (!empty($record['current_illness']) ? '<p><strong>Enfermedad Actual:</strong> ' . nl2br(htmlspecialchars($record['current_illness'])) . '</p>' : '') . '
                    ' . (!empty($record['personal_history']) ? '<p><strong>Antecedentes Personales:</strong> ' . nl2br(htmlspecialchars($record['personal_history'])) . '</p>' : '') . '
                    ' . (!empty($record['family_history']) ? '<p><strong>Antecedentes Familiares:</strong> ' . nl2br(htmlspecialchars($record['family_history'])) . '</p>' : '') . '
                    ' . (!empty($record['physical_examination']) ? '<p><strong>Examen Físico:</strong> ' . nl2br(htmlspecialchars($record['physical_examination'])) . '</p>' : '') . '
                    ' . (isset($record['vital_signs_data']) && !empty($record['vital_signs_data']) ? renderVitalSigns($record['vital_signs_data'][0]) : '') . '
                    ' . (!empty($record['diagnosis']) ? '<p><strong>Diagnóstico:</strong> ' . nl2br(htmlspecialchars($record['diagnosis'])) . '</p>' : '') . '
                    ' . (!empty($record['treatment_plan']) ? '<p><strong>Plan de Tratamiento:</strong> ' . nl2br(htmlspecialchars($record['treatment_plan'])) . '</p>' : '') . '
                    ' . (!empty($prescriptions) ? renderPrescriptions($prescriptions) : '') . '
                    ' . (!empty($record['observations']) ? '<p><strong>Observaciones:</strong> ' . nl2br(htmlspecialchars($record['observations'])) . '</p>' : '') . '
                    ' . (!empty($record['next_appointment']) ? '<p><strong>Próxima Cita:</strong> ' . htmlspecialchars($record['next_appointment']) . '</p>' : '') . '
                </div>
            </div>';
        }
    }

    // Agregar sección de citas
    $html .= '<h2>Historial de Citas</h2>';

    if (empty($appointments)) {
        $html .= '<p>No hay citas registradas para este paciente.</p>';
    } else {
        $html .= '
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Doctor</th>
                    <th>Estado</th>
                    <th>Notas</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($appointments as $appointment) {
            // Formatear el estado de la cita
            $status = '';
            if (isset($appointment['status'])) {
                switch ($appointment['status']) {
                    case 'A':
                        $status = 'Activo';
                        break;
                    case 'F':
                        $status = 'Finalizado';
                        break;
                    case 'T':
                        $status = 'Terminado';
                        break;
                    case 'C':
                        $status = 'Cancelado';
                        break;
                    case 'X':
                        $status = 'Cancelado';
                        break;
                    default:
                        $status = $appointment['status'];
                }
            }

            $html .= '
                <tr>
                    <td>' . (isset($appointment['appointment_date']) ? htmlspecialchars($appointment['appointment_date']) : 'N/A') . '</td>
                    <td>' . (isset($appointment['doctor_names']) ? htmlspecialchars($appointment['doctor_names'] . ' ' . $appointment['doctor_last_name'] . ' ' . $appointment['doctor_last_name2']) : 'N/A') . '</td>
                    <td>' . htmlspecialchars($status) . '</td>
                    <td>' . (isset($appointment['notes']) && !empty($appointment['notes']) ? htmlspecialchars($appointment['notes']) : '-') . '</td>
                </tr>';
        }

        $html .= '
            </tbody>
        </table>';
    }

    $html .= '
        <div class="footer">
            <p>Este documento es un historial médico generado por Medic Life. Fecha de generación: ' . date('Y-m-d H:i:s') . '</p>
        </div>
    </body>
    </html>';

    // Configurar cabeceras para descargar como PDF
    $filename = 'Historial_Medico_' . $patient['CURP'] . '_' . date('Y-m-d') . '.pdf';

    // Generar PDF con TCPDF
    // Verificar si TCPDF está instalado
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/vendor/tecnickcom/tcpdf/tcpdf.php')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/tecnickcom/tcpdf/tcpdf.php';

        // Crear instancia de TCPDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Configurar el PDF
        $pdf->SetCreator('Medic Life');
        $pdf->SetAuthor('Medic Life');
        $pdf->SetTitle('Historial Médico - ' . $patient['names'] . ' ' . $patient['last_name'] . ' ' . $patient['last_name2']);
        $pdf->SetSubject('Historial Médico');

        // Eliminar cabecera y pie de página predeterminados
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Establecer márgenes
        $pdf->SetMargins(15, 15, 15);

        // Establecer saltos de página automáticos
        $pdf->SetAutoPageBreak(true, 15);

        // Agregar una página
        $pdf->AddPage();

        // Escribir el HTML en el PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Generar el PDF y enviarlo al navegador
        $pdf->Output($filename, 'D');
    } else {
        // Si TCPDF no está instalado, mostrar mensaje y devolver HTML
        error_log("TCPDF no está instalado. Por favor, ejecute: composer require tecnickcom/tcpdf");

        header('Content-Type: text/html; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.html"');

        echo '<div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 5px;">
            <h3>Aviso Importante</h3>
            <p>Para generar PDFs, es necesario instalar la biblioteca TCPDF. Por favor, contacte al administrador del sistema.</p>
            <p>Comando para instalar TCPDF: <code>composer require tecnickcom/tcpdf</code></p>
        </div>';

        echo $html;
    }

} catch (Exception $e) {
    // Registrar el error
    error_log('Error al generar PDF del historial: ' . $e->getMessage());

    // Redirigir con mensaje de error
    header('Location: /views/public/medical_history/view-history.view.php?error=' . urlencode('Error al generar el PDF: ' . $e->getMessage()));
}
?>
