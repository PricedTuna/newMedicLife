<?php
/* Smarty version 5.4.5, created on 2025-05-18 21:05:22
  from 'file:steps/step1.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_682a4b92c6c3e2_84399288',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '42805e6dfe39fb526a61a9d625e84551e849cf26' => 
    array (
      0 => 'steps/step1.tpl',
      1 => 1747601896,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_682a4b92c6c3e2_84399288 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/angel/Desktop/mediclife/newMedicLife/views/patient/register/steps';
?><input type="hidden" name="patient_id" value="<?php echo (($tmp = $_smarty_tpl->getValue('patient')['id'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
">

<div class="form-step" id="step-1">
    <div class="form-group">
        <label for="lastName">Apellido Paterno</label>
        <input type="text" id="lastName" value="<?php echo (($tmp = $_smarty_tpl->getValue('patient')['last_name'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" name="fatherLastName" required>
    </div>
    <div class="form-group">
        <label for="motherLastName">Apellido Materno</label>
        <input type="text" id="motherLastName" value="<?php echo (($tmp = $_smarty_tpl->getValue('patient')['last_name2'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" name="motherLastName" required>
    </div>
    <div class="form-group">
        <label for="firstName">Nombre</label>
        <input type="text" id="firstName" value="<?php echo (($tmp = $_smarty_tpl->getValue('patient')['names'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" name="name" required>
    </div>
    <div class="form-group">
        <label for="phoneNumber">Número Telefónico</label>
        <input type="number" id="phoneNumber" value="<?php echo (($tmp = $_smarty_tpl->getValue('patient')['phone'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" name="phoneNumber" required>
    </div>
    <div class="form-group">
        <label for="email">Correo Electrónico</label>
        <input type="email" id="email" value="<?php echo (($tmp = $_smarty_tpl->getValue('patient')['email'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" name="email" required>
    </div>
    <div class="form-group">
        <label for="gender">Sexo</label>
        <select id="gender" name="gender" required>
            <option value="">Seleccione...</option>
            <option value="M" <?php if ((true && ($_smarty_tpl->hasVariable('patient') && null !== ($_smarty_tpl->getValue('patient') ?? null))) && $_smarty_tpl->getValue('patient')['gender'] == 'M') {?>selected<?php }?>>Masculino</option>
            <option value="F" <?php if ((true && ($_smarty_tpl->hasVariable('patient') && null !== ($_smarty_tpl->getValue('patient') ?? null))) && $_smarty_tpl->getValue('patient')['gender'] == 'F') {?>selected<?php }?>>Femenino</option>
        </select>
    </div>
    <div class="form-group">
        <label for="birthDate">Fecha de Nacimiento</label>
        <input type="date" id="birthDate" value="<?php echo (($tmp = $_smarty_tpl->getValue('patient')['birth_date'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" name="birthDate" required>
    </div>
    <div class="form-group">
        <label for="curp">CURP</label>
        <input type="text" id="curp" value="<?php echo (($tmp = $_smarty_tpl->getValue('patient')['CURP'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" name="curp" required>
    </div>
    <div class="form-group">
        <label for="rfc">RFC</label>
        <input type="text" id="rfc" value="<?php echo (($tmp = $_smarty_tpl->getValue('patient')['RFC'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" name="rfc" required>
    </div>
    <div class="form-group">
        <label for="affiliationNumber">Número de Afiliación</label>
        <input type="text" id="affiliationNumber" value="<?php echo (($tmp = $_smarty_tpl->getValue('patient')['insurance_number'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" name="affiliationNumber" required>
    </div>
    <button type="button" class="next-btn" onclick="nextStep(2)">Siguiente</button>
</div><?php }
}
