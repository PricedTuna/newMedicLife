<?php
class PaymentModel
{
    private $clientId;
    private $secret;
    private $baseUrl;

    public function __construct()
    {
        $this->clientId = 'Adlc27kvKviM2KXGbjAP-4jx9YX8rubKFn1v8bx_oYVa6A4S5JcvPWpd6tYoPcbVA8bau-CZF9IhpC5u';
        $this->secret = 'EA9MDIc37gHd0QRS0MjSXwA4DGi3J_engTdgDCVSqzzRwxMHhaCZGwu6b3uIGMIeH2uWzMDNDVWT5rBL';
        $this->baseUrl = 'https://api-m.sandbox.paypal.com'; // Usa "api-m.paypal.com" en producción
    }

    private function getAccessToken()
    {
        $ch = curl_init("{$this->baseUrl}/v1/oauth2/token");
        curl_setopt($ch, CURLOPT_USERPWD, "$this->clientId:$this->secret");
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = ["Accept: application/json", "Accept-Language: en_US"];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        if (!$response) throw new Exception("Error obteniendo token: " . curl_error($ch));
        curl_close($ch);

        $result = json_decode($response);
        return $result->access_token;
    }

    public function createPayment($name, $email, $amount, $currency = "MXN")
    {
        $accessToken = $this->getAccessToken();

        $paymentData = [
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "amount" => [
                    "currency_code" => $currency,
                    "value" => number_format($amount, 2, '.', '')
                ]
            ]],
            "application_context" => [
                "return_url" => "http://localhost/controller/paypal.success.php",
                "cancel_url" => "http://localhost/controller/paypal.cancel.php"
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
        foreach ($result->links as $link) {
            if ($link->rel === 'approve') {
                // Redirecciona al usuario a la URL de PayPal
                header("Location: " . $link->href);
                exit;
            }
        }

        throw new Exception("No se pudo obtener la URL de aprobación.");
    }
}
