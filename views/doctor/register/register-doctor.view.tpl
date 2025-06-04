<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/views/dashboard/icons/Untitled-design-_1_.ico" type="image/x-icon">
    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/forms.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/assets/css/cancel-button.css">
    <script src="/views/components/sidebar.app.js" defer></script>
    <script src="/views/doctor/list/views-handler.js" defer></script>
    <script src="/views/doctor/register/register-doctor.app.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Registro de Médicos</title>
    <style>
        .required {
            color: red;
            margin-left: 2px;
        }

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

        .back-btn {
            background-color: #dc3545;
            color: white;
        }
    </style>

    <script>
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
                            window.location.href = '/views/doctor/main/main-doctor.view.php';
                        }
                    });
                });
            }

            // Add event listener for form submission
            const doctorForm = document.getElementById('doctor-form');
            if (doctorForm) {
                doctorForm.addEventListener('submit', function(e) {
                    // Only show the dialog for new doctors, not for updates
                    if (!document.querySelector('input[name="id"]').value) {
                        e.preventDefault();
                        Swal.fire({
                            title: '¿Crear usuario para este doctor?',
                            text: 'Desea crear un usuario en la sección de usuarios para este doctor?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, crear usuario',
                            cancelButtonText: 'No, solo registrar doctor'
                        }).then((result) => {
                            // Add a hidden field to the form with the result
                            const createUserInput = document.createElement('input');
                            createUserInput.type = 'hidden';
                            createUserInput.name = 'create_user';
                            createUserInput.value = result.isConfirmed ? '1' : '0';
                            doctorForm.appendChild(createUserInput);

                            // Submit the form
                            doctorForm.submit();
                        });
                    }
                });
            }
        });
    </script>

</head>

<body>

    {include file=$sidebarPath}

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
                    <a href="#" class="form-back-btn" id="cancel-btn">
                        <button class="back-btn" style="background-color: #dc3545; color: white;">Cancelar</button>
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

                <div class="steps">
                    <div class="step step-active" data-step="1">Paso 1</div>
                    <div class="step" data-step="2">Paso 2</div>
                    <div class="step" data-step="3">Paso 3</div>
                </div>

                {if isset($error)}
                    <div
                        style="color: red; margin-bottom: 1rem; border: 1px solid red; padding: 0.5rem; border-radius: 5px;">
                        {$error|escape}
                    </div>
                {/if}

                <form action="/controllers/doctor/register-doctor.controller.php" method="POST" id="doctor-form" enctype="multipart/form-data">
                    {include file='steps/step1.tpl'}
                    {include file='steps/step2.tpl'}
                    {include file='steps/step3.tpl'}
                </form>
            </div>
        </div>
    </main>

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
                            window.location.href = '/views/doctor/main/main-doctor.view.php';
                        }
                    });
                });
            }
        });
    </script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listener for form submission
        const doctorForm = document.getElementById('doctor-form');
        if (doctorForm) {
            const isUpdateMode = document.querySelector('input[name="id"]') && document.querySelector('input[name="id"]').value;

            if (!isUpdateMode) {
                doctorForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Crear usuario para este doctor?',
                        text: '¿Desea crear un usuario en la sección de usuarios para este doctor?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, crear usuario',
                        cancelButtonText: 'No, solo registrar doctor'
                    }).then((result) => {
                        // Add a hidden field to the form with the result
                        const createUserInput = document.createElement('input');
                        createUserInput.type = 'hidden';
                        createUserInput.name = 'create_user';
                        createUserInput.value = result.isConfirmed ? '1' : '0';
                        doctorForm.appendChild(createUserInput);

                        // Submit the form
                        doctorForm.submit();
                    });
                });
            }
        }
    });
</script>
</body>

</html>
