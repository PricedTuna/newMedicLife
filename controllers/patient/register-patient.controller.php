<?php
// Conexión a la base de datos (asegúrate de tener $pdo configurado)
require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Función para validar longitud
    function validarLongitud($campo, $valor, $max)
    {
        if (strlen($valor) > $max) {
            throw new Exception("El campo $campo excede la longitud máxima de $max caracteres.");
        }
    }

    try {
        $names = $_POST['names'];
        $last_name = $_POST['last_name'];
        $last_name2 = $_POST['last_name2'];
        $id_state = $_POST['state'];
        $id_municipality = $_POST['municipality'];
        $id_locality = $_POST['locality'];
        $CP = $_POST['CP'];
        $street = $_POST['street'];
        $external_number = $_POST['external_number'];
        $internal_number = $_POST['internal_number'];
        $neighborhood = $_POST['neighborhood'];
        $insurance_number = $_POST['insurance_number'];
        $birth_date = $_POST['birth_date'];
        $CURP = $_POST['CURP'];
        $RFC = $_POST['RFC'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $gender = $_POST['gender'];
        $weight = $_POST['weight'];
        $height = $_POST['height'];
        $blood_type = $_POST['blood_type'];
        $marital_status = $_POST['marital_status'];
        $ethnic_group = $_POST['ethnic_group'];
        $religion = $_POST['religion'];

        try {
            validarLongitud('names', $names, 40);

            validarLongitud('last_name', $last_name, 40);

            validarLongitud('last_name2', $last_name2, 40);

            if (strlen($CP) !== 5) throw new Exception("El código postal debe ser de 5 caracteres.");

            validarLongitud('street', $street, 50);

            validarLongitud('external_number', $external_number, 8);

            validarLongitud('internal_number', $internal_number, 8);

            validarLongitud('neighborhood', $neighborhood, 50);

            validarLongitud('insurance_number', $insurance_number, 20);

            if ($CURP && strlen($CURP) !== 18) throw new Exception("La CURP debe tener 18 caracteres.");

            if ($RFC && strlen($RFC) !== 13) throw new Exception("El RFC debe tener 13 caracteres.");

            if (strlen($phone) !== 10) throw new Exception("El teléfono debe tener 10 dígitos.");

            validarLongitud('email', $email, 50);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception("El correo electrónico no es válido.");

            if (strlen($gender) > 2) throw new Exception("El género debe ser máximo de 2 caracteres.");
        } catch (Exception $e) {
            header("Location: /views/patient/register/register-patient.view.php?error=" . urlencode($e->getMessage()));
            exit;
        }

        $status = 'A'; // Puedes establecer un status por defecto

        try {
            $stmt = $pdo->prepare("INSERT INTO patients (
                names, last_name, last_name2, id_state, id_municipality, id_locality, CP, street, external_number, internal_number, neighborhood, insurance_number, birth_date, CURP, RFC, phone, email, gender, weight, height, blood_type, marital_status, ethnic_group, religion, status
            ) VALUES (
                :names, :last_name, :last_name2, :id_state, :id_municipality, :id_locality, :CP, :street, :external_number, :internal_number, :neighborhood, :insurance_number, :birth_date, :CURP, :RFC, :phone, :email, :gender, :weight, :height, :blood_type, :marital_status, :ethnic_group, :religion, :status
            )");

            $stmt->execute([
                ':names' => $names,
                ':last_name' => $last_name,
                ':last_name2' => $last_name2,
                ':id_state' => $id_state,
                ':id_municipality' => $id_municipality,
                ':id_locality' => $id_locality,
                ':CP' => $CP,
                ':street' => $street,
                ':external_number' => $external_number,
                ':internal_number' => $internal_number,
                ':neighborhood' => $neighborhood,
                ':insurance_number' => $insurance_number,
                ':birth_date' => $birth_date,
                ':CURP' => $CURP,
                ':RFC' => $RFC,
                ':phone' => $phone,
                ':email' => $email,
                ':gender' => $gender,
                ':weight' => $weight,
                ':height' => $height,
                ':blood_type' => $blood_type,
                ':marital_status' => $marital_status,
                ':ethnic_group' => $ethnic_group,
                ':religion' => $religion,
                ':status' => $status,
            ]);

            header('Location: /views/dashboard/dashboard.view.php?success=' . urlencode("paciente creado"));
        } catch (Exception $e) {
            header("Location: /views/patient/register/register-patient.view.php?error=" . urlencode($e->getMessage()));
        }
    } catch (\Throwable $th) {
        header("Location: /views/patient/register/register-patient.view.php?error=" . urlencode($th->getMessage()));
    }    
}
