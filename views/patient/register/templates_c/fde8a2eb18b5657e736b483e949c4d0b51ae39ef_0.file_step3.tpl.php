<?php
/* Smarty version 5.4.5, created on 2025-05-18 21:05:22
  from 'file:steps/step3.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_682a4b92c83ca7_94308619',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fde8a2eb18b5657e736b483e949c4d0b51ae39ef' => 
    array (
      0 => 'steps/step3.tpl',
      1 => 1747601896,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_682a4b92c83ca7_94308619 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/angel/Desktop/mediclife/newMedicLife/views/patient/register/steps';
?><div class="form-step" id="step-3" style="display: none;">
    <div class="form-group">
        <label for="bloodType">Tipo de Sangre</label>
        <select id="bloodType" name="blood_type" required>
            <option value="">Seleccione...</option>
            <option value="A+" <?php if ((true && ($_smarty_tpl->hasVariable('patient') && null !== ($_smarty_tpl->getValue('patient') ?? null))) && $_smarty_tpl->getValue('patient')['blood_type'] == 'A+') {?>selected<?php }?>>A+</option>
            <option value="A-" <?php if ((true && ($_smarty_tpl->hasVariable('patient') && null !== ($_smarty_tpl->getValue('patient') ?? null))) && $_smarty_tpl->getValue('patient')['blood_type'] == 'A-') {?>selected<?php }?>>A-</option>
            <option value="B+" <?php if ((true && ($_smarty_tpl->hasVariable('patient') && null !== ($_smarty_tpl->getValue('patient') ?? null))) && $_smarty_tpl->getValue('patient')['blood_type'] == 'B+') {?>selected<?php }?>>B+</option>
            <option value="B-" <?php if ((true && ($_smarty_tpl->hasVariable('patient') && null !== ($_smarty_tpl->getValue('patient') ?? null))) && $_smarty_tpl->getValue('patient')['blood_type'] == 'B-') {?>selected<?php }?>>B-</option>
            <option value="AB+" <?php if ((true && ($_smarty_tpl->hasVariable('patient') && null !== ($_smarty_tpl->getValue('patient') ?? null))) && $_smarty_tpl->getValue('patient')['blood_type'] == 'AB+') {?>selected<?php }?>>AB+</option>
            <option value="AB-" <?php if ((true && ($_smarty_tpl->hasVariable('patient') && null !== ($_smarty_tpl->getValue('patient') ?? null))) && $_smarty_tpl->getValue('patient')['blood_type'] == 'AB-') {?>selected<?php }?>>AB-</option>
            <option value="O+" <?php if ((true && ($_smarty_tpl->hasVariable('patient') && null !== ($_smarty_tpl->getValue('patient') ?? null))) && $_smarty_tpl->getValue('patient')['blood_type'] == 'O+') {?>selected<?php }?>>O+</option>
            <option value="O-" <?php if ((true && ($_smarty_tpl->hasVariable('patient') && null !== ($_smarty_tpl->getValue('patient') ?? null))) && $_smarty_tpl->getValue('patient')['blood_type'] == 'O-') {?>selected<?php }?>>O-</option>
        </select>
    </div>
    <div class="form-group">
        <label for="maritalStatus">Estado Civil</label>
        <select id="maritalStatus" name="marital_status" required>
            <option value="">Seleccione...</option>
            <option>Soltero(a)</option>
            <option>Casado(a)</option>
            <option>Viudo(a)</option>
            <option>Unión libre</option>
        </select>
    </div>
    <div class="form-group">
        <label for="weight">Peso (kg)</label>
        <input type="number" id="weight" value="<?php echo (($tmp = $_smarty_tpl->getValue('patient')['weight'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" name="weight" required>
    </div>
    <div class="form-group">
        <label for="height">Altura (cm)</label>
        <input type="number" id="height" value="<?php echo (($tmp = $_smarty_tpl->getValue('patient')['height'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" name="height" required>
    </div>
    <div class="form-group">
        <label for="ethnicGroup">Grupo Étnico</label>
        <input type="text" id="ethnicGroup" value="<?php echo (($tmp = $_smarty_tpl->getValue('patient')['ethnic_group'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" name="ethnic_group">
    </div>
    <div class="form-group">
        <label for="religion">Religión</label>
        <input type="text" id="religion" value="<?php echo (($tmp = $_smarty_tpl->getValue('patient')['religion'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" name="religion">
    </div>
    <button type="button" class="prev-btn" onclick="prevStep(2)">Atrás</button>
    <button type="button" class="next-btn" onclick="nextStep(4)">Siguiente</button>
</div><?php }
}
