<?php
// obtener_doctores.php
require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';

try {
    $stmt = $pdo->query("SELECT * FROM doctors");
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener doctores: " . $e->getMessage());
}
?>

<link rel="stylesheet" href="./list-doctors.styles.css">
<script src="./list-doctors.js" defer></script>
<!-- Tabla -->
<?php if (isset($_GET['error'])): ?>
    <div style="color: red; margin-bottom: 1rem; border: 1px solid red; padding: 0.5rem; border-radius: 5px;">
        <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
<?php endif; ?>
<div class="table-container">

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>CURP</th>
                <th>RFC</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Género</th>
                <th>Fecha de nacimiento</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($doctors) > 0): ?>
                <?php foreach ($doctors as $doctor): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($doctor['id']); ?></td>
                        <td><?php echo htmlspecialchars($doctor['names']); ?></td>
                        <td><?php echo htmlspecialchars($doctor['last_name'] . ' ' . $doctor['last_name2']); ?></td>
                        <td><?php echo htmlspecialchars($doctor['CURP']); ?></td>
                        <td><?php echo htmlspecialchars($doctor['RFC']); ?></td>
                        <td><?php echo htmlspecialchars($doctor['phone']); ?></td>
                        <td><?php echo htmlspecialchars($doctor['email']); ?></td>
                        <td><?php echo htmlspecialchars($doctor['gender']); ?></td>
                        <td><?php echo htmlspecialchars($doctor['birth_date']); ?></td>
                        <td><?php echo htmlspecialchars($doctor['id_state']); ?></td>

                        <td classs="actions-td">
                            <form action="/controllers/doctor/delete-doctor.controller.php" method="POST" style="margin-bottom: 5px;"> <input type="text" style="display: none;" value="<?php echo $doctor['id'] ?>" name="doctor_id"> <button type="submit" class="delete-btn" data-id="<?php echo $doctor['id']; ?>">Eliminar</button></form>
                            <a href="/views/doctor/register/register-doctor.view.php?id=<?php echo $doctor['id']; ?>">
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