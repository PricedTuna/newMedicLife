<?php
class PaymentModel
{
    private $clientId;
    private $secret;
    private $baseUrl;
    private $pdo;

    public function __construct($pdo)
    {
        $this->clientId = 'Adlc27kvKviM2KXGbjAP-4jx9YX8rubKFn1v8bx_oYVa6A4S5JcvPWpd6tYoPcbVA8bau-CZF9IhpC5u';
        $this->secret = 'EA9MDIc37gHd0QRS0MjSXwA4DGi3J_engTdgDCVSqzzRwxMHhaCZGwu6b3uIGMIeH2uWzMDNDVWT5rBL';
        $this->baseUrl = 'https://api-m.sandbox.paypal.com'; // Usa "api-m.paypal.com" en producción
        $this->pdo = $pdo;
    }

    private function getAccessToken()
    {
        $ch = curl_init("{$this->baseUrl}/v1/oauth2/token");
        curl_setopt($ch, CURLOPT_USERPWD, "$this->clientId:$this->secret");
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Accept-Language: en_US"
        ]);
        $response = curl_exec($ch);
        if (!$response) throw new Exception("Error obteniendo token: " . curl_error($ch));
        curl_close($ch);

        $result = json_decode($response);
        return $result->access_token;
    }

    private function registerGeneralPayment($userId, $patientId, $amount, $currency = 'MXN', $type = 'paypal')
    {
        $stmt = $this->pdo->prepare("INSERT INTO payments (user_id, patient_id, type, amount, currency) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $patientId, $type, $amount, $currency]);
        return $this->pdo->lastInsertId();
    }

    private function registerPaypalPayment($paymentId, $paypalOrderId, $status, $payerId = null, $response = null)
    {
        $stmt = $this->pdo->prepare("INSERT INTO paypal_payments (payment_id, paypal_order_id, paypal_payer_id, status, response_json) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $paymentId,
            $paypalOrderId,
            $payerId,
            $status,
            json_encode($response)
        ]);
    }

    public function createPayment($name, $email, $amount, $userId, $patientId, $currency = "MXN")
    {
        $accessToken = $this->getAccessToken();

        // Registra primero en la tabla general
        $paymentId = $this->registerGeneralPayment($userId, $patientId, $amount, $currency, 'paypal');

        $paymentData = [
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "amount" => [
                    "currency_code" => $currency,
                    "value" => number_format($amount, 2, '.', '')
                ]
            ]],
            "application_context" => [
                "return_url" => "http://localhost/controller/pay/pay.controller.php?payment_id={$paymentId}",
                "cancel_url" => "http://localhost/controller/pay/pay.controller.php?payment_id={$paymentId}"
            ]
        ];

        $ch = curl_init("{$this->baseUrl}/v2/checkout/orders");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer $accessToken"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paymentData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if (!$response) throw new Exception("Error creando orden: " . curl_error($ch));
        curl_close($ch);

        $result = json_decode($response);
        $orderId = $result->id ?? null;

        if ($orderId) {
            $this->registerPaypalPayment($paymentId, $orderId, 'CREATED', null, $result);
        }

        foreach ($result->links as $link) {
            if ($link->rel === 'approve') {
                header("Location: " . $link->href);
                exit;
            }
        }

        throw new Exception("No se pudo obtener la URL de aprobación.");
    }

    // --- Nueva función para capturar el pago aprobado y guardarlo en BD ---
    public function captureAndStorePayment($orderId, $payerId)
    {
        $accessToken = $this->getAccessToken();

        // Capturar pago en PayPal
        $ch = curl_init("{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer $accessToken"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if (!$response) throw new Exception("Error capturando pago: " . curl_error($ch));
        curl_close($ch);

        $result = json_decode($response, true);

        if (isset($result['status']) && $result['status'] === 'COMPLETED') {
            // Actualizar estado en tabla paypal_payments
            $status = $result['status'];
            // Buscar el payment_id relacionado con este orderId
            $stmt = $this->pdo->prepare("SELECT payment_id FROM paypal_payments WHERE paypal_order_id = ?");
            $stmt->execute([$orderId]);
            $paymentId = $stmt->fetchColumn();

            if (!$paymentId) {
                throw new Exception("No se encontró el pago general relacionado.");
            }

            // Actualizar registro con payer_id y status
            $stmt = $this->pdo->prepare("UPDATE paypal_payments SET paypal_payer_id = ?, status = ?, response_json = ? WHERE paypal_order_id = ?");
            $stmt->execute([$payerId, $status, json_encode($result), $orderId]);

            return $status;
        } else {
            throw new Exception("El pago no fue completado correctamente.");
        }
    }
}
