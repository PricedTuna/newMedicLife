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
                        <input type="text" id="name" name="name" placeholder="Nombre completo" value="{if $editMode}{$userData.name}{/if}" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo Electrónico <span class="required">*</span></label>
                        <input type="email" id="email" name="email" placeholder="Correo electrónico" value="{if $editMode}{$userData.email}{/if}" required>
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
                        <input type="password" id="password" name="password" placeholder="Contraseña" {if !$editMode || $passwordChangeMode}required{/if} onblur="validatePassword()">
                        <div id="password-error" class="error-message" style="color: red; display: none;"></div>
                        {if $editMode && !$passwordChangeMode}<small style="color: #666;">Dejar en blanco para mantener la contraseña actual</small>{/if}
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirmar Contraseña {if !$editMode || $passwordChangeMode}<span class="required">*</span>{/if}</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmar contraseña" {if !$editMode || $passwordChangeMode}required{/if} onblur="validatePasswordMatch()">
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

            return true;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const userForm = document.getElementById('user-form');

            userForm.addEventListener('submit', function(event) {
                event.preventDefault();

                const isPasswordValid = validatePassword();
                const isPasswordMatchValid = validatePasswordMatch();

                if (!isPasswordValid || !isPasswordMatchValid) {
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
