<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial Médico Público | Medic Life</title>
    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/forms.css">
    <link rel="icon" href="/views/dashboard/icons/Untitled-design-_1_.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .public-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background-color: var(--hover-clr);
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .public-header img {
            max-width: 200px;
            margin-bottom: 15px;
        }

        .public-header h1 {
            color: var(--text-clr);
            margin: 0;
            font-size: 24px;
        }

        .public-header p {
            color: var(--secondary-text-clr);
            margin-top: 10px;
        }

        .info-box {
            background-color: #e1f5fe;
            border-left: 4px solid var(--accent-clr);
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .info-box h3 {
            margin-top: 0;
            color: var(--accent-clr);
        }

        .info-box p {
            margin-bottom: 0;
        }

        .input-with-icon {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-with-icon i {
            position: absolute;
            left: 12px;
            color: var(--secondary-text-clr);
            font-size: 18px;
        }

        .input-with-icon input {
            padding-left: 40px;
        }

        .form-help {
            display: block;
            margin-top: 5px;
            color: var(--secondary-text-clr);
            font-size: 12px;
        }

        .patient-info {
            margin-bottom: 30px;
            padding: 20px;
            background-color: var(--base-clr);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 4px solid var(--accent-clr);
        }

        .patient-info h3 {
            margin-top: 0;
            color: var(--text-clr);
            border-bottom: 1px solid var(--line-clr);
            padding-bottom: 10px;
            font-size: 20px;
        }

        .patient-info p {
            margin: 10px 0;
            line-height: 1.5;
        }

        .patient-info strong {
            color: var(--text-clr);
            display: inline-block;
            min-width: 150px;
        }

        .record-card {
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid var(--line-clr);
            border-radius: 8px;
            background-color: var(--hover-clr);
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
            border-bottom: 1px solid var(--line-clr);
            padding-bottom: 12px;
        }

        .record-title {
            font-weight: bold;
            color: var(--text-clr);
            font-size: 18px;
        }

        .record-date {
            color: var(--secondary-text-clr);
            font-style: italic;
        }

        .record-content p {
            margin: 12px 0;
            line-height: 1.6;
        }

        .record-content strong {
            color: var(--text-clr);
        }

        .actions-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .actions-container .submit-btn {
            min-width: 180px;
            text-align: center;
            margin: 5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .actions-container .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        .no-records {
            text-align: center;
            padding: 20px;
            color: var(--secondary-text-clr);
            font-style: italic;
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
            color: var(--secondary-text-clr);
            font-size: 14px;
        }

        /* Override body grid display that causes layout issues */
        body {
            display: block;
        }

        /* Center the form-container in the page */
        .form-container {
            max-width: 800px;
            width: 90%;
            margin: 0 auto;
        }

        /* Ensure center-container works properly with any content height */
        .center-container {
            min-height: 100%;
            padding: 20px 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        @media (max-width: 768px) {
            .actions-container {
                flex-direction: column;
                gap: 10px;
                align-items: center;
            }

            .actions-container div {
                display: flex;
                flex-direction: column;
                width: 100%;
                align-items: center;
            }

            .actions-container .submit-btn {
                width: 90%;
                margin: 5px 0;
                padding: 12px 0;
            }
        }
    </style>
</head>
<body>
    <div class="center-container">
        <div class="form-container">
            <div class="public-header">
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
                    <button type="submit" class="submit-btn">
                        <i class="bi bi-search"></i> Consultar Historial
                    </button>
                </form>
        {else}
            <div class="form-section">
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

                <h3 class="section-title">Registros Médicos</h3>

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
                                <p><strong>Observaciones:</strong> {$record.observations|escape|nl2br}</p>
                                <p><strong>Tratamiento:</strong> {$record.treatment|escape|nl2br}</p>
                            </div>
                        </div>
                    {/foreach}
                {/if}

                <div class="actions-container">
                    <div>
                        <a href="/controllers/public/medical_history/download-history-pdf.controller.php?curp={$patient.CURP|escape}" class="submit-btn">
                            <i class="bi bi-file-earmark-pdf"></i> Descargar PDF
                        </a>
                        <a href="/controllers/public/medical_history/send-history-report.controller.php?curp={$patient.CURP|escape}" class="submit-btn" style="background-color: #2ecc71;">
                            <i class="bi bi-envelope"></i> Enviar por Correo
                        </a>
                    </div>
                    <a href="/views/public/medical_history/view-history.view.php" class="submit-btn" style="background-color: #95a5a6;">
                        <i class="bi bi-search"></i> Nueva Consulta
                    </a>
                </div>
            </div>
        {/if}

        <div class="footer">
            <p>&copy; {$smarty.now|date_format:"%Y"} Medic Life. Todos los derechos reservados.</p>
        </div>
        </div>
    </div>
</body>
</html>
