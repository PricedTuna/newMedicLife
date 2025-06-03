<?php
// email.model.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Mailgun\Mailgun;
use Mailgun\Exception\HttpClientException;

class EmailModel
{
    private $mg;
    private $domain;

    public function __construct()
    {
        $apiKey = getenv('MAILGUN_API_KEY') ?: '';
        $this->domain = getenv('MAILGUN_DOMAIN') ?: '';

        if (!class_exists('Mailgun\Mailgun')) {
            error_log("Mailgun class not found. Make sure to run 'composer update' to install dependencies.");
            $this->mg = null;
        } else {
            $this->mg = Mailgun::create($apiKey);
        }
    }

    /**
     * Envía un correo usando Mailgun (texto plano)
     */
    public function sendEmail(string $to, string $subject, string $text, string $from = null)
    {
        $from = $from ?: 'Tu Nombre <no-reply@tudominio.com>';

        if ($this->mg === null) {
            error_log("Mailgun is not available. Email not sent to: $to, Subject: $subject");
            return "Email service is currently unavailable. Please run 'composer update'. Message to $to was not sent.";
        }

        try {
            $response = $this->mg->messages()->send($this->domain, [
                'from'    => $from,
                'to'      => $to,
                'subject' => $subject,
                'text'    => $text,
            ]);

            return $response->getMessage() === 'Queued. Thank you.'
                ? true
                : "Error: Respuesta inesperada de Mailgun → " . $response->getMessage();
        } catch (HttpClientException $e) {
            return "Error HTTP al enviar correo: " . $e->getMessage();
        } catch (Exception $e) {
            return "Error general al enviar correo: " . $e->getMessage();
        }
    }

    /**
     * Envía un correo con un archivo adjunto (PDF)
     * @param string $to destinatario
     * @param string $subject asunto del correo
     * @param string $text cuerpo del mensaje
     * @param string $attachmentPath ruta absoluta al archivo PDF
     * @param string|null $from remitente opcional
     * @return bool|string true si éxito, o mensaje de error
     */
    public function sendEmailWithAttachment(string $to, string $subject, string $text, string $attachmentPath, string $from = null)
    {
        $from = $from ?: 'Tu Nombre <no-reply@tudominio.com>';

        if ($this->mg === null) {
            error_log("Mailgun is not available. Email not sent to: $to");
            return "Email service unavailable. Run 'composer update'. Message not sent.";
        }

        if (!file_exists($attachmentPath)) {
            return "Error: El archivo no existe en '$attachmentPath'";
        }

        try {
            $response = $this->mg->messages()->send($this->domain, [
                'from'    => $from,
                'to'      => $to,
                'subject' => $subject,
                'text'    => $text,
                'attachment' => [
                    ['filePath' => $attachmentPath, 'filename' => basename($attachmentPath)]
                ]
            ]);

            return $response->getMessage() === 'Queued. Thank you.'
                ? true
                : "Error: Respuesta inesperada de Mailgun → " . $response->getMessage();
        } catch (HttpClientException $e) {
            return "Error HTTP al enviar correo: " . $e->getMessage();
        } catch (Exception $e) {
            return "Error general al enviar correo: " . $e->getMessage();
        }
    }

    public function sendEmailHTML(string $to, string $subject, string $htmlContent, string $from = null)
    {
        $from = $from ?: 'Medic Life <no-reply@tudominio.com>';

        if ($this->mg === null) {
            return "Error: El servicio de correo no está disponible.";
        }

        try {
            $response = $this->mg->messages()->send($this->domain, [
                'from'    => $from,
                'to'      => $to,
                'subject' => $subject,
                'html'    => $htmlContent,
            ]);

            return $response->getMessage() === 'Queued. Thank you.'
                ? true
                : "Error: Respuesta inesperada de Mailgun → " . $response->getMessage();
        } catch (HttpClientException $e) {
            return "Error HTTP al enviar correo: " . $e->getMessage();
        } catch (Exception $e) {
            return "Error general al enviar correo: " . $e->getMessage();
        }
    }
}
