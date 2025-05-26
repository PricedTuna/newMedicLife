<?php
/* Smarty version 5.4.5, created on 2025-05-19 01:48:58
  from 'file:steps/step3.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_682a8e0ad102f9_98268808',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '814737f791cf58065a3b9f066fd459e730fdd1b2' => 
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
function content_682a8e0ad102f9_98268808 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/angel/Desktop/mediclife/newMedicLife/views/doctor/register/steps';
?><!-- Paso 3 -->
<div class="form-step" id="step-3" style="display: none;">
    <div class="form-group">
        <label for="curp">CURP</label>
        <input type="text" id="curp" name="curp" value="<?php echo (($tmp = $_smarty_tpl->getValue('doctor')['CURP'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" required>
    </div>
    <div class="form-group">
        <label for="rfc">RFC</label>
        <input type="text" id="rfc" name="rfc" value="<?php echo (($tmp = $_smarty_tpl->getValue('doctor')['RFC'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" required>
    </div>
    <div class="form-group">
        <label for="affiliationNumber">Número de Afiliación</label>
        <input type="text" id="affiliationNumber" name="affiliationNumber" value="<?php echo (($tmp = $_smarty_tpl->getValue('doctor')['insurance_number'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" required>
    </div>
    <div class="form-group">
        <label for="professionalLicense">Cédula Profesional</label>
        <input type="text" id="professionalLicense" name="professionalLicense" value="<?php echo (($tmp = $_smarty_tpl->getValue('doctor')['professional_id'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" required>
    </div>

    <div class="form-group">
        <label for="speciality">Especialidad</label>
        <select name="medical_area" id="speciality" required>
            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('medical_areas'), 'medical_area');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('medical_area')->value) {
$foreach1DoElse = false;
?>
                <option value="<?php echo $_smarty_tpl->getValue('medical_area')['id'];?>
" <?php if ($_smarty_tpl->getValue('doctor')['medical_area'] == $_smarty_tpl->getValue('medical_area')['id']) {?>selected<?php }?>>
                    <?php echo $_smarty_tpl->getValue('medical_area')['name'];?>

                </option>
            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
        </select>
    </div>

    <div class="form-group">
        <label for="photo" class="file-label" id="photo-label">Subir Foto</label>
        <input type="file" id="photo" name="photo" accept="image/*" <?php if (!$_smarty_tpl->getValue('doctor')) {?>required<?php }?>>
    </div>

    <button type="button" class="prev-btn" onclick="prevStep(2)">Atrás</button>
    <button type="submit" class="submit-btn">
        <?php if ($_smarty_tpl->getValue('doctor')) {?>Actualizar<?php } else { ?>Registrar<?php }?>
    </button>
    <input type="hidden" name="id" value="<?php echo (($tmp = $_smarty_tpl->getValue('doctor')['id'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
">
</div>
<?php }
}
