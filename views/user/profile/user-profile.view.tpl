<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="/views/components/sidebar.app.js" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <link rel="stylesheet" href="./user-profile.styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="/views/dashboard/dashboard.styles.css">
    <title>Perfil de usuario | Medic Life</title>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                        <div class="profile-info">
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
                            </div>
                        </div>
                    {/if}
                </div>
            {/if}

            <!-- Nueva sección de Configuraciones -->
            <div class="profile-section settings-section">
                <h2>Configuraciones</h2>
                <div class="settings-container">
                    <div class="setting-item">
                        <span class="setting-label">Asistente de voz:</span>
                        <button id="voiceToggleBtn" aria-label="Asistente de voz" title="Asistente de voz" class="voice-toggle-btn">
                            🔈
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
