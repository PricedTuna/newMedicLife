<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen de Pagos | Medic Life</title>
    <link rel="stylesheet" href="payment-summary.view.css">
    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/forms.css">
    <link rel="icon" href="/views/dashboard/icons/Untitled-design-_1_.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/views/dashboard/dashboard.styles.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <script src="/views/components/sidebar.app.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/views/pay/payment-summary.view.js" defer></script>
</head>

<body>
    {include file=$sidebarPath}

    <main class="content">
        <div class="center-container">
            <div class="summary-container">
                <div class="summary-header">
                    <h1>Resumen de Pagos Diarios</h1>
                    <div class="date-filter">
                        <label for="date-filter">Fecha:</label>
                        <input type="date" id="date-filter" value="{$date}" onchange="changeDate(this.value)">
                    </div>
                </div>

                {if isset($smarty.get.success)}
                <div class="alert alert-success">
                    {$smarty.get.success|escape}
                </div>
                {/if}

                {if isset($smarty.get.error)}
                <div class="alert alert-danger">
                    {$smarty.get.error|escape}
                </div>
                {/if}

                <div class="summary-cards">
                    <div class="summary-card">
                        <div class="card-icon cash-icon">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <div class="card-content">
                            <h3>Efectivo</h3>
                            <p class="amount">${$totals.cash|number_format:2}</p>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="card-icon paypal-icon">
                            <i class="bi bi-paypal"></i>
                        </div>
                        <div class="card-content">
                            <h3>PayPal</h3>
                            <p class="amount">${$totals.paypal|number_format:2}</p>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="card-icon card-icon">
                            <i class="bi bi-credit-card"></i>
                        </div>
                        <div class="card-content">
                            <h3>Tarjeta</h3>
                            <p class="amount">${$totals.card|number_format:2}</p>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="card-icon transfer-icon">
                            <i class="bi bi-bank"></i>
                        </div>
                        <div class="card-content">
                            <h3>Transferencia</h3>
                            <p class="amount">${$totals.transfer|number_format:2}</p>
                        </div>
                    </div>
                    <div class="summary-card total-card">
                        <div class="card-icon total-icon">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div class="card-content">
                            <h3>Total</h3>
                            <p class="amount">${$totals.total|number_format:2}</p>
                        </div>
                    </div>
                </div>

                <div class="payments-table-container">
                    <h2>Detalle de Pagos</h2>
                    <table class="payments-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Paciente</th>
                                <th>Método</th>
                                <th>Monto</th>
                                <th>Fecha</th>
                                <th>Procesado por</th>
                            </tr>
                        </thead>
                        <tbody>
                            {if $payments|@count > 0}
                                {foreach from=$payments item=payment}
                                <tr>
                                    <td data-label="ID">{$payment.id}</td>
                                    <td data-label="Paciente">{$payment.patient_name} {$payment.patient_lastname} {$payment.patient_lastname2}</td>
                                    <td data-label="Método">
                                        {if $payment.type == 'cash'}
                                            <span class="payment-method cash">Efectivo</span>
                                        {elseif $payment.type == 'paypal'}
                                            <span class="payment-method paypal">PayPal</span>
                                        {elseif $payment.type == 'card'}
                                            <span class="payment-method card">Tarjeta</span>
                                        {elseif $payment.type == 'transfer'}
                                            <span class="payment-method transfer">Transferencia</span>
                                        {else}
                                            <span class="payment-method other">{$payment.type}</span>
                                        {/if}
                                    </td>
                                    <td data-label="Monto">${$payment.amount|number_format:2} {$payment.currency}</td>
                                    <td data-label="Fecha">{$payment.created_at|date_format:"%d/%m/%Y %H:%M"}</td>
                                    <td data-label="Procesado por">{$payment.user_name}</td>
                                </tr>
                                {/foreach}
                            {else}
                                <tr>
                                    <td colspan="6" class="no-data">No hay pagos registrados para esta fecha.</td>
                                </tr>
                            {/if}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>

</html>
