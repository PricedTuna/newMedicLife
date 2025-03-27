<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';

try {

    // Obtener los datos del doctor si se pasa un ID
    $doctor = null;
    if (isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = :id");
        $stmt->execute([':id' => $_GET['id']]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
    }

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

        $stmt = $pdo->query("SELECT id, name, name FROM medical_areas");
        $stmt->execute();
        $medical_areas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\Throwable $th) {
        print_r($th);
        exit;
    }
} catch (PDOException $e) {
    die("Error al obtener tablas: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./register-doctor.styles.css">
    <script src="./register-doctor.app.js" defer></script>
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <script src="/views/components/sidebar.app.js" defer></script>
    <script src="/views/components/sidebar.app.js" defer></script>
    <script src="/views/doctor/list/views-handler.js" defer></script>
    <script src="/views/doctor/register/register-doctor.app.js" defer></script>
    <link rel="stylesheet" href="/views/doctor/main/main-doctor.styles.css">
    <link rel="stylesheet" href="/views/doctor/list/list-doctors.styles.css">
    <link rel="stylesheet" href="/views/doctor/register/register-doctor.styles.css">
    <title>Registro de Doctores</title>
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

    <main class="content">

        <?php
        $headerPath = $_SERVER['DOCUMENT_ROOT'] . '/views/doctor/main/header-doctor.view.php';
        if (file_exists($headerPath)) {
            include $headerPath;
        } else {
            echo "<p style='color: red;'>Error: No se encontró el archivo header-doctor.view.php en '$headerPath'</p>";
        }
        ?>

        <div class="center-container">
            <div class="form-container">
                <div class="steps">
                    <div class="step step-active" data-step="1">Paso 1</div>
                    <div class="step" data-step="2">Paso 2</div>
                    <div class="step" data-step="3">Paso 3</div>
                </div>

                <h2><?php echo $doctor ? 'Actualizar Doctor' : 'Registrar Doctor'; ?></h2>
                <?php if (isset($_GET['error'])): ?>
                    <div style="color: red; margin-bottom: 1rem; border: 1px solid red; padding: 0.5rem; border-radius: 5px;">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>


                <form action="/controllers/doctor/register-doctor.controller.php" method="POST" id="doctor-form" enctype="multipart/form-data">
                    <!-- Paso 1 -->
                    <input type="hidden" name="doctor_id" value="<?php echo $doctor['id'] ?? ''; ?>">

                    <div class="form-step" id="step-1">

                        <div class="form-group">
                            <label for="lastName">Apellido Paterno</label>
                            <input type="text" id="lastName" name="fatherLastName" value="<?php echo $doctor['last_name'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="motherLastName">Apellido Materno</label>
                            <input type="text" id="motherLastName" name="motherLastName" value="<?php echo $doctor['last_name2'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="firstName">Nombre</label>
                            <input type="text" id="firstName" name="name" value="<?php echo $doctor['names'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phoneNumber">Número Telefónico</label>
                            <input type="number" id="phoneNumber" name="phoneNumber" value="<?php echo $doctor['phone'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" id="email" name="email" value="<?php echo $doctor['email'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="gender">Sexo</label>
                            <select id="gender" name="gender" required>
                                <option value="">Seleccione...</option>
                                <option value="M" <?php echo $doctor['gender'] == 'M' ? 'selected' : ''; ?>>Masculino</option>
                                <option value="F" <?php echo $doctor['gender'] == 'F' ? 'selected' : ''; ?>>Femenino</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="birthDate">Fecha de Nacimiento</label>
                            <input type="date" id="birthDate" name="birthDate" value="<?php echo $doctor['birth_date'] ?? ''; ?>" required>
                        </div>
                        <button type="button" class="next-btn" onclick="nextStep(2)">Siguiente</button>
                    </div>

                    <!-- Paso 2 -->
                    <div class="form-step" id="step-2" style="display: none;">
                        <div class="form-group">
                            <label for="street">Calle</label>
                            <input type="text" id="street" name="street" value="<?php echo $doctor['street'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="neighborhood">Colonia</label>
                            <input type="text" id="neighborhood" name="neighborhood" value="<?php echo $doctor['neighborhood'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="postalCode">Código Postal</label>
                            <input type="number" id="postalCode" name="postalCode" value="<?php echo $doctor['CP'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="extNumber">Número Exterior</label>
                            <input type="text" id="extNumber" name="extNumber" value="<?php echo $doctor['external_number'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="intNumber">Número Interior (Opcional)</label>
                            <input type="text" id="intNumber" name="intNumber" value="<?php echo $doctor['internal_number'] ?? ''; ?>">
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

                    <!-- Paso 3 -->
                    <div class="form-step" id="step-3" style="display: none;">
                        <div class="form-group">
                            <label for="curp">CURP</label>
                            <input type="text" id="curp" name="curp" value="<?php echo $doctor['CURP'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="rfc">RFC</label>
                            <input type="text" id="rfc" name="rfc" value="<?php echo $doctor['RFC'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="affiliationNumber">Número de Afiliación</label>
                            <input type="text" id="affiliationNumber" name="affiliationNumber" value="<?php echo $doctor['insurance_number'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="professionalLicense">Cédula Profesional</label>
                            <input type="text" id="professionalLicense" name="professionalLicense" value="<?php echo $doctor['professional_id'] ?? ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="speciality">Especialidad</label>
                            <select name="medical_area" id="speciality" required>
                                <?php foreach ($medical_areas as $medical_area): ?>
                                    <option value="<?php echo $medical_area['id']; ?>"><?php echo $medical_area['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                        <label for="photo" class="file-label" id="photo-label">Subir Foto</label>
                            <input type="file" id="photo" name="photo" accept="image/*" <?php echo $doctor ? '' : 'required'; ?>>
                        </div>

                        <button type="button" class="prev-btn" onclick="prevStep(2)">Atrás</button>
                        <button type="submit" class="submit-btn"><?php echo $doctor ? 'Actualizar' : 'Registrar'; ?></button>
                        <input type="hidden" name="id" value="<?php echo $doctor['id'] ?? ''; ?>">
                    </div>

                </form>
            </div>
        </div>
    </main>
</body>

</html>


<script defer>
    function showErrorMessage(input, message) {
        let errorElement = input.nextElementSibling;

        if (!errorElement || !errorElement.classList.contains('error-message')) {
            errorElement = document.createElement('span');
            errorElement.classList.add('error-message');
            errorElement.style.color = 'red';
            input.parentNode.appendChild(errorElement);
        }

        errorElement.textContent = message;
        input.style.border = '2px solid red';
    }

    // Función para limpiar mensajes de error
    function clearErrorMessage(input) {
        let errorElement = input.nextElementSibling;

        if (errorElement && errorElement.classList.contains('error-message')) {
            errorElement.remove();
        }

        input.style.border = '2px solid var(--line-clr)';
    }

    function validateInputs() {
        let valid = true;

        // Validar CURP (18 caracteres, formato oficial)
        let curpInput = document.getElementById('curp');
        let curpPattern = /^[A-Z]{4}\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]\d$/;
        const curp = curpInput.value.trim();
        if (curp.length !== 18) {
            showErrorMessage(curpInput, 'La CURP debe tener exactamente 18 caracteres.');
            valid = false;
        } else if (!curpPattern.test(curp)) {
            showErrorMessage(curpInput, 'CURP inválida. Revisa el formato.');
            valid = false;
        } else {
            clearErrorMessage(curpInput);
        }

        // Validar RFC (12 o 13 caracteres)
        let rfcInput = document.getElementById('rfc');
        let rfcPattern = /^[A-ZÑ&]{3,4}\d{6}[A-Z\d]{3}$/i;
        if (rfcInput.value.trim() !== '' && !rfcPattern.test(rfcInput.value.trim())) {
            showErrorMessage(rfcInput, 'RFC inválido. Debe tener entre 12 y 13 caracteres.');
            valid = false;
        } else {
            clearErrorMessage(rfcInput);
        }

        // input file
        let photoInput = document.getElementById('photo');
        let photoFile = photoInput.files[0];

        if (!photoFile) {
            showErrorMessage(photoInput, 'Debes seleccionar una imagen.');
            valid = false;
        } else if (!photoFile.type.startsWith('image/')) {
            showErrorMessage(photoInput, 'El archivo debe ser una imagen.');
            valid = false;
        } else if (photoFile.size > 2 * 1024 * 1024) { // 2MB
            showErrorMessage(photoInput, 'La imagen no debe pesar más de 2MB.');
            valid = false;
        } else {
            clearErrorMessage(photoInput);
        }


        // Validar Número de Afiliación (solo letras y números, opcional)
        let affiliationInput = document.getElementById('affiliationNumber');
        let textNumberPattern = /^[A-Za-z0-9]+$/;
        if (affiliationInput.value.trim() !== '' && !textNumberPattern.test(affiliationInput.value.trim())) {
            showErrorMessage(affiliationInput, 'El número de afiliación solo puede contener letras y números.');
            valid = false;
        } else {
            clearErrorMessage(affiliationInput);
        }

        // Validar Cédula Profesional (solo letras y números, opcional)
        let licenseInput = document.getElementById('professionalLicense');
        if (licenseInput.value.trim() !== '' && !textNumberPattern.test(licenseInput.value.trim())) {
            showErrorMessage(licenseInput, 'La cédula profesional solo puede contener letras y números.');
            valid = false;
        } else {
            clearErrorMessage(licenseInput);
        }

        return valid;
    }

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

    document.getElementById('photo').addEventListener('change', function(event) {
        const fileName = event.target.files[0] ? event.target.files[0].name : 'Subir Foto';
        document.getElementById('photo-label').textContent = fileName;
    });

    // Puedes conectar esta función al botón de envío del formulario
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!validateInputs()) {
            e.preventDefault();
        }
    });
</script>