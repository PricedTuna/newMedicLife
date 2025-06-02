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
        $apiKey = getenv('MAILGUN_API_KEY') ?: 'key-yourmailgunapikey';
        $this->domain = getenv('MAILGUN_DOMAIN') ?: 'yourdomain.com';

        // Check if Mailgun class exists
        if (!class_exists('Mailgun\Mailgun')) {
            error_log("Mailgun class not found. Make sure to run 'composer update' to install dependencies.");
            // Set mg to null to indicate Mailgun is not available
            $this->mg = null;
        } else {
            $this->mg = Mailgun::create($apiKey);
        }
    }

    /**
     * Envía un correo usando Mailgun
     * @param string $to destinatario
     * @param string $subject asunto del correo
     * @param string $text contenido en texto plano
     * @param string|null $from correo remitente (opcional)
     * @return bool|string true si éxito, o mensaje de error
     */
    public function sendEmail(string $to, string $subject, string $text, string $from = null)
    {
        $from = $from ?: 'Tu Nombre <no-reply@tudominio.com>';

        // Check if Mailgun is available
        if ($this->mg === null) {
            error_log("Mailgun is not available. Email not sent to: $to, Subject: $subject");

            // Return a message indicating that email functionality is disabled
            return "Email service is currently unavailable. Please run 'composer update' to install the required dependencies. Message to $to was not sent.";
        }

        try {
            $response = $this->mg->messages()->send($this->domain, [
                'from'    => $from,
                'to'      => $to,
                'subject' => $subject,
                'text'    => $text,
            ]);

            // Validar si Mailgun lo aceptó
            if ($response->getMessage() === 'Queued. Thank you.') {
                return true;
            } else {
                return "Error: Respuesta inesperada de Mailgun → " . $response->getMessage();
            }
        } catch (HttpClientException $e) {
            return "Error HTTP al enviar correo: " . $e->getMessage();
        } catch (Exception $e) {
            return "Error general al enviar correo: " . $e->getMessage();
        }
    }
}
