<?php
header('Content-Type: application/json');
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/studies/patient-studies.model.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$action = $_POST['action'] ?? null;
$id = $_POST['id'] ?? null;

if (!$action || !$id || !is_numeric($id)) {
    echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
    exit;
}

$pdo = getConnection();
if ($pdo === null) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit;
}

$model = new PatientStudiesModel($pdo);
try {
    // Debug: log incoming request for troubleshooting
    error_log('[update-patient-study] action=' . ($action ?? 'NULL') . ' id=' . ($id ?? 'NULL') . ' raw=' . json_encode($_POST));
    if ($action === 'cancel') {
        $ok = $model->updateStatus((int)$id, 'Cancelado');
        error_log('[update-patient-study] cancel result for id=' . $id . ' -> ' . ($ok ? 'OK' : 'FAIL'));
        if ($ok) echo json_encode(['success' => true, 'message' => 'Estudio cancelado']);
        else echo json_encode(['success' => false, 'message' => 'No se pudo cancelar el estudio']);
        exit;
    }

    if ($action === 'reschedule') {
        $scheduledAt = $_POST['scheduled_at'] ?? null; // 'YYYY-MM-DD HH:MM:SS'
        $clientTzOffset = isset($_POST['client_tz_offset']) ? intval($_POST['client_tz_offset']) : null;
        if (!$scheduledAt) {
            echo json_encode(['success' => false, 'message' => 'scheduled_at faltante']);
            exit;
        }
        // Parsear y validar scheduled_at
        // scheduledAt es hora local del cliente 'YYYY-MM-DD HH:MM:SS'
        // clientTzOffset es getTimezoneOffset() en minutos
        if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $scheduledAt)) {
            echo json_encode(['success' => false, 'message' => 'Formato de fecha/hora inválido']);
            exit;
        }

        // Parse as UTC-based DateTime (just to get a comparable object)
        $scheduled = DateTime::createFromFormat('Y-m-d H:i:s', $scheduledAt, new DateTimeZone('UTC'));
        if (!$scheduled) {
            echo json_encode(['success' => false, 'message' => 'Formato de fecha/hora inválido']);
            exit;
        }

        // Get current time in UTC
        $nowUtc = new DateTime('now', new DateTimeZone('UTC'));

        // Calculate "now" in client's timezone using offset
        if ($clientTzOffset !== null) {
            $nowClient = clone $nowUtc;
            if ($clientTzOffset !== 0) {
                $sign = ($clientTzOffset > 0) ? '-' : '+';
                $abs = abs($clientTzOffset);
                $nowClient->modify("{$sign}{$abs} minutes");
            }
        } else {
            $nowClient = clone $nowUtc;
        }

        // Minimum allowed: now + 10 minutes
        $minClient = clone $nowClient;
        $minClient->modify('+10 minutes');

        if ($scheduled < $minClient) {
            error_log('[update-patient-study] rejected time too soon: scheduled=' . $scheduled->format('Y-m-d H:i:s') . ' min=' . $minClient->format('Y-m-d H:i:s'));
            echo json_encode(['success' => false, 'message' => 'La fecha y hora deben ser al menos 10 minutos en el futuro']);
            exit;
        }

        // Store client-local time as-is
        $scheduledNormalized = $scheduledAt;

        // Antes de reprogramar, verificar si ya existe otro estudio del mismo tipo o del mismo paciente en esa fecha
        // Necesitamos conocer el study_type_id y patient_id del registro actual
        $stmt = $pdo->prepare('SELECT study_type_id, patient_id FROM patient_studies WHERE id = ?');
        $stmt->execute([(int)$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            echo json_encode(['success' => false, 'message' => 'Registro no encontrado']);
            exit;
        }
        $studyTypeId = $row['study_type_id'];
        $patientId = $row['patient_id'];

        // Verificar conflicto (excluir el propio registro)
        if ($model->existsConflict($scheduledNormalized, (int)$patientId, (int)$studyTypeId, (int)$id)) {
            echo json_encode(['success' => false, 'message' => 'No se puede reprogramar: ya existe un estudio en esa fecha/hora para el mismo paciente o mismo tipo de estudio.']);
            exit;
        }
        $ok = $model->updateScheduledAt((int)$id, $scheduledNormalized, 'Reprogramado');
        error_log('[update-patient-study] reschedule result for id=' . $id . ' -> ' . ($ok ? 'OK' : 'FAIL'));
        if ($ok) echo json_encode(['success' => true, 'message' => 'Estudio reprogramado']);
        else echo json_encode(['success' => false, 'message' => 'No se pudo reprogramar el estudio']);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Acción desconocida']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
