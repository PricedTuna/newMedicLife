<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de citas | Medic Life </title>

    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/lists.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <link rel="stylesheet" href="/assets/css/usability-improvements.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/views/doctor/list/views-handler.js" defer></script>
    <script src="/views/appointment/register/register-appoiment.js" defer></script>
    <script src="/views/components/sidebar.app.js" defer></script>
    <link rel="icon" href="/views/dashboard/icons/Untitled-design-_1_.ico" type="image/x-icon">
    <script src="/scripts/form-validations.js"></script>
    <script src="/views/appointment/list/list-appointments.js" defer></script>
    <style>
        /* Add spacing between tables */
        .table-container {
            margin-bottom: 30px;
        }

        /* Different colors for each table */
        h2 + .table-container table {
            border: 1px solid #ddd;
        }

        /* Active appointments table - Blue */
        h2:nth-of-type(1) + .table-container table {
            background-color: #e6f2ff;
            border-color: #007bff;
        }

        h2:nth-of-type(1) + .table-container table thead {
            background-color: #007bff;
            color: white;
        }

        /* Terminated appointments table - Yellow with black text */
        h2:nth-of-type(2) + .table-container table {
            background-color: #fff8cc;
            border-color: #ffc107;
        }

        h2:nth-of-type(2) + .table-container table thead {
            background-color: #ffc107;
            color: black;
        }

        /* Finalized appointments table - Green */
        h2:nth-of-type(3) + .table-container table {
            background-color: #e6ffe6;
            border-color: #28a745;
        }

        h2:nth-of-type(3) + .table-container table thead {
            background-color: #28a745;
            color: white;
        }
    </style>
</head>

<body>
    {include file="../../components/sidebar.tpl"}

    <main>
        <div class="main-content">
            <div class="table-header">
                <h1>Lista de citas</h1>
                <a href="/views/appointment/register/register-appoiment.php">
                    <button class="create-btn">Crear cita</button>
                    <button class="icon-btn table-add-btn">+</button>
                </a>
            </div>

            <div class="filter-container">
                <input type="text" id="searchInput" placeholder="Buscar por paciente o médico...">
                <select id="statusFilter">
                    <option value="all">Todos los estados</option>
                    <option value="A">Pendientes</option>
                    <option value="T">Terminadas</option>
                    <option value="F">Pagadas</option>
                </select>
                <button id="clearFilters">Limpiar filtros</button>
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

            <h2>Citas Pendientes</h2>
            <div class="table-container">
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
                                        <td data-label="Médico">{$appointment.doctor_name}</td>
                                        <td data-label="Fecha">{$appointment.appointment_date}</td>
                                        <td class="actions-td">
                                            <a href="/views/pay/pay.view.php?id={$appointment.cita}" class="action-wrapper">
                                                <button class="finish-btn">Finalizar Cita</button>
                                            </a>
                                            <form action="/controllers/appoiment/delete-appointment.controller.php" method="POST"
                                                class="action-wrapper">
                                                <input type="hidden" name="appointment_id" value="{$appointment.cita}">
                                                <input type="hidden" name="patient_name" value="{$appointment.patient_name} {$appointment.last_name} {$appointment.last_name2}">
                                                <input type="hidden" name="patient_email" value="{$appointment.patient_email}">
                                                <input type="hidden" name="appointment_date" value="{$appointment.appointment_date}">
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

            <h2>Citas Terminadas</h2>
            <div class="table-container">
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
                                        <td data-label="Médico">{$appointment.doctor_name}</td>
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

            <h2>Citas Pagadas</h2>
            <div class="table-container">
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
                                        <td data-label="Médico">{$appointment.doctor_name}</td>
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
