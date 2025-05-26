<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="/views/components/sidebar.app.js" defer></script>
    <link rel="stylesheet" href="./register-user.styles.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <title>Registro de Usuarios</title>
</head>

<body>

    {include file=$sidebarPath} <!-- Aquí se incluye el sidebar, según la variable Smarty -->

    <main class="content">

        <div class="center-container">
            <div class="form-container">
                <div class="form-header">
                    <a href="/views/dashboard/dashboard.view.php" class="form-back-btn">
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

                <!-- Si hay un error, lo mostramos aquí -->
                {if isset($error)}
                    <div
                        style="color: red; margin-bottom: 1rem; border: 1px solid red; padding: 0.5rem; border-radius: 5px;">
                        {$error|escape}
                    </div>
                {/if}

                <!-- Si hay un mensaje de éxito, lo mostramos aquí -->
                {if isset($success)}
                    <div
                        style="color: darkgreen; margin-bottom: 1rem; border: 1px solid green; padding: 0.5rem; border-radius: 5px; background-color: lightgreen;">
                        {$success|escape}
                    </div>
                {/if}

                <!-- Formulario para registrar o actualizar usuario -->
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
                        <label for="role">Rol</label>
                        <select id="role" name="role" required>
                            <option value="S" {if $editMode && $userData.role == 'S'}selected{/if}>Secretaria</option>
                            <option value="A" {if $editMode && $userData.role == 'A'}selected{/if}>Administrador</option>
                            <option value="D" {if $editMode && $userData.role == 'D'}selected{/if}>Doctor</option>
                      </select>
                    </div>

                    <div class="form-group" id="doctor-select-container" style="display: none;">
                        <label for="id_doctor">Seleccionar doctor</label>
                        <select id="id_doctor" name="id_doctor">
                            <option value="">Seleccione un doctor</option>
                            {foreach from=$doctors item=doctor}
                                <option value="{$doctor.id}" {if $editMode && $userData.id_doctor == $doctor.id}selected{/if}>{$doctor.names} {$doctor.last_name} {$doctor.last_name2} - {$doctor.email}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Nombre completo</label>
                        <input type="text" id="name" name="name" placeholder="Nombre completo" value="{if $editMode}{$userData.name}{/if}" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
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
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="Contraseña" {if !$editMode || $passwordChangeMode}required{/if}>
                        {if $editMode && !$passwordChangeMode}<small style="color: #666;">Dejar en blanco para mantener la contraseña actual</small>{/if}
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirmar Contraseña</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmar contraseña" {if !$editMode || $passwordChangeMode}required{/if}>
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
                    </script>
                </form>
            </div>

            <!-- Lista de usuarios registrados -->
            <div class="users-list-container">
                <h3 class="users-list-title">Usuarios Registrados</h3>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>ID Doctor</th>
                            <th>Fecha de Creación</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$users item=user}
                            <tr>
                                <td>{$user.id}</td>
                                <td>{$user.name}</td>
                                <td>{$user.email}</td>
                                <td>
                                    {if $user.role == 'S'}Secretaria
                                    {elseif $user.role == 'A'}Administrador
                                    {elseif $user.role == 'D'}Doctor
                                    {else}{$user.role}
                                    {/if}
                                </td>
                                <td>{$user.id_doctor|default:'-'}</td>
                                <td>{$user.created_at}</td>
                                <td>
                                    {if $user.status == 'AC'}Activo
                                    {else}{$user.status}
                                    {/if}
                                </td>
                                <td class="actions-td">
                                    <form action="/controllers/auth/delete-user.controller.php" method="POST" class="action-wrapper">
                                        <input type="hidden" name="user_id" value="{$user.id}">
                                        <button type="submit" class="delete-btn" data-id="{$user.id}">Eliminar</button>
                                    </form>
                                    <a href="/views/user/register/register-user.view.php?id={$user.id}" class="action-wrapper">
                                        <button class="update-btn">Actualizar</button>
                                    </a>
                                    <a href="/views/user/register/register-user.view.php?id={$user.id}&password_change=1" class="action-wrapper">
                                        <button class="password-btn">Cambiar contraseña</button>
                                    </a>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>

</html>
