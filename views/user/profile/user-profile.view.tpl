<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/views/components/sidebar.app.js" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/views/user/profile/user-profile.styles.css">
    <link rel="stylesheet" href="/views/dashboard/dashboard.styles.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <title>Perfil de usuario | Medic Life</title>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Helper functions for form validation
            function showInputError(input, message) {
                // Remove any existing error message
                clearInputError(input);

                // Add error class to input
                input.classList.add('input-error');

                // Create and append error message
                const errorElement = document.createElement('div');
                errorElement.className = 'error-message';
                errorElement.textContent = message;
                input.parentNode.appendChild(errorElement);
            }

            function clearInputError(input) {
                // Remove error class from input
                input.classList.remove('input-error');

                // Remove any existing error message
                const errorElement = input.parentNode.querySelector('.error-message');
                if (errorElement) {
                    errorElement.remove();
                }
            }

            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }
            // Photo preview functionality
            const fileInput = document.getElementById('doctor-photo-profile');
            const imagePreview = document.getElementById('image-preview');
            const previewPlaceholder = document.getElementById('preview-placeholder');

            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            imagePreview.src = e.target.result;
                            imagePreview.style.display = 'block';
                            previewPlaceholder.style.display = 'none';
                        }

                        reader.readAsDataURL(this.files[0]);
                    } else {
                        imagePreview.style.display = 'none';
                        previewPlaceholder.style.display = 'flex';
                    }
                });
            }

            // Profile edit functionality
            const editProfileBtn = document.getElementById('edit-profile-btn');
            const cancelEditBtn = document.getElementById('cancel-edit-btn');
            const profileInfoView = document.getElementById('profile-info-view');
            const profileEditForm = document.getElementById('profile-edit-form');

            if (editProfileBtn && cancelEditBtn && profileInfoView && profileEditForm) {
                // Toggle to edit mode
                editProfileBtn.addEventListener('click', function() {
                    profileInfoView.style.display = 'none';
                    profileEditForm.style.display = 'block';
                    editProfileBtn.style.display = 'none';
                });

                // Toggle back to view mode
                cancelEditBtn.addEventListener('click', function() {
                    profileInfoView.style.display = 'flex';
                    profileEditForm.style.display = 'none';
                    editProfileBtn.style.display = 'inline-block';
                });

                // Form validation
                const profileForm = profileEditForm.querySelector('form');
                profileForm.addEventListener('submit', function(event) {
                    const nameInput = document.getElementById('name');
                    const emailInput = document.getElementById('email');
                    let isValid = true;

                    // Validate name
                    if (!nameInput.value.trim()) {
                        showInputError(nameInput, 'El nombre es obligatorio');
                        isValid = false;
                    } else {
                        clearInputError(nameInput);
                    }

                    // Validate email
                    if (!emailInput.value.trim()) {
                        showInputError(emailInput, 'El correo electrónico es obligatorio');
                        isValid = false;
                    } else if (!isValidEmail(emailInput.value.trim())) {
                        showInputError(emailInput, 'El correo electrónico no es válido');
                        isValid = false;
                    } else {
                        clearInputError(emailInput);
                    }

                    if (!isValid) {
                        event.preventDefault();
                    }
                });
            }

            // Password change functionality
            const changePasswordBtn = document.getElementById('change-password-btn');
            const cancelPasswordBtn = document.getElementById('cancel-password-btn');
            const passwordChangeForm = document.getElementById('password-change-form');

            if (changePasswordBtn && cancelPasswordBtn && passwordChangeForm) {
                // Toggle to password change mode
                changePasswordBtn.addEventListener('click', function() {
                    passwordChangeForm.style.display = 'block';
                    changePasswordBtn.style.display = 'none';
                });

                // Toggle back to view mode
                cancelPasswordBtn.addEventListener('click', function() {
                    passwordChangeForm.style.display = 'none';
                    changePasswordBtn.style.display = 'inline-block';

                    // Clear form fields
                    const passwordForm = passwordChangeForm.querySelector('form');
                    if (passwordForm) {
                        passwordForm.reset();
                    }

                    // Clear any error messages
                    const errorMessages = passwordChangeForm.querySelectorAll('.error-message');
                    errorMessages.forEach(function(errorMessage) {
                        errorMessage.remove();
                    });

                    // Remove error classes from inputs
                    const inputs = passwordChangeForm.querySelectorAll('input');
                    inputs.forEach(function(input) {
                        input.classList.remove('input-error');
                    });
                });

                // Form validation
                const passwordForm = passwordChangeForm.querySelector('form');
                passwordForm.addEventListener('submit', function(event) {
                    const currentPasswordInput = document.getElementById('current_password');
                    const newPasswordInput = document.getElementById('new_password');
                    const confirmPasswordInput = document.getElementById('confirm_password');
                    let isValid = true;

                    // Validate current password
                    if (!currentPasswordInput.value.trim()) {
                        showInputError(currentPasswordInput, 'La contraseña actual es obligatoria');
                        isValid = false;
                    } else {
                        clearInputError(currentPasswordInput);
                    }

                    // Validate new password
                    if (!newPasswordInput.value.trim()) {
                        showInputError(newPasswordInput, 'La nueva contraseña es obligatoria');
                        isValid = false;
                    } else if (newPasswordInput.value.length < 8) {
                        showInputError(newPasswordInput, 'La nueva contraseña debe tener al menos 8 caracteres');
                        isValid = false;
                    } else {
                        clearInputError(newPasswordInput);
                    }

                    // Validate confirm password
                    if (!confirmPasswordInput.value.trim()) {
                        showInputError(confirmPasswordInput, 'Debe confirmar la nueva contraseña');
                        isValid = false;
                    } else if (confirmPasswordInput.value !== newPasswordInput.value) {
                        showInputError(confirmPasswordInput, 'Las contraseñas no coinciden');
                        isValid = false;
                    } else {
                        clearInputError(confirmPasswordInput);
                    }

                    if (!isValid) {
                        event.preventDefault();
                    }
                });
            }
        });
    </script>
