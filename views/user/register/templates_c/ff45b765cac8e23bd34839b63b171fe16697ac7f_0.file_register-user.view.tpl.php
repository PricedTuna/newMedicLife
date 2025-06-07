<?php
/* Smarty version 5.4.5, created on 2025-06-07 00:03:18
  from 'file:register-user.view.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_684381c6255cc7_17153978',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ff45b765cac8e23bd34839b63b171fe16697ac7f' => 
    array (
      0 => 'register-user.view.tpl',
      1 => 1749254595,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_684381c6255cc7_17153978 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/var/www/html/views/user/register';
?><!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/forms.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/assets/css/cancel-button.css">
    <?php echo '<script'; ?>
 src="/views/components/sidebar.app.js" defer><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="https://cdn.jsdelivr.net/npm/sweetalert2@11"><?php echo '</script'; ?>
>
    <title>Registro de Usuarios | Medic Life</title>
    <style>
        .required {
            color: red;
            margin-left: 2px;
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }

        .password-toggle:hover {
            color: #333;
        }

        .tooltip-icon {
            margin-left: 5px;
            color: #007bff;
            cursor: help;
            font-size: 14px;
        }

        .tooltip-text {
            visibility: hidden;
            width: 200px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -100px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .tooltip-container {
            position: relative;
            display: inline-block;
        }

        .tooltip-container:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }
    </style>
</head>

<body>

    <?php $_smarty_tpl->renderSubTemplate($_smarty_tpl->getValue('sidebarPath'), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

    <main class="content">

        <div class="center-container">
            <div class="form-container">
                <div class="form-header">
                    <a href="#" class="form-back-btn" id="cancel-btn">
                        <button class="back-btn">Cancelar</button>
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

                <?php if ((true && ($_smarty_tpl->hasVariable('error') && null !== ($_smarty_tpl->getValue('error') ?? null)))) {?>
                    <div
                        style="color: red; margin-bottom: 1rem; border: 1px solid red; padding: 0.5rem; border-radius: 5px;">
                        <?php echo htmlspecialchars((string)$_smarty_tpl->getValue('error'), ENT_QUOTES, 'UTF-8', true);?>

                    </div>
                <?php }?>

                <?php if ((true && ($_smarty_tpl->hasVariable('success') && null !== ($_smarty_tpl->getValue('success') ?? null)))) {?>
                    <div
                        style="color: darkgreen; margin-bottom: 1rem; border: 1px solid green; padding: 0.5rem; border-radius: 5px; background-color: lightgreen;">
                        <?php echo htmlspecialchars((string)$_smarty_tpl->getValue('success'), ENT_QUOTES, 'UTF-8', true);?>

                    </div>
                <?php }?>

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
                        <label for="role">Rol <span class="required">*</span>
                            <span class="tooltip-container">
                                <i class="bi bi-question-circle tooltip-icon"></i>
                                <span class="tooltip-text">Seleccione el rol que tendrá el usuario en el sistema.</span>
                            </span>
                        </label>
                        <select id="role" name="role" required>
                            <option value="S" <?php if ($_smarty_tpl->getValue('editMode') && $_smarty_tpl->getValue('userData')['role'] == 'S') {?>selected<?php }?>>Recepcionista</option>
                            <option value="A" <?php if ($_smarty_tpl->getValue('editMode') && $_smarty_tpl->getValue('userData')['role'] == 'A') {?>selected<?php }?>>Administrador</option>
                            <option value="D" <?php if ($_smarty_tpl->getValue('editMode') && $_smarty_tpl->getValue('userData')['role'] == 'D') {?>selected<?php }?>>Medico</option>
                      </select>
                    </div>

                    <div class="form-group" id="doctor-select-container" style="display: none;">
                        <label for="id_doctor">Seleccionar medico <span class="required">*</span>
                            <span class="tooltip-container">
                                <i class="bi bi-question-circle tooltip-icon"></i>
                                <span class="tooltip-text">Seleccione el medico al que estará asociado este usuario.</span>
                            </span>
                        </label>
                        <select id="id_doctor" name="id_doctor">
                            <option value="">Seleccione un medico</option>
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
                        <label for="name">Nombre completo <span class="required">*</span>
                            <span class="tooltip-container">
                                <i class="bi bi-question-circle tooltip-icon"></i>
                                <span class="tooltip-text">Ingrese el nombre completo del usuario. Máximo 30 caracteres.</span>
                            </span>
                        </label>
                        <input type="text" id="name" name="name" placeholder="Nombre completo" value="<?php if ($_smarty_tpl->getValue('editMode')) {
echo $_smarty_tpl->getValue('userData')['name'];
}?>" required maxlength="30" onblur="validateNameLength(this)">
                        <div id="name-error" class="error-message" style="color: red; display: none;"></div>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo Electrónico <span class="required">*</span>
                            <span class="tooltip-container">
                                <i class="bi bi-question-circle tooltip-icon"></i>
                                <span class="tooltip-text">Ingrese el correo electrónico del usuario. Debe tener un formato válido (ejemplo@dominio.com).</span>
                            </span>
                        </label>
                        <input type="email" id="email" name="email" placeholder="Correo electrónico" value="<?php if ($_smarty_tpl->getValue('editMode')) {
echo $_smarty_tpl->getValue('userData')['email'];
}?>" required maxlength="100" onblur="validateEmailLength(this) && checkEmailUniqueness(this)">
                        <div id="email-error" class="error-message" style="color: red; display: none;"></div>
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
                        <label for="password">Contraseña <?php if (!$_smarty_tpl->getValue('editMode') || $_smarty_tpl->getValue('passwordChangeMode')) {?><span class="required">*</span><?php }?>
                            <span class="tooltip-container">
                                <i class="bi bi-question-circle tooltip-icon"></i>
                                <span class="tooltip-text">Ingrese una contraseña segura. Debe tener al menos 8 caracteres.</span>
                            </span>
                        </label>
                        <div style="position: relative;">
                            <input type="password" id="password" name="password" placeholder="Contraseña" <?php if (!$_smarty_tpl->getValue('editMode') || $_smarty_tpl->getValue('passwordChangeMode')) {?>required<?php }?> maxlength="50" onblur="validatePassword()">
                            <span class="password-toggle" onclick="togglePasswordVisibility('password')">
                                <i class="bi bi-eye-fill" id="password-toggle-icon"></i>
                            </span>
                        </div>
                        <div id="password-error" class="error-message" style="color: red; display: none;"></div>
                        <?php if ($_smarty_tpl->getValue('editMode') && !$_smarty_tpl->getValue('passwordChangeMode')) {?><small style="color: #666;">Dejar en blanco para mantener la contraseña actual</small><?php }?>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirmar Contraseña <?php if (!$_smarty_tpl->getValue('editMode') || $_smarty_tpl->getValue('passwordChangeMode')) {?><span class="required">*</span><?php }?>
                            <span class="tooltip-container">
                                <i class="bi bi-question-circle tooltip-icon"></i>
                                <span class="tooltip-text">Repita la contraseña para confirmar que es correcta.</span>
                            </span>
                        </label>
                        <div style="position: relative;">
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmar contraseña" <?php if (!$_smarty_tpl->getValue('editMode') || $_smarty_tpl->getValue('passwordChangeMode')) {?>required<?php }?> maxlength="50" onblur="validatePasswordMatch()">
                            <span class="password-toggle" onclick="togglePasswordVisibility('confirm_password')">
                                <i class="bi bi-eye-fill" id="confirm_password-toggle-icon"></i>
                            </span>
                        </div>
                        <div id="confirm-password-error" class="error-message" style="color: red; display: none;"></div>
                    </div>
                    <button type="submit" class="submit-btn" id="submit-btn">
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

                            toggleDoctorSelect();

                            const editMode = document.querySelector('input[name="edit_mode"]');
                            if (editMode) {
                                // Allow role changes in edit mode

                                if (roleSelect.value === 'D' && doctorSelect.value) {
                                    const selectedOption = doctorSelect.options[doctorSelect.selectedIndex];
                                    const doctorInfo = selectedOption.text.split(' - ');
                                    if (doctorInfo.length === 2) {
                                        nameInput.readOnly = true;
                                        emailInput.readOnly = true;
                                        nameInput.classList.add('input-disabled');
                                        emailInput.classList.add('input-disabled');

                                        doctorSelect.disabled = true;
                                        doctorSelect.classList.add('input-disabled');
                                    }
                                }
                            }

                            roleSelect.addEventListener('change', toggleDoctorSelect);

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
        function validateNameLength(input) {
            const nameError = document.getElementById('name-error');
            nameError.style.display = 'none';
            nameError.textContent = '';

            if (input.value.length > 30) {
                nameError.textContent = 'El campo nombre supera la longitud máxima de 30 caracteres.';
                nameError.style.display = 'block';
                return false;
            }
            return true;
        }

        function validateEmailLength(input) {
            const emailError = document.getElementById('email-error');
            emailError.style.display = 'none';
            emailError.textContent = '';

            if (input.value.length > 100) {
                emailError.textContent = 'El correo electrónico supera la longitud máxima de 100 caracteres.';
                emailError.style.display = 'block';
                return false;
            }
            return true;
        }

        function checkEmailUniqueness(input) {
            const emailError = document.getElementById('email-error');
            const email = input.value.trim();

            // Validar formato de correo electrónico
            if (!isValidEmail(email)) {
                emailError.textContent = 'Correo electrónico no válido. Debe tener formato usuario@dominio.com';
                emailError.style.display = 'block';
                emailError.style.color = "red";
                input.style.border = "2px solid red";
                isEmailValid = false;
                return false;
            }

            // Mostrar indicador de carga
            emailError.textContent = "Verificando disponibilidad...";
            emailError.style.display = 'block';
            emailError.style.color = "#FFA500"; // Naranja para indicar verificación en progreso
            input.style.border = "2px solid #FFA500";

            // Obtener el ID del usuario si estamos en modo edición
            const userIdInput = document.querySelector('input[name="user_id"]');
            const userId = userIdInput ? userIdInput.value : '';

            const formData = new FormData();
            formData.append('email', email);
            if (userId) {
                formData.append('userId', userId);
            }

            // Realizar la petición AJAX para verificar la unicidad del email
            fetch('/controllers/user/check-email-uniqueness.controller.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Email disponible
                    emailError.textContent = "Email disponible";
                    emailError.style.display = 'block';
                    emailError.style.color = "green";
                    input.style.border = "2px solid green";
                    isEmailValid = true;
                    // Eliminar el mensaje después de 3 segundos
                    setTimeout(() => {
                        emailError.style.display = 'none';
                        input.style.border = "2px solid var(--line-clr)";
                    }, 3000);
                    return true;
                } else {
                    // Email ya registrado
                    emailError.textContent = data.message || "Este email ya está registrado";
                    emailError.style.display = 'block';
                    emailError.style.color = "red";
                    input.style.border = "2px solid red";
                    isEmailValid = false;
                    return false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // En caso de error, permitimos continuar para no bloquear el registro
                emailError.textContent = "No se pudo verificar el email, pero puede continuar";
                emailError.style.display = 'block';
                emailError.style.color = "#FFA500";
                input.style.border = "2px solid #FFA500";
                isEmailValid = true; // Permitimos continuar
                return true;
            });

            // Siempre devolvemos true para permitir la validación del formulario
            // La validación real se hará en el servidor
            return true;
        }

        function isValidEmail(email) {
            // Función simple para validar formato de email
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        function validatePassword() {
            const passwordInput = document.getElementById('password');
            const passwordError = document.getElementById('password-error');
            const isEditMode = document.querySelector('input[name="edit_mode"]') !== null;
            const isPasswordChangeMode = document.querySelector('input[name="password_change_mode"]') !== null;

            passwordError.style.display = 'none';
            passwordError.textContent = '';

            if (passwordInput.value === '' && isEditMode && !isPasswordChangeMode) {
                return true;
            }

            if (passwordInput.value.length < 8) {
                passwordError.textContent = 'La contraseña debe tener al menos 8 caracteres.';
                passwordError.style.display = 'block';
                return false;
            }

            if (passwordInput.value.length > 50) {
                passwordError.textContent = 'La contraseña supera la longitud máxima de 50 caracteres.';
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

            confirmPasswordError.style.display = 'none';
            confirmPasswordError.textContent = '';

            if (passwordInput.value === '' && confirmPasswordInput.value === '' && isEditMode && !isPasswordChangeMode) {
                return true;
            }

            if (passwordInput.value !== confirmPasswordInput.value) {
                confirmPasswordError.textContent = 'Las contraseñas no coinciden.';
                confirmPasswordError.style.display = 'block';
                return false;
            }

            if (confirmPasswordInput.value.length > 50) {
                confirmPasswordError.textContent = 'La contraseña supera la longitud máxima de 50 caracteres.';
                confirmPasswordError.style.display = 'block';
                return false;
            }

            return true;
        }

        // Variable global para rastrear si el email es válido
        // Siempre asumimos que el email es válido a menos que se demuestre lo contrario
        let isEmailValid = true;

        document.addEventListener('DOMContentLoaded', function() {
            const userForm = document.getElementById('user-form');

            // Check if userForm exists
            if (!userForm) {
                console.error('Form with ID "user-form" not found');
                return;
            }

            console.log('Form action URL:', userForm.action);

            // Add real-time validation for name field
            const nameInput = document.getElementById('name');
            if (nameInput) {
                nameInput.addEventListener('input', function() {
                    if (this.value.length > 30) {
                        document.getElementById('name-error').textContent = 'El campo nombre supera la longitud máxima de 30 caracteres.';
                        document.getElementById('name-error').style.display = 'block';
                        // Truncar el valor a 30 caracteres
                        this.value = this.value.slice(0, 30);
                    } else {
                        document.getElementById('name-error').style.display = 'none';
                    }
                });
            }

            // Add real-time validation for email field
            const emailInput = document.getElementById('email');
            if (emailInput) {
                emailInput.addEventListener('input', function() {
                    if (this.value.length > 100) {
                        document.getElementById('email-error').textContent = 'El correo electrónico supera la longitud máxima de 100 caracteres.';
                        document.getElementById('email-error').style.display = 'block';
                        isEmailValid = false;
                    } else {
                        document.getElementById('email-error').style.display = 'none';
                        // No marcamos como válido aquí, ya que se validará en checkEmailUniqueness
                    }
                });

                // Validar email cuando pierde el foco
                emailInput.addEventListener('blur', function() {
                    if (this.value.trim() && this.value.length <= 100) {
                        checkEmailUniqueness(this);
                    }
                });
            }

            userForm.addEventListener('submit', function(event) {
                event.preventDefault();
                console.log('Form submission event triggered');

                const nameInput = document.getElementById('name');
                const emailInput = document.getElementById('email');

                const isNameValid = nameInput ? validateNameLength(nameInput) : true;
                const isEmailLengthValid = emailInput ? validateEmailLength(emailInput) : true;
                const isPasswordValid = validatePassword();
                const isPasswordMatchValid = validatePasswordMatch();

                console.log('Validation results:', {
                    isNameValid,
                    isEmailLengthValid,
                    isPasswordValid,
                    isPasswordMatchValid
                });

                // Verificar si el email es válido (no está ya registrado)
                if (emailInput && emailInput.value.trim() !== '') {
                    // Solo validamos si hay un error explícito de email ya registrado
                    if (document.getElementById('email-error').style.display === 'block' &&
                        document.getElementById('email-error').style.color === "red" &&
                        document.getElementById('email-error').textContent.includes("ya está registrado")) {
                        // Mostrar mensaje de error
                        Swal.fire({
                            title: 'Error de validación',
                            text: 'El correo electrónico ya está registrado en el sistema. Por favor, utilice otro.',
                            icon: 'error',
                            confirmButtonText: 'Entendido'
                        });
                        return;
                    }
                }

                if (!isNameValid || !isEmailLengthValid || !isPasswordValid || !isPasswordMatchValid) {
                    // Solo mostramos mensaje genérico de validación
                    return;
                }

                // Verificamos si hay un error explícito de email ya registrado
                if (document.getElementById('email-error').style.display === 'block' &&
                    document.getElementById('email-error').style.color === "red" &&
                    document.getElementById('email-error').textContent.includes("ya está registrado")) {
                    return;
                }

                let title, confirmButtonText;
                const isEditMode = document.querySelector('input[name="edit_mode"]') !== null;
                const isPasswordChangeMode = document.querySelector('input[name="password_change_mode"]') !== null;

                console.log('Mode detection:', {
                    isEditMode,
                    isPasswordChangeMode
                });

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

                Swal.fire({
                    title: title,
                    text: "",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: confirmButtonText,
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        console.log('Form is being submitted');

                        // Log form data
                        console.log('Form data:');
                        const formData = new FormData(userForm);
                        for (let pair of formData.entries()) {
                            console.log(pair[0] + ': ' + pair[1]);
                        }

                        // Use a direct form submission instead of userForm.submit()
                        // This is to ensure the form is submitted properly
                        document.getElementById('user-form').submit();
                    }
                });
            });
        });
    <?php echo '</script'; ?>
>

    <?php echo '<script'; ?>
>
        function togglePasswordVisibility(inputId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(inputId + '-toggle-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye-fill');
                toggleIcon.classList.add('bi-eye-slash-fill');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash-fill');
                toggleIcon.classList.add('bi-eye-fill');
            }
        }

        // Add confirmation dialog for cancel button
        document.addEventListener('DOMContentLoaded', function() {
            const cancelBtn = document.getElementById('cancel-btn');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: 'Si cancelas, se perderán todos los datos ingresados en el formulario.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, cancelar',
                        cancelButtonText: 'No, continuar editando'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/views/user/list/list-users.view.php';
                        }
                    });
                });
            }
        });
    <?php echo '</script'; ?>
>
</body>

</html>
<?php }
}
