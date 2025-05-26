<?php
/* Smarty version 5.4.5, created on 2025-05-27 00:04:15
  from 'file:steps/step2.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_6834e55ff28198_00985853',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'bf91bd9e132431ab44546b3ed72558e9f850884b' => 
    array (
      0 => 'steps/step2.tpl',
      1 => 1748231918,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6834e55ff28198_00985853 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = 'C:\\proyectos\\mediclife\\newMedicLife\\views\\doctor\\register\\steps';
?><!-- Paso 2 -->
<div class="form-step" id="step-2" style="display: none;">
    <div class="form-group">
        <label for="street">Calle</label>
        <input type="text" id="street" name="street" value="<?php echo (($tmp = $_smarty_tpl->getValue('doctor')['street'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" required>
    </div>
    <div class="form-group">
        <label for="neighborhood">Colonia</label>
        <input type="text" id="neighborhood" name="neighborhood" value="<?php echo (($tmp = $_smarty_tpl->getValue('doctor')['neighborhood'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" required>
    </div>
    <div class="form-group">
        <label for="postalCode">Código Postal</label>
        <input type="number" id="postalCode" name="postalCode" value="<?php echo (($tmp = $_smarty_tpl->getValue('doctor')['CP'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" required>
    </div>
    <div class="form-group">
        <label for="extNumber">Número Exterior</label>
        <input type="text" id="extNumber" name="extNumber" value="<?php echo (($tmp = $_smarty_tpl->getValue('doctor')['external_number'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" required>
    </div>
    <div class="form-group">
        <label for="intNumber">Número Interior (Opcional)</label>
        <input type="text" id="intNumber" name="intNumber" value="<?php echo (($tmp = $_smarty_tpl->getValue('doctor')['internal_number'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
">
    </div>
    <div class="form-group">
        <label for="state">Estado</label>
        <select name="state" id="state" required>
            <option value="">Seleccione...</option>
            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('states'), 'state');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('state')->value) {
$foreach0DoElse = false;
?>
                <option value="<?php echo $_smarty_tpl->getValue('state')['id'];?>
" <?php if ($_smarty_tpl->getValue('doctor')['state'] == $_smarty_tpl->getValue('state')['id']) {?>selected<?php }?>>
                    <?php echo $_smarty_tpl->getValue('state')['name'];?>

                </option>
            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
        </select>
    </div>
    <div class="form-group">
        <label for="municipality">Municipio</label>
        <select name="municipality" id="municipality" required>
            <option value="">Seleccione un estado primero...</option>
        </select>
    </div>
    <div class="form-group">
        <label for="locality">Localidad</label>
        <select name="locality" id="locality" required>
            <option value="">Seleccione un municipio primero...</option>
        </select>
    </div>

    <button type="button" class="prev-btn" onclick="prevStep(1)">Atrás</button>
    <button type="button" class="next-btn" onclick="nextStep(3)">Siguiente</button>
</div>
<?php }
}
