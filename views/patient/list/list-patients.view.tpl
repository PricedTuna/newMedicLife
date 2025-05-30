<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {* <link rel="stylesheet" href="./register-patient.styles.css">
    <link rel="stylesheet" href="../../components/sidebar.styles.css">
    <script src="../../components/sidebar.app.js" defer></script>
    <script src="../../patient/register/register-patient.app.js" defer></script> *}

    <script src="/views/patient/list/views-handler.js" defer></script>
    <script src="/views/patient/register/register-patient.app.js" defer></script>
    <link rel="stylesheet" href="/views/patient/main/main-patient.styles.css">
    <link rel="stylesheet" href="/views/patient/register/register-patient.styles.css">
    <link rel="stylesheet" href="/views/dashboard/dashboard.styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <script src="/views/patient/list/list-patients.js" defer></script>
    <link rel="stylesheet" href="/register-patient.styles.css">
    <link rel="stylesheet" href="/views/patient/list/list-patients.styles.css">
    <link rel="stylesheet" href="/assets/css/usability-improvements.css">
    {* <link rel="stylesheet" href="../../../resset.css">  *}
    <script src="/views/components/sidebar.app.js" defer></script>

    <title>Lista de pacientes</title>
</head>
<body>
    {include file="../../components/sidebar.tpl"}

    <main >
        <div class="main-content">
            <div class="table-header">
                <h1>Lista de pacientes</h1>
                <a href="/views/patient/register/register-patient.view.php">
                    <button class="create-btn">Agregar Paciente</button>
                </a>
            </div>

            <div class="filter-container">
                <input type="text" id="searchInput" placeholder="Buscar por nombre, apellido o CURP...">
                <select id="genderFilter">
                    <option value="all">Todos los géneros</option>
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
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

                <a href="/views/patient/register/register-patient.view.php" aria-label="Agregar paciente">
                    <button class="icon-btn table-add-btn">+</button>
                </a>

                <table>
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nombre(s)</th>
                            <th>Apellido(s)</th>
                            <th>CURP</th>
                            <th>Teléfono</th>
                            <th>Sexo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if $patients|@count > 0}
                            {foreach from=$patients item=patient}
                                <tr>
                                <td class="photo-column">
                                        {if $patient.photo}
                                            <img src="/controllers/patient/mostrar_foto.php?id={$patient.id}" alt="Foto del paciente" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                        {else}
                                            <p>Sin foto</p>
                                        {/if}
                                    </td>
                                    </td>
                                    <td data-label="Nombre">{$patient.names|escape}</td>
                                    <td data-label="Apellidos">{$patient.last_name|escape} {$patient.last_name2|escape}</td>
                                    <td data-label="CURP">{$patient.CURP|escape}</td>
                                    <td data-label="Teléfono">{$patient.phone|escape}</td>
                                    <td data-label="Sexo">{$patient.gender|escape}</td>
                                    <td class="actions-td">
                                        <form action="/controllers/patient/delete-patient.controller.php" method="POST" class="action-wrapper">
                                            <input type="hidden" name="patient_id" value="{$patient.id}">
                                            <button type="submit" class="delete-btn" data-id="{$patient.id}"> Eliminar</button>
                                        </form>
                                        <a href="/views/patient/register/register-patient.view.php?id={$patient.id}" class="action-wrapper">
                                            <button class="update-btn">Actualizar</button>
                                        </a>
                                    </td>
                                </tr>
                            {/foreach}
                        {else}
                            <tr>
                                <td colspan="14">No hay pacientes registrados.</td>
                            </tr>
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
