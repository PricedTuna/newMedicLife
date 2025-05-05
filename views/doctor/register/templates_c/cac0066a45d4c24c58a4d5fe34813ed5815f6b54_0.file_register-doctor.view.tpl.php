<?php
/* Smarty version 5.4.5, created on 2025-04-30 04:24:23
  from 'file:register-doctor.view.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_6811a5f7c66f19_29211287',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cac0066a45d4c24c58a4d5fe34813ed5815f6b54' => 
    array (
      0 => 'register-doctor.view.tpl',
      1 => 1745980391,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:steps/step1.tpl' => 1,
    'file:steps/step2.tpl' => 1,
    'file:steps/step3.tpl' => 1,
  ),
))) {
function content_6811a5f7c66f19_29211287 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/angel/Desktop/newMedicLife/views/doctor/register';
?><!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./register-doctor.styles.css">
    <?php echo '<script'; ?>
 src="./register-doctor.app.js" defer><?php echo '</script'; ?>
>
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <?php echo '<script'; ?>
 src="/views/components/sidebar.app.js" defer><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="/views/components/sidebar.app.js" defer><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="/views/doctor/list/views-handler.js" defer><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="/views/doctor/register/register-doctor.app.js" defer><?php echo '</script'; ?>
>
    <link rel="stylesheet" href="/views/doctor/main/main-doctor.styles.css">
    <link rel="stylesheet" href="/views/doctor/list/list-doctors.styles.css">
    <link rel="stylesheet" href="/views/doctor/register/register-doctor.styles.css">
    <title>Registro de Doctores</title>

    <?php echo '<script'; ?>
>
        // Asegúrate de que las variables de Smarty se inyecten correctamente en JavaScript
        window.municipalities = <?php echo json_encode($_smarty_tpl->getValue('municipalities'));?>
;
        window.localities = <?php echo json_encode($_smarty_tpl->getValue('localities'));?>
;
        window.states = <?php echo json_encode($_smarty_tpl->getValue('states'));?>
;
    <?php echo '</script'; ?>
>

    <?php echo '<script'; ?>
 src="register-doctor.view.js" defer><?php echo '</script'; ?>
>
</head>

<body>

    <?php $_smarty_tpl->renderSubTemplate($_smarty_tpl->getValue('sidebarPath'), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?> <!-- Aquí se incluye el sidebar, según la variable Smarty -->

    <main class="content">

        <div class="center-container">
            <div class="form-container">
                <div class="form-header">
                    <a href="/views/doctor/main/main-doctor.view.php" class="form-back-btn">
                        <button class="back-btn">Volver</button>
                        <span class="back-btn-icon">&#8617;</span>
                    </a>
                    <h2 class="form-title">
                        <?php if ($_smarty_tpl->getValue('doctor')) {?>
                            Actualizar Doctor
                        <?php } else { ?>
                            Registrar Doctor
                        <?php }?>
                    </h2>
                </div>

                <!-- Indicadores de los pasos -->
                <div class="steps">
                    <div class="step step-active" data-step="1">Paso 1</div>
                    <div class="step" data-step="2">Paso 2</div>
                    <div class="step" data-step="3">Paso 3</div>
                </div>

                <!-- Si hay un error, lo mostramos aquí -->
                <?php if ((true && ($_smarty_tpl->hasVariable('error') && null !== ($_smarty_tpl->getValue('error') ?? null)))) {?>
                    <div
                        style="color: red; margin-bottom: 1rem; border: 1px solid red; padding: 0.5rem; border-radius: 5px;">
                        <?php echo htmlspecialchars((string)$_smarty_tpl->getValue('error'), ENT_QUOTES, 'UTF-8', true);?>

                    </div>
                <?php }?>

                <!-- Formulario para registrar o actualizar al doctor -->
                <form action="/controllers/doctor/register-doctor.controller.php" method="POST" id="doctor-form" enctype="multipart/form-data">
                    <?php $_smarty_tpl->renderSubTemplate('file:steps/step1.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?> <!-- Paso 1 -->
                    <?php $_smarty_tpl->renderSubTemplate('file:steps/step2.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?> <!-- Paso 2 -->
                    <?php $_smarty_tpl->renderSubTemplate('file:steps/step3.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?> <!-- Paso 3 -->
                </form>
            </div>
        </div>
    </main>
</body>

</html>
<?php }
}
