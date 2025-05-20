<?php
/* Smarty version 5.4.5, created on 2025-05-19 01:48:25
  from 'file:list-appointments.view.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_682a8de94ffca8_74367336',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd995f454fd46c38dda4f55b977c176ae522feeda' => 
    array (
      0 => 'list-appointments.view.tpl',
      1 => 1747619302,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:../../components/sidebar.tpl' => 1,
  ),
))) {
function content_682a8de94ffca8_74367336 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/angel/Desktop/mediclife/newMedicLife/views/appointment/list';
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
    <?php echo '<script'; ?>
 src="/views/doctor/list/views-handler.js" defer><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="/views/appointment/register/register-appointment.app.js" defer><?php echo '</script'; ?>
>
    <link rel="stylesheet" href="/views/appointment/main/main-appointment.styles.css">
    <link rel="stylesheet" href="/views/appointment/register/register-appoiment.css">
    <link rel="stylesheet" href="/views/dashboard/dashboard.styles.css">
    <?php echo '<script'; ?>
 src="./list-appointment.js" defer><?php echo '</script'; ?>
>
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <link rel="stylesheet" href="/register-appointment.styles.css">
    <link rel="stylesheet" href="/views/appointment/list/list-appointments.styles.css">
        <?php echo '<script'; ?>
 src="/views/components/sidebar.app.js" defer><?php echo '</script'; ?>
>

    <title>Lista de médicos</title>
</head>
<body>

    <?php $_smarty_tpl->renderSubTemplate("file:../../components/sidebar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
    
    <main >

        <div class="main-content">
            <div class="table-header">
                <h1>Lista de Citas    </h1>
                <a href="/views/appointment/register/register-appoiment.php">
                    <button class="create-btn">Crear cita</button>
                </a>
            </div>
            <div class="table-container">
                <?php if ((true && ($_smarty_tpl->hasVariable('error') && null !== ($_smarty_tpl->getValue('error') ?? null)))) {?>
                    <div style="color: red; margin-bottom: 1rem; border: 1px solid red; padding: 0.5rem; border-radius: 5px;">
                        <?php echo htmlspecialchars((string)$_smarty_tpl->getValue('error'), ENT_QUOTES, 'UTF-8', true);?>

                    </div>
                <?php }?>

                <?php if ((true && ($_smarty_tpl->hasVariable('success') && null !== ($_smarty_tpl->getValue('success') ?? null)))) {?>
                    <div style="color: darkgreen; margin-bottom: 1rem; border: 1px solid green; padding: 0.5rem; border-radius: 5px; background-color: lightgreen;">
                        <?php echo htmlspecialchars((string)$_smarty_tpl->getValue('success'), ENT_QUOTES, 'UTF-8', true);?>

                    </div>
                <?php }?>

                <a href="/views/doctor/register/register-doctor.view.php" aria-label="Agregar cita">
                    <button class="icon-btn table-add-btn">+</button>
                </a>

                <table>
                    <thead>
                        <tr>
                            <th>Numero de cita</th>
                            <th>Paciente</th>
                            <th>Area medica</th>
                            <th>Medico</th>
                            <th>Fecha y hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('doctors')) > 0) {?>
                            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('doctors'), 'doctor');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('doctor')->value) {
$foreach0DoElse = false;
?>
                                <tr>
                                    
                                    </td>
                                </tr>
                            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="14">No hay citas registradas.</td>
                            </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
<?php }
}
