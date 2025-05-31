<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <link rel="stylesheet" href="./register-appoiment.css">
    <link rel="stylesheet" href="/assets/css/flatpickr.min.css">
    <link rel="stylesheet" href="/assets/css/usability-improvements.css">
    <script src="../../components/sidebar.app.js" defer></script>
    <script src="/assets/js/flatpickr.min.js"></script>
    <script src="/assets/js/es.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/scripts/form-validations.js"></script>
    <title>Solicitar Cita</title>

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
            <h2>Solicitar Citas</h2>
            <form id="solicitarCita" method="POST" action="/controllers/appoiment/register-appoiment.controller.php">
                <input type="hidden" name="appointment_id" value="{$appointment.id|default: ''}"></input>
                <input type="hidden" id="appointmentId" name="appointment_id" value="{$appointment.id|default: ''}">
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

                <div class="form-group">
                    <label for="name">Nombre del paciente</label>
                    <input type="text" id="patientName" placeholder="Nombre completo"
                        value="{$appointment.patient_name|default:''} {$appointment.last_name|default:''} {$appointment.last_name2|default:''}"
                        disabled></input>
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
                </div>



                <div class="form-group">
                    <label for="speciality">Médico</label>
                    <select name="id_doctor" id="doctor" required>
                        <option value="">Seleccione un médico</option>
                    </select>
                </div>


                <div class="form-group" id="appointmentDateContainer" style="display: none;">
                    <label for="appointmentDate">Hora y Fecha</label>
                    <input type="text" id="appointmentDate" name="appointment_date"
                        value="{$appointment.appointment_date|default:''}" required>
                    <span id="dateError" style="color:red; display:none;">La fecha/hora no está en el horario del
                        doctor</span>
                </div>


                <div class="form-group" id="horariosDisponiblesContainer" style="display: none;">
                    <label>Horarios disponibles del médico:</label>
                    <ul id="horariosDisponibles">
                        {* Este bloque se llenará dinámicamente con JavaScript *}
                    </ul>
                </div>


                <button type="submit" class="submit-btn">Registrar Datos</button>
            </form>
        </div>
        </div>
    </main>
    <script src="/views/appointment/register/schedules.js"></script>
</body>

</html>