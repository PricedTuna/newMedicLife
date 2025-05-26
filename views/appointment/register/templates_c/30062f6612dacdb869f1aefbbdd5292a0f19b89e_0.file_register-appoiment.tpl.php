<?php
/* Smarty version 5.4.5, created on 2025-05-26 09:44:04
  from 'file:register-appoiment.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_68341bc47fb795_34256368',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '30062f6612dacdb869f1aefbbdd5292a0f19b89e' => 
    array (
      0 => 'register-appoiment.tpl',
      1 => 1748241640,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_68341bc47fb795_34256368 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = 'C:\\proyectos\\mediclife\\newMedicLife\\views\\appointment\\register';
?><!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./register-appoiment.css">
    <link rel="stylesheet" href="/assets/css/flatpickr.min.css">
    <?php echo '<script'; ?>
 src="../../components/sidebar.app.js" defer><?php echo '</script'; ?>
>
    <link rel="stylesheet" href="../../components/sidebar.styles.css">
    <?php echo '<script'; ?>
 src="/assets/js/flatpickr.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="/assets/js/es.js"><?php echo '</script'; ?>
>
    <title>Solicitar Cita</title>

    <?php echo '<script'; ?>
>
        window.doctors = <?php echo json_encode($_smarty_tpl->getValue('doctors'));?>
;
        window.patients = <?php echo json_encode($_smarty_tpl->getValue('patients'));?>
;
        window.schedules = <?php echo json_encode($_smarty_tpl->getValue('schedules'));?>
;
        window.allAppointments = <?php echo json_encode($_smarty_tpl->getValue('allAppointments'));?>
;
    <?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="/views/appointment/register/register-appoiment.js"><?php echo '</script'; ?>
>

</head>

<body>

    <?php $_smarty_tpl->renderSubTemplate($_smarty_tpl->getValue('sidebarPath'), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

    <div class="center-container">
        <div class="form-container">
            <h2>Solicitar Citas</h2>
            <form id="solicitarCita" method="POST" action="/controllers/appoiment/register-appoiment.controller.php">
            <input type="hidden" name="appointment_id" value="<?php echo (($tmp = $_smarty_tpl->getValue('appointment')['id'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
"></input>
            <input type="hidden" id="appointmentId" name="appointment_id" value="<?php echo (($tmp = $_smarty_tpl->getValue('appointment')['id'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
">
                <div class="form-group">
                    <label>Busqueda de paciente: Ingrese el nombre o CURP</label>
                    <input type="text" id="CURP" name="curp" list="curpList" autocomplete="off" value="<?php echo (($tmp = $_smarty_tpl->getValue('appointment')['curp'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
 <?php echo (($tmp = $_smarty_tpl->getValue('appointment')['patient_name'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
 <?php echo (($tmp = $_smarty_tpl->getValue('appointment')['last_name'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
 <?php echo (($tmp = $_smarty_tpl->getValue('appointment')['last_name2'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" required>
                    <datalist id="curpList"></datalist>
                    <small id="curpError" style="color: red; display: none;"></small>
                </div>

                <div class="form-group">
                    <label for="patientId">Número de identificación del paciente</label>
                    <input type="text" name="id_patient" id="patientId" value="<?php echo (($tmp = $_smarty_tpl->getValue('appointment')['id_patient'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" required>
                </div>

                <div class="form-group">
                    <label for="name">Nombre del paciente</label>
                    <input type="text" id="patientName" placeholder="Nombre completo" value="<?php echo (($tmp = $_smarty_tpl->getValue('appointment')['patient_name'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
 <?php echo (($tmp = $_smarty_tpl->getValue('appointment')['last_name'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
 <?php echo (($tmp = $_smarty_tpl->getValue('appointment')['last_name2'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" disabled></input>
                </div>

                <div class="form-group">
                    <label for="speciality">Especialidad</label>
                    <select name="id_medical_area" id="speciality" required>
                        <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('medical_areas'), 'area');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('area')->value) {
$foreach0DoElse = false;
?>
                            <option value="<?php echo $_smarty_tpl->getValue('area')['id'];?>
">
                                <?php echo $_smarty_tpl->getValue('area')['name'];?>

                            </option>
                        <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                    </select>
                </div>


                <div class="form-group">
                    <label for="speciality">Médico</label>
                    <select name="id_doctor" id="doctor" required>
                        <option value="">Seleccione un médico</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="appointmentDate">Hora y Fecha</label>
                    <input type="text" id="appointmentDate" name="appointment_date" required>
                    <span id="dateError" style="color:red; display:none;">La fecha/hora no está en el horario del
                        doctor</span>
                </div>

                <button type="submit" class="submit-btn">Registrar Datos</button>
            </form>
        </div>
    </div>

</body>

</html><?php }
}
