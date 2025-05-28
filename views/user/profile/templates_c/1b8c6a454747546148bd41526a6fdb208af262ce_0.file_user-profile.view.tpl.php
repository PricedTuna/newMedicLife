<?php
/* Smarty version 5.4.5, created on 2025-05-28 03:44:53
  from 'file:user-profile.view.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_683686b5160c86_95625788',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1b8c6a454747546148bd41526a6fdb208af262ce' => 
    array (
      0 => 'user-profile.view.tpl',
      1 => 1748401887,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:../../components/sidebar.tpl' => 1,
  ),
))) {
function content_683686b5160c86_95625788 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/var/www/html/views/user/profile';
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo '<script'; ?>
 src="/views/components/sidebar.app.js" defer><?php echo '</script'; ?>
>
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <link rel="stylesheet" href="/views/dashboard/dashboard.styles.css">
    <link rel="stylesheet" href="./user-profile.styles.css">
    <?php echo '<script'; ?>
 src="https://cdn.jsdelivr.net/npm/sweetalert2@11"><?php echo '</script'; ?>
>
    <title>Perfil de Usuario</title>
</head>

<body>
    <?php $_smarty_tpl->renderSubTemplate("file:../../components/sidebar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

    <main>
        <div class="main-content">
            <div class="profile-header">
                <h1>Perfil de Usuario</h1>
            </div>

            <?php if ((true && ($_smarty_tpl->hasVariable('error') && null !== ($_smarty_tpl->getValue('error') ?? null)))) {?>
                <div class="error-message">
                    <?php echo htmlspecialchars((string)$_smarty_tpl->getValue('error'), ENT_QUOTES, 'UTF-8', true);?>

                </div>
            <?php }?>

            <?php if ((true && ($_smarty_tpl->hasVariable('user') && null !== ($_smarty_tpl->getValue('user') ?? null)))) {?>
                <div class="profile-container">
                    <div class="profile-section">
                        <h2>Información del Usuario</h2>
                        <div class="profile-info">
                            <div class="info-item">
                                <span class="label">ID:</span>
                                <span class="value"><?php echo $_smarty_tpl->getValue('user')['id'];?>
</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Nombre:</span>
                                <span class="value"><?php echo $_smarty_tpl->getValue('user')['name'];?>
</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Correo Electrónico:</span>
                                <span class="value"><?php echo $_smarty_tpl->getValue('user')['email'];?>
</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Rol:</span>
                                <span class="value">
                                    <?php if ($_smarty_tpl->getValue('user')['role'] == 'S') {?>Secretaria
                                    <?php } elseif ($_smarty_tpl->getValue('user')['role'] == 'A') {?>Administrador
                                    <?php } elseif ($_smarty_tpl->getValue('user')['role'] == 'D') {?>Doctor
                                    <?php } else {
echo $_smarty_tpl->getValue('user')['role'];?>

                                    <?php }?>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="label">Fecha de Creación:</span>
                                <span class="value"><?php echo $_smarty_tpl->getValue('user')['created_at'];?>
</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Estado:</span>
                                <span class="value">
                                    <?php if ($_smarty_tpl->getValue('user')['status'] == 'AC') {?>Activo
                                    <?php } else {
echo $_smarty_tpl->getValue('user')['status'];?>

                                    <?php }?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <?php if ($_smarty_tpl->getValue('doctorData')) {?>
                        <div class="profile-section doctor-section">
                            <h2>Información del Doctor</h2>
                            <div class="profile-info">
                                <?php if ($_smarty_tpl->getValue('doctorData')['photo']) {?>
                                    <div class="doctor-photo">
                                        <img src="/controllers/doctor/mostrar_foto.php?id=<?php echo $_smarty_tpl->getValue('doctorData')['id'];?>
" alt="Foto del doctor">
                                    </div>
                                <?php }?>
                                <div class="info-item">
                                    <span class="label">Nombre Completo:</span>
                                    <span class="value"><?php echo $_smarty_tpl->getValue('doctorData')['names'];?>
 <?php echo $_smarty_tpl->getValue('doctorData')['last_name'];?>
 <?php echo $_smarty_tpl->getValue('doctorData')['last_name2'];?>
</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">CURP:</span>
                                    <span class="value"><?php echo $_smarty_tpl->getValue('doctorData')['CURP'];?>
</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">RFC:</span>
                                    <span class="value"><?php echo $_smarty_tpl->getValue('doctorData')['RFC'];?>
</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Fecha de Nacimiento:</span>
                                    <span class="value"><?php echo $_smarty_tpl->getValue('doctorData')['birth_date'];?>
</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Género:</span>
                                    <span class="value"><?php echo $_smarty_tpl->getValue('doctorData')['gender'];?>
</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Teléfono:</span>
                                    <span class="value"><?php echo $_smarty_tpl->getValue('doctorData')['phone'];?>
</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Correo Electrónico:</span>
                                    <span class="value"><?php echo $_smarty_tpl->getValue('doctorData')['email'];?>
</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Número de Afiliación:</span>
                                    <span class="value"><?php echo $_smarty_tpl->getValue('doctorData')['insurance_number'];?>
</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Cédula Profesional:</span>
                                    <span class="value"><?php echo $_smarty_tpl->getValue('doctorData')['professional_id'];?>
</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Dirección:</span>
                                    <span class="value">
                                        <?php echo $_smarty_tpl->getValue('doctorData')['street'];?>
 <?php echo $_smarty_tpl->getValue('doctorData')['external_number'];?>

                                        <?php if ($_smarty_tpl->getValue('doctorData')['internal_number']) {?>, Int. <?php echo $_smarty_tpl->getValue('doctorData')['internal_number'];
}?>,
                                        Col. <?php echo $_smarty_tpl->getValue('doctorData')['neighborhood'];?>
, CP <?php echo $_smarty_tpl->getValue('doctorData')['CP'];?>

                                    </span>
                                </div>
                                <?php if ((true && (true && null !== ($_smarty_tpl->getValue('doctorData')['medical_areas'] ?? null))) && $_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('doctorData')['medical_areas']) > 0) {?>
                                    <div class="info-item">
                                        <span class="label">Áreas Médicas:</span>
                                        <span class="value">
                                            <ul class="medical-areas-list">
                                                <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('doctorData')['medical_areas'], 'area');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('area')->value) {
$foreach0DoElse = false;
?>
                                                    <li><?php echo $_smarty_tpl->getValue('area');?>
</li>
                                                <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                                            </ul>
                                        </span>
                                    </div>
                                <?php }?>
                            </div>
                        </div>
                    <?php }?>
                </div>
            <?php }?>
        </div>
    </main>
</body>
</html>
<?php }
}
