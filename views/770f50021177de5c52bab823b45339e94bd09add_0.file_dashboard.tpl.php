<?php
/* Smarty version 5.4.3, created on 2025-03-26 02:52:27
  from 'file:dashboard.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.3',
  'unifunc' => 'content_67e36bebdcb2a8_20991478',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '770f50021177de5c52bab823b45339e94bd09add' => 
    array (
      0 => 'dashboard.tpl',
      1 => 1742957531,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:components/sidebar.tpl' => 1,
  ),
))) {
function content_67e36bebdcb2a8_20991478 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/var/www/html/views/dashboard';
?><!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="<?php echo $_smarty_tpl->getValue('base_path');?>
/views/components/sidebar.styles.css">
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->getValue('base_path');?>
/views/components/sidebar.app.js" defer><?php echo '</script'; ?>
>
    <link rel="stylesheet" href="<?php echo $_smarty_tpl->getValue('base_path');?>
/views/dashboard/dashboard.styles.css">
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->getValue('base_path');?>
/views/dashboard/dashboard.app.js" async><?php echo '</script'; ?>
>
</head>

<body>
    <?php $_smarty_tpl->renderSubTemplate("file:components/sidebar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

    <main>
        <div class="main-content">
            <header>
                <h1>Dashboard</h1>
                <?php if ((true && ($_smarty_tpl->hasVariable('success') && null !== ($_smarty_tpl->getValue('success') ?? null)))) {?>
                    <div style="color: darkgreen; margin-bottom: 1rem; border: 1px solid green; padding: 0.5rem; border-radius: 5px; background-color: lightgreen;">
                        <?php echo htmlspecialchars((string)$_smarty_tpl->getValue('success'), ENT_QUOTES, 'UTF-8', true);?>

                    </div>
                <?php }?>
                <div class="search-container">
                    <input type="text" placeholder="Search type of keywords">
                </div>
            </header>
            <section class="stats">
                <div class="card">
                    <h3></h3>
                    <p></p>
                </div>
                <div class="card">
                    <h3></h3>
                    <p></p>
                </div>
                <div class="card">
                    <h3></h3>
                    <p></p>
                </div>
            </section>
            <section class="chart">
                <h3>Visitas de Pacientes</h3>
                <div class="chart-placeholder"></div>
            </section>
            <section class="patient-data">
                <h3>Calendario</h3>
                <div id="calendar" class="calendar"></div>
            </section>
        </div>
        <div class="doctor-info">
            <div class="doctor-card">
                <div class="doctor-photo">Foto DR</div>
                <h3>Nombre del doctor</h3>
                <div class="doctor-stats">
                    <p>Citas <br> <strong>5</strong></p>
                    <p>Pacientes <br> <strong>40</strong></p>
                </div>
            </div>
            <section class="upcoming-appointments">
                <h3>Siguientes Citas</h3>
                <p><strong>15 /marzo/2025</strong></p>
                <p>08:30 am - 10:30 am <br><strong>Cita</strong> <br> Paciente</p>
                <p>08:30 am - 10:30 am <br><strong>Cita</strong> <br> Paciente</p>
            </section>
            <section class="upcoming-appointments-month">
                <h3>Citas del mes</h3>
                <p><strong>15 /marzo/2025</strong></p>
                <p>08:30 am - 10:30 am <br><strong>Cita</strong> <br> Paciente</p>
                <p>08:30 am - 10:30 am <br><strong>Cita</strong> <br> Paciente</p>
            </section>
        </div>
    </main>
</body>

</html>
<?php }
}
