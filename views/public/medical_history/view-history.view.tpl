<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial Médico Público | Medic Life</title>
    <link rel="icon" href="/views/dashboard/icons/Untitled-design-_1_.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
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
        .form-container, .history-container {
            background-color: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
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
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        input[type="text"]:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
            outline: none;
        }
        .input-with-icon {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-with-icon i {
            position: absolute;
            left: 12px;
            color: #7f8c8d;
            font-size: 18px;
        }
        .input-with-icon input {
            padding-left: 40px;
        }
        .form-help {
            display: block;
            margin-top: 5px;
            color: #7f8c8d;
            font-size: 12px;
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
            transition: all 0.3s ease;
            text-decoration: none;
            margin-right: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            font-weight: 500;
        }
        .btn:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 3px rgba(0,0,0,0.1);
        }
        .btn-secondary {
            background-color: #95a5a6;
        }
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        .btn-success {
            background-color: #2ecc71;
        }
        .btn-success:hover {
            background-color: #27ae60;
        }
        .btn i {
            margin-right: 8px;
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
        .patient-info {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f5f5f5;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 4px solid #3498db;
        }
        .patient-info h3 {
            margin-top: 0;
            color: #2c3e50;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            font-size: 20px;
        }
        .patient-info p {
            margin: 10px 0;
            line-height: 1.5;
        }
        .patient-info strong {
            color: #34495e;
            display: inline-block;
            min-width: 150px;
        }
        .record-card {
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .record-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .record-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 12px;
        }
        .record-title {
            font-weight: bold;
            color: #2c3e50;
            font-size: 18px;
        }
        .record-date {
            color: #7f8c8d;
            font-style: italic;
        }
        .record-content p {
            margin: 12px 0;
            line-height: 1.6;
        }
        .record-content strong {
            color: #34495e;
        }
        .actions-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .no-records {
            text-align: center;
            padding: 20px;
            color: #7f8c8d;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="/views/dashboard/icons/medicLifeLogo.svg" alt="Medic Life Logo">
            <h1>Historial Médico Público</h1>
            <p>Consulte su historial médico proporcionando su CURP</p>
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

        {if !$curp || !$patient}
            <div class="form-container">
                <div class="info-box">
                    <h3>Información Importante</h3>
                    <p>Ingrese su CURP para consultar su historial médico.</p>
                </div>

                <form action="/views/public/medical_history/view-history.view.php" method="GET">
                    <div class="form-group">
                        <label for="curp">CURP:</label>
                        <div class="input-with-icon">
                            <i class="bi bi-person-badge"></i>
                            <input type="text" id="curp" name="curp" placeholder="Ingrese su CURP" value="{$curp|escape}" required>
                        </div>
                        <small class="form-help">Ingrese su CURP completa (18 caracteres)</small>
                    </div>
                    <button type="submit" class="btn">
                        <i class="bi bi-search"></i> Consultar Historial
                    </button>
                </form>
            </div>
        {else}
            <div class="history-container">
                <div class="patient-info">
                    <h3>Información del Paciente</h3>
                    <p><strong>Nombre:</strong> {$patient.names} {$patient.last_name} {$patient.last_name2}</p>
                    <p><strong>CURP:</strong> {$patient.CURP}</p>
                    <p><strong>Fecha de Nacimiento:</strong> {$patient.birth_date}</p>
                    <p><strong>Género:</strong> {if $patient.gender == 'M'}Masculino{else}Femenino{/if}</p>
                    <p><strong>Tipo de Sangre:</strong> {$patient.blood_type}</p>
                    <p><strong>Peso:</strong> {$patient.weight} kg</p>
                    <p><strong>Altura:</strong> {$patient.height} cm</p>
                </div>

                <h3>Registros Médicos</h3>

                {if empty($history)}
                    <div class="no-records">
                        <p>No hay registros médicos para este paciente.</p>
                    </div>
                {else}
                    {foreach from=$history item=record}
                        <div class="record-card">
                            <div class="record-header">
                                <div class="record-title">{$record.diagnosis|escape}</div>
                                <div class="record-date">{$record.record_date|escape}</div>
                            </div>
                            <div class="record-content">
                                <p><strong>Doctor:</strong> {$record.doctor_names|escape} {$record.doctor_last_name|escape} {$record.doctor_last_name2|escape}</p>
                                <p><strong>Observaciones:</strong> {$record.observations|escape|nl2br}</p>
                                <p><strong>Tratamiento:</strong> {$record.treatment|escape|nl2br}</p>
                            </div>
                        </div>
                    {/foreach}
                {/if}

                <div class="actions-container">
                    <div>
                        <a href="/controllers/public/medical_history/download-history-pdf.controller.php?curp={$patient.CURP|escape}" class="btn">
                            <i class="bi bi-file-earmark-pdf"></i> Descargar PDF
                        </a>
                        <a href="/controllers/public/medical_history/send-history-report.controller.php?curp={$patient.CURP|escape}" class="btn btn-success">
                            <i class="bi bi-envelope"></i> Enviar por Correo
                        </a>
                    </div>
                    <a href="/views/public/medical_history/view-history.view.php" class="btn btn-secondary">
                        <i class="bi bi-search"></i> Nueva Consulta
                    </a>
                </div>
            </div>
        {/if}

        <div class="footer">
            <p>&copy; {$smarty.now|date_format:"%Y"} Medic Life. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
