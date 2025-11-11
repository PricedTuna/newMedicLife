<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial Médico | Medic Life</title>
    <link rel="icon" href="/views/dashboard/icons/Untitled-design-_1_.ico" type="image/x-icon">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <link rel="stylesheet" href="/views/dashboard/dashboard.styles.css">
    <link rel="stylesheet" href="/views/doctor/medical_history/medical-history.styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/views/components/sidebar.app.js" defer></script>
    <script src="/views/doctor/medical_history/medical-history.app.js" defer></script>
    <script>
        // Pass PHP data to JavaScript
        window.isDoctor = {$isDoctor|json_encode};
        window.doctorId = {$doctorId|json_encode};

        document.addEventListener('DOMContentLoaded', function() {
            {if isset($patient) && isset($patient.id)}
            // Set the current patient ID for JavaScript functions
            window.currentPatientId = {$patient.id};
            console.log("Patient ID set from PHP:", window.currentPatientId);
            {/if}

            console.log("Doctor ID set from PHP:", window.doctorId);
        });
    </script>
</head>
<body>
    {include file='../../components/sidebar.tpl'}

    <main>
        <div class="main-content">
            <header>
                <h1>Historial Médico del Paciente</h1>

                {if isset($smarty.get.success)}
                    <div class="success-message">
                        {$smarty.get.success|escape}
                    </div>
                {/if}

                {if isset($smarty.get.error)}
                    <div class="error-message">
                        {$smarty.get.error|escape}
                    </div>
                {/if}

                {if isset($smarty.get.db_error) || isset($smarty.get.table_error)}
                    <div class="error-message">
                        <p>{if isset($smarty.get.db_error)}{$smarty.get.db_error|escape}{else}Error en las tablas de la base de datos.{/if}</p>
                        <p>Es posible que necesite crear las tablas para el historial médico.</p>
                        <a href="/create-medical-history-tables.php" class="action-btn" target="_blank">Crear Tablas</a>
                    </div>
                {/if}
            </header>

            <section class="patient-search" style="{if isset($showPatientHistory) && $showPatientHistory}display: none;{/if}">
                <h2>Buscar Paciente</h2>
                <form id="searchPatientForm" class="search-form">
                    <div class="form-group">
                        <label for="search-term">Buscar por CURP o Nombre:</label>
                        <input type="text" id="search-term" name="search-term" placeholder="Ingrese CURP o nombre del paciente" required>
                    </div>
                    <button type="submit" class="search-btn">Buscar</button>
                </form>
            </section>

            <section id="patient-results" class="patient-results" style="{if isset($showPatientHistory) && $showPatientHistory}display: none;{/if}">
                <h2>Resultados de la Búsqueda</h2>
                <div class="results-container">
                    <table id="patients-table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>CURP</th>
                                <th>Fecha de Nacimiento</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="patients-list">
                            <!-- Los resultados se insertarán aquí -->
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="patient-history" class="patient-history" style="{if isset($showPatientHistory) && $showPatientHistory}display: block;{else}display: none;{/if}">
                <div class="patient-info">
                    <h2>Historial Médico de <span id="patient-name">{if isset($patient)}{$patient.names|escape} {$patient.last_name|escape} {$patient.last_name2|escape}{/if}</span></h2>
                    <div class="patient-details" style="background-color: #e6f7ff;">
                        <p><strong>CURP:</strong> <span id="patient-curp">{if isset($patient) && (isset($patient.CURP) || isset($patient.curp))}{$patient.CURP|default:$patient.curp|escape}{else}No disponible{/if}</span></p>
                        <p><strong>Fecha de Nacimiento:</strong> <span id="patient-birth-date">{if isset($patient) && isset($patient.birth_date)}{$patient.birth_date|date_format:"%d de %B de %Y"}{else}No disponible{/if}</span></p>
                        <p><strong>Correo Electrónico:</strong> <span id="patient-email">{if isset($patient) && isset($patient.email)}{$patient.email|escape}{else}No disponible{/if}</span></p>
                    </div>
                </div>


                <div class="history-actions">
                    <button id="new-record-btn" class="action-btn">Crear Historial</button>
                    <button id="new-study-btn" class="action-btn">Agendar Estudio</button>
                    <button id="download-pdf-btn" class="action-btn">Descargar PDF</button>
                    <button id="upload-pdf-btn" class="action-btn">Subir PDF</button>
                </div>

                <div style="display:flex; gap:20px; align-items:flex-start;">
                    <div id="history-list" class="history-list" style="{if isset($showPatientHistory) && $showPatientHistory}display: block;{/if}; flex:1;">
                    <h3>Registros Médicos</h3>
                    <div class="records-container">
                        {if isset($patientHistory) && $patientHistory|@count > 0}
                            {foreach from=$patientHistory item=record}
                                <div class="record-card">
                                    <div class="record-header">
                                        <div class="record-title">
                                            {if isset($record.diagnosis) && $record.diagnosis}
                                                {$record.diagnosis|escape}
                                            {elseif isset($record.chief_complaint) && $record.chief_complaint}
                                                {$record.chief_complaint|escape}
                                            {else}
                                                Registro médico
                                            {/if}
                                        </div>
                                        <div class="record-date">
                                            {if isset($record.date_created) && $record.date_created}
                                                {$record.date_created|date_format:"%d de %B de %Y"}
                                            {elseif isset($record.record_date) && $record.record_date}
                                                {$record.record_date|date_format:"%d de %B de %Y"}
                                            {else}
                                                Fecha no disponible
                                            {/if}
                                        </div>
                                    </div>
                                    <div class="record-content">
                                        <p><strong>Doctor:</strong> {$record.doctor_names|escape} {$record.doctor_last_name|escape} {$record.doctor_last_name2|escape}</p>

                                        {if isset($record.chief_complaint) && $record.chief_complaint}
                                            <p><strong>Motivo de Consulta:</strong> {$record.chief_complaint|escape}</p>
                                        {/if}

                                        {if isset($record.current_illness) && $record.current_illness}
                                            <p><strong>Enfermedad Actual:</strong> {$record.current_illness|escape}</p>
                                        {/if}

                                        {if isset($record.personal_history) && $record.personal_history}
                                            <p><strong>Antecedentes Personales:</strong> {$record.personal_history|escape}</p>
                                        {/if}

                                        {if isset($record.family_history) && $record.family_history}
                                            <p><strong>Antecedentes Familiares:</strong> {$record.family_history|escape}</p>
                                        {/if}

                                        {if isset($record.physical_examination) && $record.physical_examination}
                                            <p><strong>Examen Físico:</strong> {$record.physical_examination|escape}</p>
                                        {/if}

                                        {if isset($record.diagnosis) && $record.diagnosis}
                                            <p><strong>Diagnóstico:</strong> {$record.diagnosis|escape}</p>
                                        {/if}

                                        {if isset($record.treatment_plan) && $record.treatment_plan}
                                            <p><strong>Plan de Tratamiento:</strong> {$record.treatment_plan|escape}</p>
                                        {elseif isset($record.treatment) && $record.treatment}
                                            <p><strong>Tratamiento:</strong> {$record.treatment|escape}</p>
                                        {/if}

                                        {if isset($record.observations) && $record.observations}
                                            <p><strong>Observaciones:</strong> {$record.observations|escape}</p>
                                        {/if}

                                        {if isset($record.next_appointment) && $record.next_appointment}
                                            <p><strong>Próxima Cita:</strong> {$record.next_appointment|date_format:"%d de %B de %Y"}</p>
                                        {/if}

                                        {if isset($record.vital_signs_data) && $record.vital_signs_data|@count > 0}
                                            <div class="vital-signs">
                                                <h3>Signos Vitales</h3>
                                                <table>
                                                    <tr>
                                                        <th>Parámetro</th>
                                                        <th>Valor</th>
                                                    </tr>
                                                    {assign var=vitalSigns value=$record.vital_signs_data[0]}

                                                    {if isset($vitalSigns.temperature) && $vitalSigns.temperature}
                                                        <tr>
                                                            <td>Temperatura</td>
                                                            <td>{$vitalSigns.temperature} °C</td>
                                                        </tr>
                                                    {/if}

                                                    {if isset($vitalSigns.blood_pressure) && $vitalSigns.blood_pressure}
                                                        <tr>
                                                            <td>Presión Arterial</td>
                                                            <td>{$vitalSigns.blood_pressure} mmHg</td>
                                                        </tr>
                                                    {/if}

                                                    {if isset($vitalSigns.heart_rate) && $vitalSigns.heart_rate}
                                                        <tr>
                                                            <td>Frecuencia Cardíaca</td>
                                                            <td>{$vitalSigns.heart_rate} lpm</td>
                                                        </tr>
                                                    {/if}

                                                    {if isset($vitalSigns.respiratory_rate) && $vitalSigns.respiratory_rate}
                                                        <tr>
                                                            <td>Frecuencia Respiratoria</td>
                                                            <td>{$vitalSigns.respiratory_rate} rpm</td>
                                                        </tr>
                                                    {/if}

                                                    {if isset($vitalSigns.weight) && $vitalSigns.weight}
                                                        <tr>
                                                            <td>Peso</td>
                                                            <td>{$vitalSigns.weight} kg</td>
                                                        </tr>
                                                    {/if}

                                                    {if isset($vitalSigns.height) && $vitalSigns.height}
                                                        <tr>
                                                            <td>Altura</td>
                                                            <td>{$vitalSigns.height} cm</td>
                                                        </tr>
                                                    {/if}

                                                    {if isset($vitalSigns.bmi) && $vitalSigns.bmi}
                                                        <tr>
                                                            <td>IMC</td>
                                                            <td>{$vitalSigns.bmi} kg/m²</td>
                                                        </tr>
                                                    {/if}

                                                    {if isset($vitalSigns.oxygen_saturation) && $vitalSigns.oxygen_saturation}
                                                        <tr>
                                                            <td>Saturación de Oxígeno</td>
                                                            <td>{$vitalSigns.oxygen_saturation} %</td>
                                                        </tr>
                                                    {/if}

                                                    {if isset($vitalSigns.glucose_level) && $vitalSigns.glucose_level}
                                                        <tr>
                                                            <td>Nivel de Glucosa</td>
                                                            <td>{$vitalSigns.glucose_level} mg/dL</td>
                                                        </tr>
                                                    {/if}
                                                </table>
                                            </div>
                                        {/if}
                                    </div>
                                    <div class="record-actions">
                                        <a href="/controllers/doctor/medical_history/download-history-pdf.controller.php?record_id={$record.id}" class="download-pdf-btn">Descargar PDF</a>
                                    </div>
                                </div>
                            {/foreach}
                        {else}
                            <p id="no-records-message">No hay registros médicos para este paciente.</p>
                        {/if}
                    </div>
                    </div>

                    <!-- Panel derecho: Estudios solicitados -->
                    <aside id="patient-studies-panel" style="width:360px; flex:0 0 360px;">
                        <h3>Estudios Solicitados</h3>
                        <div id="patient-studies-list">
                            <p class="muted">No hay estudios cargados.</p>
                        </div>
                    </aside>
                </div>



            <!-- Modal para nueva entrada de historial médico -->
            <div id="record-modal" class="modal" style="display: none;">
                <div class="modal-content">
                    <span class="close-modal">&times;</span>
                    <h2>Nueva Entrada de Historial Médico</h2>
                    <form id="record-form">
                        <input type="hidden" id="patient-id" name="patient-id" value="{if isset($patient)}{$patient.id}{/if}">
                        <!-- Hidden inputs to store the selected values from the outside selects -->
                        <input type="hidden" id="form-medical-area" name="medical-area" value="">
                        <input type="hidden" id="form-doctor-id" name="doctor-id" value="">

                        <div class="form-group">
                            <label for="record-date">Fecha: <span class="required">*</span></label>
                            <input type="date" id="record-date" name="record-date">
                            <span class="error-message" id="record-date-error"></span>
                        </div>


                        <div class="form-group">
                            <label for="chief-complaint">Motivo de Consulta: <span class="required">*</span></label>
                            <textarea id="chief-complaint" name="chief-complaint" rows="2" placeholder="Describa el motivo de la consulta"></textarea>
                            <span class="error-message" id="chief-complaint-error"></span>
                            <small class="help-text">* Se requiere al menos Motivo de Consulta o Diagnóstico</small>
                        </div>

                        <div class="form-group">
                            <label for="current-illness">Enfermedad Actual:</label>
                            <textarea id="current-illness" name="current-illness" rows="2" placeholder="Describa la enfermedad actual del paciente"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="personal-history">Antecedentes Personales:</label>
                            <textarea id="personal-history" name="personal-history" rows="2" placeholder="Describa los antecedentes personales del paciente"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="family-history">Antecedentes Familiares:</label>
                            <textarea id="family-history" name="family-history" rows="2" placeholder="Describa los antecedentes familiares del paciente"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="physical-examination">Examen Físico:</label>
                            <textarea id="physical-examination" name="physical-examination" rows="3" placeholder="Describa los resultados del examen físico"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Signos Vitales:</label>
                            <div class="vital-signs-grid">
                                <div class="vital-sign-item">
                                    <label for="temperature">Temperatura (°C):</label>
                                    <input type="number" id="temperature" name="temperature" step="0.1" min="30" max="45">
                                </div>
                                <div class="vital-sign-item">
                                    <label for="blood-pressure">Presión Arterial (mmHg):</label>
                                    <input type="text" id="blood-pressure" name="blood-pressure" placeholder="Ej: 120/80">
                                </div>
                                <div class="vital-sign-item">
                                    <label for="heart-rate">Frecuencia Cardíaca (lpm):</label>
                                    <input type="number" id="heart-rate" name="heart-rate" min="30" max="250">
                                </div>
                                <div class="vital-sign-item">
                                    <label for="respiratory-rate">Frecuencia Respiratoria (rpm):</label>
                                    <input type="number" id="respiratory-rate" name="respiratory-rate" min="5" max="60">
                                </div>
                                <div class="vital-sign-item">
                                    <label for="weight">Peso (kg):</label>
                                    <input type="number" id="weight" name="weight" step="0.1" min="0" max="500">
                                </div>
                                <div class="vital-sign-item">
                                    <label for="height">Altura (cm):</label>
                                    <input type="number" id="height" name="height" min="0" max="300">
                                </div>
                                <div class="vital-sign-item">
                                    <label for="bmi">IMC (kg/m²):</label>
                                    <input type="number" id="bmi" name="bmi" step="0.01" min="0" max="100">
                                </div>
                                <div class="vital-sign-item">
                                    <label for="oxygen-saturation">Saturación de Oxígeno (%):</label>
                                    <input type="number" id="oxygen-saturation" name="oxygen-saturation" min="0" max="100">
                                </div>
                                <div class="vital-sign-item">
                                    <label for="glucose-level">Nivel de Glucosa (mg/dL):</label>
                                    <input type="number" id="glucose-level" name="glucose-level" min="0" max="1000">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="diagnosis">Diagnóstico: <span class="required">*</span></label>
                            <textarea id="diagnosis" name="diagnosis" rows="2" placeholder="Ingrese el diagnóstico del paciente"></textarea>
                            <span class="error-message" id="diagnosis-error"></span>
                            <small class="help-text">* Se requiere al menos Motivo de Consulta o Diagnóstico</small>
                        </div>

                        <div class="form-group">
                            <label for="treatment-plan">Plan de Tratamiento:</label>
                            <textarea id="treatment-plan" name="treatment-plan" rows="3" placeholder="Describa el plan de tratamiento recomendado"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Prescripciones Médicas:</label>
                            <div class="prescriptions-container">
                                <div class="prescription-item">
                                    <div class="prescription-row">
                                        <div class="prescription-field">
                                            <label for="medication-type">Tipo de Medicamento:</label>
                                            <select id="medication-type" name="medication-type">
                                                <option value="">Seleccione un tipo</option>
                                                <!-- Will be populated via JavaScript -->
                                            </select>
                                        </div>
                                        <div class="prescription-field">
                                            <label for="medication">Medicamento:</label>
                                            <select id="medication" name="medication">
                                                <option value="">Seleccione un medicamento</option>
                                                <!-- Will be populated via JavaScript based on selected type -->
                                            </select>
                                        </div>
                                    </div>
                                    <div class="prescription-row">
                                        <div class="prescription-field">
                                            <label for="medication-dose">Dosis:</label>
                                            <input type="text" id="medication-dose" name="medication-dose" placeholder="Ej: 1 tableta">
                                        </div>
                                        <div class="prescription-field">
                                            <label for="medication-frequency">Frecuencia:</label>
                                            <input type="text" id="medication-frequency" name="medication-frequency" placeholder="Ej: Cada 8 horas">
                                        </div>
                                        <div class="prescription-field">
                                            <label for="medication-duration">Duración:</label>
                                            <input type="text" id="medication-duration" name="medication-duration" placeholder="Ej: 7 días">
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="add-prescription-btn" class="add-btn">Agregar Medicamento</button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="observations">Observaciones:</label>
                            <textarea id="observations" name="observations" rows="3" placeholder="Agregue cualquier observación adicional"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Próxima Cita:</label>
                            <div class="next-appointment-container">
                                <a href="/views/appointment/register/register-appoiment.php?patient_id={if isset($patient)}{$patient.id}{/if}" class="appointment-link-btn" target="_blank">Solicitar Cita</a>
                            </div>
                            <small class="help-text">Puede programar una cita directamente haciendo clic en "Solicitar Cita"</small>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="save-btn">Guardar</button>
                            <button type="button" class="cancel-btn" id="cancel-record">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal para subir PDF -->
            <div id="upload-modal" class="modal" style="display: none;">
                <div class="modal-content">
                    <span class="close-upload-modal">&times;</span>
                    <h2>Subir Documento PDF</h2>
                    <form id="upload-form" enctype="multipart/form-data">
                        <input type="hidden" id="upload-patient-id" name="patient-id" value="{if isset($patient)}{$patient.id}{/if}">
                        <!-- Hidden input to store the selected doctor ID from the outside select -->
                        <input type="hidden" id="upload-doctor-id" name="doctor-id" value="">

                        <div class="form-group">
                            <label for="pdf-file">Seleccionar Archivo PDF:</label>
                            <input type="file" id="pdf-file" name="pdf-file" accept=".pdf" required>
                        </div>

                        <div class="form-group">
                            <label for="document-title">Título del Documento:</label>
                            <input type="text" id="document-title" name="document-title" required>
                        </div>

                        <div class="form-group">
                            <label for="document-description">Descripción:</label>
                            <textarea id="document-description" name="document-description" rows="3"></textarea>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="save-btn">Subir</button>
                            <button type="button" class="cancel-btn" id="cancel-upload">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal para agendar estudio -->
            <div id="study-modal" class="modal" style="display: none;">
                <div class="modal-content">
                    <span class="close-study-modal">&times;</span>
                    <h2>Agendar Estudio</h2>
                    <form id="study-form">
                        <input type="hidden" id="study-patient-id" name="patient-id" value="{if isset($patient)}{$patient.id}{/if}">

                        <div class="form-group">
                            <label for="study-patient-display">Paciente:</label>
                            <input type="text" id="study-patient-display" name="patient-display" value="{if isset($patient)}{$patient.names|escape} {$patient.last_name|escape}{/if}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="study-select">Estudio: <span class="required">*</span></label>
                            <select id="study-select" name="study-id">
                                <option value="">Seleccione un estudio</option>
                                <!-- Opciones cargadas vía AJAX -->
                            </select>
                            <span class="error-message" id="study-select-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="study-date">Fecha y Hora: <span class="required">*</span></label>
                            <input type="datetime-local" id="study-date" name="study-date">
                            <span class="error-message" id="study-date-error"></span>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="save-btn">Agendar</button>
                            <button type="button" class="cancel-btn" id="cancel-study">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
