<?php
/* Smarty version 5.4.3, created on 2025-03-26 03:08:09
  from 'file:components/sidebar.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.3',
  'unifunc' => 'content_67e36f99f020b1_19877561',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '54102a9bbc4c0ec1a45f5a1c0565c96ecd765191' => 
    array (
      0 => 'components/sidebar.tpl',
      1 => 1742958488,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_67e36f99f020b1_19877561 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/var/www/html/views/components';
?><link rel="stylesheet" href="<?php echo $_smarty_tpl->getValue('base_path');?>
/components/sidebar.styles.css">
<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->getValue('base_path');?>
/components/sidebar.app.js" defer><?php echo '</script'; ?>
>
<nav id="sidebar">
    <ul>
        <li>
            <span class="logo">
                MedicLife</span>

            <button onclick="toggleSidebar()" id="toggle-btn">
                <img src="./icons/left-double-arrow.svg" alt="Dashboard icon" width="25" height="25">
            </button>
        </li>
        <li class="active">
            <a href="<?php echo $_smarty_tpl->getValue('base_path');?>
/views/dashboard/dashboard.view.php">
                <img src="./icons/dashboard.svg" alt="Dashboard icon" width="25" height="25">
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="<?php echo $_smarty_tpl->getValue('base_path');?>
/views/patient/register/register-patient.view.php">
                <img src="./icons/patients.svg" alt="Dashboard icon" width="25" height="25">
                <span>Pacientes</span>
            </a>
        </li>

        <li>
            <a href="<?php echo $_smarty_tpl->getValue('base_path');?>
/views/doctor/main/main-doctor.view.php">
                <img src="./icons/doctors.svg" alt="Dashboard icon" width="25" height="25">
                <span>Medicos</span>
            </a>
        </li>
        <li>
            <a href="usuario.html">
                <img src="./icons/account.svg" alt="Dashboard icon" width="25" height="25">
                <span>Usuario</span>
            </a>
        </li>
        <li>
            <a href="<?php echo $_smarty_tpl->getValue('base_path');?>
/index.php">
                <img src="./icons/logout.svg" alt="Dashboard icon" width="25" height="25">
                <span>Cerrar Sesión</span>
            </a>
        </li>
    </ul>
</nav><?php }
}
