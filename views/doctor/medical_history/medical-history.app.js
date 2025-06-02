document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const searchForm = document.getElementById('searchPatientForm');
    const searchTerm = document.getElementById('search-term');
    const patientResults = document.getElementById('patient-results');
    const patientsList = document.getElementById('patients-list');
    const patientHistory = document.getElementById('patient-history');
    const patientName = document.getElementById('patient-name');
    const patientCurp = document.getElementById('patient-curp');
    const patientBirthDate = document.getElementById('patient-birth-date');
    const patientEmail = document.getElementById('patient-email');
    const newRecordBtn = document.getElementById('new-record-btn');
    const downloadPdfBtn = document.getElementById('download-pdf-btn');
    const uploadPdfBtn = document.getElementById('upload-pdf-btn');
    const historyList = document.getElementById('history-list');
    const noRecordsMessage = document.getElementById('no-records-message');
    const recordModal = document.getElementById('record-modal');
    const closeModal = document.querySelector('.close-modal');
    const recordForm = document.getElementById('record-form');
    const patientId = document.getElementById('patient-id');
    const appointmentSelect = document.getElementById('appointment-select');
    const cancelRecord = document.getElementById('cancel-record');
    const uploadModal = document.getElementById('upload-modal');
    const closeUploadModal = document.querySelector('.close-upload-modal');
    const uploadForm = document.getElementById('upload-form');
    const uploadPatientId = document.getElementById('upload-patient-id');
    const cancelUpload = document.getElementById('cancel-upload');

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
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayPatientHistory(data.patient, data.history, data.appointments);
            } else {
                showAlert(data.message || 'No se pudo cargar el historial del paciente', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Ocurrió un error al cargar el historial del paciente', 'error');
        });
    }

    // Display patient history
    function displayPatientHistory(patient, history, appointments) {
        // Set patient details
        patientName.textContent = `${patient.names} ${patient.last_name} ${patient.last_name2}`;
        patientCurp.textContent = patient.curp;
        patientBirthDate.textContent = formatDate(patient.birth_date);
        patientEmail.textContent = patient.email || 'No disponible';

        // Populate appointments dropdown
        appointmentSelect.innerHTML = '<option value="">Seleccione una cita</option>';
        appointments.forEach(appointment => {
            const option = document.createElement('option');
            option.value = appointment.id;
            option.textContent = `${formatDate(appointment.appointment_date)} - ${appointment.medical_area}`;
            appointmentSelect.appendChild(option);
        });

        // Display history records
        const recordsContainer = document.querySelector('.records-container');
        recordsContainer.innerHTML = '';

        if (history.length === 0) {
            recordsContainer.appendChild(noRecordsMessage);
        } else {
            history.forEach(record => {
                const recordCard = createRecordCard(record);
                recordsContainer.appendChild(recordCard);
            });
        }

        // Show patient history section
        patientResults.style.display = 'none';
        patientHistory.style.display = 'block';

        // Set patient ID for forms
        patientId.value = patient.id;
        uploadPatientId.value = patient.id;
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

        actions.appendChild(downloadBtn);

        card.appendChild(header);
        card.appendChild(content);
        card.appendChild(actions);

        return card;
    }

    // New record button click
    newRecordBtn.addEventListener('click', function() {
        // Reset form
        recordForm.reset();

        // Set current date as default
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('record-date').value = today;

        // Show modal
        recordModal.style.display = 'flex';
    });

    // Close record modal
    closeModal.addEventListener('click', function() {
        recordModal.style.display = 'none';
    });

    // Cancel record button
    cancelRecord.addEventListener('click', function() {
        recordModal.style.display = 'none';
    });

    // Submit record form
    recordForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(recordForm);

        // AJAX request to save record
        fetch('/controllers/doctor/medical_history/save-record.controller.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Registro guardado correctamente', 'success');
                recordModal.style.display = 'none';

                // Reload patient history
                loadPatientHistory(currentPatientId);
            } else {
                showAlert(data.message || 'No se pudo guardar el registro', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Ocurrió un error al guardar el registro', 'error');
        });
    });

    // Download PDF button click
    downloadPdfBtn.addEventListener('click', function() {
        if (!currentPatientId) return;

        // Redirect to PDF download endpoint
        window.location.href = `/controllers/doctor/medical_history/download-history-pdf.controller.php?patient_id=${currentPatientId}`;
    });

    // Download individual record as PDF
    function downloadRecordPdf(recordId) {
        // Redirect to PDF download endpoint for specific record
        // Using the existing download-history-pdf controller with record_id parameter
        window.location.href = `/controllers/doctor/medical_history/download-history-pdf.controller.php?record_id=${recordId}`;
    }

    // Upload PDF button click
    uploadPdfBtn.addEventListener('click', function() {
        // Reset form
        uploadForm.reset();

        // Show modal
        uploadModal.style.display = 'flex';
    });

    // Close upload modal
    closeUploadModal.addEventListener('click', function() {
        uploadModal.style.display = 'none';
    });

    // Cancel upload button
    cancelUpload.addEventListener('click', function() {
        uploadModal.style.display = 'none';
    });

    // Submit upload form
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(uploadForm);

        // AJAX request to upload PDF
        fetch('/controllers/doctor/medical_history/upload-pdf.controller.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Documento subido correctamente', 'success');
                uploadModal.style.display = 'none';

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

    // Format date for display
    function formatDate(dateString) {
        if (!dateString) return 'No disponible';

        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        const date = new Date(dateString);

        return date.toLocaleDateString('es-ES', options);
    }

    // Show alert using SweetAlert2
    function showAlert(message, type) {
        Swal.fire({
            text: message,
            icon: type,
            confirmButtonText: 'Aceptar'
        });
    }

    // Close modals when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target === recordModal) {
            recordModal.style.display = 'none';
        }
        if (e.target === uploadModal) {
            uploadModal.style.display = 'none';
        }
    });
});
