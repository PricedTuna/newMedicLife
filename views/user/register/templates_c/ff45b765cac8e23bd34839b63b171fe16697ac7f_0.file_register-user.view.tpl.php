<?php
/* Smarty version 5.4.5, created on 2025-05-26 02:23:06
  from 'file:register-user.view.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_6833d08a450838_76211589',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ff45b765cac8e23bd34839b63b171fe16697ac7f' => 
    array (
      0 => 'register-user.view.tpl',
      1 => 1748226156,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6833d08a450838_76211589 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/var/www/html/views/user/register';
?><!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo '<script'; ?>
 src="/views/components/sidebar.app.js" defer><?php echo '</script'; ?>
>
    <link rel="stylesheet" href="./register-user.styles.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <title>Registro de Usuarios</title>
</head>

<body>

    <?php $_smarty_tpl->renderSubTemplate($_smarty_tpl->getValue('sidebarPath'), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?> <!-- Aquí se incluye el sidebar, según la variable Smarty -->

    <main class="content">

        <div class="center-container">
            <div class="form-container">
                <div class="form-header">
                    <a href="/views/dashboard/dashboard.view.php" class="form-back-btn">
                        <button class="back-btn">Volver</button>
                        <span class="back-btn-icon">&#8617;</span>
                    </a>
                    <h2 class="form-title">Registrar usuario</h2>
                </div>

                <!-- Si hay un error, lo mostramos aquí -->
                <?php if ((true && ($_smarty_tpl->hasVariable('error') && null !== ($_smarty_tpl->getValue('error') ?? null)))) {?>
                    <div
                        style="color: red; margin-bottom: 1rem; border: 1px solid red; padding: 0.5rem; border-radius: 5px;">
                        <?php echo htmlspecialchars((string)$_smarty_tpl->getValue('error'), ENT_QUOTES, 'UTF-8', true);?>

                    </div>
                <?php }?>

                <!-- Formulario para registrar usuario -->
                <form action="/controllers/auth/register.controller.php" method="POST" id="user-form">
                    <div class="form-group">
                        <label for="role">Rol</label>
                        <select id="role" name="role" required>
                            <option value="S">Secretaria</option>
                            <option value="A">Administrador</option>
                            <option value="D">Doctor</option>
                      </select>
                    </div>

                    <div class="form-group" id="doctor-select-container" style="display: none;">
                        <label for="id_doctor">Seleccionar doctor</label>
                        <select id="id_doctor" name="id_doctor">
                            <option value="">Seleccione un doctor</option>
                            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('doctors'), 'doctor');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('doctor')->value) {
$foreach0DoElse = false;
?>
                                <option value="<?php echo $_smarty_tpl->getValue('doctor')['id'];?>
"><?php echo $_smarty_tpl->getValue('doctor')['names'];?>
 <?php echo $_smarty_tpl->getValue('doctor')['last_name'];?>
 <?php echo $_smarty_tpl->getValue('doctor')['last_name2'];?>
 - <?php echo $_smarty_tpl->getValue('doctor')['email'];?>
</option>
                            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Nombre completo</label>
                        <input type="text" id="name" name="name" placeholder="Nombre completo" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" placeholder="Correo electrónico" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="Contraseña" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirmar Contraseña</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmar contraseña" required>
                    </div>
                    <button type="submit" class="submit-btn">Registrar</button>

                    <?php echo '<script'; ?>
>
                        document.addEventListener('DOMContentLoaded', function() {
                            const roleSelect = document.getElementById('role');
                            const doctorSelectContainer = document.getElementById('doctor-select-container');
                            const doctorSelect = document.getElementById('id_doctor');
                            const nameInput = document.getElementById('name');
                            const emailInput = document.getElementById('email');

                            // Función para mostrar/ocultar el selector de doctores
                            function toggleDoctorSelect() {
                                if (roleSelect.value === 'D') {
                                    doctorSelectContainer.style.display = 'flex';
                                    doctorSelect.required = true;
                                } else {
                                    doctorSelectContainer.style.display = 'none';
                                    doctorSelect.required = false;
                                    doctorSelect.value = '';
                                }
                            }

                            // Inicializar el estado
                            toggleDoctorSelect();

                            // Escuchar cambios en el selector de rol
                            roleSelect.addEventListener('change', toggleDoctorSelect);

                            // Cuando se selecciona un doctor, autocompletar nombre y email
                            doctorSelect.addEventListener('change', function() {
                                if (this.value && roleSelect.value === 'D') {
                                    const selectedOption = this.options[this.selectedIndex];
                                    const doctorInfo = selectedOption.text.split(' - ');
                                    if (doctorInfo.length === 2) {
                                        nameInput.value = doctorInfo[0];
                                        emailInput.value = doctorInfo[1];

                                        nameInput.readOnly = true;
                                        emailInput.readOnly = true;

                                        nameInput.classList.add('input-disabled');
                                        emailInput.classList.add('input-disabled');
                                    }
                                } else {
                                    nameInput.disabled = false;
                                    emailInput.disabled = false;

                                    nameInput.value = '';
                                    emailInput.value = '';

                                    nameInput.classList.remove('input-disabled');
                                    emailInput.classList.remove('input-disabled');
                                }
                            });
                        });
                    <?php echo '</script'; ?>
>
                </form>
            </div>
        </div>
    </main>
</body>

</html>
<?php }
}
