<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="/views/components/sidebar.app.js" defer></script>
    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/lists.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <link rel="stylesheet" href="/assets/css/usability-improvements.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./list-users.js" defer></script>
    <title>Lista de usuarios</title>
</head>

<body>

    {include file="../../components/sidebar.tpl"}

    <main>
        <div class="main-content">
            <div class="table-header">
                <h1>Lista de usuarios</h1>
                <a href="/views/user/register/register-user.view.php">
                    <button class="create-btn">Agregar Usuario</button>
                    <button class="icon-btn table-add-btn">+</button>
                </a>
            </div>

            <div class="filter-container">
                <input type="text" id="searchInput" placeholder="Buscar por nombre o correo...">
                <select id="roleFilter">
                    <option value="all">Todos los roles</option>
                    <option value="S">Administración</option>
                    <option value="A">Administrador</option>
                    <option value="D">Doctor</option>
                </select>
                <select id="statusFilter">
                    <option value="all">Todos los estados</option>
                    <option value="AC">Activo</option>
                    <option value="IN">Inactivo</option>
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
            <div class="table-container">

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>ID Doctor</th>
                            <th>Fecha de Creación</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if $users|@count > 0}
                            {foreach from=$users item=user}
                                <tr>
                                    <td data-label="ID">{$user.id}</td>
                                    <td data-label="Nombre">{$user.name}</td>
                                    <td data-label="Correo">{$user.email}</td>
                                    <td data-label="Rol">
                                        {if $user.role == 'S'}Administración
                                        {elseif $user.role == 'A'}Administrador
                                        {elseif $user.role == 'D'}Doctor
                                        {else}{$user.role}
                                        {/if}
                                    </td>
                                    <td data-label="ID Doctor">{$user.id_doctor|default:'-'}</td>
                                    <td data-label="Fecha de Creación">{$user.created_at}</td>
                                    <td data-label="Estado">
                                        {if $user.status == 'AC'}Activo
                                        {else}{$user.status}
                                        {/if}
                                    </td>
                                    <td class="actions-td">
                                        <form action="/controllers/auth/delete-user.controller.php" method="POST" class="action-wrapper">
                                            <input type="hidden" name="user_id" value="{$user.id}">
                                            <button type="submit" class="delete-btn" data-id="{$user.id}">Eliminar</button>
                                        </form>
                                        <a href="/views/user/register/register-user.view.php?id={$user.id}" class="action-wrapper">
                                            <button class="update-btn">Actualizar</button>
                                        </a>
                                        <a href="/views/user/register/register-user.view.php?id={$user.id}&password_change=1" class="action-wrapper">
                                            <button class="password-btn">Cambiar contraseña</button>
                                        </a>
                                    </td>
                                </tr>
                            {/foreach}
                        {else}
                            <tr>
                                <td colspan="8">No hay usuarios registrados.</td>
                            </tr>
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
