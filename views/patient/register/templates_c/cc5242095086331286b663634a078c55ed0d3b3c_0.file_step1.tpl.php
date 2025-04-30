<?php
/* Smarty version 5.4.5, created on 2025-04-27 06:48:20
  from 'file:steps/step1.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_680dd3344af0f6_02995560',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cc5242095086331286b663634a078c55ed0d3b3c' => 
    array (
      0 => 'steps/step1.tpl',
      1 => 1745736498,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_680dd3344af0f6_02995560 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/angel/Desktop/newMedicLife/views/patient/register/steps';
?><div class="form-step" id="step-1">
    <div class="form-group">
        <label for="lastName">Apellido Paterno</label>
        <input type="text" id="lastName" name="last_name" required>
    </div>
    <div class="form-group">
        <label for="motherLastName">Apellido Materno</label>
        <input type="text" id="motherLastName" name="last_name2" required>
    </div>
    <div class="form-group">
        <label for="firstName">Nombre</label>
        <input type="text" id="firstName" name="names" required>
    </div>
    <div class="form-group">
        <label for="phoneNumber">Número Telefónico</label>
        <input type="number" id="phoneNumber" name="phone" required>
    </div>
    <div class="form-group">
        <label for="email">Correo Electrónico</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div class="form-group">
        <label for="gender">Sexo</label>
        <select id="gender" name="gender" required>
            <option value="">Seleccione...</option>
            <option value="M">Masculino</option>
            <option value="F">Femenino</option>
        </select>
    </div>
    <div class="form-group">
        <label for="birthDate">Fecha de Nacimiento</label>
        <input type="date" id="birthDate" name="birth_date" required>
    </div>
    <div class="form-group">
        <label for="curp">CURP</label>
        <input type="text" id="curp" name="CURP" required>
    </div>
    <div class="form-group">
        <label for="rfc">RFC</label>
        <input type="text" id="rfc" name="RFC" required>
    </div>
    <div class="form-group">
        <label for="affiliationNumber">Número de Afiliación</label>
        <input type="text" id="affiliationNumber" name="insurance_number" required>
    </div>
    <button type="button" class="next-btn" onclick="nextStep(2)">Siguiente</button>
</div>
<?php }
}
