<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/views/dashboard/icons/Untitled-design-_1_.ico" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/views/components/sidebar.app.js" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/views/settings/settings.styles.css">
    <link rel="stylesheet" href="/views/dashboard/dashboard.styles.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <title>Configuraciones | Medic Life</title>
</head>

<body>
    {include file=$sidebarPath}

    <main>
        <div class="main-content">
            <div class="settings-header">
                <h1>Configuraciones</h1>
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

            <div class="settings-container">
                <!-- Sección de Configuraciones de Voz -->
                <div class="settings-section voice-settings-section">
                    <h2>Asistente de Voz</h2>
                    <div class="settings-content">
                        <div class="setting-item">
                            <span class="setting-label">Asistente de voz:</span>
                            <button id="voiceToggleBtn" aria-label="Asistente de voz" title="Asistente de voz" class="voice-toggle-btn">
                                🔈
                            </button>
                        </div>
                        <div class="setting-description">
                            <p>El asistente de voz te guiará por la aplicación proporcionando información audible sobre cada página que visites.</p>
                            <p>Puedes activar o desactivar esta función en cualquier momento utilizando el botón de arriba.</p>
                        </div>
                    </div>
                </div>

                <!-- Sección de Atajos de Teclado -->
                <div class="settings-section shortcuts-info-section">
                    <h2>Atajos de Teclado</h2>
                    <div class="shortcuts-container">
                        <div class="shortcuts-group">
                            <h3>Navegación Principal</h3>
                            <div class="shortcut-item">
                                <span class="shortcut-keys"><kbd>Ctrl</kbd> + <kbd>D</kbd></span>
                                <span class="shortcut-description">Ir al Dashboard</span>
                            </div>
                            <div class="shortcut-item">
                                <span class="shortcut-keys"><kbd>Ctrl</kbd> + <kbd>M</kbd></span>
                                <span class="shortcut-description">Ir a Médicos</span>
                            </div>
                            <div class="shortcut-item">
                                <span class="shortcut-keys"><kbd>Ctrl</kbd> + <kbd>P</kbd></span>
                                <span class="shortcut-description">Ir a Pacientes</span>
                            </div>
                            <div class="shortcut-item">
                                <span class="shortcut-keys"><kbd>Ctrl</kbd> + <kbd>U</kbd></span>
                                <span class="shortcut-description">Ir a Usuarios</span>
                            </div>
                            <div class="shortcut-item">
                                <span class="shortcut-keys"><kbd>Ctrl</kbd> + <kbd>C</kbd></span>
                                <span class="shortcut-description">Ir a Citas</span>
                            </div>
                        </div>

                        <div class="shortcuts-group">
                            <h3>Acceso Directo a Formularios</h3>
                            <div class="shortcut-item">
                                <span class="shortcut-keys"><kbd>Ctrl</kbd> + <kbd>Shift</kbd> + <kbd>M</kbd></span>
                                <span class="shortcut-description">Formulario de registro de médicos</span>
                            </div>
                            <div class="shortcut-item">
                                <span class="shortcut-keys"><kbd>Ctrl</kbd> + <kbd>Shift</kbd> + <kbd>P</kbd></span>
                                <span class="shortcut-description">Formulario de registro de pacientes</span>
                            </div>
                            <div class="shortcut-item">
                                <span class="shortcut-keys"><kbd>Ctrl</kbd> + <kbd>Shift</kbd> + <kbd>C</kbd></span>
                                <span class="shortcut-description">Formulario de registro de citas</span>
                            </div>
                            <div class="shortcut-item">
                                <span class="shortcut-keys"><kbd>Ctrl</kbd> + <kbd>Shift</kbd> + <kbd>U</kbd></span>
                                <span class="shortcut-description">Formulario de registro de usuarios</span>
                            </div>
                        </div>

                        <div class="shortcuts-group">
                            <h3>Otros Atajos</h3>
                            <div class="shortcut-item">
                                <span class="shortcut-keys"><kbd>Ctrl</kbd> + <kbd>K</kbd></span>
                                <span class="shortcut-description">Mostrar esta sección de atajos</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
