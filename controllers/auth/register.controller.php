<?php
// controllers/auth/register.controller.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/doctor/doctor.model.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recopilación de datos del formulario
    $data = [
        'name'      => $_POST['name'] ?? '',
        'email'     => $_POST['email'] ?? '',
        'password'  => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? '',
        'role'      => $_POST['role'] ?? 'S', // Default role is 'S' (Secretaria)
        'doctor_id' => $_POST['doctor_id'] ?? null,
    ];

    // Si el rol es Doctor y se seleccionó un doctor, obtener sus datos
    if ($data['role'] === 'D' && !empty($data['doctor_id'])) {
        $doctorModel = new DoctorModel($pdo);
        $stmt = $pdo->prepare("SELECT names, last_name, last_name2, email FROM doctors WHERE id = :doctor_id AND status = 'A'");
        $stmt->execute([':doctor_id' => $data['doctor_id']]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($doctor) {
            // Usar los datos del doctor para el usuario
            $data['name'] = $doctor['names'] . ' ' . $doctor['last_name'] . ' ' . $doctor['last_name2'];
            $data['email'] = $doctor['email'];
        }
    }

    // Validación básica
    $errors = [];

    // Validar nombre
    if (empty($data['name'])) {
        $errors[] = "El nombre es obligatorio.";
    } elseif (strlen($data['name']) > 100) {
        $errors[] = "El nombre no puede exceder los 100 caracteres.";
    }

    // Validar email
    if (empty($data['email'])) {
        $errors[] = "El correo electrónico es obligatorio.";
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El formato del correo electrónico no es válido.";
    } else {
        // Verificar si el email ya existe
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => $data['email']]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $errors[] = "Este correo electrónico ya está registrado.";
        }
    }

    // Validar contraseña
    if (empty($data['password'])) {
        $errors[] = "La contraseña es obligatoria.";
    } elseif (strlen($data['password']) < 8) {
        $errors[] = "La contraseña debe tener al menos 8 caracteres.";
    }

    // Validar confirmación de contraseña
    if ($data['password'] !== $data['confirm_password']) {
        $errors[] = "Las contraseñas no coinciden.";
    }

    // Si hay errores, redirigir de vuelta al formulario
    if (!empty($errors)) {
        $errorString = implode(", ", $errors);
        header('Location: /views/user/register/register-user.view.php?error=' . urlencode($errorString));
        exit;
    }

    try {
        // Encriptar la contraseña
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        // Insertar el nuevo usuario
        if ($data['role'] === 'D' && !empty($data['doctor_id'])) {
            // Si es un doctor, guardar también el ID del doctor
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, doctor_id, status) VALUES (:name, :email, :password, :role, :doctor_id, 'AC')");
            $stmt->execute([
                ':name'     => $data['name'],
                ':email'    => $data['email'],
                ':password' => $hashedPassword,
                ':role'     => $data['role'],
                ':doctor_id'=> $data['doctor_id']
            ]);
        } else {
            // Para otros roles
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, status) VALUES (:name, :email, :password, :role, 'AC')");
            $stmt->execute([
                ':name'     => $data['name'],
                ':email'    => $data['email'],
                ':password' => $hashedPassword,
                ':role'     => $data['role']
            ]);
        }

        // Redirigir al dashboard con mensaje de éxito
        header('Location: /views/dashboard/dashboard.view.php?success=' . urlencode("Usuario registrado con éxito"));
        exit;
    } catch (PDOException $e) {
        header('Location: /views/user/register/register-user.view.php?error=' . urlencode("Error al registrar el usuario: " . $e->getMessage()));
        exit;
    }
} else {
    // Si no es una solicitud POST, redirigir al formulario
    header('Location: /views/user/register/register-user.view.php');
    exit;
}
?>
