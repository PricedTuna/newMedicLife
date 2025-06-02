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
            </header>

            <section class="patient-search">
                <h2>Buscar Paciente</h2>
                <form id="searchPatientForm" class="search-form">
                    <div class="form-group">
                        <label for="search-term">Buscar por CURP o Nombre:</label>
                        <input type="text" id="search-term" name="search-term" placeholder="Ingrese CURP o nombre del paciente" required>
                    </div>
                    <button type="submit" class="search-btn">Buscar</button>
                </form>
            </section>

            <section id="patient-results" class="patient-results" style="display: none;">
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

            <section id="patient-history" class="patient-history" style="display: none;">
                <div class="patient-info">
                    <h2>Historial Médico de <span id="patient-name"></span></h2>
                    <div class="patient-details">
                        <p><strong>CURP:</strong> <span id="patient-curp"></span></p>
                        <p><strong>Fecha de Nacimiento:</strong> <span id="patient-birth-date"></span></p>
                        <p><strong>Correo Electrónico:</strong> <span id="patient-email"></span></p>
                    </div>
                </div>

                <div class="history-actions">
                    <button id="new-record-btn" class="action-btn">Nueva Entrada</button>
                    <button id="download-pdf-btn" class="action-btn">Descargar PDF</button>
                    <button id="upload-pdf-btn" class="action-btn">Subir PDF</button>
                </div>

                <div id="history-list" class="history-list">
                    <h3>Registros Médicos</h3>
                    <div class="records-container">
                        <!-- Los registros se insertarán aquí -->
                        <p id="no-records-message">No hay registros médicos para este paciente.</p>
                    </div>
                </div>
            </section>

            <!-- Modal para nueva entrada de historial médico -->
            <div id="record-modal" class="modal" style="display: none;">
                <div class="modal-content">
                    <span class="close-modal">&times;</span>
                    <h2>Nueva Entrada de Historial Médico</h2>
                    <form id="record-form">
                        <input type="hidden" id="patient-id" name="patient-id">
                        
                        <div class="form-group">
                            <label for="appointment-select">Cita Relacionada:</label>
                            <select id="appointment-select" name="appointment-id">
                                <option value="">Seleccione una cita</option>
                                <!-- Las citas se cargarán dinámicamente -->
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="record-date">Fecha:</label>
                            <input type="date" id="record-date" name="record-date" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="diagnosis">Diagnóstico:</label>
                            <input type="text" id="diagnosis" name="diagnosis" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="observations">Observaciones:</label>
                            <textarea id="observations" name="observations" rows="5" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="treatment">Tratamiento:</label>
                            <textarea id="treatment" name="treatment" rows="3" required></textarea>
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
                        <input type="hidden" id="upload-patient-id" name="patient-id">
                        
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
        </div>
    </main>
</body>
</html>