</head>

<body>
    {include file="../../components/sidebar.tpl"}

    <main>
        <div class="main-content">
            <div class="profile-header">
                <h1>Perfil de usuario</h1>
            </div>

            {if isset($error)}
                <div class="error-message">
                    {$error|escape}
                </div>
            {/if}

            {if isset($success)}
                <div class="success-message">
                    {$success|escape}
                </div>
            {/if}

            {if isset($user)}
                <div class="profile-container">
                    <div class="profile-section">
                        <h2>Información del Usuario</h2>
                        <div class="profile-actions">
                            <button type="button" id="edit-profile-btn" class="edit-profile-btn">
                                <i class="bi bi-pencil"></i> Editar Perfil
                            </button>
                        </div>

                        <!-- Vista de información (modo predeterminado) -->
                        <div id="profile-info-view" class="profile-info">
                            <div class="info-item">
                                <span class="label">ID:</span>
                                <span class="value">{$user.id}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Nombre:</span>
                                <span class="value">{$user.name}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Correo Electrónico:</span>
                                <span class="value">{$user.email}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Rol:</span>
                                <span class="value">
                                    {if $user.role == 'S'}Administración
                                    {elseif $user.role == 'A'}Administrador
                                    {elseif $user.role == 'D'}Doctor
                                    {else}{$user.role}
                                    {/if}
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="label">Fecha de Creación:</span>
                                <span class="value">{$user.created_at}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Estado:</span>
                                <span class="value">
                                    {if $user.status == 'AC'}Activo
                                    {else}{$user.status}
                                    {/if}
                                </span>
                            </div>
                        </div>

                        <!-- Formulario de edición (oculto por defecto) -->
                        <div id="profile-edit-form" class="profile-edit-form" style="display: none;">
                            <form action="/controllers/user/update-profile.controller.php" method="POST">
                                <input type="hidden" name="user_id" value="{$user.id}">

                                <div class="form-group">
                                    <label for="name">Nombre:</label>
                                    <input type="text" id="name" name="name" value="{$user.name}" required>
                                </div>

                                <div class="form-group">
                                    <label for="email">Correo Electrónico:</label>
                                    <input type="email" id="email" name="email" value="{$user.email}" required>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="save-btn">Guardar Cambios</button>
                                    <button type="button" id="cancel-edit-btn" class="cancel-btn">Cancelar</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Sección de Cambio de Contraseña -->
                    <div class="profile-section password-section">
                        <h2>Cambiar Contraseña</h2>
                        <div class="profile-actions">
                            <button type="button" id="change-password-btn" class="edit-profile-btn">
                                <i class="bi bi-key"></i> Cambiar Contraseña
                            </button>
                        </div>

                        <!-- Formulario de cambio de contraseña (oculto por defecto) -->
                        <div id="password-change-form" class="profile-edit-form" style="display: none;">
                            <form action="/controllers/user/change-password.controller.php" method="POST">
                                <input type="hidden" name="user_id" value="{$user.id}">

                                <div class="form-group">
                                    <label for="current_password">Contraseña Actual:</label>
                                    <input type="password" id="current_password" name="current_password" required>
                                </div>

                                <div class="form-group">
                                    <label for="new_password">Nueva Contraseña:</label>
                                    <input type="password" id="new_password" name="new_password" required>
                                    <div class="password-requirements">
                                        <small>La contraseña debe tener al menos 8 caracteres</small>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="confirm_password">Confirmar Contraseña:</label>
                                    <input type="password" id="confirm_password" name="confirm_password" required>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="save-btn">Guardar Cambios</button>
                                    <button type="button" id="cancel-password-btn" class="cancel-btn">Cancelar</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {if $doctorData}
                        <div class="profile-section doctor-section">
                            <h2>Información del Doctor</h2>
                            <div class="profile-info">
                                <div class="doctor-photo-profile">
                                    {if $doctorData.photo}
                                        <img src="/controllers/doctor/mostrar_foto.php?id={$doctorData.id}" alt="Foto del doctor">
                                    {else}
                                        <div class="no-photo">Sin foto</div>
                                    {/if}
                                    <button type="button" class="update-photo-btn" onclick="document.getElementById('photo-upload-form').style.display='block'">
                                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                                      </svg>
                                    </button>
                                </div>

                                <div id="photo-upload-form" class="photo-upload-form" style="display: none;">
                                    <form action="/controllers/doctor/update_photo.php" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="doctor_id" value="{$doctorData.id}">
                                        <div class="form-group">
                                            <label for="doctor-photo-profile" class="custom-file-upload">
                                                <i class="bi bi-cloud-arrow-up"></i> Seleccionar nueva foto
                                            </label>
                                            <input type="file" id="doctor-photo-profile" name="doctor_photo" accept="image/*" required>
                                            <div id="image-preview-container" class="image-preview-container">
                                                <img id="image-preview" class="image-preview" src="" alt="Vista previa" style="display: none;">
                                                <div id="preview-placeholder" class="preview-placeholder">
                                                    <i class="bi bi-image"></i>
                                                    <span>Vista previa de la imagen</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <button type="submit" class="save-photo-btn">Guardar</button>
                                            <button type="button" class="cancel-btn" onclick="document.getElementById('photo-upload-form').style.display='none'; document.getElementById('image-preview').style.display='none'; document.getElementById('preview-placeholder').style.display='flex';">Cancelar</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="info-item">
                                    <span class="label">Nombre Completo:</span>
                                    <span class="value">{$doctorData.names} {$doctorData.last_name} {$doctorData.last_name2}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">CURP:</span>
                                    <span class="value">{$doctorData.CURP}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">RFC:</span>
                                    <span class="value">{$doctorData.RFC}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Fecha de Nacimiento:</span>
                                    <span class="value">{$doctorData.birth_date}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Género:</span>
                                    <span class="value">{$doctorData.gender}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Teléfono:</span>
                                    <span class="value">{$doctorData.phone}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Correo Electrónico:</span>
                                    <span class="value">{$doctorData.email}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Número de Afiliación:</span>
                                    <span class="value">{$doctorData.insurance_number}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Cédula Profesional:</span>
                                    <span class="value">{$doctorData.professional_id}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Dirección:</span>
                                    <span class="value">
                                        {$doctorData.street} {$doctorData.external_number}
                                        {if $doctorData.internal_number}, Int. {$doctorData.internal_number}{/if},
                                        Col. {$doctorData.neighborhood}, CP {$doctorData.CP}
                                    </span>
                                </div>
                                {if isset($doctorData.medical_areas) && $doctorData.medical_areas|@count > 0}
                                    <div class="info-item">
                                        <span class="label">Áreas Médicas:</span>
                                        <span class="value">
                                            <ul class="medical-areas-list">
                                                {foreach from=$doctorData.medical_areas item=area}
                                                    <li>{$area}</li>
                                                {/foreach}
                                            </ul>
                                        </span>
                                    </div>
                                {/if}

                                {if isset($doctorData.schedules) && $doctorData.schedules|@count > 0}
                                    <div class="info-item">
                                        <span class="label">Horarios:</span>
                                        <span class="value">
                                            <table class="schedule-table">
                                                <thead>
                                                    <tr>
                                                        <th>Día</th>
                                                        <th>Hora de inicio</th>
                                                        <th>Hora de fin</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {foreach from=$doctorData.schedules item=schedule}
                                                        <tr>
                                                            <td>{$schedule.day}</td>
                                                            <td>{$schedule.start_time}</td>
                                                            <td>{$schedule.end_time}</td>
                                                        </tr>
                                                    {/foreach}
                                                </tbody>
                                            </table>
                                        </span>
                                    </div>
                                {/if}
                            </div>
                        </div>
                    {/if}
                </div>
            {/if}

        </div>
    </main>
</body>
</html>
