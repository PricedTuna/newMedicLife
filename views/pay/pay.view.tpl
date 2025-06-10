<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="pay.view.css">
    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/forms.css">
    <link rel="icon" href="/views/dashboard/icons/Untitled-design-_1_.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script src="/views/doctor/list/views-handler.js" defer></script>
    <script src="/views/appointment/register/register-appoiment.js" defer></script>
    <link rel="stylesheet" href="/views/dashboard/dashboard.styles.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <script src="/views/components/sidebar.app.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/views/pay/pay.view.js" defer></script>
    <title>Pagos | Medic Life</title>
    <style>
        .hidden {
            display: none;
        }
        .cancel-btn {
            background-color: var(--delete-btn-clr) !important;
        }
        .cancel-btn:hover {
            background-color: var(--delete-btn-hover-clr) !important;
        }
        .payment-options {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 25px;
        }
        .payment-option {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 2px solid #e0e6f1;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #f8fafc;
        }
        .payment-option:hover {
            border-color: #0070ba;
            background-color: #f0f7ff;
        }
        .payment-option.selected {
            border-color: #0070ba;
            background-color: #f0f7ff;
            box-shadow: 0 4px 8px rgba(0, 112, 186, 0.15);
        }
        .payment-option-icon {
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            background-color: #e6f2ff;
            border-radius: 50%;
            padding: 10px;
        }
        .payment-option-details {
            flex: 1;
        }
        .payment-option-title {
            font-weight: 600;
            font-size: 16px;
            color: #333;
            margin-bottom: 4px;
        }
        .payment-option-description {
            font-size: 13px;
            color: #666;
        }
        .tooltip-icon {
            margin-left: 5px;
            color: #007bff;
            cursor: help;
            font-size: 14px;
        }
        .tooltip-text {
            visibility: hidden;
            width: 200px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -100px;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .tooltip-container {
            position: relative;
            display: inline-block;
        }
        .tooltip-container:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }
        .form-group {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    {include file=$sidebarPath}

    <main class="content">
        <div class="center-container">
            <div class="form-container">
                <div class="form-header">
                    <a href="#" class="form-back-btn" id="cancel-btn">
                        <button class="back-btn cancel-btn">Cancelar</button>
                        <span class="back-btn-icon">&#8617;</span>
                    </a>
                    <h2 class="form-title">Método de Pago</h2>
                </div>

                <form action="/controllers/pay/pay.controller.php" method="POST" id="payment-form">
                    <input type="hidden" name="id_patient" value="{$appointment.id_patient|default:''}">
                    <input type="hidden" name="email" value="{$appointment.email|default:''}">
                    <input type="hidden" name="id_cita" value="{$appointment.id|default:''}">
                    <input type="hidden" id="paypal_name" name="name"
                        value="{$appointment.patient_name|default:''} {$appointment.last_name|default:''} {$appointment.last_name2|default:''}" />

                    <!-- Sección de información de pago -->
                    <div class="form-section">
                        <h3 class="section-title">Información de Pago</h3>
                        <div class="form-group">
                            <label for="monto">
                                Monto a pagar
                                <span class="tooltip-container">
                                    <i class="bi bi-question-circle tooltip-icon"></i>
                                    <span class="tooltip-text">Ingrese el monto total a pagar por la consulta médica.</span>
                                </span>
                            </label>
                            <input type="number" step="0.01" id="monto" name="monto" required placeholder="$0.00" />
                        </div>
                    </div>

                    <!-- Sección de métodos de pago -->
                    <div class="form-section">
                        <h3 class="section-title">Selecciona un método de pago</h3>
                        <div class="payment-options">
                            <!-- Opción de Efectivo -->
                            <div class="payment-option" id="efectivo-option">
                                <div class="payment-option-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="#0070ba"
                                        class="bi bi-cash-coin" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8m5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0" />
                                        <path
                                            d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.44h.375v-.443c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.1l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.45c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195z" />
                                        <path
                                            d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083q.088-.517.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1z" />
                                        <path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 6 6 0 0 1 3.13-1.567" />
                                    </svg>
                                </div>
                                <div class="payment-option-details">
                                    <div class="payment-option-title">Pago en Efectivo</div>
                                    <div class="payment-option-description">Paga directamente en la clínica antes de tu cita</div>
                                </div>
                                <input type="radio" name="metodo_pago" value="efectivo" id="efectivo" required />
                            </div>

                            <!-- Opción de PayPal -->
                            <div class="payment-option" id="paypal-option">
                                <div class="payment-option-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="#0070ba"
                                        class="bi bi-paypal" viewBox="0 0 16 16">
                                        <path
                                            d="M14.06 3.713c.12-1.071-.093-1.832-.702-2.526C12.628.356 11.312 0 9.626 0H4.734a.7.7 0 0 0-.691.59L2.005 13.509a.42.42 0 0 0 .415.486h2.756l-.202 1.28a.628.628 0 0 0 .62.726H8.14c.429 0 .793-.31.862-.731l.025-.13.48-3.043.03-.164.001-.007a.35.35 0 0 1 .348-.297h.38c1.266 0 2.425-.256 3.345-.91q.57-.403.993-1.005a4.94 4.94 0 0 0 .88-2.195c.242-1.246.13-2.356-.57-3.154a2.7 2.7 0 0 0-.76-.59l-.094-.061ZM6.543 8.82a.7.7 0 0 1 .321-.079H8.3c2.82 0 5.027-1.144 5.672-4.456l.003-.016q.326.186.548.438c.546.623.679 1.535.45 2.71-.272 1.397-.866 2.307-1.663 2.874-.802.57-1.842.815-3.043.815h-.38a.87.87 0 0 0-.863.734l-.03.164-.48 3.043-.024.13-.001.004a.35.35 0 0 1-.348.296H5.595a.106.106 0 0 1-.105-.123l.208-1.32z" />
                                    </svg>
                                </div>
                                <div class="payment-option-details">
                                    <div class="payment-option-title">PayPal</div>
                                    <div class="payment-option-description">Paga de forma segura con tu cuenta de PayPal</div>
                                </div>
                                <input type="radio" name="metodo_pago" value="paypal" id="paypal" required />
                            </div>

                            <!-- Opción de Tarjeta de Crédito/Débito -->
                            <div class="payment-option" id="card-option">
                                <div class="payment-option-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="#f57c00"
                                        class="bi bi-credit-card" viewBox="0 0 16 16">
                                        <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z"/>
                                        <path d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/>
                                    </svg>
                                </div>
                                <div class="payment-option-details">
                                    <div class="payment-option-title">Tarjeta de Crédito/Débito</div>
                                    <div class="payment-option-description">Paga de forma segura con tu tarjeta bancaria</div>
                                </div>
                                <input type="radio" name="metodo_pago" value="card" id="card" required />
                            </div>

                            <!-- Opción de Transferencia Bancaria -->
                            <div class="payment-option" id="transfer-option">
                                <div class="payment-option-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="#2e7d32"
                                        class="bi bi-bank" viewBox="0 0 16 16">
                                        <path d="M8 .95 14.61 4h.89a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5H15v7a.5.5 0 0 1 .485.379l.5 2A.5.5 0 0 1 15.5 17H.5a.5.5 0 0 1-.485-.621l.5-2A.5.5 0 0 1 1 14V7H.5a.5.5 0 0 1-.5-.5v-2A.5.5 0 0 1 .5 4h.89L8 .95zM3.776 4h8.447L8 2.05 3.776 4zM2 7v7h1V7H2zm2 0v7h2.5V7H4zm3.5 0v7h1V7h-1zm2 0v7H12V7H9.5zM13 7v7h1V7h-1zm2-1V5H1v1h14zm-.39 9H1.39l-.25 1h13.72l-.25-1z"/>
                                    </svg>
                                </div>
                                <div class="payment-option-details">
                                    <div class="payment-option-title">Transferencia Bancaria</div>
                                    <div class="payment-option-description">Realiza una transferencia a nuestra cuenta bancaria</div>
                                </div>
                                <input type="radio" name="metodo_pago" value="transfer" id="transfer" required />
                            </div>
                        </div>

                        <!-- Campos específicos para PayPal -->
                        <div id="paypal-fields" class="hidden">
                            <div class="form-group">
                                <label for="paypal_country">
                                    País
                                    <span class="tooltip-container">
                                        <i class="bi bi-question-circle tooltip-icon"></i>
                                        <span class="tooltip-text">Seleccione el país asociado a su cuenta de PayPal.</span>
                                    </span>
                                </label>
                                <select id="paypal_country" name="paypal_country">
                                    <option value="">Selecciona un país</option>
                                    <option value="MX">México</option>
                                    <option value="US">Estados Unidos</option>
                                    <option value="ES">España</option>
                                </select>
                            </div>
                        </div>

                        <!-- Campos específicos para Tarjeta -->
                        <div id="card-fields" class="hidden">
                            <div class="form-group">
                                <label for="card_reference">
                                    Referencia de Pago con Tarjeta
                                    <span class="tooltip-container">
                                        <i class="bi bi-question-circle tooltip-icon"></i>
                                        <span class="tooltip-text">Ingrese la referencia del pago con tarjeta proporcionada por la terminal.</span>
                                    </span>
                                </label>
                                <input type="text" id="card_reference" name="card_reference" placeholder="Ej. 1234567890" />
                            </div>
                        </div>

                        <!-- Campos específicos para Transferencia -->
                        <div id="transfer-fields" class="hidden">
                            <div class="form-group">
                                <label for="transfer_bank">
                                    Banco
                                    <span class="tooltip-container">
                                        <i class="bi bi-question-circle tooltip-icon"></i>
                                        <span class="tooltip-text">Seleccione el banco desde el que realizará la transferencia.</span>
                                    </span>
                                </label>
                                <select id="transfer_bank" name="transfer_bank">
                                    <option value="">Selecciona un banco</option>
                                    <option value="BBVA">BBVA</option>
                                    <option value="Santander">Santander</option>
                                    <option value="Banorte">Banorte</option>
                                    <option value="HSBC">HSBC</option>
                                    <option value="Citibanamex">Citibanamex</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="transfer_reference">
                                    Número de Referencia
                                    <span class="tooltip-container">
                                        <i class="bi bi-question-circle tooltip-icon"></i>
                                        <span class="tooltip-text">Ingrese el número de referencia de la transferencia.</span>
                                    </span>
                                </label>
                                <input type="text" id="transfer_reference" name="transfer_reference" placeholder="Ej. 1234567890" />
                            </div>
                            <div class="form-group">
                                <label for="transfer_date">Fecha de Transferencia</label>
                                <input type="date" id="transfer_date" name="transfer_date" />
                            </div>
                            <div class="alert alert-info" style="padding: 15px; background-color: #e8f5e9; border: 1px solid #2e7d32; border-radius: 4px; color: #2e7d32; margin-bottom: 15px;">
                                <p><strong>Datos para transferencia:</strong></p>
                                <p>Banco: {$transferConfig.bank_name|default:'BBVA'}</p>
                                <p>Titular: {$transferConfig.account_holder|default:'Medic Life S.A. de C.V.'}</p>
                                <p>CLABE: {$transferConfig.clabe|default:'012 345 6789 0123 45'}</p>
                                <p>Cuenta: {$transferConfig.account_number|default:'0123456789'}</p>
                                {if $transferConfig.additional_info}
                                <p>{$transferConfig.additional_info}</p>
                                {/if}
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">Continuar con el Pago</button>
                </form>
            </div>
        </div>
    </main>
</body>

</html>
