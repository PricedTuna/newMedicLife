<?php
/* Smarty version 5.4.5, created on 2025-05-14 19:40:22
  from 'file:steps/step4.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_6824f1a62f2c73_58377957',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4504de774c28f8c0c8b52c800fb2a4b07e5428b9' => 
    array (
      0 => 'steps/step4.tpl',
      1 => 1747251579,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6824f1a62f2c73_58377957 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/angel/Desktop/newMedicLife/views/patient/register/steps';
?><div class="form-step" id="step-4" style="display: none;">
    <div class="form-group">
        <label for="contactFirstName">Nombre del Contacto</label>
        <input type="text" id="contactFirstName"  required>
    </div>
    <div class="form-group">
        <label for="contactLastName">Apellido Paterno</label>
        <input type="text" id="contactLastName" required>
    </div>
    <div class="form-group">
        <label for="contactMotherLastName">Apellido Materno</label>
        <input type="text" id="contactMotherLastName" required>
    </div>
    <div class="form-group">
        <label for="contactPhone">Número Telefónico</label>
        <input type="number" id="contactPhone" required>
    </div>
    <div class="form-group">
        <label for="contactRelation">Relación con el Paciente</label>
        <input type="text" id="contactRelation" required>
    </div>
    <button type="button" class="prev-btn" onclick="prevStep(3)">Atrás</button>
    <button type="submit" class="submit-btn">Registrar</button>
</div>
<?php }
}
