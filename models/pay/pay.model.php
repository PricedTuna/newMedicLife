 <?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/utils.php';
    require_once __DIR__ . '/../../load_env.php';
    loadEnv(__DIR__ . '/../../.env');



    class PaymentModel
    {
        private $clientId;
        private $secret;
        private $baseUrl;
        private $pdo;
        private $paypalReturnBaseUrl;



        public function __construct($pdo)
        {
            $this->pdo = $pdo;
            $this->clientId = getenv('PAYPAL_CLIENT') ?: '';
            $this->secret = getenv('PAYPAL_SECRET') ?: '';
            $this->baseUrl = 'https://api-m.sandbox.paypal.com'; // Usa "api-m.paypal.com" en producción

            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'];
            $this->paypalReturnBaseUrl = $protocol . "://" . $host;
        }

        function updateAppointment($id_appointment)
        {
            $stmt = $this->pdo->prepare("UPDATE appointments SET status = :status WHERE id = :id");
            $stmt->execute([
                'status' => 'F',
                'id'     => $id_appointment
            ]);
        }

        function savePay($id_user, $id_patient, $amount, $type, $currency = "MXN")
        {
            $stmt = $this->pdo->prepare("INSERT INTO payments(user_id, patient_id, type, amount, currency) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$id_user, $id_patient, $type, $amount, $currency]);
            return $this->pdo->lastInsertId();
        }

        function savePayPal($payment_id, $paypal_order_id, $paypal_payer_id, $status, $response_json)
        {
            $stmt = $this->pdo->prepare("INSERT INTO paypal_payments(payment_id, paypal_order_id, paypal_payer_id, status, response_json) VALUES (?, ?, ?, ?, ?)");
            return $stmt->execute([$payment_id, $paypal_order_id, $paypal_payer_id, $status, $response_json]);
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
                    "return_url" => $this->paypalReturnBaseUrl . "/controllers/pay/pay.controller.php?name=" . urlencode($name) . "&amount=" . urlencode($amount),
                    "cancel_url" => $this->paypalReturnBaseUrl . "/views/doctor/list/list-doctors.view.php?error=" . urlencode("Pago cancelado")
                ]
            ];

            $ch = curl_init("{$this->baseUrl}/v2/checkout/orders");
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
                "Authorization: Bearer $accessToken"
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(utf8ize($paymentData)));
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

        public function captureAndStorePayment($token, $payerId)
        {
            $accessToken = $this->getAccessToken();

            $ch = curl_init("{$this->baseUrl}/v2/checkout/orders/{$token}/capture");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
                "Authorization: Bearer $accessToken"
            ]);

            $response = curl_exec($ch);
            if (!$response) throw new Exception("Error capturando el pago: " . curl_error($ch));
            curl_close($ch);

            $result = json_decode($response);

            // Verifica que la captura haya ocurrido
            $amount = $result->purchase_units[0]->payments->captures[0]->amount->value ?? null;
            $currency = $result->purchase_units[0]->payments->captures[0]->amount->currency_code ?? null;
            $paypal_order_id = $result->id ?? null;
            $paypal_payer_id = $result->payer->payer_id ?? $payerId; // Fallback al parámetro recibido
            $status = $result->status ?? 'UNKNOWN';
            $response_json = json_encode(utf8ize($result));

            session_start();
            $id_user = $_SESSION['id_user'] ?? null;
            $id_patient = $_SESSION['id_patient'] ?? null;

            if ($id_user && $id_patient && $amount && $paypal_order_id && $paypal_payer_id) {
                $payment_id = $this->savePay($id_user, $id_patient, $amount, 'paypal', $currency);
                $this->savePayPal($payment_id, $paypal_order_id, $paypal_payer_id, $status, $response_json);
            }

            return $status;
        }

        /**
         * Get payment summary for a specific date
         * 
         * @param string $date Date in Y-m-d format
         * @return array Payment summary data
         */
        public function getPaymentSummary($date)
        {
            // Get all payments for the specified date
            $stmt = $this->pdo->prepare("
                SELECT p.id, p.user_id, p.patient_id, p.type, p.amount, p.currency, p.created_at,
                       u.name as user_name,
                       pt.names as patient_name, pt.last_name as patient_lastname, pt.last_name2 as patient_lastname2,
                       a.id as appointment_id, a.appointment_date
                FROM payments p
                LEFT JOIN users u ON p.user_id = u.id
                LEFT JOIN patients pt ON p.patient_id = pt.id
                LEFT JOIN appointments a ON a.id_patient = p.patient_id AND DATE(a.created_at) = DATE(p.created_at)
                WHERE DATE(p.created_at) = :date
                ORDER BY p.created_at DESC
            ");
            $stmt->execute(['date' => $date]);
            $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calculate totals by payment type
            $totals = [
                'cash' => 0,
                'paypal' => 0,
                'card' => 0,
                'transfer' => 0,
                'total' => 0
            ];

            foreach ($payments as $payment) {
                $type = $payment['type'];
                $amount = floatval($payment['amount']);

                if (isset($totals[$type])) {
                    $totals[$type] += $amount;
                }
                $totals['total'] += $amount;
            }

            return [
                'payments' => $payments,
                'totals' => $totals,
                'date' => $date
            ];
        }
    }
