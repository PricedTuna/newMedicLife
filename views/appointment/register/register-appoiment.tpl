<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./register-appoiment.css">
    <link rel="stylesheet" href="/assets/css/flatpickr.min.css">
    <script src="../../components/sidebar.app.js" defer></script>
    <link rel="stylesheet" href="../../components/sidebar.styles.css">
    <script src="/assets/js/flatpickr.min.js"></script>
    <script src="/assets/js/es.js"></script>
    <title>Solicitar Cita</title>

    <script>
        window.doctors = {$doctors|json_encode};
        window.patients = {$patients|json_encode};
        window.schedules = {$schedules|json_encode};
    </script>
    <script src="/views/appointment/register/register-appoiment.js"></script>

</head>

<body>

    {include file=$sidebarPath}

    <div class="center-container">
        <div class="form-container">
            <h2>Solicitar Citas</h2>
            <form id="solicitarCita" method="POST" action="/controllers/appoiment/register-appoiment.controller.php">
                <div class="form-group">
                    <label>Busqueda de paciente: Ingrese el nombre o CURP</label>
                    <input type="text" id="CURP" name="curp" list="curpList" autocomplete="off" required>
                    <datalist id="curpList"></datalist>
                    <small id="curpError" style="color: red; display: none;"></small>
                </div>

                <div class="form-group">
                    <label for="patientId">Número de identificación del paciente</label>
                    <input type="text" name="id_patient" id="patientId" required>
                </div>

                <div class="form-group">
                    <label for="name">Nombre del paciente</label>
                    <input type="text" id="patientName" placeholder="Nombre completo" disabled></input>
                </div>

                <div class="form-group">
                    <label for="speciality">Especialidad</label>
                    <select name="id_medical_area" id="speciality" required>
                        {foreach from=$medical_areas item=area}
                            <option value="{$area.id}">
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

                <div class="form-group">
                    <label for="appointmentDate">Hora y Fecha</label>
                    <input type="text" id="appointmentDate" name="appointment_date" required>
                    <span id="dateError" style="color:red; display:none;">La fecha/hora no está en el horario del
                        doctor</span>
                </div>

                <button type="submit" class="submit-btn">Registrar Datos</button>
            </form>
        </div>
    </div>
</body>

</html>