<?php
/* Smarty version 5.4.5, created on 2025-05-26 07:16:45
  from 'file:list-appointments.view.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_6833f93d8430d9_84234245',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8ed0fd094283f5c249492164cf955b5372611d21' => 
    array (
      0 => 'list-appointments.view.tpl',
      1 => 1748236420,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:../../components/sidebar.tpl' => 1,
  ),
))) {
function content_6833f93d8430d9_84234245 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = 'C:\\proyectos\\mediclife\\newMedicLife\\views\\appointment\\list';
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de citas</title>

    <?php echo '<script'; ?>
 src="/views/doctor/list/views-handler.js" defer><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="/views/appointment/register/register-appointment.app.js" defer><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="./list-appointments.js" defer><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="/views/components/sidebar.app.js" defer><?php echo '</script'; ?>
>

    <link rel="stylesheet" href="/views/appointment/main/main-appointment.styles.css">
    <link rel="stylesheet" href="/views/appointment/register/register-appoiment.css">
    <link rel="stylesheet" href="/views/dashboard/dashboard.styles.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <link rel="stylesheet" href="/views/appointment/list/list-appointments.styles.css">
</head>
<body>
    <?php $_smarty_tpl->renderSubTemplate("file:../../components/sidebar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

    <main>
        <div class="main-content">
            <div class="table-header">
                <h1>Lista de Citas</h1>
                <a href="/views/appointment/register/register-appoiment.php">
                    <button class="create-btn">Crear cita</button>
                </a>
            </div>

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

            <div class="table-container">
                <h2>Citas Activas</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Numero de cita</th>
                            <th>Paciente</th>
                            <th>Área médica</th>
                            <th>Médico</th>
                            <th>Fecha y hora</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('appointments'), 'appointment');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('appointment')->value) {
$foreach0DoElse = false;
?>
                            <?php if ($_smarty_tpl->getValue('appointment')['status'] == 'A') {?>
                                <tr>
                                    <td data-label="ID"><?php echo $_smarty_tpl->getValue('appointment')['cita'];?>
</td>
                                    <td data-label="Paciente"><?php echo $_smarty_tpl->getValue('appointment')['patient_name'];?>
 <?php echo $_smarty_tpl->getValue('appointment')['last_name'];?>
 <?php echo $_smarty_tpl->getValue('appointment')['last_name2'];?>
</td>
                                    <td data-label="Área Médica"><?php echo $_smarty_tpl->getValue('appointment')['medical_area'];?>
</td>
                                    <td data-label="Doctor"><?php echo $_smarty_tpl->getValue('appointment')['doctor_name'];?>
</td>
                                    <td data-label="Fecha"><?php echo $_smarty_tpl->getValue('appointment')['appointment_date'];?>
</td>
                                    <td class="actions-td">
                                        <a href="/views/pay/pay.view.php?id=<?php echo $_smarty_tpl->getValue('appointment')['cita'];?>
" class="action-wrapper">
                                            <button class="finish-btn">Finalizar Cita</button>
                                        </a>
                                        <form action="/controllers/appoiment/delete-appointment.controller.php" method="POST" class="action-wrapper">
                                            <input type="hidden" name="appointment_id" value="<?php echo $_smarty_tpl->getValue('appointment')['cita'];?>
">
                                            <button type="submit" class="delete-btn" data-id="<?php echo $_smarty_tpl->getValue('appointment')['cita'];?>
">Eliminar</button>
                                        </form>
                                        <a href="/views/appointment/register/register-appoiment.php?id=<?php echo $_smarty_tpl->getValue('appointment')['cita'];?>
" class="action-wrapper">
                                            <button class="update-btn">Actualizar</button>
                                        </a>
                                    </td>
                                </tr>
                            <?php }?>
                        <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                    </tbody>
                </table>
            </div>

            <div class="table-container">
                <h2>Citas Finalizadas</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Numero de cita</th>
                            <th>Paciente</th>
                            <th>Área médica</th>
                            <th>Médico</th>
                            <th>Fecha y hora</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('appointments'), 'appointment');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('appointment')->value) {
$foreach1DoElse = false;
?>
                            <?php if ($_smarty_tpl->getValue('appointment')['status'] == 'F') {?>
                                <tr>
                                    <td data-label="ID"><?php echo $_smarty_tpl->getValue('appointment')['cita'];?>
</td>
                                    <td data-label="Paciente"><?php echo $_smarty_tpl->getValue('appointment')['patient_name'];?>
 <?php echo $_smarty_tpl->getValue('appointment')['last_name'];?>
 <?php echo $_smarty_tpl->getValue('appointment')['last_name2'];?>
</td>
                                    <td data-label="Área Médica"><?php echo $_smarty_tpl->getValue('appointment')['medical_area'];?>
</td>
                                    <td data-label="Doctor"><?php echo $_smarty_tpl->getValue('appointment')['doctor_name'];?>
</td>
                                    <td data-label="Fecha"><?php echo $_smarty_tpl->getValue('appointment')['appointment_date'];?>
</td>
                                    <td class="actions-td">
                                        <span style="color: gray;">Finalizada</span>
                                    </td>
                                </tr>
                            <?php }?>
                        <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>


<?php }
}
