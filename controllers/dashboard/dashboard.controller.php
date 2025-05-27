<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/config/database.config.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/models/dashboard/dashboard.model.php";

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $id_cita = $_POST['id_cita'] ?? '';
    $action = $_POST['action'] ?? '';

    $dashboardModel = new Dashboard($pdo);

    try {
        if (trim($action) === 'update') {
        $result = $dashboardModel->updateAppointment($id_cita);
        header("Location: /views/dashboard/dashboard.view.php?success=" . urlencode("Cita Terminada con éxito"));
        }
        if (trim($action) === 'cancel') {
        $result = $dashboardModel->updateAppointment($id_cita);
        header("Location: /views/dashboard/dashboard.view.php?success=" . urlencode("Cita Terminada con éxito"));
        }
    } catch (PDOException $e) {
        die ("ERROR" . $e->getMessage());
    }
    
}