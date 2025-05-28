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
        $this->mg = Mailgun::create($apiKey);
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
