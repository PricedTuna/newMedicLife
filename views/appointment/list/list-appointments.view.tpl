<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de citas</title>

    <script src="/views/doctor/list/views-handler.js" defer></script>
    <script src="/views/appointment/register/register-appoiment.js" defer></script>
    <script src="/views/components/sidebar.app.js" defer></script>
    <link rel="stylesheet" href="/views/appointment/main/main-appointment.styles.css">
    <link rel="stylesheet" href="/views/appointment/register/register-appoiment.css">
    <link rel="stylesheet" href="/views/dashboard/dashboard.styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <link rel="stylesheet" href="/views/appointment/list/list-appointments.styles.css">
    <link rel="stylesheet" href="/assets/css/usability-improvements.css">
    <link rel="icon" href="/views/dashboard/icons/Untitled-design-_1_.ico" type="image/x-icon">
    <script src="/scripts/form-validations.js"></script>
    <script src="/views/appointment/list/list-appointments.js" defer></script>
</head>

<body>
    {include file="../../components/sidebar.tpl"}

    <main>
        <div class="main-content">
            <div class="table-header">
                <h1>Lista de citas</h1>
                <a href="/views/appointment/register/register-appoiment.php">
                    <button class="create-btn">Crear cita</button>
                </a>
            </div>

            <div class="filter-container">
                <input type="text" id="searchInput" placeholder="Buscar por paciente o médico...">
                <select id="statusFilter">
                    <option value="all">Todos los estados</option>
                    <option value="A">Activas</option>
                    <option value="T">Terminadas</option>
                    <option value="F">Finalizadas</option>
                </select>
                <button id="clearFilters">Limpiar filtros</button>
            </div>

            <div class="table-container">
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

                <a href="/views/appointment/register/register-appoiment.php" aria-label="Crear cita">
                    <button class="icon-btn table-add-btn">+</button>
                </a>

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
                        {if isset($appointments) && count($appointments) > 0}
                            {foreach from=$appointments item=appointment}
                                {if $appointment.status == 'A'}
                                    <tr data-status="A">
                                        <td data-label="ID">{$appointment.cita}</td>
                                        <td data-label="Paciente">{$appointment.patient_name} {$appointment.last_name}
                                            {$appointment.last_name2}</td>
                                        <td data-label="Área Médica">{$appointment.medical_area}</td>
                                        <td data-label="Doctor">{$appointment.doctor_name}</td>
                                        <td data-label="Fecha">{$appointment.appointment_date}</td>
                                        <td class="actions-td">
                                            <a href="/views/pay/pay.view.php?id={$appointment.cita}" class="action-wrapper">
                                                <button class="finish-btn">Finalizar Cita</button>
                                            </a>
                                            <form action="/controllers/appoiment/delete-appointment.controller.php" method="POST"
                                                class="action-wrapper">
                                                <input type="hidden" name="appointment_id" value="{$appointment.cita}">
                                                <button type="submit" class="delete-btn"
                                                    data-id="{$appointment.cita}">Eliminar</button>
                                            </form>
                                            <a href="/views/appointment/register/register-appoiment.php?id={$appointment.cita}"
                                                class="action-wrapper">
                                                <button class="update-btn">Actualizar</button>
                                            </a>
                                        </td>
                                    </tr>
                                {/if}
                            {/foreach}
                        {else}
                            <tr>
                                <td colspan="6">No hay citas activas registradas.</td>
                            </tr>
                        {/if}
                    </tbody>
                </table>
            </div>

            <div class="table-container">
                <h2>Citas Terminadas</h2>
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
                        {if isset($appointments) && count($appointments) > 0}
                            {assign var="hasTerminated" value=false}
                            {foreach from=$appointments item=appointment}
                                {if $appointment.status == 'T'}
                                    {assign var="hasTerminated" value=true}
                                    <tr data-status="T">
                                        <td data-label="ID">{$appointment.cita}</td>
                                        <td data-label="Paciente">{$appointment.patient_name} {$appointment.last_name}
                                            {$appointment.last_name2}</td>
                                        <td data-label="Área Médica">{$appointment.medical_area}</td>
                                        <td data-label="Doctor">{$appointment.doctor_name}</td>
                                        <td data-label="Fecha">{$appointment.appointment_date}</td>
                                        <td class="actions-td">
                                            <a href="/views/pay/pay.view.php?id={$appointment.cita}" class="action-wrapper">
                                                <button class="finish-btn">Finalizar Cita</button>
                                            </a>
                                        </td>
                                    </tr>
                                {/if}
                            {/foreach}
                            {if !$hasTerminated}
                                <tr>
                                    <td colspan="6">No hay citas terminadas.</td>
                                </tr>
                            {/if}
                        {else}
                            <tr>
                                <td colspan="6">No hay citas terminadas registradas.</td>
                            </tr>
                        {/if}
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
                        {if isset($appointments) && count($appointments) > 0}
                            {assign var="hasFinalized" value=false}
                            {foreach from=$appointments item=appointment}
                                {if $appointment.status == 'F'}
                                    {assign var="hasFinalized" value=true}
                                    <tr data-status="F">
                                        <td data-label="ID">{$appointment.cita}</td>
                                        <td data-label="Paciente">{$appointment.patient_name} {$appointment.last_name}
                                            {$appointment.last_name2}</td>
                                        <td data-label="Área Médica">{$appointment.medical_area}</td>
                                        <td data-label="Doctor">{$appointment.doctor_name}</td>
                                        <td data-label="Fecha">{$appointment.appointment_date}</td>
                                        <td class="actions-td">
                                            <span style="color: gray;">Finalizada</span>
                                        </td>
                                    </tr>
                                {/if}
                            {/foreach}
                            {if !$hasFinalized}
                                <tr>
                                    <td colspan="6">No hay citas finalizadas.</td>
                                </tr>
                            {/if}
                        {else}
                            <tr>
                                <td colspan="6">No hay citas finalizadas registradas.</td>
                            </tr>
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
