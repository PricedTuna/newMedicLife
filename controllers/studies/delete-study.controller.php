<?php
// controllers/studies/delete-study.controller.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// -------------------------------
// Protección de ruta
// -------------------------------
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';
checkUserRole(['A', 'T']); // Solo administradores y técnicos

// -------------------------------
// Configuración y modelo
// -------------------------------
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/studies/studies.model.php';

$pdo = getConnection();
$model = new StudyModel($pdo);

// -------------------------------
// Validar ID del estudio
// -------------------------------
if (!isset($_POST['id']) && !isset($_GET['id'])) {
    header('Location: /views/studies/list/list-studies.view.php?error=' . urlencode('ID del estudio no proporcionado.'));
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : (int) $_GET['id'];

// -------------------------------
// Eliminar estudio
// -------------------------------
$result = $model->deleteStudy($id);

if ($result['success']) {
    header('Location: /views/studies/list/list-studies.view.php?success=' . urlencode('Estudio eliminado correctamente.'));
} else {
    header('Location: /views/studies/list/list-studies.view.php?error=' . urlencode('No se pudo eliminar el estudio: ' . $result['message']));
}
exit;
