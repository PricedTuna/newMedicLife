<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="/views/components/sidebar.app.js" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <link rel="stylesheet" href="/views/dashboard/dashboard.styles.css">
    <link rel="stylesheet" href="./user-profile.styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Perfil de Usuario | Medic Life</title>
</head>

<body>
    {include file="../../components/sidebar.tpl"}

    <main>
        <div class="main-content">
            <div class="profile-header">
                <h1>Perfil de Usuario</h1>
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
                                    {if $user.role == 'S'}Secretaria
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
                                <div class="doctor-photo">
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
                                            <label for="doctor-photo">Seleccionar nueva foto:</label>
                                            <input type="file" id="doctor-photo" name="doctor_photo" accept="image/*" required>
                                        </div>
                                        <div class="form-actions">
                                            <button type="submit" class="save-photo-btn">Guardar</button>
                                            <button type="button" class="cancel-btn" onclick="document.getElementById('photo-upload-form').style.display='none'">Cancelar</button>
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
        </div>
    </main>
</body>
</html>
