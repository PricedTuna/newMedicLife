<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/views/dashboard/icons/Untitled-design-_1_.ico" type="image/x-icon">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <script src="/views/components/sidebar.app.js" defer></script>
    <script src="/views/doctor/list/views-handler.js" defer></script>
    <script src="/views/doctor/register/register-doctor.app.js" defer></script>
    <link rel="stylesheet" href="/views/doctor/main/main-doctor.styles.css">
    <link rel="stylesheet" href="/views/doctor/list/list-doctors.styles.css">
    <link rel="stylesheet" href="/views/doctor/register/register-doctor.styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Registro de Médicos</title>

    <script>
        // Asegúrate de que las variables de Smarty se inyecten correctamente en JavaScript
        window.municipalities = {$municipalities|json_encode};
        window.localities = {$localities|json_encode};
        window.states = {$states|json_encode};

        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('photo');
            const imagePreview = document.getElementById('doctor-image-preview');
            const previewPlaceholder = document.getElementById('doctor-preview-placeholder');

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

    <script src="register-doctor.view.js" defer></script>
    <script>
        window.preselectedDoctorData = {
            state: {$doctor.id_state|default:'null'},
            municipality: {$doctor.id_municipality|default:'null'},
            locality: {$doctor.id_locality|default:'null'}
        };
    </script>

</head>

<body>

    {include file=$sidebarPath} <!-- Aquí se incluye el sidebar, según la variable Smarty -->

    <main class="content">
        <div id="doctor-data"
            data-doctor="{$doctor|default:''}"
            data-state="{$doctor.state|default:''}"
            data-municipality="{$doctor.municipality|default:''}"
            data-locality="{$doctor.locality|default:''}">
        </div>


        <div class="center-container">
            <div class="form-container">
                <div class="form-header">
                    <a href="/views/doctor/main/main-doctor.view.php" class="form-back-btn">
                        <button class="back-btn">Volver</button>
                        <span class="back-btn-icon">&#8617;</span>
                    </a>
                    <h2 class="form-title">
                        {if $doctor}
                            Actualizar Médico
                        {else}
                            Registrar Médico
                        {/if}
                    </h2>
                </div>

                <!-- Indicadores de los pasos -->
                <div class="steps">
                    <div class="step step-active" data-step="1">Paso 1</div>
                    <div class="step" data-step="2">Paso 2</div>
                    <div class="step" data-step="3">Paso 3</div>
                </div>

                <!-- Si hay un error, lo mostramos aquí -->
                {if isset($error)}
                    <div
                        style="color: red; margin-bottom: 1rem; border: 1px solid red; padding: 0.5rem; border-radius: 5px;">
                        {$error|escape}
                    </div>
                {/if}

                <!-- Formulario para registrar o actualizar al doctor -->
                <form action="/controllers/doctor/register-doctor.controller.php" method="POST" id="doctor-form" enctype="multipart/form-data">
                    {include file='steps/step1.tpl'} <!-- Paso 1 -->
                    {include file='steps/step2.tpl'} <!-- Paso 2 -->
                    {include file='steps/step3.tpl'} <!-- Paso 3 -->
                </form>
            </div>
        </div>
    </main>
</body>

</html>
