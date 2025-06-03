<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Búsqueda de Historial Médico</title>
    <link rel="stylesheet" href="/views/patient/medical_story/medical-story.styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/views/patient/medical_story/medical-story.app.js" defer></script>
</head>
<body>
    <main>
        <div class="container">
            <h1>Búsqueda de Historial Médico</h1>

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

            <div class="search-container">
                <form id="searchForm">
                    <div class="form-group">
                        <label for="curp">CURP del Paciente</label>
                        <input type="text" id="curp" name="curp" placeholder="Ingrese el CURP del paciente" required>
                    </div>
                    <button type="submit" id="searchButton">Buscar</button>
                </form>
            </div>

            <div id="patientDetails" class="patient-details" style="display: none;">
                <h2>Confirmar Identidad del Paciente</h2>
                <p>Por favor, confirme que este es el paciente correcto:</p>

                <div class="patient-info">
                    <p><strong>Nombre completo:</strong> <span id="patientName"></span></p>
                    <p><strong>Fecha de nacimiento:</strong> <span id="patientBirthDate"></span></p>
                    <p><strong>Correo electrónico:</strong> <span id="patientEmail"></span></p>
                </div>

                <div class="confirmation-buttons">
                    <button id="confirmButton" class="confirm-btn">Confirmar</button>
                    <button id="cancelButton" class="cancel-btn">Cancelar</button>
                </div>
            </div>

            <div id="successMessage" class="success-container" style="display: none;">
                <h2>Historial Médico Aceptado</h2>
                <p>Se aceptó tu historial médico. Se ha enviado un correo electrónico con el asunto "Historial médico" a tu dirección de correo registrada.</p>
                <button id="newSearchButton" class="new-search-btn">Nueva Búsqueda</button>
            </div>
        </div>
    </main>
</body>
</html>
