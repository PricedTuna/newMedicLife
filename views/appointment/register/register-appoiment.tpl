<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/forms.css">
    <link rel="stylesheet" href="/assets/css/flatpickr.min.css">
    <link rel="stylesheet" href="/assets/css/usability-improvements.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <link rel="icon" href="/views/dashboard/icons/Untitled-design-_1_.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/assets/css/cancel-button.css">
    <script src="/views/components/sidebar.app.js" defer></script>
    <script src="/assets/js/flatpickr.min.js"></script>
    <script src="/assets/js/es.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/scripts/form-validations.js"></script>
    <title>Solicitar Cita | Medic Life</title>

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
        window.doctors = {$doctors|json_encode};
        window.patients = {$patients|json_encode};
        window.schedules = {$schedules|json_encode};
        window.allAppointments = {$allAppointments|json_encode};
        window.appointment = {$appointment|json_encode};
        window.isDoctor = {$isDoctor|json_encode};
    </script>
    <script src="/views/appointment/register/register-appoiment.js"></script>

</head>

<body>

    {include file=$sidebarPath}

    <main class="content">
        <div class="center-container">
            <div class="form-container">
                <div class="form-header">
                    <a href="#" class="form-back-btn" id="cancel-btn">
                        <button class="back-btn">Cancelar</button>
                        <span class="back-btn-icon">&#8617;</span>
                    </a>
                    <h2 class="form-title">Solicitar Citas</h2>
                </div>
                <form id="solicitarCita" method="POST"
                    action="/controllers/appoiment/register-appoiment.controller.php">
                    <input type="hidden" name="appointment_id" value="{$appointment.id|default: ''}"></input>
                    <input type="hidden" id="appointmentId" name="appointment_id" value="{$appointment.id|default: ''}">

                    {* Se realiza la busqueda del paciente por su curp o nombre *}
                    <div class="form-group">
                        <label>Busqueda de paciente: Ingrese el nombre o CURP
                            <span class="tooltip-container">
                                <i class="bi bi-question-circle tooltip-icon"></i>
                                <span class="tooltip-text">Ingrese el nombre completo o CURP del paciente para buscarlo en el sistema.</span>
                            </span>
                        </label>
                        <input type="text" id="CURP" name="curp" list="curpList" autocomplete="off"
                            value="{$appointment.curp|default:''} {$appointment.full_name|default:''}" required>
                        <datalist id="curpList"></datalist>
                        <small id="curpError" style="color: red; display: none;"></small>
                    </div>

                    <div class="form-group">
                        <label for="patientId">Número de identificación del paciente
                            <span class="tooltip-container">
                                <i class="bi bi-question-circle tooltip-icon"></i>
                                <span class="tooltip-text">Identificador único del paciente en el sistema. Este campo se llena automáticamente.</span>
                            </span>
                        </label>
                        <input type="text" name="id_patient" id="patientId" value="{$appointment.id_patient|default:''}"
                            readonly required>
                    </div>

                    {* Aquí se envía el email del paciente para notificarlo por correo electrónico *}
                    <div class="form-group">
                        <input type="hidden" name="patient_email" id="patientEmail"
                            value="{$appointment.patient_email|default:''}" required>
                    </div>

                    <div class="form-group">
                        <label for="name">Nombre del paciente
                            <span class="tooltip-container">
                                <i class="bi bi-question-circle tooltip-icon"></i>
                                <span class="tooltip-text">Nombre completo del paciente. Este campo se llena automáticamente.</span>
                            </span>
                        </label>
                        <input type="text" name="patientName" id="patientName" placeholder="Nombre completo"
                            value="{$appointment.patient_name|default:''} {$appointment.last_name|default:''} {$appointment.last_name2|default:''}"
                            readonly required></input>
                    </div>

                    <div class="form-group">
                        <label for="speciality">Especialidad
                            <span class="tooltip-container">
                                <i class="bi bi-question-circle tooltip-icon"></i>
                                <span class="tooltip-text">Seleccione la especialidad médica requerida para la cita.</span>
                            </span>
                        </label>
                        <select name="id_medical_area" id="speciality" required>
                            {foreach from=$medical_areas item=area}
                                <option value="{$area.id}"
                                    {if isset($appointment) && isset($appointment.id_medical_area) && $area.id == $appointment.id_medical_area}
                                    selected {/if}>
                                    {$area.name}
                                </option>
                            {/foreach}
                        </select>
                        <input type="hidden" name="name_medical_area" id="name_medical_area"
                            value="{$appointment.name_medical_area|default:''}">
                    </div>

                    <div class="form-group">
                        <label for="doctor">Médico
                            <span class="tooltip-container">
                                <i class="bi bi-question-circle tooltip-icon"></i>
                                <span class="tooltip-text">Seleccione el médico con quien desea agendar la cita.</span>
                            </span>
                        </label>
                        <select name="id_doctor" id="doctor" required>
                            <option value="">Seleccione un médico</option>
                        </select>
                    </div>

                    <div class="form-group" id="doctorScheduleContainer" style="display: none;">
                        <label>Horarios disponibles del médico
                            <span class="tooltip-container">
                                <i class="bi bi-question-circle tooltip-icon"></i>
                                <span class="tooltip-text">Estos son los horarios en los que el médico seleccionado atiende pacientes.</span>
                            </span>
                        </label>
                        <ul id="doctorScheduleList" style="padding-left: 20px;"></ul>
                    </div>


                    <input type="hidden" id="name_doctor" name="name_doctor"
                        value="{$appointment.name_doctor|default:''} {$appointment.doctor_last_name|default:''} {$appointment.doctor_last_name2|default:''}"
                        required>


                    <div class="form-group">
                        <label for="appointmentDate">Hora y Fecha
                            <span class="tooltip-container">
                                <i class="bi bi-question-circle tooltip-icon"></i>
                                <span class="tooltip-text">Seleccione la fecha y hora para la cita. Debe estar dentro del horario de atención del médico.</span>
                            </span>
                        </label>
                        <input type="text" id="appointmentDate" name="appointment_date"
                            value="{$appointment.appointment_date|default:''}" required>
                        <span id="dateError" style="color:red; display:none;">La fecha/hora no está en el horario del
                            doctor</span>
                    </div>

                    <button type="submit" class="submit-btn">Registrar Datos</button>
                </form>
            </div>
        </div>
    </main>
    <script src="/views/appointment/register/validate.js"></script>
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
                            window.location.href = '/views/appointment/list/list-appointments.view.php';
                        }
                    });
                });
            }
        });
    </script>
</body>

</html>
