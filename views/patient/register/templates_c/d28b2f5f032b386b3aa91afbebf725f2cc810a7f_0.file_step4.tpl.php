<?php
/* Smarty version 5.4.5, created on 2025-06-01 23:46:16
  from 'file:steps/step4.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_683ce6482e81b1_83646311',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd28b2f5f032b386b3aa91afbebf725f2cc810a7f' => 
    array (
      0 => 'steps/step4.tpl',
      1 => 1748820898,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_683ce6482e81b1_83646311 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/var/www/html/views/patient/register/steps';
?><input type="hidden" name="emergency_contacts_id" value="<?php echo (($tmp = $_smarty_tpl->getValue('emergencyContacts')['id'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
">

<div class="form-step" id="step-4">
    <h2 class="formSubtitle">Contacto de emergencía</h1>
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
