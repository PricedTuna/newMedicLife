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
     * Envía un correo simple (sin adjuntos)
     */
    public function sendEmail($to, $subject, $text, $from = null)
    {
        $to = trim($to);
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return "Error: El correo '$to' no es válido.";
        }

        $subject = trim($subject);
        $text = trim($text);
        if ($from !== null) {
            $from = trim($from);
        }

        $result = $this->emailModel->sendEmail($to, $subject, $text, $from);

        return $result === true
            ? "Correo enviado correctamente a $to"
            : $result;
    }

    /**
     * Envía un correo con archivo adjunto PDF
     */
    public function sendEmailWithAttachment($to, $subject, $text, $attachmentPath, $from = null)
    {
        $to = trim($to);
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return "Error: El correo '$to' no es válido.";
        }

        $subject = trim($subject);
        $text = trim($text);
        $attachmentPath = trim($attachmentPath);
        if ($from !== null) {
            $from = trim($from);
        }

        $result = $this->emailModel->sendEmailWithAttachment($to, $subject, $text, $attachmentPath, $from);

        return $result === true
            ? "Correo con PDF enviado correctamente a $to"
            : $result;
    }

    public function sendEmailAsHTML($to, $subject, $htmlContent, $from = null)
{
    $to = trim($to);
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return "Error: El correo '$to' no es válido.";
    }

    if ($from !== null) {
        $from = trim($from);
    }

    $result = $this->emailModel->sendEmailHTML($to, $subject, $htmlContent, $from);

    return $result === true
        ? "Correo enviado correctamente a $to"
        : $result;
}

}
