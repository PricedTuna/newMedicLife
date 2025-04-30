<?php
/* Smarty version 5.4.5, created on 2025-04-27 06:48:20
  from 'file:steps/step3.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_680dd3344be5d9_19443127',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1b7ecf9d21f285262f1c9a7da014e1c69f2a8a49' => 
    array (
      0 => 'steps/step3.tpl',
      1 => 1745736428,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_680dd3344be5d9_19443127 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/angel/Desktop/newMedicLife/views/patient/register/steps';
?><div class="form-step" id="step-3" style="display: none;">
    <div class="form-group">
        <label for="bloodType">Tipo de Sangre</label>
        <select id="bloodType" name="blood_type" required>
            <option value="">Seleccione...</option>
            <option value="A+">A+</option>
            <option value="A-">A-</option>
            <option value="B+">B+</option>
            <option value="B-">B-</option>
            <option value="AB+">AB+</option>
            <option value="AB-">AB-</option>
            <option value="O+">O+</option>
            <option value="O-">O-</option>
        </select>
    </div>
    <div class="form-group">
        <label for="maritalStatus">Estado Civil</label>
        <select id="maritalStatus" name="marital_status" required>
            <option value="">Seleccione...</option>
            <option value="Soltero(a)">Soltero/a</option>
            <option value="Casado(a)">Casado/a</option>
            <option value="Viudo(a)">Viudo/a</option>
            <option value="Unión libre">Unión libre</option>
        </select>
    </div>
    <div class="form-group">
        <label for="weight">Peso (kg)</label>
        <input type="number" id="weight" name="weight" required>
    </div>
    <div class="form-group">
        <label for="height">Altura (cm)</label>
        <input type="number" id="height" name="height" required>
    </div>
    <div class="form-group">
        <label for="ethnicGroup">Grupo Étnico</label>
        <input type="text" id="ethnicGroup" name="ethnic_group">
    </div>
    <div class="form-group">
        <label for="religion">Religión</label>
        <input type="text" id="religion" name="religion">
    </div>
    <button type="button" class="prev-btn" onclick="prevStep(2)">Atrás</button>
    <button type="button" class="next-btn" onclick="nextStep(4)">Siguiente</button>
</div>
<?php }
}
