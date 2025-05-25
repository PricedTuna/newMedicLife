<?php
/* Smarty version 5.4.5, created on 2025-05-25 02:22:54
  from 'file:list-doctors.view.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_683262de6846b3_10014522',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '783cea893aad609ff7936bf7307beed6aeed237b' => 
    array (
      0 => 'list-doctors.view.tpl',
      1 => 1748132536,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:../../components/sidebar.tpl' => 1,
  ),
))) {
function content_683262de6846b3_10014522 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = 'C:\\proyectos\\mediclife\\newMedicLife\\views\\doctor\\list';
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
    <?php echo '<script'; ?>
 src="/views/doctor/list/views-handler.js" defer><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="/views/doctor/register/register-doctor.app.js" defer><?php echo '</script'; ?>
>
    <link rel="stylesheet" href="/views/doctor/main/main-doctor.styles.css">
    <link rel="stylesheet" href="/views/doctor/register/register-doctor.styles.css">
    <link rel="stylesheet" href="/views/dashboard/dashboard.styles.css">
    <?php echo '<script'; ?>
 src="./list-doctors.js" defer><?php echo '</script'; ?>
>
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <link rel="stylesheet" href="/register-patient.styles.css">
    <link rel="stylesheet" href="/views/doctor/list/list-doctors.styles.css">
        <?php echo '<script'; ?>
 src="/views/components/sidebar.app.js" defer><?php echo '</script'; ?>
>

    <title>Lista de médicos</title>
</head>
<body>

    <?php $_smarty_tpl->renderSubTemplate("file:../../components/sidebar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>
    
    <main >

        <div class="main-content">
            <div class="table-header">
                <h1>Lista de médicos    </h1>
                <a href="/views/doctor/register/register-doctor.view.php">
                    <button class="create-btn">Agregar médicos</button>
                </a>
            </div>
            <div class="table-container">
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

                <a href="/views/doctor/register/register-doctor.view.php" aria-label="Agregar doctor">
                    <button class="icon-btn table-add-btn">+</button>
                </a>

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
                        <?php if ($_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('doctors')) > 0) {?>
                            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('doctors'), 'doctor');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('doctor')->value) {
$foreach0DoElse = false;
?>
                                <tr>
                                    <td class="photo-column">
                                        <?php if ($_smarty_tpl->getValue('doctor')['photo']) {?>
                                            <img src="/controllers/doctor/mostrar_foto.php?id=<?php echo $_smarty_tpl->getValue('doctor')['id'];?>
" alt="Foto del doctor" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                        <?php } else { ?>
                                            <p>Sin foto</p>
                                        <?php }?>
                                    </td>
                                    <td data-label="Nombre"><?php echo htmlspecialchars((string)$_smarty_tpl->getValue('doctor')['names'], ENT_QUOTES, 'UTF-8', true);?>
</td>
                                    <td data-label="Apellidos"><?php echo htmlspecialchars((string)$_smarty_tpl->getValue('doctor')['last_name'], ENT_QUOTES, 'UTF-8', true);?>
 <?php echo htmlspecialchars((string)$_smarty_tpl->getValue('doctor')['last_name2'], ENT_QUOTES, 'UTF-8', true);?>
</td>
                                    <td data-label="CURP"><?php echo htmlspecialchars((string)$_smarty_tpl->getValue('doctor')['CURP'], ENT_QUOTES, 'UTF-8', true);?>
</td>
                                    <td data-label="Teléfono"><?php echo htmlspecialchars((string)$_smarty_tpl->getValue('doctor')['phone'], ENT_QUOTES, 'UTF-8', true);?>
</td>
                                    <td data-label="Sexo"><?php echo htmlspecialchars((string)$_smarty_tpl->getValue('doctor')['gender'], ENT_QUOTES, 'UTF-8', true);?>
</td>
                                    <td class="actions-td">
                                        <form action="/controllers/doctor/delete-doctor.controller.php" method="POST" class="action-wrapper">
                                            <input type="hidden" name="doctor_id" value="<?php echo $_smarty_tpl->getValue('doctor')['id'];?>
">
                                            <button type="submit" class="delete-btn" data-id="<?php echo $_smarty_tpl->getValue('doctor')['id'];?>
">Eliminar</button>
                                        </form>
                                        <a href="/views/doctor/register/register-doctor.view.php?id=<?php echo $_smarty_tpl->getValue('doctor')['id'];?>
" class="action-wrapper">
                                            <button class="update-btn">Actualizar</button>
                                        </a>
                                    </td>
                                </tr>
                            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="14">No hay médicos registrados.</td>
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
