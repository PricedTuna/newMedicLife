<?php
/* Smarty version 5.4.5, created on 2025-05-25 02:56:49
  from 'file:pay.view.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_68326ad1883e04_58361130',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'baba7ce0c015fb7b25d6b6ec4921fad357cc236b' => 
    array (
      0 => 'pay.view.tpl',
      1 => 1748134606,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_68326ad1883e04_58361130 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = 'C:\\proyectos\\mediclife\\newMedicLife\\views\\pay';
?><!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="pay.view.css">
    <?php echo '<script'; ?>
 src="/views/doctor/list/views-handler.js" defer><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="/views/appointment/register/register-appointment.app.js" defer><?php echo '</script'; ?>
>
    <link rel="stylesheet" href="/views/dashboard/dashboard.styles.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <?php echo '<script'; ?>
 src="/views/components/sidebar.app.js" defer><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="pay.view.js" defer><?php echo '</script'; ?>
>
    <title>Pago con PayPal</title>
</head>

<body>

    <?php $_smarty_tpl->renderSubTemplate($_smarty_tpl->getValue('sidebarPath'), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

    <main class="payment-container">
        <div class="payment-box">
            <h2 class="payment-title">Selecciona el método de pago</h2>
            <form action="/controllers/pay/pay.controller.php" method="POST">
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
                        <input type="text" id="paypal_name" name="paypal_name" required />
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

</html><?php }
}
