<?php
// email.controller.php
require_once $_SERVER["DOCUMENT_ROOT"] . "/models/email/email.model.php";

class EmailController
{
    private $emailModel;

    public function __construct()
    {
        $this->emailModel = new EmailModel();
    }

    /**
     * Función para procesar el envío de correo
     * Recibe los datos (to, subject, text, from) y usa el modelo
     */
    public function sendEmail($to, $subject, $text, $from = null)
    {
        // Limpiar y validar el correo destinatario
        $to = trim($to);
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return "Error: El correo '$to' no es válido.";
        }

        // También limpiar otros campos (opcional pero recomendable)
        $subject = trim($subject);
        $text = trim($text);
        if ($from !== null) {
            $from = trim($from);
        }

        // Debug opcional
        // var_dump(['from' => $from, 'to' => $to, 'subject' => $subject, 'text' => $text]);

        // Llamar al modelo para enviar el correo
        $result = $this->emailModel->sendEmail($to, $subject, $text, $from);

        if ($result === true) {
            return "Correo enviado correctamente a $to";
        } else {
            return $result; // Mensaje de error del modelo
        }
    }
}

// Ejemplo de uso desde POST
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $to = $_POST['to'] ?? '';
//     $subject = $_POST['subject'] ?? '';
//     $text = $_POST['text'] ?? '';
//     $from = $_POST['from'] ?? null;

//     $controller = new EmailController();
//     $response = $controller->sendEmail($to, $subject, $text, $from);

//     echo $response;
// }
