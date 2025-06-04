<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./register-patient.styles.css">
    <link rel="stylesheet" href="../../components/sidebar.styles.css">
    <link rel="icon" href="/views/dashboard/icons/Untitled-design-_1_.ico" type="image/x-icon">
    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/forms.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/assets/css/cancel-button.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/views/components/sidebar.app.js" defer></script>
    <script src="./register-patient.app.js" defer></script>
    <title>Registro de Paciente</title>

    <script>
        window.municipalities = {$municipalities|json_encode};
        window.localities = {$localities|json_encode};
        window.states = {$states|json_encode};

        window.preselectedPatientData = {
            state: {$patient.id_state|default:'null'},
            municipality: {$patient.id_municipality|default:'null'},
            locality: {$patient.id_locality|default:'null'}
        };
    </script>
    <script src="form_steps.js" defer></script>
    <style>
        .tooltip-icon {
            margin-left: 5px;
            color: #007bff;
            cursor: help;
            font-size: 14px;
        }

        .tooltip-text {
            visibility: hidden;
            width: 200px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -100px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .tooltip-container {
            position: relative;
            display: inline-block;
        }

        .tooltip-container:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cancelBtn = document.getElementById('cancel-btn');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: 'Si cancelas, se perderán todos los datos ingresados en el formulario.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, cancelar',
                        cancelButtonText: 'No, continuar editando'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/views/patient/main/main-patient.view.php';
                        }
                    });
                });
            }
        });
    </script>
</head>

<body>

    {include file="../../components/sidebar.tpl"}

    <main class="content">
        <div class="center-container">
            <div class="form-container">
                <div class="form-header">
                    <a href="#" class="form-back-btn" id="cancel-btn">
                        <button class="back-btn" style="background-color: #dc3545; color: white;">Cancelar</button>
                        <span class="back-btn-icon">&#8617;</span>
                    </a>
                    <h2 class="form-title">
                        {if $patient}
                            Actualizar Paciente
                        {else}
                            Registrar Paciente
                        {/if}
                    </h2>
                </div>
            {if isset($success)}
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
</main>

</body>

</html>
