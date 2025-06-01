<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/views/dashboard/icons/Untitled-design-_1_.ico" type="image/x-icon">
    {* <link rel="stylesheet" href="./register-patient.styles.css">
    <link rel="stylesheet" href="../../components/sidebar.styles.css">
    <script src="../../components/sidebar.app.js" defer></script>
    <script src="../../patient/register/register-patient.app.js" defer></script> *}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/views/doctor/list/views-handler.js" defer></script>
    <script src="/views/doctor/register/register-doctor.app.js" defer></script>
    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/lists.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <link rel="stylesheet" href="/assets/css/usability-improvements.css">
    <script src="/views/doctor/list/list-doctors.js" module defer></script>
    {* <link rel="stylesheet" href="../../../resset.css">  *}
    <script src="/views/components/sidebar.app.js" defer></script>

    <title>Lista de médicos</title>
</head>
<body>

    {include file="../../components/sidebar.tpl"}

    <main >

        <div class="main-content">
            <div class="table-header">
                <h1>Lista de médicos    </h1>
                <a href="/views/doctor/register/register-doctor.view.php">
                    <button class="create-btn">Agregar médicos</button>
                    <button class="icon-btn table-add-btn">+</button>
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


                <table>
                    <thead>
                        <tr>
                            <th class="photo-column">Foto</th>
                            <th>Nombre(s)</th>
                            <th>Apellido(s)</th>
                            <th>CURP</th>
                            <th>Teléfono</th>
                            <th>Sexo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if $doctors|@count > 0}
                            {foreach from=$doctors item=doctor}
                                <tr>
                                    <td class="photo-column">
                                        {if $doctor.photo}
                                            <img src="/controllers/doctor/mostrar_foto.php?id={$doctor.id}" alt="Foto del doctor" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                        {else}
                                            <p>Sin foto</p>
                                        {/if}
                                    </td>
                                    <td data-label="Nombre">{$doctor.names|escape}</td>
                                    <td data-label="Apellidos">{$doctor.last_name|escape} {$doctor.last_name2|escape}</td>
                                    <td data-label="CURP">{$doctor.CURP|escape}</td>
                                    <td data-label="Teléfono">{$doctor.phone|escape}</td>
                                    <td data-label="Sexo">{$doctor.gender|escape}</td>
                                    <td class="actions-td">
                                        <form action="/controllers/doctor/delete-doctor.controller.php" method="POST" class="action-wrapper">
                                            <input type="hidden" name="doctor_id" value="{$doctor.id}">
                                            <button type="submit" class="delete-btn" data-id="{$doctor.id}">Eliminar</button>
                                        </form>
                                        <a href="/views/doctor/register/register-doctor.view.php?id={$doctor.id}" class="action-wrapper">
                                            <button class="update-btn">Actualizar</button>
                                        </a>
                                    </td>
                                </tr>
                            {/foreach}
                        {else}
                            <tr>
                                <td colspan="14">No hay médicos registrados.</td>
                            </tr>
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
