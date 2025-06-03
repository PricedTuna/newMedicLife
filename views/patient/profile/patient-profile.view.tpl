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
    <title>Perfil de Paciente | Medic Life</title>
    <link rel="icon" href="/views/dashboard/icons/Untitled-design-_1_.ico" type="image/x-icon">
</head>

<body>
    {include file="../../components/sidebar.tpl"}

    <main>
        <div class="main-content">
            <div class="profile-header">
                <h1>Perfil de Paciente</h1>
                <div class="profile-actions">
                    <a href="/views/doctor/medical_history/medical-history.view.php?patient_id={$patientData.id}" class="action-btn">
                        <i class="bi bi-journal-medical"></i> Historial Médico
                    </a>
                    <a href="/views/public/medical_history/view-history.view.php?curp={$patientData.CURP}" class="action-btn" style="background-color: #2ecc71;">
                        <i class="bi bi-eye"></i> Ver Historial Público
                    </a>
                </div>
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

            {if isset($patientData)}
                <div class="profile-container">
                    <div class="profile-section patient-section">
                        <h2>Información del Paciente</h2>
                        <div class="profile-info">
                            <div class="doctor-photo-profile">
                                {if $patientData.photo}
                                    <img src="/controllers/patient/mostrar_foto.php?id={$patientData.id}" alt="Foto del paciente">
                                {else}
                                    <div class="no-photo">Sin foto</div>
                                {/if}
                            </div>
                            <div class="info-item">
                                <span class="label">Nombre Completo:</span>
                                <span class="value">{$patientData.names} {$patientData.last_name} {$patientData.last_name2}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">CURP:</span>
                                <span class="value">{$patientData.CURP}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Fecha de Nacimiento:</span>
                                <span class="value">{$patientData.birth_date}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Género:</span>
                                <span class="value">
                                    {if $patientData.gender == 'M'}Masculino
                                    {elseif $patientData.gender == 'F'}Femenino
                                    {else}{$patientData.gender}
                                    {/if}
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="label">Teléfono:</span>
                                <span class="value">{$patientData.phone}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Correo Electrónico:</span>
                                <span class="value">{$patientData.email}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Tipo de Sangre:</span>
                                <span class="value">{$patientData.blood_type}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Alergias:</span>
                                <span class="value">{if isset($patientData.allergies)}{$patientData.allergies}{else}No especificadas{/if}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Enfermedades Crónicas:</span>
                                <span class="value">{if isset($patientData.chronic_diseases)}{$patientData.chronic_diseases}{else}No especificadas{/if}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Dirección:</span>
                                <span class="value">
                                    {$patientData.street} {$patientData.external_number}
                                    {if $patientData.internal_number}, Int. {$patientData.internal_number}{/if},
                                    Col. {$patientData.neighborhood}, CP {$patientData.CP}
                                </span>
                            </div>

                            {if isset($patientData.emergency_contacts) && $patientData.emergency_contacts|@count > 0}
                                <div class="info-item" style="flex-basis: 100%;">
                                    <span class="label">Contactos de Emergencia:</span>
                                    <span class="value">
                                        {foreach from=$patientData.emergency_contacts item=contact}
                                            <div class="emergency-contact-card">
                                                <div class="emergency-contact-header">
                                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                                    <h3>Contacto de Emergencia</h3>
                                                </div>
                                                <div class="emergency-contact-info">
                                                    <div class="emergency-contact-detail">
                                                        <span class="emergency-contact-label">Nombre</span>
                                                        <span class="emergency-contact-value">{if isset($contact.name)}{$contact.name}{else}No especificado{/if}</span>
                                                    </div>
                                                    <div class="emergency-contact-detail">
                                                        <span class="emergency-contact-label">Relación</span>
                                                        <span class="emergency-contact-value">{if isset($contact.relationship)}{$contact.relationship}{else}No especificado{/if}</span>
                                                    </div>
                                                    <div class="emergency-contact-detail">
                                                        <span class="emergency-contact-label">Teléfono</span>
                                                        <span class="emergency-contact-value emergency-phone">
                                                            <i class="bi bi-telephone-fill"></i>
                                                            {if isset($contact.phone)}{$contact.phone}{else}No especificado{/if}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        {/foreach}
                                    </span>
                                </div>
                            {/if}
                        </div>
                    </div>
                </div>
            {/if}
        </div>
    </main>
</body>
</html>
