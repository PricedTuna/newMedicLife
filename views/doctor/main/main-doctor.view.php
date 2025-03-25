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
    $sidebarPath = $_SERVER['DOCUMENT_ROOT'] . '/views/components/sidebar.php';
    if (file_exists($sidebarPath)) {
        include $sidebarPath;
    } else {
        echo "<p style='color: red;'>Error: No se encontró el archivo sidebar.php en '$sidebarPath'</p>";
    }
    ?>

    <!-- Contenido principal -->
    <main class="content">
        <?php
        $headerPath = $_SERVER['DOCUMENT_ROOT'] . '/views/doctor/main/header-doctor.view.php';
        if (file_exists($headerPath)) {
            include $headerPath;
        } else {
            echo "<p style='color: red;'>Error: No se encontró el archivo header-doctor.view.php en '$headerPath'</p>";
        }
        ?>
        <?php if (isset($_GET['error'])): ?>
            <div style="color: red; margin-bottom: 1rem; border: 1px solid red; padding: 0.5rem; border-radius: 5px;">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php $_GET['error']=""; endif; ?>
        <div id="dynamic-content">

        </div>
    </main>

    <script>
        if (window.location.search.includes('error')) {
            const url = new URL(window.location);
            url.searchParams.delete('error');
            window.history.replaceState({}, document.title, url.pathname + url.search);
        }
    </script>

</body>

</html>