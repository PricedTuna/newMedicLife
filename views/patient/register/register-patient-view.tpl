<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./register-patient.styles.css">
    <link rel="stylesheet" href="../../components/sidebar.styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../components/sidebar.app.js" defer></script>
    <script src="./register-patient.app.js" defer></script>
    <title>Registro de Paciente</title>

    <!-- Inyectar las variables PHP en JavaScript -->
    <script>
        // Asegúrate de que las variables de Smarty se inyecten correctamente en JavaScript
        window.municipalities = {$municipalities|json_encode};
        window.localities = {$localities|json_encode};
        window.states = {$states|json_encode};

        // Datos preseleccionados para actualización
        window.preselectedPatientData = {
            state: {$patient.id_state|default:'null'},
            municipality: {$patient.id_municipality|default:'null'},
            locality: {$patient.id_locality|default:'null'}
        };
    </script>
    <script src="form_steps.js" defer></script>
</head>

<body>

    <div class="registerPatientWrapper">
        {include file="../../components/sidebar.tpl"}

        <div class="form-container">
            <h2 class="form-title">
                {if $patient}
                    Actualizar Paciente
                {else}
                    Registrar Paciente
                {/if}
            </h2>
            {if isset($success)}
                <!-- Mostrar mensaje de éxito -->
                <div
                    style="color: darkgreen; margin-bottom: 1rem; border: 1px solid green; padding: 0.5rem; border-radius: 5px; background-color: lightgreen;">
                    {$success|escape}
                </div>
            {/if}

            <div class="steps">
                <div class="step step-active" data-step="1">Paso 1</div>
                <div class="step" data-step="2">Paso 2</div>
                <div class="step" data-step="3">Paso 3</div>
                <div class="step" data-step="4">Paso 4</div>
            </div>

            <!-- Si hay un error, lo mostramos aquí -->
            {if isset($error)}
                <div style="color: red; margin-bottom: 1rem; border: 1px solid red; padding: 0.5rem; border-radius: 5px;">
                    {$error|escape}
                </div>
            {/if}

            <form id="patient-form" action="/controllers/patient/register-patient.controller.php" method="POST" enctype="multipart/form-data">
                {include file='steps/step1.tpl'}
                {include file='steps/step2.tpl'}
                {include file='steps/step3.tpl'}
                {include file='steps/step4.tpl'}
            </form>
        </div>
    </div>

</body>

</html>
