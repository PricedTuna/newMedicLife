<?php
// controllers/doctor/medical_history/schedule-study.controller.php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/studies/patient-studies.model.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$patientId = $_POST['patient_id'] ?? null;
$studyId = $_POST['study_id'] ?? null;
$scheduledAt = $_POST['scheduled_at'] ?? null; // Expected format: 'YYYY-MM-DD HH:MM:SS'
$clientTzOffset = isset($_POST['client_tz_offset']) ? intval($_POST['client_tz_offset']) : null; // minutes

$errors = [];
if (!$patientId || !is_numeric($patientId)) $errors[] = 'ID de paciente inválido';
if (!$studyId || !is_numeric($studyId)) $errors[] = 'ID de estudio inválido';
if (!$scheduledAt) $errors[] = 'Fecha y hora inválida';

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode('; ', $errors)]);
    exit;
}

// Parse and validate the scheduled datetime
// scheduledAt is client-local time 'YYYY-MM-DD HH:MM:SS'
// clientTzOffset is getTimezoneOffset() in minutes (positive if behind UTC, negative if ahead)
// Strategy: use offset to calculate when now is in client's timezone, then compare

if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $scheduledAt)) {
    echo json_encode(['success' => false, 'message' => 'Formato de fecha/hora inválido. Esperado: YYYY-MM-DD HH:MM:SS']);
    exit;
}

// Parse scheduled time as if it were a UTC instant (just to get the components), then treat as client-local
$scheduled = DateTime::createFromFormat('Y-m-d H:i:s', $scheduledAt, new DateTimeZone('UTC'));
if (!$scheduled) {
    echo json_encode(['success' => false, 'message' => 'Formato de fecha/hora inválido']);
    exit;
}

// Get current time in UTC
$nowUtc = new DateTime('now', new DateTimeZone('UTC'));

// If we have client offset, calculate what "now" is in the client's timezone
// Client's local time = UTC + (offset in minutes)
// But getTimezoneOffset() returns minutes FROM UTC, so:
// Client local = UTC - offset (in signed minutes)
if ($clientTzOffset !== null) {
    // Create a copy of now in client's timezone by subtracting the offset
    $nowClient = clone $nowUtc;
    $offsetSeconds = $clientTzOffset * 60; // Convert to seconds (but will be negative or positive)
    if ($clientTzOffset !== 0) {
        // Offset is minutes to ADD to UTC to get local, so we subtract it
        $sign = ($clientTzOffset > 0) ? '-' : '+';
        $abs = abs($clientTzOffset);
        $nowClient->modify("{$sign}{$abs} minutes");
    }
} else {
    $nowClient = clone $nowUtc;
}

// Minimum allowed time: now + 10 minutes in client's timezone
$minClient = clone $nowClient;
$minClient->modify('+10 minutes');

// Compare: scheduled (as client-local) must be >= minClient (as client-local)
if ($scheduled < $minClient) {
    error_log('[schedule-study] rejected time too soon: scheduled=' . $scheduled->format('Y-m-d H:i:s') . ' min_client=' . $minClient->format('Y-m-d H:i:s') . ' offset=' . $clientTzOffset);
    echo json_encode(['success' => false, 'message' => 'La fecha y hora deben ser al menos 10 minutos en el futuro']);
    exit;
}

// Store the exact client-local string as-is
$scheduledNormalized = $scheduledAt;

try {
    $pdo = getConnection();
    if ($pdo === null) {
        echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
        exit;
    }

    $model = new PatientStudiesModel($pdo);

    // Verificar conflictos: mismo study_type en la misma fecha/hora o mismo paciente en la misma fecha/hora
    if ($model->existsConflict($scheduledNormalized, (int)$patientId, (int)$studyId)) {
        echo json_encode(['success' => false, 'message' => 'No se puede agendar: ya existe un estudio en esa fecha/hora para el mismo paciente o mismo tipo de estudio.']);
        exit;
    }

    // Insertar
    $insertedId = $model->createStudyAppointment((int)$patientId, (int)$studyId, $scheduledNormalized);
    error_log('[schedule-study] patient_id=' . $patientId . ' study_id=' . $studyId . ' scheduled_at=' . $scheduledNormalized . ' insertedId=' . json_encode($insertedId));
    if ($insertedId === false) {
        echo json_encode(['success' => false, 'message' => 'No se pudo crear el agendamiento.']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Estudio agendado correctamente.', 'id' => $insertedId]);
    exit;

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    exit;
}
