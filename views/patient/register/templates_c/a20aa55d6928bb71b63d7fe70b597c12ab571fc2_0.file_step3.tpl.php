<?php
/* Smarty version 5.4.5, created on 2025-06-01 05:31:22
  from 'file:steps/step3.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_683be5aab6d501_26996037',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a20aa55d6928bb71b63d7fe70b597c12ab571fc2' => 
    array (
      0 => 'steps/step3.tpl',
      1 => 1748755867,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_683be5aab6d501_26996037 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/var/www/html/views/patient/register/steps';
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
            <option value="Soltero(a)" <?php if ((true && ($_smarty_tpl->hasVariable('patient') && null !== ($_smarty_tpl->getValue('patient') ?? null))) && $_smarty_tpl->getValue('patient')['marital_status'] == "Soltero(a)") {?>selected<?php }?>>Soltero(a)</option>
            <option value="Casado(a)" <?php if ((true && ($_smarty_tpl->hasVariable('patient') && null !== ($_smarty_tpl->getValue('patient') ?? null))) && $_smarty_tpl->getValue('patient')['marital_status'] == "Casado(a)") {?>selected<?php }?>>Casado(a)</option>
            <option value="Viudo(a)" <?php if ((true && ($_smarty_tpl->hasVariable('patient') && null !== ($_smarty_tpl->getValue('patient') ?? null))) && $_smarty_tpl->getValue('patient')['marital_status'] == "Viudo(a)") {?>selected<?php }?>>Viudo(a)</option>
            <option value="Unión libre" <?php if ((true && ($_smarty_tpl->hasVariable('patient') && null !== ($_smarty_tpl->getValue('patient') ?? null))) && $_smarty_tpl->getValue('patient')['marital_status'] == "Unión libre") {?>selected<?php }?>>Unión libre</option>
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
</div>
<?php }
}
