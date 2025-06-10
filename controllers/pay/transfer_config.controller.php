<?php
/**
 * Transfer Configuration Controller
 * This controller handles bank transfer configuration operations
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/pay/transfer_config.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';

// Only administrators can access transfer configuration functionality
if (!isAdmin(false)) {
    header('Location: /views/dashboard/dashboard.view.php?error=' . urlencode("Acceso denegado. Solo los administradores pueden configurar los datos de transferencia bancaria."));
    exit;
}

// Initialize the transfer configuration model
$transferConfigModel = new TransferConfigModel($GLOBALS['pdo']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_transfer_config') {
    try {
        // Validate form data
        $requiredFields = ['bank_name', 'account_holder', 'account_number', 'clabe'];
        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
                throw new Exception("El campo " . str_replace('_', ' ', $field) . " es obligatorio.");
            }
        }

        // Prepare data for saving
        $data = [
            'bank_name' => $_POST['bank_name'],
            'account_holder' => $_POST['account_holder'],
            'account_number' => $_POST['account_number'],
            'clabe' => $_POST['clabe'],
            'additional_info' => $_POST['additional_info'] ?? ''
        ];

        // Get current user ID
        $userId = $_SESSION['id_user'] ?? 1;

        // Save the configuration
        $result = $transferConfigModel->saveConfig($data, $userId);

        if ($result) {
            // Redirect with success message
            header('Location: /views/settings/settings.view.php?success=' . urlencode("Configuración de transferencia bancaria guardada con éxito."));
            exit;
        } else {
            throw new Exception("Error al guardar la configuración de transferencia bancaria.");
        }
    } catch (Exception $e) {
        // Redirect with error message
        header('Location: /views/settings/settings.view.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}

// Get current configuration
$transferConfig = $transferConfigModel->getConfig();

// Return JSON if requested
if (isset($_GET['format']) && $_GET['format'] === 'json') {
    header('Content-Type: application/json');
    echo json_encode($transferConfig);
    exit;
}

// Otherwise, return the configuration data
return $transferConfig;
?>