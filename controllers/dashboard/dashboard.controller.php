<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/config/database.config.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/models/dashboard/dashboard.model.php";

/**
 * Controlador para el dashboard
 * Maneja tanto solicitudes GET como POST
 */
class DashboardController {
    private $dashboardModel;

    public function __construct($pdo) {
        $this->dashboardModel = new Dashboard($pdo);
    }

    /**
     * Obtiene los datos necesarios para mostrar en el dashboard
     * @return array Datos para la vista del dashboard
     */
    public function getDashboardData() {
        $data = [];

        // Obtener doctores con áreas médicas
        $data['doctors'] = $this->dashboardModel->getDoctorsWithMedicalAreas();

        // Obtener citas
        $data['appointments'] = $this->dashboardModel->getAllAppointments();

        return $data;
    }

    /**
     * Procesa acciones sobre citas (actualizar o cancelar)
     * @param string $action Acción a realizar (update o cancel)
     * @param int $id_cita ID de la cita
     * @return bool Resultado de la operación
     */
    public function processAppointmentAction($action, $id_cita) {
        try {
            if (trim($action) === 'update') {
                return $this->dashboardModel->updateAppointment($id_cita);
            }
            if (trim($action) === 'cancel') {
                return $this->dashboardModel->cancelAppointment($id_cita);
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error al procesar acción de cita: " . $e->getMessage());
            return false;
        }
    }
}

// Manejo de solicitudes POST
if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $id_cita = $_POST['id_cita'] ?? '';
    $action = $_POST['action'] ?? '';

    $controller = new DashboardController($pdo);

    try {
        $result = $controller->processAppointmentAction($action, $id_cita);
        if ($result) {
            $message = ($action === 'cancel') ? "Cita cancelada con éxito" : "Cita terminada con éxito";
            header("Location: /views/dashboard/dashboard.view.php?success=" . urlencode($message));
        } else {
            header("Location: /views/dashboard/dashboard.view.php?error=" . urlencode("Error al procesar la cita"));
        }
    } catch (Exception $e) {
        header("Location: /views/dashboard/dashboard.view.php?error=" . urlencode($e->getMessage()));
    }
    exit();
}
