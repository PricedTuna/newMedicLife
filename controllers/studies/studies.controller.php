<?php
// controllers/studies/studies.controller.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Protección de ruta (solo Admin y Técnicos)
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';
checkUserRole(['A', 'T']);

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/studies/studies.model.php';

// Inicializar conexión y modelo
$pdo = getConnection();
$model = new StudyModel($pdo);

// =======================================================
// 1️⃣ Si se envía el formulario (POST)
// =======================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    $data = [
        'code' => trim($_POST['code'] ?? ''),
        'name' => trim($_POST['name'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'specialty' => trim($_POST['specialty'] ?? ''),
        'default_duration' => isset($_POST['default_duration']) ? (int) $_POST['default_duration'] : 0,
        'preparation' => trim($_POST['preparation'] ?? ''),
        'sla_days' => isset($_POST['sla_days']) ? (int) $_POST['sla_days'] : 0,
        'requires_technician' => isset($_POST['requires_technician']) ? 1 : 0,
        'requires_machine' => isset($_POST['requires_machine']) ? 1 : 0,
        'result_type' => $_POST['result_type'] ?? 'texto',
        'price' => isset($_POST['price']) ? (float) $_POST['price'] : 0.00,
        'active' => isset($_POST['active']) ? 1 : 0,
        'machine_id' => isset($_POST['machine_id']) ? (int) $_POST['machine_id'] : null
    ];

    // Validaciones básicas
    $errors = [];
    if ($data['code'] === '')
        $errors[] = "El código es obligatorio.";
    if ($data['name'] === '')
        $errors[] = "El nombre es obligatorio.";

    if ($errors) {
        $redirect = ($action === 'edit' && isset($_POST['id']))
            ? "/views/studies/list/list-studies.view.php?id=" . (int) $_POST['id'] . "&error=" . urlencode(implode('; ', $errors))
            : "/views/studies/create-study.view.php?error=" . urlencode(implode('; ', $errors));
        header("Location: $redirect");
        exit;
    }

    // --- Crear estudio ---
    if ($action === 'create') {
        $result = $model->createStudy($data);

        if ($result['success']) {
            header('Location: /views/studies/list/list-studies.view.php?success=' . urlencode('Estudio creado correctamente.'));
            exit;
        } else {
            header('Location: /views/studies/create-study.view.php?error=' . urlencode($result['message']));
            exit;
        }
    }

    // --- Editar estudio ---
    if ($action === 'edit' && isset($_POST['id'])) {
        $id = (int) $_POST['id'];
        $result = $model->updateStudyWithMachine($id, $data);

        if ($result['success']) {
            header('Location: /views/studies/list/list-studies.view.php?success=' . urlencode('Estudio actualizado correctamente.'));
            exit;
        } else {
            header('Location: /views/studies/list/list-studies.view.php?id=' . $id . '&error=' . urlencode($result['message']));
            exit;
        }
    }

    // --- Eliminar estudio ---
    if ($action === 'delete' && isset($_POST['id'])) {
        $id = (int) $_POST['id'];
        $result = $model->deleteStudy($id);

        if ($result['success']) {
            header('Location: /views/studies/list/list-studies.view.php?success=' . urlencode('Estudio eliminado correctamente.'));
            exit;
        } else {
            header('Location: /views/studies/list/list-studies.view.php?error=' . urlencode($result['message']));
            exit;
        }
    }
}

// =======================================================
// 2️⃣ Si se accede por GET: preparar datos para la vista
// =======================================================
// controllers/studies/studies.controller.php

function getStudyViewData(StudyModel $model): array
{
    $studyData = null;
    $machineId = null; // <--- nuevo

    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id = (int) $_GET['id'];
        $studyData = $model->getStudyById($id);

        // Obtener compatibilidad con máquina (si existe)
        $machinesCompatibility = $model->getMachineCompatibility($id);
        if (!empty($machinesCompatibility)) {
            // Solo tomamos la primera máquina si hay varias
            $machineId = $machinesCompatibility[0]['machine_id'];
            $studyData['machine_id'] = $machineId; // agregamos al array para el template
        }
    }

    $allStudies = $model->getAllStudies();

    return [
        'studyData' => $studyData,
        'allStudies' => $allStudies
    ];
}


// Cargar datos para la vista
$viewData = getStudyViewData($model);
