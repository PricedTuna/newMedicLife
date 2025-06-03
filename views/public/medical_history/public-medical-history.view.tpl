<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial Médico Público | Medic Life</title>
    <link rel="icon" href="/views/dashboard/icons/Untitled-design-_1_.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header img {
            max-width: 200px;
            margin-bottom: 15px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #7f8c8d;
            margin-top: 10px;
        }
        .form-container {
            background-color: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #2c3e50;
        }
        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            display: inline-block;
            text-align: center;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #2980b9;
        }
        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            color: #7f8c8d;
            font-size: 14px;
        }
        .info-box {
            background-color: #e1f5fe;
            border-left: 4px solid #03a9f4;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .info-box h3 {
            margin-top: 0;
            color: #0288d1;
        }
        .info-box p {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="/views/dashboard/icons/medicLifeLogo.svg" alt="Medic Life Logo">
            <h1>Historial Médico Público</h1>
            <p>Acceda a su historial médico proporcionando su CURP</p>
        </div>

        {if isset($error)}
            <div class="error-message">
                {$error|escape}
            </div>
        {/if}

        {if isset($success)}
            <div class="success-message">
                {$success|escape}
            </div>
        {/if}

        <div class="form-container">
            <div class="info-box">
                <h3>Información Importante</h3>
                <p>Al proporcionar su CURP, puede ver su historial médico directamente o solicitar que se envíe a su correo electrónico registrado.</p>
            </div>

            <form action="/controllers/public/medical_history/send-history-report.controller.php" method="POST" id="email-form">
                <div class="form-group">
                    <label for="curp">CURP:</label>
                    <input type="text" id="curp" name="curp" placeholder="Ingrese su CURP" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn">Enviar por Correo</button>
                    <a href="javascript:void(0);" onclick="viewHistory();" class="btn" style="background-color: #2ecc71;">Ver Directamente</a>
                </div>
            </form>

            <script>
                function viewHistory() {
                    const curp = document.getElementById('curp').value;
                    if (curp) {
                        window.location.href = '/views/public/medical_history/view-history.view.php?curp=' + encodeURIComponent(curp);
                    } else {
                        Swal.fire({
                            title: 'Campo requerido',
                            text: 'Por favor, ingrese su CURP',
                            icon: 'warning',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Entendido'
                        });
                    }
                }
            </script>
        </div>

        <div class="footer">
            <p>&copy; {$smarty.now|date_format:"%Y"} Medic Life. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
