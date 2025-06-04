<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/forms.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <script src="/views/components/sidebar.app.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Registro de Usuarios | Medic Life</title>
    <style>
        .required {
            color: red;
            margin-left: 2px;
        }
    </style>
</head>

<body>

    {include file=$sidebarPath}

    <main class="content">

        <div class="center-container">
            <div class="form-container">
                <div class="form-header">
                    <a href="/views/user/list/list-users.view.php" class="form-back-btn">
                        <button class="back-btn">Volver</button>
                        <span class="back-btn-icon">&#8617;</span>
                    </a>
                    <h2 class="form-title">
                        {if $passwordChangeMode}
                            Cambiar contraseña
                        {elseif $editMode}
                            Actualizar usuario
                        {else}
                            Registrar usuario
                        {/if}
                    </h2>
                </div>

                {if isset($error)}
                    <div
                        style="color: red; margin-bottom: 1rem; border: 1px solid red; padding: 0.5rem; border-radius: 5px;">
                        {$error|escape}
                    </div>
                {/if}

                {if isset($success)}
                    <div
                        style="color: darkgreen; margin-bottom: 1rem; border: 1px solid green; padding: 0.5rem; border-radius: 5px; background-color: lightgreen;">
                        {$success|escape}
                    </div>
                {/if}

                <form action="/controllers/auth/register.controller.php" method="POST" id="user-form">
                    {if $editMode}
                        <input type="hidden" name="user_id" value="{$userData.id}">
                        <input type="hidden" name="edit_mode" value="1">
                        {if $passwordChangeMode}
                        <input type="hidden" name="password_change_mode" value="1">
                        {/if}
                    {/if}
                    {if !$passwordChangeMode}
                    <div class="form-group">
                        <label for="role">Rol <span class="required">*</span></label>
                        <select id="role" name="role" required>
                            <option value="S" {if $editMode && $userData.role == 'S'}selected{/if}>Administración</option>
                            <option value="A" {if $editMode && $userData.role == 'A'}selected{/if}>Administrador</option>
                            <option value="D" {if $editMode && $userData.role == 'D'}selected{/if}>Doctor</option>
                      </select>
                    </div>

                    <div class="form-group" id="doctor-select-container" style="display: none;">
                        <label for="id_doctor">Seleccionar doctor <span class="required">*</span></label>
                        <select id="id_doctor" name="id_doctor">
                            <option value="">Seleccione un doctor</option>
                            {foreach from=$doctors item=doctor}
                                <option value="{$doctor.id}" {if $editMode && $userData.id_doctor == $doctor.id}selected{/if}>{$doctor.names} {$doctor.last_name} {$doctor.last_name2} - {$doctor.email}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Nombre completo <span class="required">*</span></label>
                        <input type="text" id="name" name="name" placeholder="Nombre completo" value="{if $editMode}{$userData.name}{/if}" required maxlength="30" onblur="validateNameLength(this)">
                        <div id="name-error" class="error-message" style="color: red; display: none;"></div>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo Electrónico <span class="required">*</span></label>
                        <input type="email" id="email" name="email" placeholder="Correo electrónico" value="{if $editMode}{$userData.email}{/if}" required maxlength="100" onblur="validateEmailLength(this) && checkEmailUniqueness(this)">
                        <div id="email-error" class="error-message" style="color: red; display: none;"></div>
                    </div>
                    {else}
                    <input type="hidden" name="name" value="{$userData.name}">
                    <input type="hidden" name="email" value="{$userData.email}">
                    <input type="hidden" name="role" value="{$userData.role}">
                    {if $userData.id_doctor}
                    <input type="hidden" name="id_doctor" value="{$userData.id_doctor}">
                    {/if}
                    <div class="form-group">
                        <p><strong>Usuario:</strong> {$userData.name}</p>
                        <p><strong>Correo:</strong> {$userData.email}</p>
                    </div>
                    {/if}
                    <div class="form-group">
                        <label for="password">Contraseña {if !$editMode || $passwordChangeMode}<span class="required">*</span>{/if}</label>
                        <input type="password" id="password" name="password" placeholder="Contraseña" {if !$editMode || $passwordChangeMode}required{/if} maxlength="50" onblur="validatePassword()">
                        <div id="password-error" class="error-message" style="color: red; display: none;"></div>
                        {if $editMode && !$passwordChangeMode}<small style="color: #666;">Dejar en blanco para mantener la contraseña actual</small>{/if}
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirmar Contraseña {if !$editMode || $passwordChangeMode}<span class="required">*</span>{/if}</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmar contraseña" {if !$editMode || $passwordChangeMode}required{/if} maxlength="50" onblur="validatePasswordMatch()">
                        <div id="confirm-password-error" class="error-message" style="color: red; display: none;"></div>
                    </div>
                    <button type="submit" class="submit-btn">
                        {if $passwordChangeMode}
                            Cambiar contraseña
                        {elseif $editMode}
                            Actualizar
                        {else}
                            Registrar
                        {/if}
                    </button>

                    <script>
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
                                roleSelect.disabled = true;
                                roleSelect.classList.add('input-disabled');

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
                    </script>
                </form>
            </div>

        </div>
    </main>

    <script>
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

                const nameInput = document.getElementById('name');
                const emailInput = document.getElementById('email');

                const isNameValid = nameInput ? validateNameLength(nameInput) : true;
                const isEmailLengthValid = emailInput ? validateEmailLength(emailInput) : true;
                const isPasswordValid = validatePassword();
                const isPasswordMatchValid = validatePasswordMatch();

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
                        userForm.submit();
                    }
                });
            });
        });
    </script>
</body>

</html>
