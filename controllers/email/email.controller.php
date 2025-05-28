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
        $result = $this->emailModel->sendEmail($to, $subject, $text, $from);

        if ($result === true) {
            return "Correo enviado correctamente a $to";
        } else {
            // Aquí $result tiene el mensaje de error retornado por el modelo
            return $result;
        }
    }
}

// Ejemplo de uso (podrías hacerlo desde un script o en respuesta a un formulario)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = $_POST['to'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $text = $_POST['text'] ?? '';
    $from = $_POST['from'] ?? null;

    $controller = new EmailController();
    $response = $controller->sendEmail($to, $subject, $text, $from);

    echo $response;
}
