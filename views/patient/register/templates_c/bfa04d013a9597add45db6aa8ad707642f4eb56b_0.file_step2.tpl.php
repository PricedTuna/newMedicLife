<?php
/* Smarty version 5.4.5, created on 2025-04-27 06:48:20
  from 'file:steps/step2.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_680dd3344ba692_91355554',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'bfa04d013a9597add45db6aa8ad707642f4eb56b' => 
    array (
      0 => 'steps/step2.tpl',
      1 => 1745736418,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_680dd3344ba692_91355554 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/home/angel/Desktop/newMedicLife/views/patient/register/steps';
?><div class="form-step" id="step-2" style="display: none;">
    <div class="form-group">
        <label for="street">Calle</label>
        <input type="text" id="street" name="street" required>
    </div>
    <div class="form-group">
        <label for="neighborhood">Colonia</label>
        <input type="text" id="neighborhood" name="neighborhood" required>
    </div>
    <div class="form-group">
        <label for="postalCode">Código Postal</label>
        <input type="number" id="postalCode" name="CP" required>
    </div>
    <div class="form-group">
        <label for="extNumber">Número Exterior</label>
        <input type="text" id="extNumber" name="external_number" required>
    </div>
    <div class="form-group">
        <label for="extNumber">Número Interior</label>
        <input type="text" id="extNumber" name="internal_number" required>
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
