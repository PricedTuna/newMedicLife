<?php
require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <script src="/views/components/sidebar.app.js" defer></script>
    <script src="/views/doctor/list/views-handler.js" defer></script>
    <script src="/views/doctor/register/register-doctor.app.js" defer></script>
    <link rel="stylesheet" href="/views/doctor/main/main-doctor.styles.css">
    <link rel="stylesheet" href="/views/doctor/list/list-doctors.styles.css">
    <link rel="stylesheet" href="/views/doctor/register/register-doctor.styles.css">
    <title>Gestión de Médicos</title>
</head>

<body style="display: flex;">

    <?php
    $sidebarPath = $_SERVER['DOCUMENT_ROOT'] . '/views/doctor/list/list-doctors.view.php';
    if (file_exists($sidebarPath)) {
        include $sidebarPath;
    } else {
        echo "<p style='color: red;'>Error: No se encontró el archivo en '$sidebarPath'</p>";
    }
    ?>


</body>

</html>