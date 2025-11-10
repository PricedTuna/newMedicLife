<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/machines/machines.model.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: /views/machines/list/list-machines.view.php?success=0&message=" . urlencode("ID inválido."));
    exit;
}

$id = (int) $_GET['id'];
$model = new MachineModel($pdo);

// Verificar si existen máquinas asociadas a este tipo
$stmt = $pdo->prepare("SELECT COUNT(*) FROM machines WHERE id_machine_type = ?");
$stmt->execute([$id]);
$count = $stmt->fetchColumn();

if ($count > 0) {
    header("Location: /views/machines/list/list-machines.view.php?success=0&message=" . urlencode("No se puede eliminar este tipo porque existen máquinas asociadas."));
    exit;
}

// Intentar eliminar el tipo
$result = $model->deleteMachineType($id);

header("Location: /views/machines/list/list-machines.view.php?success="
    . ($result['success'] ? '1' : '0')
    . "&message=" . urlencode($result['message']));
exit;
