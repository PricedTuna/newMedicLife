<?php
// controllers/auth/register.controller.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require $_SERVER['DOCUMENT_ROOT'] . '/config/database.config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/doctor/doctor.model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/auth/role.controller.php';

// Only administrators can perform user management actions
isAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si estamos en modo edición o cambio de contraseña
    $editMode = isset($_POST['edit_mode']) && $_POST['edit_mode'] == '1';
    $passwordChangeMode = isset($_POST['password_change_mode']) && $_POST['password_change_mode'] == '1';
    $userId = $editMode ? ($_POST['user_id'] ?? null) : null;

    // Si estamos en modo edición, obtener los datos actuales del usuario
    $currentUserData = null;
    if ($editMode && $userId) {
        $stmt = $pdo->prepare("SELECT role, id_doctor FROM users WHERE id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        $currentUserData = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Recopilación de datos del formulario
    $data = [
        'name'      => $_POST['name'] ?? '',
        'email'     => $_POST['email'] ?? '',
        'password'  => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? '',
        'role'      => $_POST['role'] ?? 'S', // Default role is 'S' (Secretaria)
        'id_doctor' => $_POST['id_doctor'] ?? null,
    ];

    // Si estamos en modo edición, mantener el rol original para todos los usuarios
    if ($editMode && $currentUserData) {
        // Mantener el rol original
        $data['role'] = $currentUserData['role'];

        // Si es un doctor, mantener también el id_doctor original
        if ($currentUserData['role'] === 'D' && !empty($currentUserData['id_doctor'])) {
            $data['id_doctor'] = $currentUserData['id_doctor'];
        }
    }

    // Si el rol es Doctor y se seleccionó un doctor, obtener sus datos
    if ($data['role'] === 'D' && !empty($data['id_doctor'])) {
        $doctorModel = new DoctorModel($pdo);
        $stmt = $pdo->prepare("SELECT names, last_name, last_name2, email FROM doctors WHERE id = :id_doctor AND status = 'A'");
        $stmt->execute([':id_doctor' => $data['id_doctor']]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($doctor) {
            // Usar los datos del doctor para el usuario
            $data['name'] = $doctor['names'] . ' ' . $doctor['last_name'] . ' ' . $doctor['last_name2'];
            $data['email'] = $doctor['email'];
        }
    }

    // Validación básica
    $errors = [];

    // Si estamos en modo de cambio de contraseña, solo validamos la contraseña
    if ($passwordChangeMode) {
        // En modo de cambio de contraseña, la contraseña es obligatoria
        if (empty($data['password'])) {
            $errors[] = "La contraseña es obligatoria.";
        } elseif (strlen($data['password']) < 8) {
            $errors[] = "La contraseña debe tener al menos 8 caracteres.";
        }

        // Validar confirmación de contraseña
        if ($data['password'] !== $data['confirm_password']) {
            $errors[] = "Las contraseñas no coinciden.";
        }
    } else {
        // Validación normal para registro o actualización

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
            // Verificar si el email ya existe (solo para nuevos usuarios o si el email cambió)
            if (!$editMode) {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
                $stmt->execute([':email' => $data['email']]);
                if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                    $errors[] = "Este correo electrónico ya está registrado.";
                }
            } else {
                // En modo edición, verificar si el email ya existe pero pertenece a otro usuario
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :user_id");
                $stmt->execute([':email' => $data['email'], ':user_id' => $userId]);
                if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                    $errors[] = "Este correo electrónico ya está registrado por otro usuario.";
                }
            }
        }

        // Validar contraseña
        if (!$editMode) {
            // Para nuevos usuarios, la contraseña es obligatoria
            if (empty($data['password'])) {
                $errors[] = "La contraseña es obligatoria.";
            } elseif (strlen($data['password']) < 8) {
                $errors[] = "La contraseña debe tener al menos 8 caracteres.";
            }

            // Validar confirmación de contraseña
            if ($data['password'] !== $data['confirm_password']) {
                $errors[] = "Las contraseñas no coinciden.";
            }
        } else {
            // En modo edición (no cambio de contraseña), la contraseña es opcional
            if (!empty($data['password'])) {
                // Si se proporciona una contraseña, validarla
                if (strlen($data['password']) < 8) {
                    $errors[] = "La contraseña debe tener al menos 8 caracteres.";
                }

                // Validar confirmación de contraseña
                if ($data['password'] !== $data['confirm_password']) {
                    $errors[] = "Las contraseñas no coinciden.";
                }
            }
        }
    }

    // Si hay errores, redirigir de vuelta al formulario
    if (!empty($errors)) {
        $errorString = implode(", ", $errors);
        // Si estamos en modo edición o cambio de contraseña, redirigir al formulario de registro
        // De lo contrario, redirigir a la lista de usuarios
        if ($editMode || $passwordChangeMode) {
            header('Location: /views/user/register/register-user.view.php?error=' . urlencode($errorString) . ($editMode ? '&id=' . $userId : '') . ($passwordChangeMode ? '&password_change=1' : ''));
        } else {
            header('Location: /views/user/list/list-users.view.php?error=' . urlencode($errorString));
        }
        exit;
    }

    try {
        if (!$editMode) {
            // CREAR NUEVO USUARIO
            // Encriptar la contraseña
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            // Insertar el nuevo usuario
            if ($data['role'] === 'D' && !empty($data['id_doctor'])) {
                // Si es un doctor, guardar también el ID del doctor
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, id_doctor, status) VALUES (:name, :email, :password, :role, :id_doctor, 'AC')");
                $stmt->execute([
                    ':name'     => $data['name'],
                    ':email'    => $data['email'],
                    ':password' => $hashedPassword,
                    ':role'     => $data['role'],
                    ':id_doctor'=> $data['id_doctor']
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

            // Redirigir a la lista de usuarios con mensaje de éxito
            header('Location: /views/user/list/list-users.view.php?success=' . urlencode("Usuario registrado con éxito"));
            exit;
        } else if ($passwordChangeMode) {
            // CAMBIAR CONTRASEÑA DE USUARIO
            // Encriptar la nueva contraseña
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            // Actualizar solo la contraseña del usuario
            $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :user_id");
            $stmt->execute([
                ':password' => $hashedPassword,
                ':user_id'  => $userId
            ]);

            // Redirigir a la lista de usuarios con mensaje de éxito
            header('Location: /views/user/list/list-users.view.php?success=' . urlencode("Contraseña actualizada con éxito"));
            exit;
        } else {
            // ACTUALIZAR USUARIO EXISTENTE
            if (!empty($data['password'])) {
                // Si se proporcionó una nueva contraseña, actualizarla
                $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

                if ($data['role'] === 'D' && !empty($data['id_doctor'])) {
                    // Actualizar usuario con ID de doctor y nueva contraseña
                    $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, password = :password, role = :role, id_doctor = :id_doctor WHERE id = :user_id");
                    $stmt->execute([
                        ':name'     => $data['name'],
                        ':email'    => $data['email'],
                        ':password' => $hashedPassword,
                        ':role'     => $data['role'],
                        ':id_doctor'=> $data['id_doctor'],
                        ':user_id'  => $userId
                    ]);
                } else {
                    // Actualizar usuario sin ID de doctor pero con nueva contraseña
                    $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, password = :password, role = :role, id_doctor = NULL WHERE id = :user_id");
                    $stmt->execute([
                        ':name'     => $data['name'],
                        ':email'    => $data['email'],
                        ':password' => $hashedPassword,
                        ':role'     => $data['role'],
                        ':user_id'  => $userId
                    ]);
                }
            } else {
                // Actualizar sin cambiar la contraseña
                if ($data['role'] === 'D' && !empty($data['id_doctor'])) {
                    // Actualizar usuario con ID de doctor sin cambiar contraseña
                    $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, role = :role, id_doctor = :id_doctor WHERE id = :user_id");
                    $stmt->execute([
                        ':name'     => $data['name'],
                        ':email'    => $data['email'],
                        ':role'     => $data['role'],
                        ':id_doctor'=> $data['id_doctor'],
                        ':user_id'  => $userId
                    ]);
                } else {
                    // Actualizar usuario sin ID de doctor y sin cambiar contraseña
                    $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, role = :role, id_doctor = NULL WHERE id = :user_id");
                    $stmt->execute([
                        ':name'     => $data['name'],
                        ':email'    => $data['email'],
                        ':role'     => $data['role'],
                        ':user_id'  => $userId
                    ]);
                }
            }

            // Redirigir a la lista de usuarios con mensaje de éxito
            header('Location: /views/user/list/list-users.view.php?success=' . urlencode("Usuario actualizado con éxito"));
            exit;
        }
    } catch (PDOException $e) {
        $errorMsg = $editMode ?
            "Error al actualizar el usuario: " . $e->getMessage() :
            "Error al registrar el usuario: " . $e->getMessage();
        // Si estamos en modo edición o cambio de contraseña, redirigir al formulario de registro
        // De lo contrario, redirigir a la lista de usuarios
        if ($editMode || $passwordChangeMode) {
            header('Location: /views/user/register/register-user.view.php?error=' . urlencode($errorMsg) . ($editMode ? '&id=' . $userId : '') . ($passwordChangeMode ? '&password_change=1' : ''));
        } else {
            header('Location: /views/user/list/list-users.view.php?error=' . urlencode($errorMsg));
        }
        exit;
    }
} else {
    // Si no es una solicitud POST, redirigir a la lista de usuarios
    header('Location: /views/user/list/list-users.view.php');
    exit;
}
?>
