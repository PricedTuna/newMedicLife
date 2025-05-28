<?php
/* Smarty version 5.4.5, created on 2025-05-26 11:41:22
  from 'file:steps/step2.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_683437423f8434_87913064',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7fc161ee647f391be9f37db7ce6a8468a9e13fa0' => 
    array (
      0 => 'steps/step2.tpl',
      1 => 1748132536,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_683437423f8434_87913064 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = 'C:\\proyectos\\mediclife\\newMedicLife\\views\\patient\\register\\steps';
?><div class="form-step" id="step-2" style="display: none;">
    <div class="form-group">
        <label for="street">Calle</label>
        <input type="text" id="street" value="<?php echo (($tmp = $_smarty_tpl->getValue('patient')['street'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" name="street" required>
    </div>
    <div class="form-group">
        <label for="neighborhood">Colonia</label>
        <input type="text" id="neighborhood" value="<?php echo (($tmp = $_smarty_tpl->getValue('patient')['neighborhood'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" name="neighborhood" required>
    </div>
    <div class="form-group">
        <label for="postalCode">Código Postal</label>
        <input type="number" id="postalCode" value="<?php echo (($tmp = $_smarty_tpl->getValue('patient')['CP'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" name="postalCode" required>
    </div>
    <div class="form-group">
        <label for="extNumber">Número Exterior</label>
        <input type="text" id="extNumber" value="<?php echo (($tmp = $_smarty_tpl->getValue('patient')['external_number'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" name="extNumber" required>
    </div>
    <div class="form-group">
        <label for="intNumber">Número Interior</label>
        <input type="text" id="extNumber" value="<?php echo (($tmp = $_smarty_tpl->getValue('patient')['internal_number'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
" name="intNumber" required>
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
"><?php echo $_smarty_tpl->getValue('state')['name'];?>
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
