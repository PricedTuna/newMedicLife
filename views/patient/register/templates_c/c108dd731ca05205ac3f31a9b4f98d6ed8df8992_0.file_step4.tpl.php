<?php
/* Smarty version 5.4.5, created on 2025-05-18 21:05:22
  from 'file:steps/step4.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_682a4b92c8d152_30606136',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c108dd731ca05205ac3f31a9b4f98d6ed8df8992' => 
    array (
      0 => 'steps/step4.tpl',
      1 => 1747601896,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_682a4b92c8d152_30606136 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/angel/Desktop/mediclife/newMedicLife/views/patient/register/steps';
?><input type="hidden" name="emergency_contacts_id" value="<?php echo (($tmp = $_smarty_tpl->getValue('emergencyContacts')['id'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
">

<div class="form-step" id="step-4">
    <div class="form-group">
        <label for="contactFirstName">Nombre del Contacto</label>
        <input type="text" id="contactFirstName"  value="<?php echo (($tmp = $_smarty_tpl->getValue('emergencyContacts')['names'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" name="ec_name" required>
    </div>
    <div class="form-group">
        <label for="contactLastName">Apellido Paterno</label>
        <input type="text" id="contactLastName"  value="<?php echo (($tmp = $_smarty_tpl->getValue('emergencyContacts')['last_name'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" name="ec_fatherLastName" required>
    </div>
    <div class="form-group">
        <label for="contactMotherLastName">Apellido Materno</label>
        <input type="text" id="contactMotherLastName"  value="<?php echo (($tmp = $_smarty_tpl->getValue('emergencyContacts')['last_name2'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" name="ec_motherLastName" required>
    </div>
    <div class="form-group">
        <label for="contactPhone">Número Telefónico</label>
        <input type="number" id="contactPhone"  value="<?php echo (($tmp = $_smarty_tpl->getValue('emergencyContacts')['phone'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" name="ec_phoneNumber" required>
    </div>
    <div class="form-group">
        <label for="contactRelation">Relación con el Paciente</label>
        <input type="text" id="contactRelation"  value="<?php echo (($tmp = $_smarty_tpl->getValue('emergencyContacts')['relationship'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" name="ec_relationship" required>
    </div>
    <button type="button" class="prev-btn" onclick="prevStep(3)">Atrás</button>
    <button type="submit" class="submit-btn">Registrar</button>
</div>
    <?php }
}
