<?php
// controllers/machines/delete-machine.controller.php

// =============================
// 🧩 Protección de ruta
// =============================
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';
checkUserRole(['A']); // Solo administradores

// =============================
// ⚙️ Configuración general
// =============================
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/machines/machines.model.php';

// =============================
// 🗄️ Inicializar conexión y modelo
// =============================
$pdo = getConnection();
$machineModel = new MachineModel($pdo);

// =============================
// 🔎 Validar ID recibido
// =============================
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: /views/machines/list/list-machines.view.php?error=ID de máquina no especificado');
    exit;
}

$machineId = (int) $_GET['id'];

// =============================
// ❌ Eliminar máquina
// =============================
$result = $machineModel->deleteMachine($machineId);

// =============================
// 🔄 Redirigir con resultado
// =============================
if ($result['success']) {
    header('Location: /views/machines/list/list-machines.view.php?success=' . urlencode($result['message']));
} else {
    header('Location: /views/machines/list/list-machines.view.php?error=' . urlencode($result['message']));
}
exit;
