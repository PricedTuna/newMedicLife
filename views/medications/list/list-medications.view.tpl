<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Medicamentos | Medic Life</title>

    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/lists.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <link rel="stylesheet" href="/assets/css/usability-improvements.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/views/medications/list/list-medications.js" defer></script>
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
                <h1>Administración de Medicamentos</h1>
                <a href="/views/medications/create/create-medication.view.php">
                    <button class="create-btn small-purple-btn">Agregar Medicamento</button>
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
                <div class="tab active" data-tab="medications">Medicamentos</div>
                <div class="tab" data-tab="types">Tipos de Medicamentos</div>
            </div>

            <div id="medications-tab" class="tab-content active">
                <div class="filter-container">
                    <form action="" method="GET">
                        <input type="text" id="searchInput" name="search" placeholder="Buscar por nombre..." value="{$search_term|default:''}">
                        <select id="typeFilter" name="type">
                            <option value="">Todos los tipos</option>
                            {foreach from=$medication_types item=type}
                                <option value="{$type.id}">{$type.name|escape}</option>
                            {/foreach}
                        </select>
                        <button type="submit">Buscar</button>
                        <button type="button" id="clearFilters">Limpiar filtros</button>
                    </form>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Stock</th>
                                <th>Precio</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {if $medications|@count > 0}
                                {foreach from=$medications item=medication}
                                    <tr>
                                        <td data-label="Nombre">{$medication.name|escape}</td>
                                        <td data-label="Tipo">
                                            <span class="type-badge">{$medication.medicine_type_name|escape}</span>
                                        </td>
                                        <td data-label="Stock">{$medication.stock|default:'N/A'}</td>
                                        <td data-label="Precio">{if isset($medication.price_sale) && $medication.price_sale != ''}${$medication.price_sale}{else}N/A{/if}</td>
                                        <td data-label="Estado">
                                            {if $medication.status != 'I'}
                                                <span class="status-badge status-active">Activo</span>
                                            {else}
                                                <span class="status-badge status-inactive">Inactivo</span>
                                            {/if}
                                        </td>
                                        <td class="actions-td">
                                            <a href="/views/medications/update/update-medication.view.php?id={$medication.id}" class="action-wrapper">
                                                <button class="update-btn">Editar</button>
                                            </a>
                                            <a href="#" class="action-wrapper delete-medication" data-id="{$medication.id}">
                                                <button class="delete-btn">Eliminar</button>
                                            </a>
                                        </td>
                                    </tr>
                                {/foreach}
                            {else}
                                <tr>
                                    <td colspan="6">No hay medicamentos registrados.</td>
                                </tr>
                            {/if}
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="types-tab" class="tab-content">
                <div class="table-header">
                    <h2>Tipos de Medicamentos</h2>
                    <a href="/views/medications/create/create-medication.view.php#type">
                        <button class="create-btn small-purple-btn">Agregar Tipo</button>
                        <button class="icon-btn table-add-btn">+</button>
                    </a>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {if $medication_types|@count > 0}
                                {foreach from=$medication_types item=type}
                                    <tr>
                                        <td data-label="Nombre">{$type.name|escape}</td>
                                        <td data-label="Descripción">{$type.description|default:'N/A'|escape}</td>
                                        <td data-label="Estado">
                                            {if isset($type.status) && $type.status != 'I'}
                                                <span class="status-badge status-active">Activo</span>
                                            {else}
                                                <span class="status-badge status-inactive">Inactivo</span>
                                            {/if}
                                        </td>
                                        <td class="actions-td">
                                            <a href="/views/medications/update/update-medication-type.view.php?id={$type.id}" class="action-wrapper">
                                                <button class="update-btn">Editar</button>
                                            </a>
                                            <a href="#" class="action-wrapper delete-medication-type" data-id="{$type.id}">
                                                <button class="delete-btn">Eliminar</button>
                                            </a>
                                        </td>
                                    </tr>
                                {/foreach}
                            {else}
                                <tr>
                                    <td colspan="4">No hay tipos de medicamentos registrados.</td>
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

                    // Remove active class from all tabs and contents
                    tabs.forEach(t => t.classList.remove('active'));
                    tabContents.forEach(c => c.classList.remove('active'));

                    // Add active class to clicked tab and corresponding content
                    tab.classList.add('active');
                    document.getElementById(tabId + '-tab').classList.add('active');
                });
            });

            // Delete medication confirmation
            document.querySelectorAll('.delete-medication').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');

                    Swal.fire({
                        title: '¿Está seguro?',
                        text: "Esta acción no se puede revertir. Si el medicamento está siendo utilizado en prescripciones, solo se marcará como inactivo.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/controllers/medications/delete-medication.controller.php?id=' + id;
                        }
                    });
                });
            });

            // Delete medication type confirmation
            document.querySelectorAll('.delete-medication-type').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');

                    Swal.fire({
                        title: '¿Está seguro?',
                        text: "Esta acción no se puede revertir. Si el tipo está siendo utilizado por medicamentos, solo se marcará como inactivo.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/controllers/medications/delete-medication.controller.php?id=' + id + '&type=type';
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
