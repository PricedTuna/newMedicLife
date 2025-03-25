<?php
require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
try {
    $stmt = $pdo->query("SELECT id_state, id, name FROM municipalities");
    $stmt->execute();
    $municipalities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->query("SELECT * FROM states");
    $stmt->execute();
    $states = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->query("SELECT id_state, id_municipality, id, name FROM localities");
    $stmt->execute();
    $localities = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener tablas: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./register-patient.styles.css">
    <link rel="stylesheet" href="../../components/sidebar.styles.css">
    <script src="../../components/sidebar.app.js" defer></script>
    <script src="./register-patient.app.js" defer></script>
    <title>Registro de Paciente</title>
</head>

<body>

    <div class="registerPatientWrapper" >
    <?php
$sidebarPath = $_SERVER['DOCUMENT_ROOT'] . '/views/components/sidebar.php';

if (file_exists($sidebarPath)) {
    include $sidebarPath;
} else {
    echo "<p style='color: red;'>Error: No se encontró el archivo sidebar.php en '$sidebarPath'</p>";
}
?>


        <div class="form-container">
            <h2>Registro de Paciente</h2>

            <?php if (isset($_GET['success'])): ?>
                    <div style="color: darkgreen; margin-bottom: 1rem; border: 1px solid green; padding: 0.5rem; border-radius: 5px; background-color: lightgreen;">
                        <?php echo htmlspecialchars($_GET['success']); ?>
                    </div>
                <?php endif; ?>
            
            <div class="steps">
                <div class="step step-active" data-step="1">Paso 1</div>
                <div class="step" data-step="2">Paso 2</div>
                <div class="step" data-step="3">Paso 3</div>
                <div class="step" data-step="4">Paso 4</div>
            </div>

            <form id="patient-form" action="/controllers/patient/register-patient.controller.php" method="POST">
                <!-- Paso 1: Datos Personales -->
                <div class="form-step" id="step-1">
                    <div class="form-group">
                        <label for="lastName">Apellido Paterno</label>
                        <input type="text" id="lastName" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label for="motherLastName">Apellido Materno</label>
                        <input type="text" id="motherLastName" name="last_name2" required>
                    </div>
                    <div class="form-group">
                        <label for="firstName">Nombre</label>
                        <input type="text" id="firstName" name="names" required>
                    </div>
                    <div class="form-group">
                        <label for="phoneNumber">Número Telefónico</label>
                        <input type="number" id="phoneNumber" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="gender">Sexo</label>
                        <select id="gender" name="gender" required>
                            <option value="">Seleccione...</option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="birthDate">Fecha de Nacimiento</label>
                        <input type="date" id="birthDate" name="birth_date" required>
                    </div>
                    <div class="form-group">
                        <label for="curp">CURP</label>
                        <input type="text" id="curp" name="CURP" required>
                    </div>
                    <div class="form-group">
                        <label for="rfc">RFC</label>
                        <input type="text" id="rfc" name="RFC" required>
                    </div>
                    <div class="form-group">
                        <label for="affiliationNumber">Número de Afiliación</label>
                        <input type="text" id="affiliationNumber" name="insurance_number" required>
                    </div>
                    <button type="button" class="next-btn" onclick="nextStep(2)">Siguiente</button>
                </div>

                <!-- Paso 2: Dirección -->
                <div class="form-step" id="step-2" style="display: none;">
                    <div class="form-group">
                        <label for="street">Calle</label>
                        <input type="text" id="street" name="street" required>
                    </div>
                    <div class="form-group">
                        <label for="neighborhood">Colonia</label>
                        <input type="text" id="neighborhood" name="neighborhood" required>
                    </div>
                    <div class="form-group">
                        <label for="postalCode">Código Postal</label>
                        <input type="number" id="postalCode" name="CP" required>
                    </div>
                    <div class="form-group">
                        <label for="extNumber">Número Exterior</label>
                        <input type="text" id="extNumber" name="external_number" required>
                    </div>
                    <div class="form-group">
                        <label for="extNumber">Número Interior</label>
                        <input type="text" id="extNumber" name="internal_number" required>
                    </div>
                    <div class="form-group">
                        <label for="state">Estado</label>
                        <select name="state" id="state" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($states as $state): ?>
                                <option value="<?php echo $state['id']; ?>"><?php echo $state['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="municipality">Municipio</label>
                        <select name="municipality" id="municipality" required>
                            <option value="">Seleccione un estado primero...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="locality">Localidad</label>
                        <select name="locality" id="locality" required>
                            <option value="">Seleccione un municipio primero...</option>
                        </select>
                    </div>

                    <button type="button" class="prev-btn" onclick="prevStep(1)">Atrás</button>
                    <button type="button" class="next-btn" onclick="nextStep(3)">Siguiente</button>
                </div>

                <!-- Paso 3: Información Adicional -->
                <div class="form-step" id="step-3" style="display: none;">
                    <div class="form-group">
                        <label for="bloodType">Tipo de Sangre</label>
                        <select id="bloodType" name="blood_type" required>
                            <option value="">Seleccione...</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="maritalStatus">Estado Civil</label>
                        <select id="maritalStatus" name="marital_status" required>
                            <option value="">Seleccione...</option>
                            <option value="Soltero(a)">Soltero/a</option>
                            <option value="Casado(a)">Casado/a</option>
                            <option value="Viudo(a)">Viudo/a</option>
                            <option value="Unión libre">Unión libre</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="weight">Peso (kg)</label>
                        <input type="number" id="weight" name="weight" required>
                    </div>
                    <div class="form-group">
                        <label for="height">Altura (cm)</label>
                        <input type="number" id="height" name="height" required>
                    </div>
                    <div class="form-group">
                        <label for="ethnicGroup">Grupo Étnico</label>
                        <input type="text" id="ethnicGroup" name="ethnic_group">
                    </div>
                    <div class="form-group">
                        <label for="religion">Religión</label>
                        <input type="text" id="religion" name="religion">
                    </div>
                    <button type="button" class="prev-btn" onclick="prevStep(2)">Atrás</button>
                    <button type="button" class="next-btn" onclick="nextStep(4)">Siguiente</button>
                </div>

                <!-- Paso 4: Contacto de Emergencia -->
                <div class="form-step" id="step-4" style="display: none;">
                    <div class="form-group">
                        <label for="contactFirstName">Nombre del Contacto</label>
                        <input type="text" id="contactFirstName" required>
                    </div>
                    <div class="form-group">
                        <label for="contactLastName">Apellido Paterno</label>
                        <input type="text" id="contactLastName" required>
                    </div>
                    <div class="form-group">
                        <label for="contactMotherLastName">Apellido Materno</label>
                        <input type="text" id="contactMotherLastName" required>
                    </div>
                    <div class="form-group">
                        <label for="contactPhone">Número Telefónico</label>
                        <input type="number" id="contactPhone" required>
                    </div>
                    <div class="form-group">
                        <label for="contactRelation">Relación con el Paciente</label>
                        <input type="text" id="contactRelation" required>
                    </div>
                    <button type="button" class="prev-btn" onclick="prevStep(3)">Atrás</button>
                    <button type="submit" class="submit-btn">Registrar</button>
                </div>
            </form>

        </div>
    </div>
</body>

</html>

<script>
    const municipalities = <?php echo json_encode($municipalities); ?>;
    const localities = <?php echo json_encode($localities); ?>;

    const stateSelect = document.getElementById('state');
    const municipalitySelect = document.getElementById('municipality');
    const localitySelect = document.getElementById('locality');

    stateSelect.addEventListener('change', () => {
        const selectedState = stateSelect.value;

        municipalitySelect.innerHTML = '<option value="">Seleccione...</option>';
        localitySelect.innerHTML = '<option value="">Seleccione un municipio primero...</option>';

        if (selectedState) {
            const filteredMunicipalities = municipalities.filter(m => m.id_state == selectedState);
            filteredMunicipalities.forEach(m => {
                municipalitySelect.innerHTML += `<option value="${m.id}">${m.name}</option>`;
            });
        }
    });

    municipalitySelect.addEventListener('change', () => {
        const selectedState = stateSelect.value;
        const selectedMunicipality = municipalitySelect.value;

        localitySelect.innerHTML = '<option value="">Seleccione...</option>';

        if (selectedMunicipality) {
            const filteredLocalities = localities.filter(l =>
                l.id_state == selectedState && l.id_municipality == selectedMunicipality
            );
            filteredLocalities.forEach(l => {
                localitySelect.innerHTML += `<option value="${l.id}">${l.name}</option>`;
            });
        }
    });
</script>