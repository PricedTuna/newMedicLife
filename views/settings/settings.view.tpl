<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/views/components/sidebar.app.js" defer></script>
    <script src="/views/settings/font-size-adjuster.js" defer></script>
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

                <!-- Sección de Tamaño de Letra -->
                <div class="settings-section font-size-section">
                    <h2>Tamaño de Letra</h2>
                    <div class="settings-content">
                        <div class="setting-item">
                            <span class="setting-label">Ajustar tamaño de letra:</span>
                            <div class="font-size-controls">
                                <button id="decreaseFontBtn" aria-label="Disminuir tamaño de letra" title="Disminuir tamaño de letra" class="font-size-btn">
                                    A-
                                </button>
                                <button id="resetFontBtn" aria-label="Restablecer tamaño de letra" title="Restablecer tamaño de letra" class="font-size-btn">
                                    A
                                </button>
                                <button id="increaseFontBtn" aria-label="Aumentar tamaño de letra" title="Aumentar tamaño de letra" class="font-size-btn">
                                    A+
                                </button>
                            </div>
                        </div>
                        <div class="setting-description">
                            <p>Ajusta el tamaño de letra de la interfaz para mejorar la legibilidad según tus necesidades visuales.</p>
                            <p>Puedes aumentar, disminuir o restablecer el tamaño de letra utilizando los botones de arriba.</p>
                        </div>
                    </div>
                </div>

                <!-- Sección de Configuración de Transferencia Bancaria (Solo para Administradores) -->
                {if $isAdmin}
                <div class="settings-section transfer-config-section">
                    <h2>Configuración de Transferencia Bancaria</h2>
                    <div class="settings-content">
                        <div class="setting-description">
                            <p>Configure los datos bancarios que se mostrarán a los usuarios cuando realicen pagos por transferencia.</p>
                            <p>Esta información solo puede ser modificada por administradores del sistema.</p>
                        </div>

                        <form action="/controllers/pay/transfer_config.controller.php" method="POST" class="transfer-config-form">
                            <input type="hidden" name="action" value="save_transfer_config">

                            <div class="form-group">
                                <label for="bank_name">Nombre del Banco:</label>
                                <input type="text" id="bank_name" name="bank_name" value="{$transferConfig.bank_name|escape}" required>
                            </div>

                            <div class="form-group">
                                <label for="account_holder">Titular de la Cuenta:</label>
                                <input type="text" id="account_holder" name="account_holder" value="{$transferConfig.account_holder|escape}" required>
                            </div>

                            <div class="form-group">
                                <label for="account_number">Número de Cuenta:</label>
                                <input type="text" id="account_number" name="account_number" value="{$transferConfig.account_number|escape}" required>
                            </div>

                            <div class="form-group">
                                <label for="clabe">CLABE Interbancaria:</label>
                                <input type="text" id="clabe" name="clabe" value="{$transferConfig.clabe|escape}" required>
                            </div>

                            <div class="form-group">
                                <label for="additional_info">Información Adicional:</label>
                                <textarea id="additional_info" name="additional_info" rows="4">{$transferConfig.additional_info|escape}</textarea>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn-primary">Guardar Configuración</button>
                            </div>
                        </form>
                    </div>
                </div>
                {/if}

                <!-- Sección de Atajos de Teclado -->
                <div class="settings-section shortcuts-info-section">
                    <h2>Atajos de Teclado</h2>
                    <div class="shortcuts-container">
                        <div class="shortcuts-group">
                            <h3>Navegación Principal</h3>
                            <div class="shortcut-item">
                                <span class="shortcut-keys"><kbd>Alt</kbd> + <kbd>D</kbd></span>
                                <span class="shortcut-description">Ir al panel</span>
                            </div>
                            <div class="shortcut-item">
                                <span class="shortcut-keys"><kbd>Alt</kbd> + <kbd>M</kbd></span>
                                <span class="shortcut-description">Ir a Médicos</span>
                            </div>
                            <div class="shortcut-item">
                                <span class="shortcut-keys"><kbd>Alt</kbd> + <kbd>P</kbd></span>
                                <span class="shortcut-description">Ir a Pacientes</span>
                            </div>
                            <div class="shortcut-item">
                                <span class="shortcut-keys"><kbd>Alt</kbd> + <kbd>U</kbd></span>
                                <span class="shortcut-description">Ir a Usuarios</span>
                            </div>
                            <div class="shortcut-item">
                                <span class="shortcut-keys"><kbd>Alt</kbd> + <kbd>C</kbd></span>
                                <span class="shortcut-description">Ir a Citas</span>
                            </div>
                        </div>

                        <div class="shortcuts-group">
                            <h3>Acceso Directo a Formularios</h3>
                            <div class="shortcut-item">
                                <span class="shortcut-keys"><kbd>Alt</kbd> + <kbd>Shift</kbd> + <kbd>M</kbd></span>
                                <span class="shortcut-description">Formulario de registro de médicos</span>
                            </div>
                            <div class="shortcut-item">
                                <span class="shortcut-keys"><kbd>Alt</kbd> + <kbd>Shift</kbd> + <kbd>P</kbd></span>
                                <span class="shortcut-description">Formulario de registro de pacientes</span>
                            </div>
                            <div class="shortcut-item">
                                <span class="shortcut-keys"><kbd>Alt</kbd> + <kbd>Shift</kbd> + <kbd>C</kbd></span>
                                <span class="shortcut-description">Formulario de registro de citas</span>
                            </div>
                            <div class="shortcut-item">
                                <span class="shortcut-keys"><kbd>Alt</kbd> + <kbd>Shift</kbd> + <kbd>U</kbd></span>
                                <span class="shortcut-description">Formulario de registro de usuarios</span>
                            </div>
                        </div>

                        <div class="shortcuts-group">
                            <h3>Otros Atajos</h3>
                            <div class="shortcut-item">
                                <span class="shortcut-keys"><kbd>Alt</kbd> + <kbd>K</kbd></span>
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
