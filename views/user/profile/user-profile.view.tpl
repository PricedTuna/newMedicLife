<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="/views/components/sidebar.app.js" defer></script>
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <link rel="stylesheet" href="/views/dashboard/dashboard.styles.css">
    <link rel="stylesheet" href="./user-profile.styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Perfil de Usuario</title>
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
                                {if $doctorData.photo}
                                    <div class="doctor-photo">
                                        <img src="/controllers/doctor/mostrar_foto.php?id={$doctorData.id}" alt="Foto del doctor">
                                    </div>
                                {/if}
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
