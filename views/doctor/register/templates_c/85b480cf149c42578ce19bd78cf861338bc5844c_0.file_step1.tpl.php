<?php
/* Smarty version 5.4.5, created on 2025-05-27 00:04:15
  from 'file:steps/step1.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_6834e55fdf0fb7_55031591',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '85b480cf149c42578ce19bd78cf861338bc5844c' => 
    array (
      0 => 'steps/step1.tpl',
      1 => 1748231918,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6834e55fdf0fb7_55031591 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = 'C:\\proyectos\\mediclife\\newMedicLife\\views\\doctor\\register\\steps';
?><!-- Paso 1 -->
<input type="hidden" name="doctor_id" value="<?php echo (($tmp = $_smarty_tpl->getValue('doctor')['id'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
">

<div class="form-step" id="step-1">

    <div class="form-group">
        <label for="lastName">Apellido Paterno</label>
        <input type="text" id="lastName" name="fatherLastName" value="<?php echo (($tmp = $_smarty_tpl->getValue('doctor')['last_name'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" required>
    </div>
    <div class="form-group">
        <label for="motherLastName">Apellido Materno</label>
        <input type="text" id="motherLastName" name="motherLastName" value="<?php echo (($tmp = $_smarty_tpl->getValue('doctor')['last_name2'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" required>
    </div>
    <div class="form-group">
        <label for="firstName">Nombre</label>
        <input type="text" id="firstName" name="name" value="<?php echo (($tmp = $_smarty_tpl->getValue('doctor')['names'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" required>
    </div>
    <div class="form-group">
        <label for="phoneNumber">Número Telefónico</label>
        <input type="number" id="phoneNumber" name="phoneNumber" value="<?php echo (($tmp = $_smarty_tpl->getValue('doctor')['phone'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" required>
    </div>
    <div class="form-group">
        <label for="email">Correo Electrónico</label>
        <input type="email" id="email" name="email" value="<?php echo (($tmp = $_smarty_tpl->getValue('doctor')['email'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" required>
    </div>
    <div class="form-group">
        <label for="gender">Sexo</label>
        <select id="gender" name="gender" required>
            <option value="">Seleccione...</option>
            <option value="M" <?php if ($_smarty_tpl->getValue('doctor')['gender'] == 'M') {?>selected<?php }?>>Masculino</option>
            <option value="F" <?php if ($_smarty_tpl->getValue('doctor')['gender'] == 'F') {?>selected<?php }?>>Femenino</option>
        </select>
    </div>
    <div class="form-group">
        <label for="birthDate">Fecha de Nacimiento</label>
        <input type="date" id="birthDate" name="birthDate" value="<?php echo (($tmp = $_smarty_tpl->getValue('doctor')['birth_date'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" required>
    </div>
    <button type="button" class="next-btn" onclick="nextStep(2)">Siguiente</button>
</div>
<?php }
}
