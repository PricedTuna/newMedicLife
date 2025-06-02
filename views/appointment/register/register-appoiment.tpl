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
    <script src="/views/components/sidebar.app.js" defer></script>
    <script src="/assets/js/flatpickr.min.js"></script>
    <script src="/assets/js/es.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/scripts/form-validations.js"></script>
    <title>Solicitar Cita | Medic Life</title>

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
                    <a href="/views/appointment/list/list-appointments.view.php" class="form-back-btn">
                        <button class="back-btn">Volver</button>
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
                        <label>Busqueda de paciente: Ingrese el nombre o CURP</label>
                        <input type="text" id="CURP" name="curp" list="curpList" autocomplete="off"
                            value="{$appointment.curp|default:''} {$appointment.full_name|default:''}" required>
                        <datalist id="curpList"></datalist>
                        <small id="curpError" style="color: red; display: none;"></small>
                    </div>

                    <div class="form-group">
                        <label for="patientId">Número de identificación del paciente</label>
                        <input type="text" name="id_patient" id="patientId" value="{$appointment.id_patient|default:''}"
                            required>
                    </div>

                    {* Aquí se envía el email del paciente para notificarlo por correo electrónico *}
                    <div class="form-group">
                        <input type="hidden" name="patient_email" id="patientEmail"
                            value="{$appointment.patient_email|default:''}" required>
                    </div>

                    <div class="form-group">
                        <label for="name">Nombre del paciente</label>
                        <input type="text" name="patientName" id="patientName" placeholder="Nombre completo"
                            value="{$appointment.patient_name|default:''} {$appointment.last_name|default:''} {$appointment.last_name2|default:''}"
                            required></input>
                    </div>

                    <div class="form-group">
                        <label for="speciality">Especialidad</label>
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
                        <label for="speciality">Médico</label>
                        <select name="id_doctor" id="doctor" required>
                            <option value="">Seleccione un médico</option>
                        </select>
                    </div>

                    <input type="hidden" id="name_doctor" name="name_doctor"
                        value="{$appointment.name_doctor|default:''} {$appointment.doctor_last_name|default:''} {$appointment.doctor_last_name2|default:''}"
                        required>


                    <div class="form-group">
                        <label for="appointmentDate">Hora y Fecha</label>
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

</body>

</html>