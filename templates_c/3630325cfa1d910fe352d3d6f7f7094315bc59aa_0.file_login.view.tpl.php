<?php
/* Smarty version 5.4.5, created on 2025-05-26 01:58:21
  from 'file:login.view.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_6833cabd9e77d7_91189210',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3630325cfa1d910fe352d3d6f7f7094315bc59aa' => 
    array (
      0 => 'login.view.tpl',
      1 => 1748223206,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_6833cabd9e77d7_91189210 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/var/www/html/views/login';
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="login.styles.css"> <!-- Ajusta la ruta si tienes un CSS -->
</head>
<body >

    <div class="login-container">
        <h2>Iniciar sesión</h2>

        <?php if ($_smarty_tpl->getValue('error')) {?>
            <div style="color: red; margin-bottom: 1rem; border: 1px solid red; padding: 0.5rem; border-radius: 5px;">
                <?php echo htmlspecialchars((string)$_smarty_tpl->getValue('error'), ENT_QUOTES, 'UTF-8', true);?>

            </div>
        <?php }?>

        <form id="loginForm" action="controllers/auth/login.controller.php" method="POST">
            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" placeholder="Correo electrónico" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Contraseña" required autocomplete="off">
            </div>
            <button type="submit" name="login">Ingresar</button>
        </form>
    </div>

</body>
</html>
<?php }
}
