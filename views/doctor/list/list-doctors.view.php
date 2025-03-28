<?php
// obtener_doctores.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';

try {
    $stmt = $pdo->query("SELECT * FROM doctors");
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener doctores: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <script src="/views/components/sidebar.app.js" defer></script>
    <script src="/views/doctor/list/views-handler.js" defer></script>
    <script src="/views/doctor/register/register-doctor.app.js" defer></script>
    <link rel="stylesheet" href="/views/doctor/main/main-doctor.styles.css">
    <link rel="stylesheet" href="/views/doctor/list/list-doctors.styles.css">
    <link rel="stylesheet" href="/views/doctor/register/register-doctor.styles.css">
    <link rel="stylesheet" href="./list-doctors.styles.css">
    <link rel="stylesheet" href="/views/dashboard/dashboard.styles.css">
    <script src="./list-doctors.js" defer></script>
    <title>Lista de médicos</title>
</head>

<body>
    <?php
    $sidebarPath = $_SERVER['DOCUMENT_ROOT'] . '/views/components/sidebar.php';
    if (file_exists($sidebarPath)) {
        require $sidebarPath;
    } else {
        echo "<p style='color: red;'>Error: No se encontró el archivo sidebar.php en '$sidebarPath'</p>";
    }
    ?>

    <main>
        <div class="main-content">

            <div class="table-container">
                <?php if (isset($_GET['error'])): ?>
                    <div style="color: red; margin-bottom: 1rem; border: 1px solid red; padding: 0.5rem; border-radius: 5px;">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['success'])): ?>
                    <div style="color: darkgreen; margin-bottom: 1rem; border: 1px solid green; padding: 0.5rem; border-radius: 5px; background-color: lightgreen;">
                        <?php echo htmlspecialchars($_GET['success']); ?>
                    </div>
                <?php endif; ?>
                <table>
                    <thead>
                        <tr>
                            <th class="photo-column">Foto</th>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>CURP</th>
                            <th>Teléfono</th>
                            <th>Sexo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($doctors) > 0): ?>
                            <?php foreach ($doctors as $doctor): ?>
                                <tr>
                                    <td class="photo-column" >
                                        <?php if (!empty($doctor['photo'])): ?>
                                            <img src="/controllers/doctor/mostrar_foto.php?id=<?php echo $doctor['id']; ?>" alt="Foto del doctor" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                        <?php else: ?>
                                            <p>Sin foto</p>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($doctor['names']); ?></td>
                                    <td><?php echo htmlspecialchars($doctor['last_name'] . ' ' . $doctor['last_name2']); ?></td>
                                    <td><?php echo htmlspecialchars($doctor['CURP']); ?></td>
                                    <td><?php echo htmlspecialchars($doctor['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($doctor['gender']); ?></td>
                                    <td class="actions-td">
                                        <form action="/controllers/doctor/delete-doctor.controller.php" method="POST" class="action-wrapper">
                                            <input type="hidden" name="doctor_id" value="<?php echo $doctor['id']; ?>">
                                            <button type="submit" class="delete-btn" data-id="<?php echo $doctor['id']; ?>">Eliminar</button>
                                        </form>

                                        <a href="/views/doctor/register/register-doctor.view.php?id=<?php echo $doctor['id']; ?>" class="action-wrapper">
                                            <button class="update-btn">Actualizar</button>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="14">No hay doctores registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>

</html>