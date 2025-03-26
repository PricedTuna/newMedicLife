<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';

function validarCampo($valor, $longitudMax, $campoNombre)
{
  if (empty($valor)) {
    throw new Exception("El campo $campoNombre es obligatorio.");
  }
  if (strlen($valor) > $longitudMax) {
    throw new Exception("El campo $campoNombre supera la longitud máxima de $longitudMax caracteres.");
  }
}

function validarRegex($valor, $regex, $campoNombre)
{
  if (!preg_match($regex, $valor)) {
    throw new Exception("El campo $campoNombre no tiene el formato correcto.");
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Recogemos los datos del formulario
  $names = $_POST['name'];
  $last_name = $_POST['fatherLastName'];
  $last_name2 = $_POST['motherLastName'];
  $id_state = $_POST['state'];
  $id_municipality = $_POST['municipality'];
  $id_locality = $_POST['locality'];
  $CP = $_POST['postalCode'];
  $street = $_POST['street'];
  $external_number = $_POST['extNumber'];
  $internal_number = !empty($_POST['intNumber']) ? $_POST['intNumber'] : null; // Si está vacío, guardamos NULL
  $neighborhood = $_POST['neighborhood'];
  $insurance_number = $_POST['affiliationNumber'];
  $professional_id = $_POST['professionalLicense'];
  $birth_date = $_POST['birthDate'];
  $CURP = $_POST['curp'];
  $RFC = $_POST['rfc'];
  $phone = $_POST['phoneNumber'];
  $email = $_POST['email'];
  $gender = $_POST['gender'];

  try {
    // Validamos los campos
    validarCampo($names, 40, 'nombre');
    validarCampo($last_name, 40, 'apellido paterno');
    validarCampo($last_name2, 40, 'apellido materno');
    validarRegex($CP, '/^\d{5}$/', 'código postal');
    validarCampo($street, 50, 'calle');
    validarCampo($external_number, 8, 'número exterior');
    validarCampo($neighborhood, 50, 'colonia');
    validarCampo($insurance_number, 20, 'número de afiliación');
    validarCampo($professional_id, 15, 'cédula profesional');
    validarRegex($birth_date, '/^\d{4}-\d{2}-\d{2}$/', 'fecha de nacimiento (YYYY-MM-DD)');
    validarCampo($CURP, 18, 'CURP');
    validarCampo($RFC, 13, 'RFC');
    validarRegex($phone, '/^\d{10}$/', 'teléfono');
    validarRegex($email, '/^[\w\.\-]+@[\w\.\-]+\.\w{2,4}$/', 'correo electrónico');
    validarCampo($gender, 2, 'género');

    // Nota: No validamos el número interior si está vacío
    if (!empty($internal_number)) {
      validarCampo($internal_number, 8, 'número interior');
    }
  } catch (Exception $e) {
    header('Location: /views/doctor/register/register-doctor.view.php?error=' . urlencode($e->getMessage()) . '&id=' . $_POST['doctor_id']);
    exit;
  }

  // Subida y validación de la foto
  $photoData = null;

  if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $photoTmp = $_FILES['photo']['tmp_name'];
    $photoMime = mime_content_type($photoTmp);

    // Validamos que sea una imagen
    if (!in_array($photoMime, ['image/jpeg', 'image/png', 'image/gif'])) {
      header('Location: /views/doctor/register/register-doctor.view.php?error=' . urlencode("El archivo debe ser una imagen JPG, PNG o GIF.") . '&id=' . $_POST['doctor_id']);
      exit;
    }

    // Leemos los bytes de la imagen para guardarla como binario
    $photoData = file_get_contents($photoTmp);
  } elseif (empty($_POST['doctor_id'])) {
    // En creación, la foto es obligatoria
    header('Location: /views/doctor/register/register-doctor.view.php?error=' . urlencode("Debes subir una foto.") . '&id=' . $_POST['doctor_id']);
    exit;
  }


  try {
    // Si se está actualizando, se excluye el registro actual de la validación.
    if (!empty($_POST['doctor_id']) && is_numeric($_POST['doctor_id'])) {
      $doctor_id = $_POST['doctor_id'];
      $stmt = $pdo->prepare("SELECT id FROM doctors WHERE CURP = :CURP AND id != :doctor_id");
      $stmt->execute([
        ':CURP' => $CURP,
        ':doctor_id' => $doctor_id
      ]);
    } else {
      // En inserción, se consulta cualquier registro con la CURP.
      $stmt = $pdo->prepare("SELECT id FROM doctors WHERE CURP = :CURP");
      $stmt->execute([
        ':CURP' => $CURP
      ]);
    }

    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
      throw new Exception("La CURP ya existe en la base de datos.");
    }
  } catch (Exception $e) {
    header('Location: /views/doctor/register/register-doctor.view.php?error=' . urlencode($e->getMessage()) . '&id=' . $_POST['doctor_id']);
    exit;
  }

  try {
    // Validación de unicidad de CURP
    if (!empty($_POST['doctor_id']) && is_numeric($_POST['doctor_id'])) {
      $doctor_id = $_POST['doctor_id'];
      $stmt = $pdo->prepare("SELECT id FROM doctors WHERE CURP = :CURP AND id != :doctor_id");
      $stmt->execute([
        ':CURP' => $CURP,
        ':doctor_id' => $doctor_id
      ]);
    } else {
      $stmt = $pdo->prepare("SELECT id FROM doctors WHERE CURP = :CURP");
      $stmt->execute([
        ':CURP' => $CURP
      ]);
    }

    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
      throw new Exception("La CURP ya existe en la base de datos.");
    }

    // Validación de unicidad de insurance_number (número de afiliación)
    if (!empty($_POST['doctor_id']) && is_numeric($_POST['doctor_id'])) {
      $doctor_id = $_POST['doctor_id'];
      $stmt = $pdo->prepare("SELECT id FROM doctors WHERE insurance_number = :insurance_number AND id != :doctor_id");
      $stmt->execute([
        ':insurance_number' => $insurance_number,
        ':doctor_id' => $doctor_id
      ]);
    } else {
      $stmt = $pdo->prepare("SELECT id FROM doctors WHERE insurance_number = :insurance_number");
      $stmt->execute([
        ':insurance_number' => $insurance_number
      ]);
    }

    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
      throw new Exception("El número de afiliación ya existe en la base de datos.");
    }
  } catch (Exception $e) {
    header('Location: /views/doctor/register/register-doctor.view.php?error=' . urlencode($e->getMessage()) . '&id=' . $_POST['doctor_id']);
    exit;
  }

  try {
    // Validación de unicidad de CURP
    if (!empty($_POST['doctor_id']) && is_numeric($_POST['doctor_id'])) {
      $doctor_id = $_POST['doctor_id'];
      $stmt = $pdo->prepare("SELECT id FROM doctors WHERE CURP = :CURP AND id != :doctor_id");
      $stmt->execute([
        ':CURP' => $CURP,
        ':doctor_id' => $doctor_id
      ]);
    } else {
      $stmt = $pdo->prepare("SELECT id FROM doctors WHERE CURP = :CURP");
      $stmt->execute([
        ':CURP' => $CURP
      ]);
    }

    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
      throw new Exception("La CURP ya existe en la base de datos.");
    }

    // Validación de unicidad de insurance_number (número de afiliación)
    if (!empty($_POST['doctor_id']) && is_numeric($_POST['doctor_id'])) {
      $doctor_id = $_POST['doctor_id'];
      $stmt = $pdo->prepare("SELECT id FROM doctors WHERE insurance_number = :insurance_number AND id != :doctor_id");
      $stmt->execute([
        ':insurance_number' => $insurance_number,
        ':doctor_id' => $doctor_id
      ]);
    } else {
      $stmt = $pdo->prepare("SELECT id FROM doctors WHERE insurance_number = :insurance_number");
      $stmt->execute([
        ':insurance_number' => $insurance_number
      ]);
    }

    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
      throw new Exception("El número de afiliación ya existe en la base de datos.");
    }
  } catch (Exception $e) {
    header('Location: /views/doctor/register/register-doctor.view.php?error=' . urlencode($e->getMessage()) . '&id=' . $_POST['doctor_id']);
    exit;
  }




  try {
    if (!empty($_POST['doctor_id']) && is_numeric($_POST['doctor_id'])) {
      $doctor_id = $_POST['doctor_id'];

      $stmt = $pdo->prepare("SELECT id FROM doctors WHERE id = :doctor_id");
      $stmt->execute([':doctor_id' => $doctor_id]);
      $existingDoctor = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$existingDoctor) {
        throw new Exception("El doctor con ID $doctor_id no existe.");
      }

      $stmt = $pdo->prepare("UPDATE doctors SET
        names = :names, last_name = :last_name, last_name2 = :last_name2, id_state = :id_state,
        id_municipality = :id_municipality, id_locality = :id_locality, CP = :CP, street = :street,
        external_number = :external_number, internal_number = :internal_number, neighborhood = :neighborhood,
        insurance_number = :insurance_number, professional_id = :professional_id, birth_date = :birth_date,
        CURP = :CURP, RFC = :RFC, phone = :phone, gender = :gender, email = :email, photo = :photo, status = :status
        WHERE id = :doctor_id");

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
        ':internal_number' => $internal_number, // Ahora puede ser NULL
        ':neighborhood' => $neighborhood,
        ':insurance_number' => $insurance_number,
        ':professional_id' => $professional_id,
        ':birth_date' => $birth_date,
        ':CURP' => $CURP,
        ':RFC' => $RFC,
        ':phone' => $phone,
        ':gender' => $gender,
        ':email' => $email,
        ':photo' => $photoData,
        ':status' => 'A',
        ':doctor_id' => $doctor_id
      ]);

      header('Location: /views/doctor/list/list-doctors.view.php?success=' . urlencode("Doctor actualizado con éxito"));
    } else {
      $stmt = $pdo->prepare("INSERT INTO doctors (
        names, last_name, last_name2, id_state, id_municipality, id_locality, CP, street, external_number, internal_number,
        neighborhood, insurance_number, professional_id, birth_date, CURP, RFC, phone, gender, email, photo, status
      ) VALUES (
        :names, :last_name, :last_name2, :id_state, :id_municipality, :id_locality, :CP, :street, :external_number, :internal_number,
        :neighborhood, :insurance_number, :professional_id, :birth_date, :CURP, :RFC, :phone, :gender, :email, :photo, :status
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
        ':internal_number' => $internal_number, // Ahora puede ser NULL
        ':neighborhood' => $neighborhood,
        ':insurance_number' => $insurance_number,
        ':professional_id' => $professional_id,
        ':birth_date' => $birth_date,
        ':CURP' => $CURP,
        ':RFC' => $RFC,
        ':phone' => $phone,
        ':gender' => $gender,
        ':email' => $email,
        ':photo' => $photoData,
        ':status' => 'A'
      ]);

      header('Location: /views/dashboard/dashboard.view.php?success=' . urlencode("Doctor creado con éxito"));
    }
  } catch (PDOException $e) {
    header('Location: /views/doctor/register/register-doctor.view.php?error=' . urlencode($e->getMessage()) . '&id=' . $_POST['doctor_id']);
  }
}
