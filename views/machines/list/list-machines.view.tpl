<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Maquinas | Medic Life</title>

    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/lists.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <link rel="stylesheet" href="/assets/css/usability-improvements.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/views/medications/list/list-machines.js" defer></script>
    <script src="/views/components/sidebar.app.js" defer></script>

    <link rel="icon" href="/views/dashboard/icons/Untitled-design-_1_.ico" type="image/x-icon">

    <style>
        .type-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            background-color: #e0e0e0;
            color: #333;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
            text-align: center;
        }

        .status-active {
            background-color: #2ecc71;
            color: white;
        }

        .status-inactive {
            background-color: #e74c3c;
            color: white;
        }

        .small-purple-btn {
            text-align: center;
            font-size: 0.85rem;
            padding: 0.6rem 1rem;
            background-color: #8e44ad;
            transition: all 0.3s ease;
            transform-origin: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .small-purple-btn:hover {
            background-color: #9b59b6;
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(142, 68, 173, 0.4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(142, 68, 173, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(142, 68, 173, 0);
            }
        }

        .actions-td {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            justify-content: center;
        }

        .action-wrapper {
            margin: 2px;
        }


        .delete-btn {
            background-color: #ff4d4d;
            color: white;
        }

        .delete-btn:hover {
            background-color: #ff0000;
        }

        .tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }

        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border: 1px solid transparent;
            border-bottom: none;
            margin-right: 5px;
            border-radius: 5px 5px 0 0;
        }

        .tab.active {
            background-color: #f0f0f0;
            border-color: #ddd;
            border-bottom-color: white;
            margin-bottom: -1px;
            font-weight: bold;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }
    </style>
</head>

<body>
    {include file=$sidebarPath}

    <main>
        <div class="main-content">
            <div class="table-header">
                <h1>Administración de Maquinas para estudios</h1>
                <a href="/views/machines/create/create-machine.view.php#machine">
                    <button class="create-btn small-purple-btn">Agregar Máquina</button>
                    <button class="icon-btn table-add-btn">+</button>
                </a>

            </div>

            {if isset($message) && $success == 1}
                <div class="alert alert-success">
                    {$message|escape}
                </div>
            {elseif isset($message) && $success == 0}
                <div class="alert alert-danger">
                    {$message|escape}
                </div>
            {/if}

            <div class="tabs">
                <div class="tab active" data-tab="medications">Maquinas</div>
                <div class="tab" data-tab="types">Tipos de Maquinas</div>
            </div>

            <div id="medications-tab" class="tab-content active">
                {* <div class="filter-container">
                    <form action="" method="GET">
                        <input type="text" id="searchInput" name="search" placeholder="Buscar por nombre..."
                            value="{$search_term|default:''}">
                        <select id="typeFilter" name="type">
                            <option value="">Todos los tipos</option>
                            {foreach from=$machine_types item=type}
                                <option value="{$type.id}">{$type.name|escape}</option>
                            {/foreach}

                        </select>
                        <button type="submit">Buscar</button>
                        <button type="button" id="clearFilters">Limpiar filtros</button>
                    </form>
                </div> *}

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Área Médica</th>
                                <th>Estado Operativo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {if $machines|@count > 0}
                                {foreach from=$machines item=machine}
                                    <tr>
                                        <td data-label="Nombre">{$machine.name|escape}</td>
                                        <td data-label="Tipo">{$machine.machine_type|escape}</td>
                                        <td data-label="Área Médica">{$machine.medical_area|escape}</td>
                                        <td data-label="Estado">
                                            {if $machine.operational_status == 'Operativa'}
                                                <span class="status-badge status-active">Operativa</span>
                                            {elseif $machine.operational_status == 'Mantenimiento'}
                                                <span class="status-badge"
                                                    style="background-color:#f39c12; color:white;">Mantenimiento</span>
                                            {else}
                                                <span class="status-badge status-inactive">Fuera de servicio</span>
                                            {/if}
                                        </td>
                                        <td class="actions-td">
                                            <a href="/views/machines/create/create-machine.view.php?id={$machine.id}&form=machine"
                                                class="update-btn small-purple-btn">
                                                Editar
                                            </a>


                                            <a href="#" class="action-wrapper delete-machine" data-id="{$machine.id}">
                                                <button class="delete-btn">Eliminar</button>
                                            </a>
                                        </td>
                                    </tr>
                                {/foreach}
                            {else}
                                <tr>
                                    <td colspan="6">No hay máquinas registradas.</td>
                                </tr>
                            {/if}
                        </tbody>
                    </table>
                </div>

            </div>

            <div id="types-tab" class="tab-content">
                <div class="table-header">
                    <h2>Tipos de Máquina</h2>
                    <a href="/views/machines/create/create-machine.view.php?form=type">
                        <button class="create-btn small-purple-btn">Agregar Tipo</button>
                        <button class="icon-btn table-add-btn">+</button>
                    </a>


                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {if $machine_types|@count > 0}

                                {foreach from=$machine_types item=type}
                                    <tr>
                                        <td data-label="Nombre">{$type.name|escape}</td>
                                        <td class="actions-td">
                                            <a href="/views/machines/create/create-machine.view.php?id_type={$type.id}&form=type"
                                                class="update-btn small-purple-btn">
                                                Editar
                                            </a>



                                            <a href="#" class="action-wrapper delete-machine-type" data-id="{$type.id}">
                                                <button class="delete-btn">Eliminar</button>
                                            </a>
                                        </td>
                                    </tr>
                                {/foreach}


                            {else}
                                <tr>
                                    <td colspan="1">No hay tipos de máquina registrados.</td>
                                </tr>

                            {/if}
                        </tbody>
                    </table>
                </div>
            </div>


        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const tabId = tab.getAttribute('data-tab');
                    tabs.forEach(t => t.classList.remove('active'));
                    tabContents.forEach(c => c.classList.remove('active'));
                    tab.classList.add('active');
                    document.getElementById(tabId + '-tab').classList.add('active');
                });
            });

            // Delete machine confirmation
            document.querySelectorAll('.delete-machine').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');

                    Swal.fire({
                        title: '¿Está seguro?',
                        text: "Esta acción eliminará la máquina de manera permanente.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href =
                                '/controllers/machines/delete-machine.controller.php?id=' +
                                id;
                        }
                    });
                });
            });

            // Delete machine type confirmation
            document.querySelectorAll('.delete-machine-type').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');

                    Swal.fire({
                        title: '¿Está seguro?',
                        text: "Esta acción eliminará el tipo de máquina de manera permanente.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href =
                                '/controllers/machines/delete-machine-type.controller.php?id=' +
                                id;
                        }
                    });
                });
            });



            // Clear filters
            document.getElementById('clearFilters').addEventListener('click', function() {
                window.location.href = window.location.pathname;
            });
        });
    </script>
</body>

</html>