<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard </title>
    <link rel="stylesheet" href="../components/sidebar.styles.css">
    <script src="../components/sidebar.app.js" defer></script>
    <link rel="stylesheet" href="./dashboard.styles.css">

    <script>
        var doctors = {$doctors|json_encode};
    </script>
    <script src="/views/dashboard/dashboard.app.js"></script>

</head>

<body>


    {include file='../components/sidebar.tpl'}

    <main>
        <div class="main-content">
            <header>

                <h1>Dashboard</h1>


                <div class="doctor-select-container" style="margin-top: 1rem;">
                    <label for="doctor-select">Selecciona un doctor:</label>
                    <select id="doctor-select">
                        <option value="">-- Todos los doctores --</option>
                        {foreach from=$doctors item=doctor}
                            <option value="{$doctor.id}">{$doctor.names} {$doctor.last_name} {$doctor.last_name2}</option>
                        {/foreach}
                    </select>
                </div>


                {if isset($smarty.get.success)}
                    <div
                        style="color: darkgreen; margin-bottom: 1rem; border: 1px solid green; padding: 0.5rem; border-radius: 5px; background-color: lightgreen;">
                        {$smarty.get.success|escape}
                    </div>
                {/if}
                <div class="search-container">
                    <input type="text" placeholder="Search type of keywords">
                </div>
            </header>
            {* <section class="stats">
                <div class="card">
                    <h3></h3>
                    <p></p>
                </div>
                <div class="card">
                    <h3></h3>
                    <p></p>
                </div>
                <div class="card">
                    <h3></h3>
                    <p></p>
                </div>
            </section> *}
            <section class="chart">
                <h3>Visitas de Pacientes</h3>
                <div class="chart-placeholder">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Paciente</th>
                                <th>Área Médica</th>
                                <th>Doctor</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$appointments item=appointment}
                                {if $appointment.status == 'A'}
                                    <tr data-doctor-id="{$appointment.id_doctor}">
                                        <td data-label="ID">{$appointment.cita}</td>
                                        <td data-label="Paciente">
                                            {$appointment.patient_names} {$appointment.patient_last_name}
                                            {$appointment.patient_last_name2}
                                        </td>
                                        <td data-label="Área Médica">{$appointment.medical_area}</td>
                                        <td data-label="Doctor">
                                            {$appointment.doctor_names} {$appointment.doctor_last_name}
                                            {$appointment.doctor_last_name2}
                                        </td>
                                        <td data-label="Fecha">{$appointment.appointment_date}</td>
                                        <td class="actions-td"></td>
                                    </tr>
                                {/if}
                            {/foreach}
                        </tbody>
                    </table>

                    {if not $appointments}
                        <p style="text-align: center; margin-top: 1rem;">No hay citas registradas.</p>
                    {/if}
                </div>
            </section>
            <section class="patient-data">
                <h3>Calendario</h3>
                <div id="calendar" class="calendar"></div>
            </section>
        </div>
        <div class="doctor-info">
            <div class="doctor-card">
                <div id="doctor-photo" class="doctor-photo">
                    
                </div>
                <h3 id="doctor-name">Nombre del doctor</h3>
                <div class="doctor-stats">
                    <p>Citas <br> <strong id="doctor-appointments">0</strong></p>
                </div>
            </div>
            <section class="upcoming-appointments">
                <h3>Siguientes Citas</h3>
                <div id="next-appointments">
                    <!-- Aquí se insertarán las siguientes citas -->
                </div>
            </section>
            <section class="upcoming-appointments-month">
                <h3>Citas del mes </h3>
                <div id="month-appointments">
                    <!-- Aquí se insertarán las citas del mes -->
                </div>
            </section>
        </div>

    </main>
</body>

</html>