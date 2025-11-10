<?php
// controllers/machines/create-machine.controller.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Protege la ruta (solo Admin y Técnicos)
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/session.controller.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';
checkUserRole(['A', 'T']);

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/machines/machines.model.php';

// Inicializar conexión y modelo
$pdo = getConnection();
$model = new MachineModel($pdo);

// ---------------------------------------------------
// 1️⃣ Si se envía el formulario (POST)
// ---------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    // --- Crear o Editar tipo de máquina ---
    if ($action === 'create_type' || $action === 'edit_type') {
        $id_type = isset($_POST['id_type']) ? (int) $_POST['id_type'] : null;
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($name === '') {
            header('Location: /views/machines/create/create-machine.view.php?formType=type&error=' . urlencode('El nombre del tipo de máquina es obligatorio.'));
            exit;
        }

        if ($action === 'create_type') {
            $result = $model->createMachineType([
                'name' => $name,
                'description' => $description
            ]);
        } else { // edit_type
            $result = $model->updateMachineType($id_type, [
                'name' => $name,
                'description' => $description
            ]);
        }

        if (!$result['success']) {
            header('Location: /views/machines/create/create-machine.view.php?formType=type&error=' . urlencode($result['message']));
            exit;
        }

        // 🔹 Redirigir al listado de tipos con mensaje de éxito
        header('Location: /views/machines/list/list-machines.view.php?success=' . urlencode('Tipo de máquina guardado correctamente.'));
        exit;
    }

    // --- Eliminar tipo de máquina ---
    if ($action === 'delete_type' && isset($_POST['id_type'])) {
        $id_type = (int) $_POST['id_type'];
        $result = $model->deleteMachineType($id_type);

        if (!$result['success']) {
            header('Location: /views/machines/create/create-machine.view.php?formType=type&error=' . urlencode($result['message']));
            exit;
        }

        // 🔹 Redirigir al listado de tipos después de eliminar
        header('Location: /views/machines/list/list-machines.view.php?success=' . urlencode('Tipo de máquina eliminado correctamente.'));
        exit;
    }

    // --- Crear o Editar máquina ---
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'id_machine_type' => isset($_POST['id_machine_type']) && is_numeric($_POST['id_machine_type']) ? (int) $_POST['id_machine_type'] : null,
        'id_medical_area' => isset($_POST['id_medical_area']) && is_numeric($_POST['id_medical_area']) ? (int) $_POST['id_medical_area'] : null,
        'model' => trim($_POST['model'] ?? ''),
        'location' => trim($_POST['location'] ?? ''),
        'operational_status' => $_POST['operational_status'] ?? 'Operativa',
        'buffer_minutes' => isset($_POST['buffer_minutes']) ? (int) $_POST['buffer_minutes'] : 0,
        'assignment_rules' => trim($_POST['assignment_rules'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'operational_windows' => $_POST['operational_windows'] ?? []
    ];

    try {
        if ($action === 'edit' && isset($_POST['id'])) {
            $id = (int) $_POST['id'];
            $result = $model->updateMachine($id, $data);

            if (!$result['success']) {
                $error = implode('; ', $result['errors'] ?? [$result['message']]);
                header('Location: /views/machines/create/create-machine.view.php?id=' . $id . '&error=' . urlencode($error));
                exit;
            }

            header('Location: /views/machines/list/list-machines.view.php?success=' . urlencode('Máquina actualizada correctamente.'));
            exit;
        } else { 
            // Creación de nueva máquina
            $result = $model->createMachine($data);

            if (!$result['success']) {
                $error = implode('; ', $result['errors'] ?? [$result['message']]);
                header('Location: /views/machines/create/create-machine.view.php?error=' . urlencode($error));
                exit;
            }

            header('Location: /views/machines/list/list-machines.view.php?success=' . urlencode('Máquina creada con éxito.'));
            exit;
        }
    } catch (Exception $e) {
        $id = $_POST['id'] ?? '';
        header('Location: /views/machines/create/create-machine.view.php?id=' . $id . '&error=' . urlencode($e->getMessage()));
        exit;
    }
}

// ---------------------------------------------------
// 2️⃣ Si se accede por GET: preparar datos para la vista
// ---------------------------------------------------
function getCreateMachineViewData(MachineModel $model): array
{
    $machine_types = method_exists($model, 'getMachineTypes') ? $model->getMachineTypes() : [];
    $medical_areas = method_exists($model, 'getMedicalAreas') ? $model->getMedicalAreas() : [];

    $machineData = null;
    $operationalWindows = [];
    $typeData = null;

    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $machineId = (int) $_GET['id'];
        $machineData = $model->getMachineById($machineId);

        if ($machineData) {
            $operationalWindows = $model->getOperationalWindows($machineId);
        }
    }

    if (isset($_GET['id_type']) && !empty($_GET['id_type'])) {
        $typeId = (int) $_GET['id_type'];
        $typeData = $model->getMachineTypeById($typeId);
    }

    return [
        'machine_types' => $machine_types,
        'medical_areas' => $medical_areas,
        'machineData' => $machineData,
        'operational_windows' => $operationalWindows,
        'typeData' => $typeData
    ];
}

// Para cargar los datos en la vista
$viewData = getCreateMachineViewData($model);
