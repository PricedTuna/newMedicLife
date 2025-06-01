<?php
/* Smarty version 5.4.5, created on 2025-06-01 09:49:04
  from 'file:register-user.view.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_683c221087a0f4_61272912',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ff45b765cac8e23bd34839b63b171fe16697ac7f' => 
    array (
      0 => 'register-user.view.tpl',
      1 => 1748770952,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_683c221087a0f4_61272912 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/var/www/html/views/user/register';
?><!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/forms.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <?php echo '<script'; ?>
 src="/views/components/sidebar.app.js" defer><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="https://cdn.jsdelivr.net/npm/sweetalert2@11"><?php echo '</script'; ?>
>
    <title>Registro de Usuarios</title>
</head>

<body>

    <?php $_smarty_tpl->renderSubTemplate($_smarty_tpl->getValue('sidebarPath'), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?> <!-- Aquí se incluye el sidebar, según la variable Smarty -->

    <main class="content">

        <div class="center-container">
            <div class="form-container">
                <div class="form-header">
                    <a href="/views/user/list/list-users.view.php" class="form-back-btn">
                        <button class="back-btn">Volver</button>
                        <span class="back-btn-icon">&#8617;</span>
                    </a>
                    <h2 class="form-title">
                        <?php if ($_smarty_tpl->getValue('passwordChangeMode')) {?>
                            Cambiar contraseña
                        <?php } elseif ($_smarty_tpl->getValue('editMode')) {?>
                            Actualizar usuario
                        <?php } else { ?>
                            Registrar usuario
                        <?php }?>
                    </h2>
                </div>

                <!-- Si hay un error, lo mostramos aquí -->
                <?php if ((true && ($_smarty_tpl->hasVariable('error') && null !== ($_smarty_tpl->getValue('error') ?? null)))) {?>
                    <div
                        style="color: red; margin-bottom: 1rem; border: 1px solid red; padding: 0.5rem; border-radius: 5px;">
                        <?php echo htmlspecialchars((string)$_smarty_tpl->getValue('error'), ENT_QUOTES, 'UTF-8', true);?>

                    </div>
                <?php }?>

                <!-- Si hay un mensaje de éxito, lo mostramos aquí -->
                <?php if ((true && ($_smarty_tpl->hasVariable('success') && null !== ($_smarty_tpl->getValue('success') ?? null)))) {?>
                    <div
                        style="color: darkgreen; margin-bottom: 1rem; border: 1px solid green; padding: 0.5rem; border-radius: 5px; background-color: lightgreen;">
                        <?php echo htmlspecialchars((string)$_smarty_tpl->getValue('success'), ENT_QUOTES, 'UTF-8', true);?>

                    </div>
                <?php }?>

                <!-- Formulario para registrar o actualizar usuario -->
                <form action="/controllers/auth/register.controller.php" method="POST" id="user-form">
                    <?php if ($_smarty_tpl->getValue('editMode')) {?>
                        <input type="hidden" name="user_id" value="<?php echo $_smarty_tpl->getValue('userData')['id'];?>
">
                        <input type="hidden" name="edit_mode" value="1">
                        <?php if ($_smarty_tpl->getValue('passwordChangeMode')) {?>
                        <input type="hidden" name="password_change_mode" value="1">
                        <?php }?>
                    <?php }?>
                    <?php if (!$_smarty_tpl->getValue('passwordChangeMode')) {?>
                    <div class="form-group">
                        <label for="role">Rol</label>
                        <select id="role" name="role" required>
                            <option value="S" <?php if ($_smarty_tpl->getValue('editMode') && $_smarty_tpl->getValue('userData')['role'] == 'S') {?>selected<?php }?>>Administración</option>
                            <option value="A" <?php if ($_smarty_tpl->getValue('editMode') && $_smarty_tpl->getValue('userData')['role'] == 'A') {?>selected<?php }?>>Administrador</option>
                            <option value="D" <?php if ($_smarty_tpl->getValue('editMode') && $_smarty_tpl->getValue('userData')['role'] == 'D') {?>selected<?php }?>>Doctor</option>
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
" <?php if ($_smarty_tpl->getValue('editMode') && $_smarty_tpl->getValue('userData')['id_doctor'] == $_smarty_tpl->getValue('doctor')['id']) {?>selected<?php }?>><?php echo $_smarty_tpl->getValue('doctor')['names'];?>
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
                        <input type="text" id="name" name="name" placeholder="Nombre completo" value="<?php if ($_smarty_tpl->getValue('editMode')) {
echo $_smarty_tpl->getValue('userData')['name'];
}?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" placeholder="Correo electrónico" value="<?php if ($_smarty_tpl->getValue('editMode')) {
echo $_smarty_tpl->getValue('userData')['email'];
}?>" required>
                    </div>
                    <?php } else { ?>
                    <input type="hidden" name="name" value="<?php echo $_smarty_tpl->getValue('userData')['name'];?>
">
                    <input type="hidden" name="email" value="<?php echo $_smarty_tpl->getValue('userData')['email'];?>
">
                    <input type="hidden" name="role" value="<?php echo $_smarty_tpl->getValue('userData')['role'];?>
">
                    <?php if ($_smarty_tpl->getValue('userData')['id_doctor']) {?>
                    <input type="hidden" name="id_doctor" value="<?php echo $_smarty_tpl->getValue('userData')['id_doctor'];?>
">
                    <?php }?>
                    <div class="form-group">
                        <p><strong>Usuario:</strong> <?php echo $_smarty_tpl->getValue('userData')['name'];?>
</p>
                        <p><strong>Correo:</strong> <?php echo $_smarty_tpl->getValue('userData')['email'];?>
</p>
                    </div>
                    <?php }?>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="Contraseña" <?php if (!$_smarty_tpl->getValue('editMode') || $_smarty_tpl->getValue('passwordChangeMode')) {?>required<?php }?> onblur="validatePassword()">
                        <div id="password-error" class="error-message" style="color: red; display: none;"></div>
                        <?php if ($_smarty_tpl->getValue('editMode') && !$_smarty_tpl->getValue('passwordChangeMode')) {?><small style="color: #666;">Dejar en blanco para mantener la contraseña actual</small><?php }?>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirmar Contraseña</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmar contraseña" <?php if (!$_smarty_tpl->getValue('editMode') || $_smarty_tpl->getValue('passwordChangeMode')) {?>required<?php }?> onblur="validatePasswordMatch()">
                        <div id="confirm-password-error" class="error-message" style="color: red; display: none;"></div>
                    </div>
                    <button type="submit" class="submit-btn">
                        <?php if ($_smarty_tpl->getValue('passwordChangeMode')) {?>
                            Cambiar contraseña
                        <?php } elseif ($_smarty_tpl->getValue('editMode')) {?>
                            Actualizar
                        <?php } else { ?>
                            Registrar
                        <?php }?>
                    </button>

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

                            // Si estamos en modo edición, deshabilitar el selector de rol para todos los usuarios
                            const editMode = document.querySelector('input[name="edit_mode"]');
                            if (editMode) {
                                // Deshabilitar el selector de rol para todos los usuarios en modo edición
                                roleSelect.disabled = true;
                                roleSelect.classList.add('input-disabled');

                                // Si además es un doctor y hay un doctor seleccionado, deshabilitar también el selector de doctor
                                if (roleSelect.value === 'D' && doctorSelect.value) {
                                    const selectedOption = doctorSelect.options[doctorSelect.selectedIndex];
                                    const doctorInfo = selectedOption.text.split(' - ');
                                    if (doctorInfo.length === 2) {
                                        nameInput.readOnly = true;
                                        emailInput.readOnly = true;
                                        nameInput.classList.add('input-disabled');
                                        emailInput.classList.add('input-disabled');

                                        // Deshabilitar el selector de doctor cuando se edita un usuario doctor
                                        doctorSelect.disabled = true;
                                        doctorSelect.classList.add('input-disabled');
                                    }
                                }
                            }

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
                                    nameInput.readOnly = false;
                                    emailInput.readOnly = false;

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

    <?php echo '<script'; ?>
>
        function validatePassword() {
            const passwordInput = document.getElementById('password');
            const passwordError = document.getElementById('password-error');
            const isEditMode = document.querySelector('input[name="edit_mode"]') !== null;
            const isPasswordChangeMode = document.querySelector('input[name="password_change_mode"]') !== null;

            // Clear previous error
            passwordError.style.display = 'none';
            passwordError.textContent = '';

            // Skip validation if password is empty and we're in edit mode (not password change mode)
            if (passwordInput.value === '' && isEditMode && !isPasswordChangeMode) {
                return true;
            }

            // Validate password length
            if (passwordInput.value.length < 8) {
                passwordError.textContent = 'La contraseña debe tener al menos 8 caracteres.';
                passwordError.style.display = 'block';
                return false;
            }

            return true;
        }

        function validatePasswordMatch() {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const confirmPasswordError = document.getElementById('confirm-password-error');
            const isEditMode = document.querySelector('input[name="edit_mode"]') !== null;
            const isPasswordChangeMode = document.querySelector('input[name="password_change_mode"]') !== null;

            // Clear previous error
            confirmPasswordError.style.display = 'none';
            confirmPasswordError.textContent = '';

            // Skip validation if both passwords are empty and we're in edit mode (not password change mode)
            if (passwordInput.value === '' && confirmPasswordInput.value === '' && isEditMode && !isPasswordChangeMode) {
                return true;
            }

            // Validate password match
            if (passwordInput.value !== confirmPasswordInput.value) {
                confirmPasswordError.textContent = 'Las contraseñas no coinciden.';
                confirmPasswordError.style.display = 'block';
                return false;
            }

            return true;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const userForm = document.getElementById('user-form');

            userForm.addEventListener('submit', function(event) {
                // Prevent the default form submission
                event.preventDefault();

                // Validate password fields before submission
                const isPasswordValid = validatePassword();
                const isPasswordMatchValid = validatePasswordMatch();

                // If validation fails, stop form submission
                if (!isPasswordValid || !isPasswordMatchValid) {
                    return;
                }

                // Determine the action based on form mode
                let title, confirmButtonText;
                const isEditMode = document.querySelector('input[name="edit_mode"]') !== null;
                const isPasswordChangeMode = document.querySelector('input[name="password_change_mode"]') !== null;

                if (isPasswordChangeMode) {
                    title = "¿Estás seguro de que deseas cambiar la contraseña?";
                    confirmButtonText = "Cambiar contraseña";
                } else if (isEditMode) {
                    title = "¿Estás seguro de que deseas actualizar este usuario?";
                    confirmButtonText = "Actualizar";
                } else {
                    title = "¿Estás seguro de que deseas registrar este usuario?";
                    confirmButtonText = "Registrar";
                }

                // Show SweetAlert confirmation
                Swal.fire({
                    title: title,
                    text: "",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: confirmButtonText,
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // If confirmed, submit the form
                        userForm.submit();
                    }
                });
            });
        });
    <?php echo '</script'; ?>
>
</body>

</html>
<?php }
}
