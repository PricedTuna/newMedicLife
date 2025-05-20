<?php
/* Smarty version 5.4.5, created on 2025-05-20 09:17:32
  from 'file:register-appoiment.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_682c48acde4771_96176990',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9040ae77ec06e831c4f7558815ec3d0cd6022540' => 
    array (
      0 => 'register-appoiment.tpl',
      1 => 1747732651,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_682c48acde4771_96176990 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/angel/Desktop/mediclife/newMedicLife/views/appointment/register';
?><!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./register-appoiment.css">
    <title>Solicitar Cita</title>

    <?php echo '<script'; ?>
>
        window.doctors = <?php echo json_encode($_smarty_tpl->getValue('doctors'));?>
;
        window.patients= <?php echo json_encode($_smarty_tpl->getValue('patients'));?>
;
    <?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="/views/appointment/register/register-appoiment.js"><?php echo '</script'; ?>
>

</head>

<body>

    <div class="center-container">
        <div class="form-container">
            <h2>Solicitar Citas</h2>
            <form id="solicitarCita" method="POST" action="/controllers/appoiment/register-appoiment.controller.php">
                <div class="form-group">
                    <label for="curp">CURP</label>
                    <input type="text" name="CURP" id="CURP" required>
                    <small id="curpError" style="color: red; display: none;">CURP inválido</small>
                </div>

                <div class="form-group">
                    <label for="patientId">Número de identificación del paciente</label>
                    <input type="text" name="patientId" id="patientId" required>
                </div>

                <div class="form-group">
                    <label for="name">Nombre del paciente</label>
                    <input type="text" id="patientName" placeholder="Nombre completo" disabled></input>
                </div>

                <div class="form-group">
                    <label for="speciality">Especialidad</label>
                    <select name="medical_area" id="speciality" required>
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
                    <select name="doctor" id="doctor" required>
                        <option value="">Seleccione un médico</option>

                    </select>
                </div>

                <div class="form-group">
                    <label for="appointmentDate">Hora y Fecha</label>
                    <input type="datetime-local" name="appointmentDate" required>
                </div>

                <button type="submit" class="submit-btn">Registrar Datos</button>
            </form>
        </div>
    </div>

</body>

</html><?php }
}
