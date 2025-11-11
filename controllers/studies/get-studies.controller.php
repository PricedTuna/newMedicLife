<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/studies/studies.model.php';

header('Content-Type: application/json');

$pdo = getConnection();
if ($pdo === null) {
    echo json_encode(['success' => false, 'message' => 'No se pudo conectar a la base de datos']);
    exit;
}

try {
    $model = new StudyModel($pdo);
    $all = $model->getAllStudies();

    // Normalize to id/name pairs
    $out = [];
    if (is_array($all)) {
        foreach ($all as $s) {
            $out[] = [
                'id' => $s['id'] ?? $s['ID'] ?? null,
                'name' => $s['name'] ?? $s['nombre'] ?? ''
            ];
        }
    }

    echo json_encode(['success' => true, 'studies' => $out]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
