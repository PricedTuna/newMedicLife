document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded event triggered');

    // We no longer need to check for doctors and medical areas
    console.log('Medical history app initialized');

    // Load medication types for prescriptions
    loadMedicationTypes();

    // Check if window.isDoctor is defined
    if (typeof window.isDoctor !== 'undefined') {
        console.log('window.isDoctor:', window.isDoctor);
    } else {
        console.error('window.isDoctor is not defined');
    }

    // DOM Elements
    const searchForm = document.getElementById('searchPatientForm');
    console.log('searchForm:', searchForm);

    // Check if searchForm exists
    if (!searchForm) {
        console.error('searchForm not found in the DOM');
    }

    const searchTerm = document.getElementById('search-term');
    const patientResults = document.getElementById('patient-results');
    const patientsList = document.getElementById('patients-list');
    const patientHistory = document.getElementById('patient-history');
    const patientName = document.getElementById('patient-name');
    const patientCurp = document.getElementById('patient-curp');
    const patientBirthDate = document.getElementById('patient-birth-date');
    const patientEmail = document.getElementById('patient-email');

    const newRecordBtn = document.getElementById('new-record-btn');
    console.log('newRecordBtn:', newRecordBtn);

    // Check if newRecordBtn exists
    if (!newRecordBtn) {
        console.error('newRecordBtn not found in the DOM');
    }

    const viewHistoryBtn = document.getElementById('view-history-btn');
    const downloadPdfBtn = document.getElementById('download-pdf-btn');
    const uploadPdfBtn = document.getElementById('upload-pdf-btn');
    const historyList = document.getElementById('history-list');
    const noRecordsMessage = document.getElementById('no-records-message');

    const recordModal = document.getElementById('record-modal');
    console.log('recordModal:', recordModal);

    const closeModal = document.querySelector('.close-modal');

    const recordForm = document.getElementById('record-form');
    console.log('recordForm:', recordForm);

    const patientId = document.getElementById('patient-id');

    // We no longer use appointment selection
    // const appointmentSelect = document.getElementById('appointment-select');
    // console.log('appointmentSelect:', appointmentSelect);

    const cancelRecord = document.getElementById('cancel-record');
    const uploadModal = document.getElementById('upload-modal');
    const closeUploadModal = document.querySelector('.close-upload-modal');
    const uploadForm = document.getElementById('upload-form');
    const uploadPatientId = document.getElementById('upload-patient-id');
    const cancelUpload = document.getElementById('cancel-upload');

    // Study modal elements
    const newStudyBtn = document.getElementById('new-study-btn');
    const studyModal = document.getElementById('study-modal');
    const closeStudyModal = document.querySelector('.close-study-modal');
    const studyForm = document.getElementById('study-form');
    const studyPatientId = document.getElementById('study-patient-id');
    const studyPatientDisplay = document.getElementById('study-patient-display');
    const studySelect = document.getElementById('study-select');
    const studyDate = document.getElementById('study-date');
    const cancelStudy = document.getElementById('cancel-study');

    // Doctor selection elements
    const medicalAreaSelect = document.getElementById('medical-area');
    console.log('medicalAreaSelect:', medicalAreaSelect);

    const doctorIdSelect = document.getElementById('doctor-id');
    console.log('doctorIdSelect:', doctorIdSelect);

    // We'll initialize the doctor dropdown after the filterDoctorsByArea function is defined

    let currentPatientId = null;

    // Check if patient_id is provided in URL
    const urlParams = new URLSearchParams(window.location.search);
    const patientIdParam = urlParams.get('patient_id');

    if (patientIdParam) {
        // Hide search section if patient_id is provided
        const patientSearch = document.querySelector('.patient-search');
        if (patientSearch) {
            patientSearch.style.display = 'none';
        }

        // Load patient history automatically
        loadPatientHistory(patientIdParam);
    }

    // Search for patients
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const term = searchTerm.value.trim();

            if (!term) {
                showAlert('Por favor, ingrese un término de búsqueda', 'error');
                return;
            }

            // AJAX request to search for patients
            fetch('/controllers/doctor/medical_history/search-patients.controller.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `search_term=${encodeURIComponent(term)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayPatientResults(data.patients);
                } else {
                    showAlert(data.message || 'No se encontraron pacientes', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Ocurrió un error al buscar pacientes', 'error');
            });
        });
    } else {
        console.error('searchForm is null, cannot add event listener');
    }

    // Display patient search results
    function displayPatientResults(patients) {
        patientsList.innerHTML = '';

        if (patients.length === 0) {
            patientsList.innerHTML = '<tr><td colspan="4">No se encontraron pacientes</td></tr>';
            patientResults.style.display = 'block';
            return;
        }

        patients.forEach(patient => {
            const row = document.createElement('tr');

            const nameCell = document.createElement('td');
            nameCell.textContent = `${patient.names} ${patient.last_name} ${patient.last_name2}`;

            const curpCell = document.createElement('td');
            curpCell.textContent = patient.curp;

            const birthDateCell = document.createElement('td');
            birthDateCell.textContent = formatDate(patient.birth_date);

            const actionsCell = document.createElement('td');
            const viewBtn = document.createElement('button');
            viewBtn.className = 'view-btn';
            viewBtn.textContent = 'Ver Historial';
            viewBtn.addEventListener('click', () => loadPatientHistory(patient.id));
            actionsCell.appendChild(viewBtn);

            row.appendChild(nameCell);
            row.appendChild(curpCell);
            row.appendChild(birthDateCell);
            row.appendChild(actionsCell);

            patientsList.appendChild(row);
        });

        patientResults.style.display = 'block';
    }

    // Load patient history
    function loadPatientHistory(id) {
        currentPatientId = id;

        // AJAX request to get patient details and history
        fetch(`/controllers/doctor/medical_history/get-patient-history.controller.php?patient_id=${id}`)
        .then(response => {
            // Check if response is ok (status in the range 200-299)
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // Check if response has content
            if (response.headers.get('content-length') === '0') {
                console.error('Empty response from server (content-length: 0)');
                // Return a default response instead of throwing an error
                return {
                    success: false,
                    message: 'Empty response from server',
                    patient: {
                        names: 'Unknown',
                        last_name: 'Patient',
                        last_name2: '',
                        CURP: 'Not available',
                        birth_date: null,
                        email: 'Not available'
                    },
                    history: [],
                    appointments: []
                };
            }

            // Try to parse the response as JSON
            return response.text().then(text => {
                console.log("Response text:", text);

                if (!text) {
                    console.error('Empty response from server');
                    // Return a default response instead of throwing an error
                    return {
                        success: false,
                        message: 'Empty response from server',
                        patient: {
                            names: 'Unknown',
                            last_name: 'Patient',
                            last_name2: '',
                            CURP: 'Not available',
                            birth_date: null,
                            email: 'Not available'
                        },
                        history: [],
                        appointments: []
                    };
                }

                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('JSON parse error:', e);
                    console.error('Response text:', text);

                    // Return a default response instead of throwing an error
                    return {
                        success: false,
                        message: 'Invalid JSON response from server',
                        patient: {
                            names: 'Unknown',
                            last_name: 'Patient',
                            last_name2: '',
                            CURP: 'Not available',
                            birth_date: null,
                            email: 'Not available'
                        },
                        history: [],
                        appointments: []
                    };
                }
            });
        })
        .then(data => {
            if (data.success) {
                displayPatientHistory(data.patient, data.history, data.appointments);
            } else {
                // Handle specific error types
                if (data.table_error) {
                    // Show error message with option to create tables
                    Swal.fire({
                        title: 'Error de base de datos',
                        text: data.message || 'Las tablas necesarias no existen en la base de datos.',
                        icon: 'error',
                        showCancelButton: true,
                        confirmButtonText: 'Crear Tablas',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Open the create tables script in a new tab
                            window.open('/create-medical-history-tables.php', '_blank');
                        }
                    });
                } else {
                    showAlert(data.message || 'No se pudo cargar el historial del paciente', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);

            // Log more detailed error information
            console.error('Error details:', {
                message: error.message,
                stack: error.stack
            });

            // Show a more user-friendly error message
            showAlert('Ocurrió un error al cargar el historial del paciente. Por favor, intente nuevamente.', 'error');

            // Show patient history section with error message
            patientResults.style.display = 'none';
            patientHistory.style.display = 'block';

            // Display default patient info
            patientName.textContent = 'Paciente';
            patientCurp.textContent = 'No disponible';
            patientBirthDate.textContent = 'No disponible';
            patientEmail.textContent = 'No disponible';

            // Make sure patient info is visible with a background color
            document.querySelector('.patient-details').style.backgroundColor = '#e6f7ff';

            // Clear any existing content
            const recordsContainer = document.querySelector('.records-container');
            recordsContainer.innerHTML = '';

            // Check if noRecordsMessage is a valid DOM node
            if (noRecordsMessage && noRecordsMessage.nodeType === Node.ELEMENT_NODE) {
                recordsContainer.appendChild(noRecordsMessage);
            } else {
                // Create a new message element if noRecordsMessage is not valid
                const message = document.createElement('p');
                message.id = 'no-records-message';
                message.textContent = 'No hay registros médicos para este paciente.';
                recordsContainer.appendChild(message);
            }
        });
    }

    // Display patient history
    function displayPatientHistory(patient, history, appointments) {
        console.log("Patient data received:", patient);

        // Ensure patient object exists
        if (!patient) {
            console.error("No patient data received");
            patient = {
                names: 'Unknown',
                last_name: 'Patient',
                last_name2: '',
                CURP: 'Not available',
                birth_date: null,
                email: 'Not available'
            };
        }

        // Ensure history array exists
        if (!history) {
            console.error("No history data received");
            history = [];
        }

        // Ensure appointments array exists
        if (!appointments) {
            console.error("No appointments data received");
            appointments = [];
        }

        // Set patient details
        patientName.textContent = `${patient.names || ''} ${patient.last_name || ''} ${patient.last_name2 || ''}`;

        // Make sure patient info is visible with a background color
        document.querySelector('.patient-details').style.backgroundColor = '#e6f7ff';

        // Set CURP with fallback - check both lowercase and uppercase property names
        if (patient.CURP) {
            patientCurp.textContent = patient.CURP;
        } else if (patient.curp) {
            patientCurp.textContent = patient.curp;
        } else {
            patientCurp.textContent = 'No disponible';
            patientCurp.style.color = 'red';
        }

        // Set birth date with fallback
        if (patient.birth_date) {
            patientBirthDate.textContent = formatDate(patient.birth_date);
        } else {
            patientBirthDate.textContent = 'No disponible';
            patientBirthDate.style.color = 'red';
        }

        // Set email with fallback
        patientEmail.textContent = patient.email || 'No disponible';
        if (!patient.email) {
            patientEmail.style.color = 'red';
        }

        // Log the patient data for debugging
        console.log("CURP:", patient.CURP || patient.curp || 'Not found');
        console.log("Email:", patient.email || 'Not found');
        console.log("Birth date:", patient.birth_date || 'Not found');

        // We no longer populate appointments dropdown
        console.log("Appointments data:", appointments); // Debug log

        // Display history records
        const recordsContainer = document.querySelector('.records-container');
        recordsContainer.innerHTML = '';

        if (history.length === 0) {
            // Check if noRecordsMessage is a valid DOM node
            if (noRecordsMessage && noRecordsMessage.nodeType === Node.ELEMENT_NODE) {
                recordsContainer.appendChild(noRecordsMessage);
            } else {
                // Create a new message element if noRecordsMessage is not valid
                const message = document.createElement('p');
                message.id = 'no-records-message';
                message.textContent = 'No hay registros médicos para este paciente.';
                recordsContainer.appendChild(message);
            }
        } else {
            history.forEach(record => {
                const recordCard = createRecordCard(record);
                recordsContainer.appendChild(recordCard);
            });
        }

        // Show patient history section
        patientResults.style.display = 'none';
        patientHistory.style.display = 'block';
        historyList.style.display = 'block'; // Show history list by default

        // Set patient ID for forms
        patientId.value = patient.id;
        uploadPatientId.value = patient.id;

        // Load patient studies into the right panel
        console.log('Loading patient studies for patient id:', patient.id);
        loadPatientStudies(patient.id);
    }

    // Load patient studies (patient_studies table)
    function loadPatientStudies(patientIdParam) {
        if (!patientIdParam) {
            console.warn('No patientId provided to loadPatientStudies');
            return;
        }

        fetch(`/controllers/doctor/medical_history/get-patient-studies.controller.php?patient_id=${patientIdParam}`)
            .then(resp => resp.json())
            .then(data => {
                console.log('get-patient-studies response:', data);
                if (data.success) {
                    renderPatientStudies(data.studies || []);
                } else {
                    console.error('Error cargando patient studies:', data.message);
                    const container = document.getElementById('patient-studies-list');
                    if (container) container.innerHTML = '<p class="muted">Error al cargar estudios.</p>';
                }
            })
            .catch(err => {
                console.error('Error fetching patient studies:', err);
                const container = document.getElementById('patient-studies-list');
                if (container) container.innerHTML = '<p class="muted">Error al cargar estudios.</p>';
            });
    }

    function renderPatientStudies(studies) {
        const container = document.getElementById('patient-studies-list');
        if (!container) return;
        container.innerHTML = '';

        if (!studies || studies.length === 0) {
            container.innerHTML = '<p class="muted">No hay estudios solicitados.</p>';
            return;
        }

        // Filtrar estudios cancelados para que no aparezcan en el listado (se eliminan al cancelar)
        const visibleStudies = studies.filter(st => (st.status || '').toLowerCase() !== 'cancelado');
        if (visibleStudies.length === 0) {
            container.innerHTML = '<p class="muted">No hay estudios solicitados.</p>';
            return;
        }

        visibleStudies.forEach(s => {
            const card = document.createElement('div');
            card.className = 'study-card';
            card.style = 'border:1px solid #ddd; padding:10px; margin-bottom:8px; border-radius:6px; background:#fff;';

            const title = document.createElement('div');
            title.innerHTML = `<strong>${s.study_name || 'Estudio'}</strong>`;

            const date = document.createElement('div');
            // Normalize datetime string to ISO for Date parsing
            let sched = s.scheduled_at ? s.scheduled_at.replace(' ', 'T') : null;
            const dtObj = sched ? new Date(sched) : null;
            const dateText = sched && !isNaN(dtObj) ? formatDate(s.scheduled_at) + ' ' + dtObj.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : 'Fecha no disponible';
            date.textContent = dateText;
            date.style = 'font-size:0.95em; color:#333; margin-top:6px;';

            const status = document.createElement('div');
            status.textContent = 'Estado: ' + (s.status || 'Pendiente');
            status.style = 'font-size:0.85em; color:#666; margin-top:6px;';

            const actions = document.createElement('div');
            actions.style = 'margin-top:8px; display:flex; gap:8px;';

            const btnCancel = document.createElement('button');
            btnCancel.className = 'cancel-btn small';
            // misma tipografía/estilo; texto en mayúsculas
            btnCancel.classList.add('study-action');
            btnCancel.textContent = 'Cancelar'.toUpperCase();
            btnCancel.addEventListener('click', () => handleCancelStudy(s.id));

            const btnEdit = document.createElement('button');
            // Botón MODIFICAR debe ser azul (action-btn)
            btnEdit.className = 'action-btn small';
            // misma tipografía/estilo; texto en mayúsculas
            btnEdit.classList.add('study-action');
            btnEdit.textContent = 'Modificar'.toUpperCase();
            btnEdit.addEventListener('click', () => handleEditStudy(s));

            actions.appendChild(btnEdit);
            actions.appendChild(btnCancel);

            card.appendChild(title);
            card.appendChild(date);
            card.appendChild(status);
            card.appendChild(actions);

            container.appendChild(card);
        });
    }

    function handleCancelStudy(studyId) {
        if (!studyId) return;
        Swal.fire({
            title: 'Confirmar cancelación',
            text: '¿Desea cancelar este estudio?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'No'
        }).then(result => {
            if (result.isConfirmed) {
                const payload = new URLSearchParams();
                payload.append('action', 'cancel');
                payload.append('id', studyId);

                fetch('/controllers/doctor/medical_history/update-patient-study.controller.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: payload.toString()
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message || 'Estudio cancelado', 'success');
                        // refresh list
                        const pid = patientId ? patientId.value : window.currentPatientId;
                        loadPatientStudies(pid);
                    } else showAlert(data.message || 'No se pudo cancelar', 'error');
                })
                .catch(err => {
                    console.error('Error cancelling study:', err);
                    showAlert('Ocurrió un error', 'error');
                });
            }
        });
    }

    function handleEditStudy(study) {
        if (!study) return;
        // Usar Swal para pedir nueva fecha/hora
        Swal.fire({
            title: 'Reprogramar Estudio',
            html: `<input type="datetime-local" id="swal-dt" class="swal2-input" value="${(study.scheduled_at||'').replace(' ', 'T').slice(0,16)}">`,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Reprogramar',
            preConfirm: () => {
                const val = document.getElementById('swal-dt').value;
                if (!val) {
                    Swal.showValidationMessage('Ingrese una fecha y hora');
                    return false;
                }
                // Validación cliente: al reprogramar debe ser al menos 10 minutos en el futuro
                // val is 'YYYY-MM-DDTHH:MM' (local time from datetime-local input)
                // Parse as local time: create a Date and adjust by timezone offset to get correct local instant
                const parts = val.split('T');
                const [year, month, day] = parts[0].split('-');
                const [hours, minutes] = parts[1].split(':');
                const selected = new Date(year, month - 1, day, hours, minutes, 0, 0);
                const minDate = new Date(Date.now() + 10 * 60 * 1000);
                if (selected.getTime() < minDate.getTime()) {
                    Swal.showValidationMessage('La fecha y hora deben ser al menos 10 minutos en el futuro');
                    return false;
                }
                return val;
            }
        }).then(result => {
            if (result.isConfirmed && result.value) {
                const local = result.value; // 'YYYY-MM-DDTHH:MM'
                // send local datetime and client tz offset to server to avoid timezone mismatches
                // local is 'YYYY-MM-DDTHH:MM' from the input
                const scheduledAt = local.replace('T', ' ') + ':00';
                const clientOffset = String(new Date().getTimezoneOffset());
                const payload = new URLSearchParams();
                payload.append('action', 'reschedule');
                payload.append('id', study.id);
                payload.append('scheduled_at', scheduledAt);
                payload.append('client_tz_offset', clientOffset);

                fetch('/controllers/doctor/medical_history/update-patient-study.controller.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: payload.toString()
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message || 'Estudio reprogramado', 'success');
                        const pid = patientId ? patientId.value : window.currentPatientId;
                        loadPatientStudies(pid);
                    } else {
                        showAlert(data.message || 'No se pudo reprogramar', 'error');
                    }
                })
                .catch(err => {
                    console.error('Error reprogramming:', err);
                    showAlert('Ocurrió un error', 'error');
                });
            }
        });
    }

    // Create a record card element
    function createRecordCard(record) {
        const card = document.createElement('div');
        card.className = 'record-card';

        const header = document.createElement('div');
        header.className = 'record-header';

        const title = document.createElement('div');
        title.className = 'record-title';
        title.textContent = record.diagnosis || record.chief_complaint || 'Registro médico';

        const date = document.createElement('div');
        date.className = 'record-date';
        date.textContent = formatDate(record.date_created || record.record_date);

        header.appendChild(title);
        header.appendChild(date);

        const content = document.createElement('div');
        content.className = 'record-content';

        // Add doctor information
        const doctor = document.createElement('p');
        doctor.innerHTML = `<strong>Doctor:</strong> ${record.doctor_names} ${record.doctor_last_name} ${record.doctor_last_name2 || ''}`;
        content.appendChild(doctor);

        // Add all available fields
        if (record.chief_complaint) {
            const chiefComplaint = document.createElement('p');
            chiefComplaint.innerHTML = `<strong>Motivo de Consulta:</strong> ${record.chief_complaint}`;
            content.appendChild(chiefComplaint);
        }

        if (record.current_illness) {
            const currentIllness = document.createElement('p');
            currentIllness.innerHTML = `<strong>Enfermedad Actual:</strong> ${record.current_illness}`;
            content.appendChild(currentIllness);
        }

        if (record.personal_history) {
            const personalHistory = document.createElement('p');
            personalHistory.innerHTML = `<strong>Antecedentes Personales:</strong> ${record.personal_history}`;
            content.appendChild(personalHistory);
        }

        if (record.family_history) {
            const familyHistory = document.createElement('p');
            familyHistory.innerHTML = `<strong>Antecedentes Familiares:</strong> ${record.family_history}`;
            content.appendChild(familyHistory);
        }

        if (record.physical_examination) {
            const physicalExam = document.createElement('p');
            physicalExam.innerHTML = `<strong>Examen Físico:</strong> ${record.physical_examination}`;
            content.appendChild(physicalExam);
        }

        if (record.diagnosis) {
            const diagnosis = document.createElement('p');
            diagnosis.innerHTML = `<strong>Diagnóstico:</strong> ${record.diagnosis}`;
            content.appendChild(diagnosis);
        }

        if (record.treatment_plan) {
            const treatmentPlan = document.createElement('p');
            treatmentPlan.innerHTML = `<strong>Plan de Tratamiento:</strong> ${record.treatment_plan}`;
            content.appendChild(treatmentPlan);
        } else if (record.treatment) {
            // For backward compatibility
            const treatment = document.createElement('p');
            treatment.innerHTML = `<strong>Tratamiento:</strong> ${record.treatment}`;
            content.appendChild(treatment);
        }

        if (record.observations) {
            const observations = document.createElement('p');
            observations.innerHTML = `<strong>Observaciones:</strong> ${record.observations}`;
            content.appendChild(observations);
        }

        if (record.next_appointment) {
            const nextAppointment = document.createElement('p');
            nextAppointment.innerHTML = `<strong>Próxima Cita:</strong> ${formatDate(record.next_appointment)}`;
            content.appendChild(nextAppointment);
        }

        // Add vital signs if available
        if (record.vital_signs_data && record.vital_signs_data.length > 0) {
            const vitalSigns = record.vital_signs_data[0];
            const vitalSignsContainer = document.createElement('div');
            vitalSignsContainer.className = 'vital-signs';

            const vitalSignsTitle = document.createElement('h3');
            vitalSignsTitle.textContent = 'Signos Vitales';
            vitalSignsContainer.appendChild(vitalSignsTitle);

            const vitalSignsTable = document.createElement('table');
            vitalSignsTable.innerHTML = `
                <tr>
                    <th>Parámetro</th>
                    <th>Valor</th>
                </tr>
            `;

            if (vitalSigns.temperature) {
                vitalSignsTable.innerHTML += `
                    <tr>
                        <td>Temperatura</td>
                        <td>${vitalSigns.temperature} °C</td>
                    </tr>
                `;
            }

            if (vitalSigns.blood_pressure) {
                vitalSignsTable.innerHTML += `
                    <tr>
                        <td>Presión Arterial</td>
                        <td>${vitalSigns.blood_pressure} mmHg</td>
                    </tr>
                `;
            }

            if (vitalSigns.heart_rate) {
                vitalSignsTable.innerHTML += `
                    <tr>
                        <td>Frecuencia Cardíaca</td>
                        <td>${vitalSigns.heart_rate} lpm</td>
                    </tr>
                `;
            }

            if (vitalSigns.respiratory_rate) {
                vitalSignsTable.innerHTML += `
                    <tr>
                        <td>Frecuencia Respiratoria</td>
                        <td>${vitalSigns.respiratory_rate} rpm</td>
                    </tr>
                `;
            }

            if (vitalSigns.weight) {
                vitalSignsTable.innerHTML += `
                    <tr>
                        <td>Peso</td>
                        <td>${vitalSigns.weight} kg</td>
                    </tr>
                `;
            }

            if (vitalSigns.height) {
                vitalSignsTable.innerHTML += `
                    <tr>
                        <td>Altura</td>
                        <td>${vitalSigns.height} cm</td>
                    </tr>
                `;
            }

            if (vitalSigns.bmi) {
                vitalSignsTable.innerHTML += `
                    <tr>
                        <td>IMC</td>
                        <td>${vitalSigns.bmi} kg/m²</td>
                    </tr>
                `;
            }

            if (vitalSigns.oxygen_saturation) {
                vitalSignsTable.innerHTML += `
                    <tr>
                        <td>Saturación de Oxígeno</td>
                        <td>${vitalSigns.oxygen_saturation} %</td>
                    </tr>
                `;
            }

            if (vitalSigns.glucose_level) {
                vitalSignsTable.innerHTML += `
                    <tr>
                        <td>Nivel de Glucosa</td>
                        <td>${vitalSigns.glucose_level} mg/dL</td>
                    </tr>
                `;
            }

            vitalSignsContainer.appendChild(vitalSignsTable);
            content.appendChild(vitalSignsContainer);
        }

        const actions = document.createElement('div');
        actions.className = 'record-actions';

        const downloadBtn = document.createElement('button');
        downloadBtn.className = 'download-pdf-btn';
        downloadBtn.textContent = 'Descargar PDF';
        downloadBtn.addEventListener('click', () => downloadRecordPdf(record.id));

        const downloadMedicalHistoryBtn = document.createElement('button');
        downloadMedicalHistoryBtn.className = 'download-medical-history-btn';
        downloadMedicalHistoryBtn.textContent = 'Descargar Historial Médico';
        downloadMedicalHistoryBtn.addEventListener('click', () => downloadMedicalHistoryRecordPdf(record.id));

        actions.appendChild(downloadBtn);
        actions.appendChild(downloadMedicalHistoryBtn);

        card.appendChild(header);
        card.appendChild(content);
        card.appendChild(actions);

        return card;
    }

    // We no longer need the doctor selection logic since we're using the current doctor ID from the session

    // New record button click
    if (newRecordBtn) {
        newRecordBtn.addEventListener('click', function() {
            console.log('newRecordBtn click event triggered');

            // Check if recordForm exists
            if (!recordForm) {
                console.error('recordForm not found in the DOM');
                showAlert('Error: No se pudo encontrar el formulario', 'error');
                return;
            }

            // Reset form
            recordForm.reset();
            console.log('Form reset');

            // Reset validation styles
            const invalidInputs = recordForm.querySelectorAll('.invalid');
            invalidInputs.forEach(input => input.classList.remove('invalid'));

            const errorMessages = recordForm.querySelectorAll('.error-message');
            errorMessages.forEach(msg => msg.textContent = '');

            // Set current date as default
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('record-date').value = today;
            console.log('Default date set:', today);

            // Set patient ID from currentPatientId
            if (currentPatientId) {
                document.getElementById('patient-id').value = currentPatientId;
                console.log('Patient ID set:', currentPatientId);
            } else {
                console.log('No currentPatientId available');
            }

            // Set the doctor ID from the session
            const formDoctorId = document.getElementById('form-doctor-id');
            if (formDoctorId) {
                // Use the doctorId from the PHP session
                if (typeof window.doctorId !== 'undefined' && window.doctorId) {
                    formDoctorId.value = window.doctorId;
                    console.log('Set form-doctor-id value from session:', formDoctorId.value);
                } else {
                    console.log('No doctorId available from session');
                }
            }

            // Set the upload doctor ID as well
            const uploadDoctorId = document.getElementById('upload-doctor-id');
            if (uploadDoctorId) {
                if (typeof window.doctorId !== 'undefined' && window.doctorId) {
                    uploadDoctorId.value = window.doctorId;
                    console.log('Set upload-doctor-id value from session:', uploadDoctorId.value);
                }
            }

            // Set default values for vital signs
            // Default temperature (36.5-37.5°C is normal)
            const temperature = document.getElementById('temperature');
            if (temperature) {
                temperature.value = '36.5';
            }

            // Default blood pressure (120/80 mmHg is normal)
            const bloodPressure = document.getElementById('blood-pressure');
            if (bloodPressure) {
                bloodPressure.value = '120/80';
            }

            // Default heart rate (60-100 bpm is normal)
            const heartRate = document.getElementById('heart-rate');
            if (heartRate) {
                heartRate.value = '80';
            }

            // Default respiratory rate (12-20 breaths per minute is normal)
            const respiratoryRate = document.getElementById('respiratory-rate');
            if (respiratoryRate) {
                respiratoryRate.value = '16';
            }

            // Default oxygen saturation (95-100% is normal)
            const oxygenSaturation = document.getElementById('oxygen-saturation');
            if (oxygenSaturation) {
                oxygenSaturation.value = '98';
            }

            console.log('Default vital signs set');

            // Show modal
            if (recordModal) {
                recordModal.style.display = 'flex';
            } else {
                console.error('recordModal not found in the DOM');
                showAlert('Error: No se pudo encontrar el modal del formulario', 'error');
                return;
            }

            // Hide history list
            if (historyList) {
                historyList.style.display = 'none';
            } else {
                console.error('historyList not found in the DOM');
            }

            // Validate the form initially to provide immediate feedback
            validateDoctor();
            validateDate();
            validateComplaintAndDiagnosis();
        });
    } else {
        console.error('newRecordBtn is null, cannot add event listener');
    }

    // Add real-time validation to required fields
    const recordDate = document.getElementById('record-date');
    const chiefComplaint = document.getElementById('chief-complaint');
    const diagnosis = document.getElementById('diagnosis');
    const doctorId = document.getElementById('doctor-id');

    // Vital signs fields for validation
    const temperature = document.getElementById('temperature');
    const bloodPressure = document.getElementById('blood-pressure');
    const heartRate = document.getElementById('heart-rate');
    const respiratoryRate = document.getElementById('respiratory-rate');
    const weight = document.getElementById('weight');
    const height = document.getElementById('height');
    const bmi = document.getElementById('bmi');
    const oxygenSaturation = document.getElementById('oxygen-saturation');
    const glucoseLevel = document.getElementById('glucose-level');

    // We no longer need to validate doctor selection since we're using the current doctor ID from the session
    function validateDoctor() {
        return true;
    }

    // Validate date field on input and change
    function validateDate() {
        if (!recordDate) {
            console.error('recordDate not found in the DOM');
            return false;
        }

        if (!recordDate.value) {
            recordDate.classList.add('invalid');
            const errorElement = document.getElementById('record-date-error');
            if (errorElement) {
                errorElement.textContent = 'La fecha es obligatoria';
            }
            return false;
        } else {
            recordDate.classList.remove('invalid');
            const errorElement = document.getElementById('record-date-error');
            if (errorElement) {
                errorElement.textContent = '';
            }
            return true;
        }
    }

    if (recordDate) {
        recordDate.addEventListener('input', validateDate);
        recordDate.addEventListener('change', validateDate);
        recordDate.addEventListener('blur', validateDate);
    }

    // Validate chief complaint and diagnosis fields on input
    function validateComplaintAndDiagnosis() {
        if (!chiefComplaint || !diagnosis) {
            console.error('chiefComplaint or diagnosis not found in the DOM');
            return false;
        }

        if (!chiefComplaint.value && !diagnosis.value) {
            chiefComplaint.classList.add('invalid');
            diagnosis.classList.add('invalid');
            const chiefComplaintError = document.getElementById('chief-complaint-error');
            const diagnosisError = document.getElementById('diagnosis-error');

            if (chiefComplaintError) {
                chiefComplaintError.textContent = 'Debe completar al menos uno de estos campos';
            }

            if (diagnosisError) {
                diagnosisError.textContent = 'Debe completar al menos uno de estos campos';
            }

            return false;
        } else {
            chiefComplaint.classList.remove('invalid');
            diagnosis.classList.remove('invalid');

            const chiefComplaintError = document.getElementById('chief-complaint-error');
            const diagnosisError = document.getElementById('diagnosis-error');

            if (chiefComplaintError) {
                chiefComplaintError.textContent = '';
            }

            if (diagnosisError) {
                diagnosisError.textContent = '';
            }

            return true;
        }
    }

    if (chiefComplaint && diagnosis) {
        chiefComplaint.addEventListener('input', validateComplaintAndDiagnosis);
        chiefComplaint.addEventListener('blur', validateComplaintAndDiagnosis);
        diagnosis.addEventListener('input', validateComplaintAndDiagnosis);
        diagnosis.addEventListener('blur', validateComplaintAndDiagnosis);
    }

    // Validate vital signs in real-time

    // Validate temperature (normal range: 35-42°C)
    function validateTemperature() {
        if (!temperature) return true;

        const value = parseFloat(temperature.value);
        if (temperature.value && (isNaN(value) || value < 35 || value > 42)) {
            temperature.classList.add('invalid');
            return false;
        } else {
            temperature.classList.remove('invalid');
            return true;
        }
    }

    // Validate blood pressure (format: systolic/diastolic, e.g., 120/80)
    function validateBloodPressure() {
        if (!bloodPressure) return true;

        if (bloodPressure.value) {
            const pattern = /^\d{2,3}\/\d{2,3}$/;
            if (!pattern.test(bloodPressure.value)) {
                bloodPressure.classList.add('invalid');
                return false;
            } else {
                bloodPressure.classList.remove('invalid');
                return true;
            }
        }
        return true;
    }

    // Validate heart rate (normal range: 40-200 bpm)
    function validateHeartRate() {
        if (!heartRate) return true;

        const value = parseInt(heartRate.value);
        if (heartRate.value && (isNaN(value) || value < 40 || value > 200)) {
            heartRate.classList.add('invalid');
            return false;
        } else {
            heartRate.classList.remove('invalid');
            return true;
        }
    }

    // Validate respiratory rate (normal range: 8-40 breaths per minute)
    function validateRespiratoryRate() {
        if (!respiratoryRate) return true;

        const value = parseInt(respiratoryRate.value);
        if (respiratoryRate.value && (isNaN(value) || value < 8 || value > 40)) {
            respiratoryRate.classList.add('invalid');
            return false;
        } else {
            respiratoryRate.classList.remove('invalid');
            return true;
        }
    }

    // Validate oxygen saturation (normal range: 80-100%)
    function validateOxygenSaturation() {
        if (!oxygenSaturation) return true;

        const value = parseInt(oxygenSaturation.value);
        if (oxygenSaturation.value && (isNaN(value) || value < 80 || value > 100)) {
            oxygenSaturation.classList.add('invalid');
            return false;
        } else {
            oxygenSaturation.classList.remove('invalid');
            return true;
        }
    }

    // Calculate BMI when weight or height changes
    function calculateBMI() {
        if (!weight || !height || !bmi) return;

        const weightValue = parseFloat(weight.value);
        const heightValue = parseFloat(height.value);

        if (!isNaN(weightValue) && !isNaN(heightValue) && heightValue > 0) {
            // Convert height from cm to m
            const heightInMeters = heightValue / 100;
            // Calculate BMI: weight (kg) / height² (m²)
            const bmiValue = (weightValue / (heightInMeters * heightInMeters)).toFixed(2);
            bmi.value = bmiValue;
        }
    }

    // Add event listeners for vital signs validation
    if (temperature) {
        temperature.addEventListener('input', validateTemperature);
        temperature.addEventListener('blur', validateTemperature);
    }

    if (bloodPressure) {
        bloodPressure.addEventListener('input', validateBloodPressure);
        bloodPressure.addEventListener('blur', validateBloodPressure);
    }

    if (heartRate) {
        heartRate.addEventListener('input', validateHeartRate);
        heartRate.addEventListener('blur', validateHeartRate);
    }

    if (respiratoryRate) {
        respiratoryRate.addEventListener('input', validateRespiratoryRate);
        respiratoryRate.addEventListener('blur', validateRespiratoryRate);
    }

    if (oxygenSaturation) {
        oxygenSaturation.addEventListener('input', validateOxygenSaturation);
        oxygenSaturation.addEventListener('blur', validateOxygenSaturation);
    }

    // Add event listeners for BMI calculation
    if (weight) {
        weight.addEventListener('input', calculateBMI);
        weight.addEventListener('blur', calculateBMI);
    }

    if (height) {
        height.addEventListener('input', calculateBMI);
        height.addEventListener('blur', calculateBMI);
    }

    // Medication types and medications functions
    function loadMedicationTypes() {
        // Fetch medication types from the server
        fetch('/controllers/doctor/medical_history/get-medication-types.controller.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    populateMedicationTypes(data.types);
                } else {
                    console.error('Error loading medication types:', data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching medication types:', error);
            });
    }

    function populateMedicationTypes(types) {
        const medicationTypeSelect = document.getElementById('medication-type');
        if (!medicationTypeSelect) {
            console.error('Medication type select not found');
            return;
        }

        // Clear existing options except the first one
        while (medicationTypeSelect.options.length > 1) {
            medicationTypeSelect.remove(1);
        }

        // Add new options
        types.forEach(type => {
            const option = document.createElement('option');
            option.value = type.id;
            option.textContent = type.name;
            medicationTypeSelect.appendChild(option);
        });

        // Add event listener to load medications when type changes
        medicationTypeSelect.addEventListener('change', function() {
            const typeId = this.value;
            if (typeId) {
                loadMedications(typeId);
            } else {
                // Clear medications dropdown if no type is selected
                const medicationSelect = document.getElementById('medication');
                if (medicationSelect) {
                    while (medicationSelect.options.length > 1) {
                        medicationSelect.remove(1);
                    }
                }
            }
        });
    }

    function loadMedications(typeId) {
        // Fetch medications for the selected type
        fetch(`/controllers/doctor/medical_history/get-medications.controller.php?type_id=${typeId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    populateMedications(data.medications);
                } else {
                    console.error('Error loading medications:', data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching medications:', error);
            });
    }

    function populateMedications(medications) {
        const medicationSelect = document.getElementById('medication');
        if (!medicationSelect) {
            console.error('Medication select not found');
            return;
        }

        // Clear existing options except the first one
        while (medicationSelect.options.length > 1) {
            medicationSelect.remove(1);
        }

        // Add new options
        medications.forEach(medication => {
            const option = document.createElement('option');
            option.value = medication.id;
            option.textContent = medication.name;
            medicationSelect.appendChild(option);
        });
    }

    // Add prescription button click
    const addPrescriptionBtn = document.getElementById('add-prescription-btn');
    if (addPrescriptionBtn) {
        addPrescriptionBtn.addEventListener('click', function() {
            addPrescription();
        });
    }

    function addPrescription() {
        const prescriptionsContainer = document.querySelector('.prescriptions-container');
        if (!prescriptionsContainer) {
            console.error('Prescriptions container not found');
            return;
        }

        // Create a new prescription item
        const prescriptionItem = document.createElement('div');
        prescriptionItem.className = 'prescription-item';

        // Generate a unique ID for the new prescription fields
        const prescriptionId = Date.now();

        prescriptionItem.innerHTML = `
            <div class="prescription-row">
                <div class="prescription-field">
                    <label for="medication-type-${prescriptionId}">Tipo de Medicamento:</label>
                    <select id="medication-type-${prescriptionId}" name="medication-type-${prescriptionId}" class="medication-type">
                        <option value="">Seleccione un tipo</option>
                        <!-- Will be populated via JavaScript -->
                    </select>
                </div>
                <div class="prescription-field">
                    <label for="medication-${prescriptionId}">Medicamento:</label>
                    <select id="medication-${prescriptionId}" name="medication-${prescriptionId}" class="medication">
                        <option value="">Seleccione un medicamento</option>
                        <!-- Will be populated via JavaScript based on selected type -->
                    </select>
                </div>
            </div>
            <div class="prescription-row">
                <div class="prescription-field">
                    <label for="medication-dose-${prescriptionId}">Dosis:</label>
                    <input type="text" id="medication-dose-${prescriptionId}" name="medication-dose-${prescriptionId}" placeholder="Ej: 1 tableta" class="medication-dose">
                </div>
                <div class="prescription-field">
                    <label for="medication-frequency-${prescriptionId}">Frecuencia:</label>
                    <input type="text" id="medication-frequency-${prescriptionId}" name="medication-frequency-${prescriptionId}" placeholder="Ej: Cada 8 horas" class="medication-frequency">
                </div>
                <div class="prescription-field">
                    <label for="medication-duration-${prescriptionId}">Duración:</label>
                    <input type="text" id="medication-duration-${prescriptionId}" name="medication-duration-${prescriptionId}" placeholder="Ej: 7 días" class="medication-duration">
                </div>
            </div>
            <button type="button" class="remove-prescription-btn">Eliminar Medicamento</button>
        `;

        // Insert the new prescription item before the add button
        prescriptionsContainer.insertBefore(prescriptionItem, addPrescriptionBtn);

        // Add event listener to the remove button
        const removeBtn = prescriptionItem.querySelector('.remove-prescription-btn');
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                removePrescription(prescriptionItem);
            });
        }

        // Populate the medication type dropdown
        const medicationTypeSelect = prescriptionItem.querySelector('.medication-type');
        if (medicationTypeSelect) {
            // Copy options from the first medication type dropdown
            const originalSelect = document.getElementById('medication-type');
            if (originalSelect) {
                Array.from(originalSelect.options).forEach(option => {
                    const newOption = document.createElement('option');
                    newOption.value = option.value;
                    newOption.textContent = option.textContent;
                    medicationTypeSelect.appendChild(newOption);
                });
            }

            // Add event listener to load medications when type changes
            medicationTypeSelect.addEventListener('change', function() {
                const typeId = this.value;
                if (typeId) {
                    const medicationSelect = prescriptionItem.querySelector('.medication');
                    loadMedicationsForSelect(typeId, medicationSelect);
                } else {
                    // Clear medications dropdown if no type is selected
                    const medicationSelect = prescriptionItem.querySelector('.medication');
                    if (medicationSelect) {
                        while (medicationSelect.options.length > 1) {
                            medicationSelect.remove(1);
                        }
                    }
                }
            });
        }
    }

    function loadMedicationsForSelect(typeId, medicationSelect) {
        if (!medicationSelect) {
            console.error('Medication select not found');
            return;
        }

        // Fetch medications for the selected type
        fetch(`/controllers/doctor/medical_history/get-medications.controller.php?type_id=${typeId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear existing options except the first one
                    while (medicationSelect.options.length > 1) {
                        medicationSelect.remove(1);
                    }

                    // Add new options
                    data.medications.forEach(medication => {
                        const option = document.createElement('option');
                        option.value = medication.id;
                        option.textContent = medication.name;
                        medicationSelect.appendChild(option);
                    });
                } else {
                    console.error('Error loading medications:', data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching medications:', error);
            });
    }

    function removePrescription(prescriptionItem) {
        if (prescriptionItem && prescriptionItem.parentNode) {
            prescriptionItem.parentNode.removeChild(prescriptionItem);
        }
    }

    function collectPrescriptionData() {
        const prescriptions = [];
        const prescriptionItems = document.querySelectorAll('.prescription-item');

        prescriptionItems.forEach(item => {
            const medicationTypeSelect = item.querySelector('.medication-type');
            const medicationSelect = item.querySelector('.medication');
            const doseInput = item.querySelector('.medication-dose');
            const frequencyInput = item.querySelector('.medication-frequency');
            const durationInput = item.querySelector('.medication-duration');

            if (medicationSelect && medicationSelect.value) {
                const prescription = {
                    medication_id: medicationSelect.value,
                    medication_type_id: medicationTypeSelect ? medicationTypeSelect.value : '',
                    dose: doseInput ? doseInput.value : '',
                    frequency: frequencyInput ? frequencyInput.value : '',
                    duration: durationInput ? durationInput.value : ''
                };

                prescriptions.push(prescription);
            }
        });

        return prescriptions;
    }

    // Close record modal
    if (closeModal) {
        closeModal.addEventListener('click', function() {
            if (recordModal) {
                recordModal.style.display = 'none';
            } else {
                console.error('recordModal not found in the DOM');
            }
        });
    } else {
        console.error('closeModal not found in the DOM');
    }

    // Cancel record button
    if (cancelRecord) {
        cancelRecord.addEventListener('click', function() {
            if (recordModal) {
                recordModal.style.display = 'none';
            } else {
                console.error('recordModal not found in the DOM');
            }
        });
    } else {
        console.error('cancelRecord not found in the DOM');
    }

    // Validate form fields
    function validateForm() {
        let isValid = true;

        // Validate patient ID (required)
        const patientIdField = document.getElementById('patient-id');
        if (!patientIdField || !patientIdField.value) {
            // If patient ID is missing, set it from currentPatientId
            if (currentPatientId) {
                console.log("Setting patient ID from currentPatientId:", currentPatientId);
                if (patientIdField) {
                    patientIdField.value = currentPatientId;
                } else {
                    console.error("patientIdField not found in the DOM");
                    showAlert('Error: No se ha encontrado el campo de ID del paciente', 'error');
                    isValid = false;
                }
            } else {
                console.error("No patient ID available");
                showAlert('Error: No se ha seleccionado un paciente', 'error');
                isValid = false;
            }
        } else {
            console.log("Patient ID is already set:", patientIdField.value);
        }

        // Validate doctor ID (required)
        const doctorValid = validateDoctor();
        if (!doctorValid) {
            isValid = false;
        } else {
            // Check if we have a doctor ID in the form-doctor-id hidden input
            const formDoctorId = document.getElementById('form-doctor-id');
            if (formDoctorId && formDoctorId.value) {
                console.log("Doctor ID is set:", formDoctorId.value);
            } else {
                console.log("Doctor validation passed, using session doctor ID");
            }
        }

        // Double-check that currentPatientId is set
        if (!currentPatientId) {
            console.warn("currentPatientId is not set, trying to get it from the form");
            if (patientIdField) {
                currentPatientId = patientIdField.value;
            }

            // If we have a window.currentPatientId (set from PHP), use that
            if (window.currentPatientId) {
                console.log("Using window.currentPatientId:", window.currentPatientId);
                currentPatientId = window.currentPatientId;
                if (patientIdField) {
                    patientIdField.value = currentPatientId;
                }
            }
        }

        // Validate date (required)
        const dateValid = validateDate();
        if (!dateValid) {
            isValid = false;
        }

        // Validate chief complaint and diagnosis (at least one is required)
        const complaintDiagnosisValid = validateComplaintAndDiagnosis();
        if (!complaintDiagnosisValid) {
            isValid = false;
        }

        // Validate vital signs if they have values
        if (temperature && temperature.value) {
            const tempValid = validateTemperature();
            if (!tempValid) {
                isValid = false;
                showAlert('La temperatura debe estar entre 35°C y 42°C', 'error');
                return false;
            }
        }

        if (bloodPressure && bloodPressure.value) {
            const bpValid = validateBloodPressure();
            if (!bpValid) {
                isValid = false;
                showAlert('La presión arterial debe tener el formato sistólica/diastólica (ej: 120/80)', 'error');
                return false;
            }
        }

        if (heartRate && heartRate.value) {
            const hrValid = validateHeartRate();
            if (!hrValid) {
                isValid = false;
                showAlert('La frecuencia cardíaca debe estar entre 40 y 200 lpm', 'error');
                return false;
            }
        }

        if (respiratoryRate && respiratoryRate.value) {
            const rrValid = validateRespiratoryRate();
            if (!rrValid) {
                isValid = false;
                showAlert('La frecuencia respiratoria debe estar entre 8 y 40 rpm', 'error');
                return false;
            }
        }

        if (oxygenSaturation && oxygenSaturation.value) {
            const osValid = validateOxygenSaturation();
            if (!osValid) {
                isValid = false;
                showAlert('La saturación de oxígeno debe estar entre 80% y 100%', 'error');
                return false;
            }
        }

        // If validation fails, show a more specific error message
        if (!isValid) {
            showAlert('Por favor, complete los campos requeridos: Fecha y al menos uno de Motivo de Consulta o Diagnóstico', 'error');
        }

        return isValid;
    }

    // Submit record form
    let isSubmitting = false; // Flag to prevent multiple submissions

    if (recordForm) {
        recordForm.addEventListener('submit', function(e) {
            e.preventDefault();

            console.log("Form submission started"); // Debug log

            // Prevent multiple submissions
            if (isSubmitting) {
                console.log("Form is already submitting, preventing duplicate submission");
                return;
            }

            // Validate form before submission
            if (!validateForm()) {
                console.log("Form validation failed");
                // Error message is now shown in validateForm function
                return;
            }

            console.log("Form validation passed, proceeding with submission"); // Debug log

            // Set submitting flag
            isSubmitting = true;

            // Disable submit button to prevent double submission
            const submitBtn = recordForm.querySelector('.save-btn');
            const originalBtnText = submitBtn ? submitBtn.textContent : 'Guardar';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Guardando...';
            }

            const formData = new FormData(recordForm);

            // Add prescription data to form data
            const prescriptions = collectPrescriptionData();
            if (prescriptions.length > 0) {
                formData.append('prescriptions', JSON.stringify(prescriptions));
            }

            // Debug form data being submitted
            console.log("Form data being submitted:");
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            // Check if patient ID and doctor ID are set
            if (!formData.get('patient-id')) {
                console.error("Patient ID is missing!");
            }
            if (!formData.get('doctor-id')) {
                console.error("Doctor ID is missing!");
            }

            // AJAX request to save record
            console.log("Sending AJAX request to save record"); // Debug log
            fetch('/controllers/doctor/medical_history/save-record.controller.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // Log the raw response for debugging
                console.log("Response received. Status:", response.status);
                console.log("Response headers:", response.headers);

                return response.text().then(text => {
                    console.log("Raw response text:", text);

                    if (!text) {
                        console.error("Empty response from server");
                        throw new Error("Empty response from server");
                    }

                    try {
                        const jsonData = JSON.parse(text);
                        console.log("Parsed JSON response:", jsonData);
                        return jsonData;
                    } catch (e) {
                        console.error("Error parsing JSON response:", e);
                        console.error("Response text that failed to parse:", text);
                        throw new Error("Invalid JSON response from server: " + e.message);
                    }
                });
            })
            .then(data => {
                console.log("Parsed response data:", data);

                // Reset submitting flag
                isSubmitting = false;

                // Restore submit button
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalBtnText;
                }

                if (data.success) {
                    showAlert('Registro guardado correctamente', 'success');
                    if (recordModal) {
                        recordModal.style.display = 'none';
                    }

                    // Reload patient history
                    loadPatientHistory(currentPatientId);
                } else {
                    // Handle specific error types
                    if (data.table_error) {
                        // Show error message with option to create tables
                        Swal.fire({
                            title: 'Error de base de datos',
                            text: data.message,
                            icon: 'error',
                            showCancelButton: true,
                            confirmButtonText: 'Crear Tablas',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Open the create tables script in a new tab
                                window.open('/create-medical-history-tables.php', '_blank');
                            }
                        });
                        return;
                    }

                    if (data.constraint_error) {
                        // Show error message for foreign key constraint
                        Swal.fire({
                            title: 'Error de referencia',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'Entendido'
                        });
                        return;
                    }

                    // Show more detailed error message for other errors
                    let errorMsg = data.message || 'No se pudo guardar el registro';
                    if (data.error) {
                        errorMsg += ': ' + data.error;
                    }
                    showAlert(errorMsg, 'error');

                    // If there's a specific field error, highlight it
                    if (data.field) {
                        const field = document.getElementById(data.field);
                        if (field) {
                            field.classList.add('invalid');
                            const errorElement = document.getElementById(`${data.field}-error`);
                            if (errorElement) {
                                errorElement.textContent = data.message;
                            }
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Ocurrió un error al guardar el registro: ' + error.message, 'error');

                // Reset submitting flag
                isSubmitting = false;

                // Restore submit button
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalBtnText;
                }
            });
        });
    } else {
        console.error('recordForm not found in the DOM');
    }

    // Download PDF button click
    if (downloadPdfBtn) {
        downloadPdfBtn.addEventListener('click', function() {
            if (!currentPatientId) {
                showAlert('No se ha seleccionado un paciente', 'error');
                return;
            }

            // Redirect to PDF download endpoint
            window.location.href = `/controllers/doctor/medical_history/download-history-pdf.controller.php?patient_id=${currentPatientId}`;
        });
    } else {
        console.error('downloadPdfBtn not found in the DOM');
    }

    // Download Medical History PDF button click
    const downloadMedicalHistoryBtn = document.getElementById('download-medical-history-btn');
    if (downloadMedicalHistoryBtn) {
        downloadMedicalHistoryBtn.addEventListener('click', function() {
            if (!currentPatientId) {
                showAlert('No se ha seleccionado un paciente', 'error');
                return;
            }

            // Redirect to Medical History PDF download endpoint
            window.location.href = `/controllers/doctor/medical_history/download-medical-history-pdf.controller.php?patient_id=${currentPatientId}`;
        });
    } else {
        console.error('downloadMedicalHistoryBtn not found in the DOM');
    }

    // Download individual record as PDF
    function downloadRecordPdf(recordId) {
        // Redirect to PDF download endpoint for specific record
        // Using the existing download-history-pdf controller with record_id parameter
        window.location.href = `/controllers/doctor/medical_history/download-history-pdf.controller.php?record_id=${recordId}`;
    }

    // Download individual medical history record as PDF
    function downloadMedicalHistoryRecordPdf(recordId) {
        // Redirect to Medical History PDF download endpoint for specific record
        window.location.href = `/controllers/doctor/medical_history/download-medical-history-pdf.controller.php?record_id=${recordId}`;
    }

    // Upload PDF button click
    if (uploadPdfBtn) {
        uploadPdfBtn.addEventListener('click', function() {
            // Reset form
            if (uploadForm) {
                uploadForm.reset();
            } else {
                console.error('uploadForm not found in the DOM');
                return;
            }

            // Copy the current doctor ID from the outside select to the hidden input
            const doctorIdValue = doctorIdSelect ? doctorIdSelect.value : '';
            const uploadDoctorId = document.getElementById('upload-doctor-id');

            if (uploadDoctorId) {
                uploadDoctorId.value = doctorIdValue;
                console.log('Set upload-doctor-id value:', uploadDoctorId.value);
            }

            // Show modal
            if (uploadModal) {
                uploadModal.style.display = 'flex';
            } else {
                console.error('uploadModal not found in the DOM');
                showAlert('Error: No se pudo encontrar el modal de subida', 'error');
            }
        });
    } else {
        console.error('uploadPdfBtn not found in the DOM');
    }

    // Agendar Estudio button click
    if (newStudyBtn) {
        newStudyBtn.addEventListener('click', function() {
            // Reset form
            if (studyForm) studyForm.reset();

            // Set patient id if available
            if (currentPatientId) {
                if (studyPatientId) studyPatientId.value = currentPatientId;
                if (studyPatientDisplay) studyPatientDisplay.value = patientName.textContent || '';
            }

            // Set default datetime to now (rounded to minutes)
            const now = new Date();
            now.setSeconds(0);
            now.setMilliseconds(0);
            const tzOffset = now.getTimezoneOffset() * 60000;
            const localISOTime = new Date(now - tzOffset).toISOString().slice(0,16); // YYYY-MM-DDTHH:MM
            if (studyDate) studyDate.value = localISOTime;

            // Load studies into select
            loadStudiesIntoSelect();

            if (studyModal) {
                studyModal.style.display = 'flex';
            } else {
                showAlert('Error: No se pudo encontrar el modal de agendar estudio', 'error');
            }
        });
    } else {
        console.error('newStudyBtn not found in the DOM');
    }

    function loadStudiesIntoSelect() {
        if (!studySelect) return;
        // Clear existing options except first
        while (studySelect.options.length > 1) studySelect.remove(1);

        fetch('/controllers/studies/get-studies.controller.php')
            .then(resp => resp.json())
            .then(data => {
                if (data.success && Array.isArray(data.studies)) {
                    data.studies.forEach(st => {
                        const opt = document.createElement('option');
                        opt.value = st.id;
                        opt.textContent = st.name;
                        studySelect.appendChild(opt);
                    });
                } else {
                    console.error('No se pudieron cargar los estudios', data);
                }
            })
            .catch(err => {
                console.error('Error cargando estudios:', err);
            });
    }

    // Close study modal
    if (closeStudyModal) {
        closeStudyModal.addEventListener('click', function() {
            if (studyModal) studyModal.style.display = 'none';
        });
    }

    if (cancelStudy) {
        cancelStudy.addEventListener('click', function() {
            if (studyModal) studyModal.style.display = 'none';
        });
    }

    // Submit study form
    if (studyForm) {
        studyForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Basic validation
            let valid = true;
            if (!studySelect || !studySelect.value) {
                const el = document.getElementById('study-select-error');
                if (el) el.textContent = 'Seleccione un estudio';
                valid = false;
            } else {
                const el = document.getElementById('study-select-error'); if (el) el.textContent = '';
            }

            if (!studyDate || !studyDate.value) {
                const el = document.getElementById('study-date-error');
                if (el) el.textContent = 'La fecha es obligatoria';
                valid = false;
            } else {
                const el = document.getElementById('study-date-error'); if (el) el.textContent = '';
            }

            if (!valid) return;

            // Validación cliente: la fecha debe ser al menos 10 minutos en el futuro
            try {
                const scheduledLocalStr = studyDate.value; // 'YYYY-MM-DDTHH:MM'
                if (scheduledLocalStr && scheduledLocalStr.indexOf('T') !== -1) {
                    // Parse as local time (datetime-local always gives local time)
                    const parts = scheduledLocalStr.split('T');
                    const [year, month, day] = parts[0].split('-');
                    const [hours, minutes] = parts[1].split(':');
                    const selectedDate = new Date(year, month - 1, day, hours, minutes, 0, 0);
                    const minDate = new Date(Date.now() + 10 * 60 * 1000);
                    if (selectedDate.getTime() < minDate.getTime()) {
                        showAlert('La fecha y hora deben ser al menos 10 minutos en el futuro', 'error');
                        return;
                    }
                }
            } catch (err) {
                console.error('Error validating date client-side:', err);
            }

            const payload = new URLSearchParams();
            payload.append('patient_id', studyPatientId ? studyPatientId.value : '');
            payload.append('study_id', studySelect.value);
            // send scheduled_at as local datetime plus client timezone offset to avoid timezone mismatches
            // studyDate.value is 'YYYY-MM-DDTHH:MM' (datetime-local format)
            // Transform to 'YYYY-MM-DD HH:MM:SS'
            const scheduledLocalRaw = studyDate.value || ''; // 'YYYY-MM-DDTHH:MM'
            const scheduledLocalForServer = scheduledLocalRaw.replace('T', ' ') + ':00'; // 'YYYY-MM-DD HH:MM:00'
            payload.append('scheduled_at', scheduledLocalForServer);
            // client timezone offset in minutes (as returned by Date.getTimezoneOffset())
            payload.append('client_tz_offset', String(new Date().getTimezoneOffset()));

            fetch('/controllers/doctor/medical_history/schedule-study.controller.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: payload.toString()
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message || 'Estudio agendado correctamente', 'success');
                    if (studyModal) studyModal.style.display = 'none';
                    // Refresh patient studies list
                    const pid = studyPatientId ? studyPatientId.value : window.currentPatientId;
                    if (pid) loadPatientStudies(pid);
                } else {
                    showAlert(data.message || 'No se pudo agendar el estudio', 'error');
                }
            })
            .catch(err => {
                console.error('Error agendando estudio:', err);
                showAlert('Ocurrió un error al agendar el estudio', 'error');
            });
        });
    }

    // Close upload modal
    if (closeUploadModal) {
        closeUploadModal.addEventListener('click', function() {
            if (uploadModal) {
                uploadModal.style.display = 'none';
            } else {
                console.error('uploadModal not found in the DOM');
            }
        });
    } else {
        console.error('closeUploadModal not found in the DOM');
    }

    // Cancel upload button
    if (cancelUpload) {
        cancelUpload.addEventListener('click', function() {
            if (uploadModal) {
                uploadModal.style.display = 'none';
            } else {
                console.error('uploadModal not found in the DOM');
            }
        });
    } else {
        console.error('cancelUpload not found in the DOM');
    }

    // Submit upload form
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(uploadForm);

            // Check if doctor ID is set
            if (!formData.get('doctor-id')) {
                // Try to get it from the outside select
                const doctorIdValue = doctorIdSelect ? doctorIdSelect.value : '';
                if (doctorIdValue) {
                    formData.set('doctor-id', doctorIdValue);
                    console.log('Added doctor-id to form data:', doctorIdValue);
                } else {
                    showAlert('Debe seleccionar un médico para el documento', 'error');
                    return;
                }
            }

            // Log all form data for debugging
            console.log('Form data being submitted:');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            // AJAX request to upload PDF
            fetch('/controllers/doctor/medical_history/upload-pdf.controller.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Documento subido correctamente', 'success');
                    if (uploadModal) {
                        uploadModal.style.display = 'none';
                    }

                    // Reload patient history
                    loadPatientHistory(currentPatientId);
                } else {
                    showAlert(data.message || 'No se pudo subir el documento', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Ocurrió un error al subir el documento', 'error');
            });
        });
    } else {
        console.error('uploadForm not found in the DOM');
    }

    // Format date for display
    function formatDate(dateString) {
        if (!dateString) return 'No disponible';

        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        const date = new Date(dateString);

        return date.toLocaleDateString('es-ES', options);
    }

    // Show alert using SweetAlert2
    function showAlert(message, type) {
        if (typeof Swal === 'undefined') {
            console.error('SweetAlert2 is not defined, intentando cargar dinámicamente');

            // Intentar cargar SweetAlert2 dinámicamente
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
            script.onload = function() {
                // Una vez cargado, mostrar la alerta
                Swal.fire({
                    text: message,
                    icon: type,
                    confirmButtonText: 'Aceptar'
                });
            };
            script.onerror = function() {
                console.error('No se pudo cargar SweetAlert2 dinámicamente');
                // No usar alert() como fallback
            };
            document.head.appendChild(script);
            return;
        }

        Swal.fire({
            text: message,
            icon: type,
            confirmButtonText: 'Aceptar'
        });
    }

    // Close modals when clicking outside
    window.addEventListener('click', function(e) {
        if (recordModal && e.target === recordModal) {
            recordModal.style.display = 'none';
        }
        if (uploadModal && e.target === uploadModal) {
            uploadModal.style.display = 'none';
        }
    });
});
