<?php
/* Smarty version 5.4.5, created on 2025-05-26 23:45:01
  from 'file:dashboard.view.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_6834e0dda92d56_65619615',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'daa41c9bfeb4072c560a2a02e0990612be290729' => 
    array (
      0 => 'dashboard.view.tpl',
      1 => 1748295900,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:../components/sidebar.tpl' => 1,
  ),
))) {
function content_6834e0dda92d56_65619615 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = 'C:\\proyectos\\mediclife\\newMedicLife\\views\\dashboard';
?><!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard </title>
    <link rel="stylesheet" href="../components/sidebar.styles.css">
    <?php echo '<script'; ?>
 src="../components/sidebar.app.js" defer><?php echo '</script'; ?>
>
    <link rel="stylesheet" href="./dashboard.styles.css">

    <?php echo '<script'; ?>
>
        var doctors = <?php echo json_encode($_smarty_tpl->getValue('doctors'));?>
;
    <?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="/views/dashboard/dashboard.app.js"><?php echo '</script'; ?>
>

</head>

<body>


    <?php $_smarty_tpl->renderSubTemplate('file:../components/sidebar.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

    <main>
        <div class="main-content">
            <header>

                <h1>Dashboard</h1>


                <div class="doctor-select-container" style="margin-top: 1rem;">
                    <label for="doctor-select">Selecciona un doctor:</label>
                    <select id="doctor-select">
                        <option value="">-- Todos los doctores --</option>
                        <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('doctors'), 'doctor');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('doctor')->value) {
$foreach0DoElse = false;
?>
                            <option value="<?php echo $_smarty_tpl->getValue('doctor')['id'];?>
"><?php echo $_smarty_tpl->getValue('doctor')['names'];?>
 <?php echo $_smarty_tpl->getValue('doctor')['last_name'];?>
 <?php echo $_smarty_tpl->getValue('doctor')['last_name2'];?>
</option>
                        <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                    </select>
                </div>


                <?php if ((true && (true && null !== ($_GET['success'] ?? null)))) {?>
                    <div
                        style="color: darkgreen; margin-bottom: 1rem; border: 1px solid green; padding: 0.5rem; border-radius: 5px; background-color: lightgreen;">
                        <?php echo htmlspecialchars((string)$_GET['success'], ENT_QUOTES, 'UTF-8', true);?>

                    </div>
                <?php }?>
                <div class="search-container">
                    <input type="text" placeholder="Search type of keywords">
                </div>
            </header>
                        <section class="chart">
                <h3>Visitas de Pacientes</h3>
                <div class="chart-placeholder">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Paciente</th>
                                <th>Área Médica</th>
                                <th>Doctor</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('appointments'), 'appointment');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('appointment')->value) {
$foreach1DoElse = false;
?>
                                <?php if ($_smarty_tpl->getValue('appointment')['status'] == 'A') {?>
                                    <tr data-doctor-id="<?php echo $_smarty_tpl->getValue('appointment')['id_doctor'];?>
">
                                        <td data-label="ID"><?php echo $_smarty_tpl->getValue('appointment')['cita'];?>
</td>
                                        <td data-label="Paciente">
                                            <?php echo $_smarty_tpl->getValue('appointment')['patient_names'];?>
 <?php echo $_smarty_tpl->getValue('appointment')['patient_last_name'];?>

                                            <?php echo $_smarty_tpl->getValue('appointment')['patient_last_name2'];?>

                                        </td>
                                        <td data-label="Área Médica"><?php echo $_smarty_tpl->getValue('appointment')['medical_area'];?>
</td>
                                        <td data-label="Doctor">
                                            <?php echo $_smarty_tpl->getValue('appointment')['doctor_names'];?>
 <?php echo $_smarty_tpl->getValue('appointment')['doctor_last_name'];?>

                                            <?php echo $_smarty_tpl->getValue('appointment')['doctor_last_name2'];?>

                                        </td>
                                        <td data-label="Fecha"><?php echo $_smarty_tpl->getValue('appointment')['appointment_date'];?>
</td>
                                        <td class="actions-td"></td>
                                    </tr>
                                <?php }?>
                            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                        </tbody>
                    </table>

                    <?php if (!$_smarty_tpl->getValue('appointments')) {?>
                        <p style="text-align: center; margin-top: 1rem;">No hay citas registradas.</p>
                    <?php }?>
                </div>
            </section>
            <section class="patient-data">
                <h3>Calendario</h3>
                <div id="calendar" class="calendar"></div>
            </section>
        </div>
        <div class="doctor-info">
            <div class="doctor-card">
                <div id="doctor-photo" class="doctor-photo">
                    
                </div>
                <h3 id="doctor-name">Nombre del doctor</h3>
                <div class="doctor-stats">
                    <p>Citas <br> <strong id="doctor-appointments">0</strong></p>
                </div>
            </div>
            <section class="upcoming-appointments">
                <h3>Siguientes Citas</h3>
                <div id="next-appointments">
                    <!-- Aquí se insertarán las siguientes citas -->
                </div>
            </section>
            <section class="upcoming-appointments-month">
                <h3>Citas del mes </h3>
                <div id="month-appointments">
                    <!-- Aquí se insertarán las citas del mes -->
                </div>
            </section>
        </div>

    </main>
</body>

</html><?php }
}
