<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="pay.view.css">
    <script src="/views/doctor/list/views-handler.js" defer></script>
    <script src="/views/appointment/register/register-appointment.app.js" defer></script>
    <link rel="stylesheet" href="/views/dashboard/dashboard.styles.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <script src="/views/components/sidebar.app.js" defer></script>
    <script src="pay.view.js" defer></script>
    <title>Pago con PayPal</title>
</head>

<body>

    {include file=$sidebarPath}

    <main class="payment-container">
        <div class="payment-box">
            <h2 class="payment-title">Selecciona el método de pago</h2>
            <form action="/controllers/pay/pay.controller.php" method="POST">
            <input type="hidden" name="id_patient" value="{$appointment.id_patient|default:''}" ></input>
            <input type="hidden" name="email" value="{$appointment.email|default:''}" ></input>
            <input type="hidden" name="id_cita" value="{$appointment.id|default:''}" ></input>

                <label class="option">
                    <input type="radio" name="metodo_pago" value="efectivo" id="efectivo" required />
                    Pago en Efectivo
                </label>
                <label class="option">
                    <input type="radio" name="metodo_pago" value="paypal" id="paypal" required />
                    PayPal
                </label>

                <!-- Monto común para ambos -->
                <label>
                    Monto a pagar:
                    <input type="number" step="0.01" id="monto" name="monto" required />
                </label>

                <div id="cash-fields" class="hidden">
                    <label>
                        Cliente paga con:
                        <input type="number" step="0.01" id="paga_con" name="paga_con" required />
                    </label>
                    <div id="restante">Restante: $0.00</div>
                </div>

                <div id="paypal-fields" class="hidden">
                    <label>
                        Nombre completo:
                        <input type="text" id="paypal_name" name="paypal_name" value="{$appointment.patient_name|default:''} {$appointment.last_name|default:''} {$appointment.last_name2|default:''}" required />
                    </label>
                    <label>
                        Correo electrónico (PayPal):
                        <input type="email" id="paypal_email" name="paypal_email" required />
                    </label>
                    <label>
                        País:
                        <select id="paypal_country" name="paypal_country" required>
                            <option value="">Selecciona un país</option>
                            <option value="MX">México</option>
                            <option value="US">Estados Unidos</option>
                            <option value="ES">España</option>
                        </select>
                    </label>
                </div>

                <button type="submit" class="submit-btn">Continuar</button>
            </form>

        </div>
    </main>

</body>

</html>