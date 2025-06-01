<?php
/* Smarty version 5.4.5, created on 2025-06-01 10:47:12
  from 'file:list-users.view.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_683c2fb03762d2_38341131',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '618f4818f17cbb451db906d5a4602b3d410e1a26' => 
    array (
      0 => 'list-users.view.tpl',
      1 => 1748774729,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:../../components/sidebar.tpl' => 1,
  ),
))) {
function content_683c2fb03762d2_38341131 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/var/www/html/views/user/list';
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/views/dashboard/icons/Untitled-design-_1_.ico" type="image/x-icon">
    <?php echo '<script'; ?>
 src="/views/components/sidebar.app.js" defer><?php echo '</script'; ?>
>
    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/lists.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <link rel="stylesheet" href="/assets/css/usability-improvements.css">
    <?php echo '<script'; ?>
 src="https://cdn.jsdelivr.net/npm/sweetalert2@11"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="./list-users.js" defer><?php echo '</script'; ?>
>
    <title>Lista de usuarios</title>
</head>

<body>

    <?php $_smarty_tpl->renderSubTemplate("file:../../components/sidebar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

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

            <?php if ((true && ($_smarty_tpl->hasVariable('error') && null !== ($_smarty_tpl->getValue('error') ?? null)))) {?>
                <div style="color: red; margin-bottom: 1rem; border: 1px solid red; padding: 0.5rem; border-radius: 5px;">
                    <?php echo htmlspecialchars((string)$_smarty_tpl->getValue('error'), ENT_QUOTES, 'UTF-8', true);?>

                </div>
            <?php }?>

            <?php if ((true && ($_smarty_tpl->hasVariable('success') && null !== ($_smarty_tpl->getValue('success') ?? null)))) {?>
                <div style="color: darkgreen; margin-bottom: 1rem; border: 1px solid green; padding: 0.5rem; border-radius: 5px; background-color: lightgreen;">
                    <?php echo htmlspecialchars((string)$_smarty_tpl->getValue('success'), ENT_QUOTES, 'UTF-8', true);?>

                </div>
            <?php }?>
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
                        <?php if ($_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('users')) > 0) {?>
                            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('users'), 'user');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('user')->value) {
$foreach0DoElse = false;
?>
                                <tr>
                                    <td data-label="ID"><?php echo $_smarty_tpl->getValue('user')['id'];?>
</td>
                                    <td data-label="Nombre"><?php echo $_smarty_tpl->getValue('user')['name'];?>
</td>
                                    <td data-label="Correo"><?php echo $_smarty_tpl->getValue('user')['email'];?>
</td>
                                    <td data-label="Rol">
                                        <?php if ($_smarty_tpl->getValue('user')['role'] == 'S') {?>Administración
                                        <?php } elseif ($_smarty_tpl->getValue('user')['role'] == 'A') {?>Administrador
                                        <?php } elseif ($_smarty_tpl->getValue('user')['role'] == 'D') {?>Doctor
                                        <?php } else {
echo $_smarty_tpl->getValue('user')['role'];?>

                                        <?php }?>
                                    </td>
                                    <td data-label="ID Doctor"><?php echo (($tmp = $_smarty_tpl->getValue('user')['id_doctor'] ?? null)===null||$tmp==='' ? '-' ?? null : $tmp);?>
</td>
                                    <td data-label="Fecha de Creación"><?php echo $_smarty_tpl->getValue('user')['created_at'];?>
</td>
                                    <td data-label="Estado">
                                        <?php if ($_smarty_tpl->getValue('user')['status'] == 'AC') {?>Activo
                                        <?php } else {
echo $_smarty_tpl->getValue('user')['status'];?>

                                        <?php }?>
                                    </td>
                                    <td class="actions-td">
                                        <form action="/controllers/auth/delete-user.controller.php" method="POST" class="action-wrapper">
                                            <input type="hidden" name="user_id" value="<?php echo $_smarty_tpl->getValue('user')['id'];?>
">
                                            <button type="submit" class="delete-btn" data-id="<?php echo $_smarty_tpl->getValue('user')['id'];?>
">Eliminar</button>
                                        </form>
                                        <a href="/views/user/register/register-user.view.php?id=<?php echo $_smarty_tpl->getValue('user')['id'];?>
" class="action-wrapper">
                                            <button class="update-btn">Actualizar</button>
                                        </a>
                                        <a href="/views/user/register/register-user.view.php?id=<?php echo $_smarty_tpl->getValue('user')['id'];?>
&password_change=1" class="action-wrapper">
                                            <button class="password-btn">Cambiar contraseña</button>
                                        </a>
                                    </td>
                                </tr>
                            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="8">No hay usuarios registrados.</td>
                            </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
<?php }
}
