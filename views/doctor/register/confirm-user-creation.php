<?php
// views/doctor/register/confirm-user-creation.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get the URLs from the query parameters
$confirmUrl = isset($_GET['confirm_url']) ? $_GET['confirm_url'] : '';
$cancelUrl = isset($_GET['cancel_url']) ? $_GET['cancel_url'] : '';

// If the URLs are not provided, redirect to the doctor list
if (empty($confirmUrl) || empty($cancelUrl)) {
    header('Location: /views/doctor/list/list-doctors.view.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/views/dashboard/icons/Untitled-design-_1_.ico" type="image/x-icon">
    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/forms.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Confirmación de Creación de Usuario</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: var(--base-clr);
            margin: 0;
            padding: 0;
            font-family: Poppins, "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .confirmation-container {
            background-color: var(--hover-clr);
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 90%;
            max-width: 600px;
            text-align: center;
            margin: 2rem;
        }

        .confirmation-header {
            margin-bottom: 2rem;
            position: relative;
        }

        h1 {
            color: var(--text-clr);
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        p {
            color: var(--secondary-text-clr);
            margin-bottom: 2rem;
            line-height: 1.6;
            font-size: 1.1rem;
        }

        .buttons {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            flex: 0 0 auto;
            min-width: 180px;
        }

        .btn-confirm {
            background-color: var(--accent-clr);
            color: white;
        }

        .btn-confirm:hover {
            background-color: #4a54e1;
            transform: translateY(-2px);
        }

        .btn-cancel {
            background-color: var(--delete-btn-clr);
            color: white;
        }

        .btn-cancel:hover {
            background-color: var(--delete-btn-hover-clr);
            transform: translateY(-2px);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .confirmation-container {
                padding: 1.5rem;
                margin: 1rem;
            }

            h1 {
                font-size: 1.5rem;
            }

            p {
                font-size: 1rem;
            }

            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .buttons {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="confirmation-header">
            <h1>Doctor Registrado con Éxito</h1>
        </div>
        <p>El doctor ha sido registrado correctamente en el sistema. ¿Desea crear un usuario en la sección de usuarios para este doctor?</p>
        <div class="buttons">
            <button class="btn btn-confirm" onclick="window.location.href='<?php echo htmlspecialchars($confirmUrl); ?>'">
                <i class="bi bi-check-circle"></i> Sí, crear usuario
            </button>
            <button class="btn btn-cancel" onclick="window.location.href='<?php echo htmlspecialchars($cancelUrl); ?>'">
                <i class="bi bi-x-circle"></i> No, solo registrar doctor
            </button>
        </div>
    </div>
</body>
</html>
