<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Estudios | Medic Life</title>

    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/lists.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <link rel="stylesheet" href="/assets/css/usability-improvements.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="icon" href="/views/dashboard/icons/Untitled-design-_1_.ico" type="image/x-icon">

    <style>
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
        }

        .small-purple-btn:hover {
            background-color: #9b59b6;
        }

        .actions-td {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            justify-content: center;
        }

        .delete-btn {
            background-color: #ff4d4d;
            color: white;
        }

        .delete-btn:hover {
            background-color: #ff0000;
        }
    </style>
</head>

<body>
    {include file=$sidebarPath}

    <main>
        <div class="main-content">
            <div class="table-header">
                <h1>Administración de Estudios</h1>
                <a href="/views/studies/create/create-studies.view.php">
                    <button class="create-btn small-purple-btn">Agregar Estudio</button>
                </a>
            </div>

            {if isset($success) && $success}
                <div class="alert alert-success">
                    {$success|escape}
                </div>
            {elseif isset($error) && $error}
                <div class="alert alert-danger">
                    {$error|escape}
                </div>
            {/if}

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Especialidad</th>
                            <th>Duración (min)</th>
                            <th>Preparación</th>
                            <th>Precio</th>
                            <th>Activo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if $allStudies|@count > 0}
                            {foreach from=$allStudies item=study}
                                <tr>
                                    <td data-label="Código">{$study.code|escape}</td>
                                    <td data-label="Nombre">{$study.name|escape}</td>
                                    <td data-label="Especialidad">{$study.specialty|escape}</td>
                                    <td data-label="Duración">{$study.default_duration|escape}</td>
                                    <td data-label="Preparación">{$study.preparation|escape}</td>
                                    <td data-label="Precio">{$study.price|escape}</td>
                                    <td data-label="Activo">
                                        {if $study.active}
                                            <span class="status-badge status-active">Sí</span>
                                        {else}
                                            <span class="status-badge status-inactive">No</span>
                                        {/if}
                                    </td>
                                    <td class="actions-td">
                                        <a href="/views/studies/create/create-studies.view.php?id={$study.id}"
                                            class="update-btn small-purple-btn">
                                            Editar
                                        </a>
                                        <a href="#" class="delete-study" data-id="{$study.id}">
                                            <button class="delete-btn">Eliminar</button>
                                        </a>
                                    </td>
                                </tr>
                            {/foreach}
                        {else}
                            <tr>
                                <td colspan="8">No hay estudios registrados.</td>
                            </tr>
                        {/if}
                    </tbody>
                </table>
            </div>

            {* Comentado por ahora, se adaptará más adelante para máquinas
            <div class="tabs">
                <div class="tab active" data-tab="medications">Maquinas</div>
                <div class="tab" data-tab="types">Tipos de Maquinas</div>
            </div> *}

        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delete study confirmation
            document.querySelectorAll('.delete-study').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');

                    Swal.fire({
                        title: '¿Está seguro?',
                        text: "Esta acción eliminará el estudio de manera permanente.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href =
                                '/controllers/studies/delete-study.controller.php?id=' +
                                id;
                        }
                    });
                });
            });
        });
    </script>
</body>

</html>
