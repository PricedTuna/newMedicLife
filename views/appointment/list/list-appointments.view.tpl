<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de citas</title>

    <script src="/views/doctor/list/views-handler.js" defer></script>
    <script src="/views/appointment/register/register-appointment.app.js" defer></script>
    <script src="./list-appointments.js" defer></script>
    <script src="/views/components/sidebar.app.js" defer></script>

    <link rel="stylesheet" href="/views/appointment/main/main-appointment.styles.css">
    <link rel="stylesheet" href="/views/appointment/register/register-appoiment.css">
    <link rel="stylesheet" href="/views/dashboard/dashboard.styles.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <link rel="stylesheet" href="/views/appointment/list/list-appointments.styles.css">
</head>
<body>
    {include file="../../components/sidebar.tpl"}

    <main>
        <div class="main-content">
            <div class="table-header">
                <h1>Lista de Citas</h1>
                <a href="/views/appointment/register/register-appoiment.php">
                    <button class="create-btn">Crear cita</button>
                </a>
            </div>

            {if isset($error)}
                <div style="color: red; margin-bottom: 1rem; border: 1px solid red; padding: 0.5rem; border-radius: 5px;">
                    {$error|escape}
                </div>
            {/if}

            {if isset($success)}
                <div style="color: darkgreen; margin-bottom: 1rem; border: 1px solid green; padding: 0.5rem; border-radius: 5px; background-color: lightgreen;">
                    {$success|escape}
                </div>
            {/if}

            <div class="table-container">
                <h2>Citas Activas</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Numero de cita</th>
                            <th>Paciente</th>
                            <th>Área médica</th>
                            <th>Médico</th>
                            <th>Fecha y hora</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$appointments item=appointment}
                            {if $appointment.status == 'A'}
                                <tr>
                                    <td data-label="ID">{$appointment.cita}</td>
                                    <td data-label="Paciente">{$appointment.patient_name} {$appointment.last_name} {$appointment.last_name2}</td>
                                    <td data-label="Área Médica">{$appointment.medical_area}</td>
                                    <td data-label="Doctor">{$appointment.doctor_name}</td>
                                    <td data-label="Fecha">{$appointment.appointment_date}</td>
                                    <td class="actions-td">
                                        <a href="/views/pay/pay.view.php?id={$appointment.cita}" class="action-wrapper">
                                            <button class="finish-btn">Finalizar Cita</button>
                                        </a>
                                        <form action="/controllers/appoiment/delete-appointment.controller.php" method="POST" class="action-wrapper">
                                            <input type="hidden" name="appointment_id" value="{$appointment.cita}">
                                            <button type="submit" class="delete-btn" data-id="{$appointment.cita}">Eliminar</button>
                                        </form>
                                        <a href="/views/appointment/register/register-appoiment.php?id={$appointment.cita}" class="action-wrapper">
                                            <button class="update-btn">Actualizar</button>
                                        </a>
                                    </td>
                                </tr>
                            {/if}
                        {/foreach}
                    </tbody>
                </table>
            </div>

            <div class="table-container">
                <h2>Citas Finalizadas</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Numero de cita</th>
                            <th>Paciente</th>
                            <th>Área médica</th>
                            <th>Médico</th>
                            <th>Fecha y hora</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$appointments item=appointment}
                            {if $appointment.status == 'F'}
                                <tr>
                                    <td data-label="ID">{$appointment.cita}</td>
                                    <td data-label="Paciente">{$appointment.patient_name} {$appointment.last_name} {$appointment.last_name2}</td>
                                    <td data-label="Área Médica">{$appointment.medical_area}</td>
                                    <td data-label="Doctor">{$appointment.doctor_name}</td>
                                    <td data-label="Fecha">{$appointment.appointment_date}</td>
                                    <td class="actions-td">
                                        <span style="color: gray;">Finalizada</span>
                                    </td>
                                </tr>
                            {/if}
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>


