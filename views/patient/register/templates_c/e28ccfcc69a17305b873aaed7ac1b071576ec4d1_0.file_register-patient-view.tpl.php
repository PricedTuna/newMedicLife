<?php
/* Smarty version 5.4.5, created on 2025-06-01 05:31:22
  from 'file:register-patient-view.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_683be5aab58228_26391468',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e28ccfcc69a17305b873aaed7ac1b071576ec4d1' => 
    array (
      0 => 'register-patient-view.tpl',
      1 => 1748755867,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:../../components/sidebar.tpl' => 1,
    'file:steps/step1.tpl' => 1,
    'file:steps/step2.tpl' => 1,
    'file:steps/step3.tpl' => 1,
    'file:steps/step4.tpl' => 1,
  ),
))) {
function content_683be5aab58228_26391468 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/var/www/html/views/patient/register';
?><!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/views/dashboard/icons/Untitled-design-_1_.ico" type="image/x-icon">
    <link rel="stylesheet" href="./register-patient.styles.css">
    <link rel="stylesheet" href="../../components/sidebar.styles.css">
    <?php echo '<script'; ?>
 src="https://cdn.jsdelivr.net/npm/sweetalert2@11"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="../../components/sidebar.app.js" defer><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="./register-patient.app.js" defer><?php echo '</script'; ?>
>
    <title>Registro de Paciente</title>

    <!-- Inyectar las variables PHP en JavaScript -->
    <?php echo '<script'; ?>
>
        // Asegúrate de que las variables de Smarty se inyecten correctamente en JavaScript
        window.municipalities = <?php echo json_encode($_smarty_tpl->getValue('municipalities'));?>
;
        window.localities = <?php echo json_encode($_smarty_tpl->getValue('localities'));?>
;
        window.states = <?php echo json_encode($_smarty_tpl->getValue('states'));?>
;

        // Datos preseleccionados para actualización
        window.preselectedPatientData = {
            state: <?php echo (($tmp = $_smarty_tpl->getValue('patient')['id_state'] ?? null)===null||$tmp==='' ? 'null' ?? null : $tmp);?>
,
            municipality: <?php echo (($tmp = $_smarty_tpl->getValue('patient')['id_municipality'] ?? null)===null||$tmp==='' ? 'null' ?? null : $tmp);?>
,
            locality: <?php echo (($tmp = $_smarty_tpl->getValue('patient')['id_locality'] ?? null)===null||$tmp==='' ? 'null' ?? null : $tmp);?>

        };
    <?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="form_steps.js" defer><?php echo '</script'; ?>
>
</head>

<body>

    <div class="registerPatientWrapper">
        <?php $_smarty_tpl->renderSubTemplate("file:../../components/sidebar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

        <div class="form-container">
            <h2 class="form-title">
                <?php if ($_smarty_tpl->getValue('patient')) {?>
                    Actualizar Paciente
                <?php } else { ?>
                    Registrar Paciente
                <?php }?>
            </h2>
            <?php if ((true && ($_smarty_tpl->hasVariable('success') && null !== ($_smarty_tpl->getValue('success') ?? null)))) {?>
                <!-- Mostrar mensaje de éxito -->
                <div
                    style="color: darkgreen; margin-bottom: 1rem; border: 1px solid green; padding: 0.5rem; border-radius: 5px; background-color: lightgreen;">
                    <?php echo htmlspecialchars((string)$_smarty_tpl->getValue('success'), ENT_QUOTES, 'UTF-8', true);?>

                </div>
            <?php }?>

            <div class="steps">
                <div class="step step-active" data-step="1">Paso 1</div>
                <div class="step" data-step="2">Paso 2</div>
                <div class="step" data-step="3">Paso 3</div>
                <div class="step" data-step="4">Paso 4</div>
            </div>

            <!-- Si hay un error, lo mostramos aquí -->
            <?php if ((true && ($_smarty_tpl->hasVariable('error') && null !== ($_smarty_tpl->getValue('error') ?? null)))) {?>
                <div style="color: red; margin-bottom: 1rem; border: 1px solid red; padding: 0.5rem; border-radius: 5px;">
                    <?php echo htmlspecialchars((string)$_smarty_tpl->getValue('error'), ENT_QUOTES, 'UTF-8', true);?>

                </div>
            <?php }?>

            <form id="patient-form" action="/controllers/patient/register-patient.controller.php" method="POST" enctype="multipart/form-data">
                <?php $_smarty_tpl->renderSubTemplate('file:steps/step1.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
                <?php $_smarty_tpl->renderSubTemplate('file:steps/step2.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
                <?php $_smarty_tpl->renderSubTemplate('file:steps/step3.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
                <?php $_smarty_tpl->renderSubTemplate('file:steps/step4.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
            </form>
        </div>
    </div>

</body>

</html>
<?php }
}
