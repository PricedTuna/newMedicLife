document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded event triggered');
    console.log('window.doctors:', window.doctors);
    if (window.doctors && window.doctors.length > 0) {
        console.log('First doctor:', window.doctors[0]);
    } else {
        console.error('No doctors found in window.doctors');
    }
    console.log('window.medical_areas:', window.medical_areas);
    if (window.medical_areas && window.medical_areas.length > 0) {
        console.log('First medical area:', window.medical_areas[0]);
    } else {
        console.error('No medical areas found in window.medical_areas');
    }
    console.log('window.isDoctor:', window.isDoctor);

    // DOM Elements
    const searchForm = document.getElementById('searchPatientForm');
    console.log('searchForm:', searchForm);

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

    // This element might not exist in the template
    const appointmentSelect = document.getElementById('appointment-select');
    console.log('appointmentSelect:', appointmentSelect);

    const cancelRecord = document.getElementById('cancel-record');
    const uploadModal = document.getElementById('upload-modal');
    const closeUploadModal = document.querySelector('.close-upload-modal');
    const uploadForm = document.getElementById('upload-form');
    const uploadPatientId = document.getElementById('upload-patient-id');
    const cancelUpload = document.getElementById('cancel-upload');

    // Doctor selection elements
    const medicalAreaSelect = document.getElementById('medical-area');
    console.log('medicalAreaSelect:', medicalAreaSelect);

    const doctorIdSelect = document.getElementById('doctor-id');
    console.log('doctorIdSelect:', doctorIdSelect);

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
            recordsContainer.appendChild(noRecordsMessage);
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

        // Populate appointments dropdown if it exists
        if (appointmentSelect) {
            appointmentSelect.innerHTML = '<option value="">Seleccione una cita</option>';
            console.log("Appointments data:", appointments); // Debug log
            appointments.forEach(appointment => {
                const option = document.createElement('option');
                // Use appointment.cita (the ID field) or fallback to appointment.id
                option.value = appointment.cita || appointment.id;
                option.textContent = `${formatDate(appointment.appointment_date)} - ${appointment.medical_area || 'Consulta general'}`;
                appointmentSelect.appendChild(option);
            });
        }

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
        historyList.style.display = 'block'; // Show history list by default

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

    // Function to filter doctors by medical area
    function filterDoctorsByArea(areaId) {
        console.log('filterDoctorsByArea called with areaId:', areaId);
        console.log('window.doctors:', window.doctors);

        // Check if doctors data is available
        if (!window.doctors) {
            console.error('Doctors data not available');
            return;
        }

        // Clear current options
        doctorIdSelect.innerHTML = '<option value="">Seleccione un médico</option>';

        console.log('Showing all doctors regardless of specialty');
        console.log('Total doctors available:', window.doctors.length);

        // Check if user is a doctor
        const isUserDoctor = window.isDoctor === true;

        // If user is a doctor, only show that doctor
        if (isUserDoctor) {
            // For doctor users, we'll show only their doctor profile
            // This assumes the first doctor in the list is the current doctor
            if (window.doctors.length > 0) {
                const doctor = window.doctors[0];
                // Add only the first doctor (should be the current doctor)
                const option = document.createElement('option');
                option.value = doctor.doctor_id;
                option.textContent = doctor.doctor_name + ' ' + 
                                    doctor.last_name + ' ' + 
                                    (doctor.last_name2 || '');
                doctorIdSelect.appendChild(option);

                // Select automatically
                doctorIdSelect.value = doctor.doctor_id;

                // Trigger change event
                const event = new Event('change');
                doctorIdSelect.dispatchEvent(event);
            }
        } else {
            // For non-doctor users, show ALL doctors without filtering by specialty
            window.doctors.forEach(doctor => {
                const option = document.createElement('option');
                option.value = doctor.doctor_id;

                let doctorText = doctor.doctor_name + ' ' + doctor.last_name + ' ' + (doctor.last_name2 || '');
                if (doctor.specialty) {
                    doctorText += ' - ' + doctor.specialty;
                }
                if (doctor.medical_area_name) {
                    doctorText += ' (' + doctor.medical_area_name + ')';
                }

                option.textContent = doctorText;
                doctorIdSelect.appendChild(option);
            });
        }

        // Validate doctor selection
        validateDoctor();
    }

    // Add event listener for medical area change
    if (medicalAreaSelect) {
        console.log('medicalAreaSelect found:', medicalAreaSelect);
        console.log('medicalAreaSelect ID:', medicalAreaSelect.id);
        console.log('medicalAreaSelect name:', medicalAreaSelect.name);
        console.log('medicalAreaSelect options:', medicalAreaSelect.options.length);

        // Log all options
        for (let i = 0; i < medicalAreaSelect.options.length; i++) {
            console.log(`Option ${i}:`, {
                value: medicalAreaSelect.options[i].value,
                text: medicalAreaSelect.options[i].text
            });
        }

        // We no longer need to filter doctors by medical area
        // The doctor dropdown is now pre-populated with all doctors
        // This event listener is kept for backward compatibility
        medicalAreaSelect.addEventListener('change', function() {
            console.log('medicalAreaSelect change event triggered');
            const selectedAreaId = this.value;
            console.log('selectedAreaId:', selectedAreaId);
            console.log('Selected option text:', this.options[this.selectedIndex].text);

            // No need to filter doctors or clear the dropdown
            // The doctor selection is now independent of the medical area

            // Validate doctor selection
            validateDoctor();
        });
    } else {
        console.error('medicalAreaSelect not found');
    }

    // New record button click
    newRecordBtn.addEventListener('click', function() {
        console.log('newRecordBtn click event triggered');

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

        // If there's only one medical area, select it automatically
        console.log('window.medical_areas:', window.medical_areas);
        console.log('medicalAreaSelect in newRecordBtn click:', medicalAreaSelect);

        if (window.medical_areas && window.medical_areas.length === 1 && medicalAreaSelect) {
            console.log('Only one medical area found, selecting it automatically');
            console.log('Medical area to select:', window.medical_areas[0]);

            // Check if the medical area has an id property
            if (window.medical_areas[0].id) {
                medicalAreaSelect.value = window.medical_areas[0].id;
                console.log('Medical area selected by id:', window.medical_areas[0].id);
            } else {
                // Try to find the first property that might be the id
                const possibleIdProps = Object.keys(window.medical_areas[0]);
                console.log('Possible ID properties:', possibleIdProps);

                if (possibleIdProps.length > 0) {
                    const firstProp = possibleIdProps[0];
                    medicalAreaSelect.value = window.medical_areas[0][firstProp];
                    console.log(`Medical area selected by ${firstProp}:`, window.medical_areas[0][firstProp]);
                }
            }

            // Log the current value of the select
            console.log('medicalAreaSelect value after setting:', medicalAreaSelect.value);

            // Trigger change event to populate doctors
            const event = new Event('change');
            medicalAreaSelect.dispatchEvent(event);
            console.log('Change event dispatched to medicalAreaSelect');
        } else if (window.medical_areas && window.medical_areas.length > 1) {
            console.log('Multiple medical areas found:', window.medical_areas.length);

            // Log all medical areas
            window.medical_areas.forEach((area, index) => {
                console.log(`Medical area ${index}:`, area);
            });

            // Log the current value of the select
            console.log('medicalAreaSelect value:', medicalAreaSelect.value);
        } else {
            console.log('No medical areas found or medicalAreaSelect not available');
        }

        // Show modal
        recordModal.style.display = 'flex';

        // Hide history list
        historyList.style.display = 'none';

        // Validate the form initially to provide immediate feedback
        validateDoctor();
        validateDate();
        validateComplaintAndDiagnosis();
    });

    // Add real-time validation to required fields
    const recordDate = document.getElementById('record-date');
    const chiefComplaint = document.getElementById('chief-complaint');
    const diagnosis = document.getElementById('diagnosis');
    const doctorId = document.getElementById('doctor-id');

    // Validate doctor selection on input and change
    function validateDoctor() {
        if (!doctorId.value) {
            doctorId.classList.add('invalid');
            document.getElementById('doctor-id-error').textContent = 'Debe seleccionar un médico';
            return false;
        } else {
            doctorId.classList.remove('invalid');
            document.getElementById('doctor-id-error').textContent = '';
            return true;
        }
    }

    doctorId.addEventListener('change', validateDoctor);
    doctorId.addEventListener('blur', validateDoctor);

    // Validate date field on input and change
    function validateDate() {
        if (!recordDate.value) {
            recordDate.classList.add('invalid');
            document.getElementById('record-date-error').textContent = 'La fecha es obligatoria';
            return false;
        } else {
            recordDate.classList.remove('invalid');
            document.getElementById('record-date-error').textContent = '';
            return true;
        }
    }

    recordDate.addEventListener('input', validateDate);
    recordDate.addEventListener('change', validateDate);
    recordDate.addEventListener('blur', validateDate);

    // Validate chief complaint and diagnosis fields on input
    function validateComplaintAndDiagnosis() {
        if (!chiefComplaint.value && !diagnosis.value) {
            chiefComplaint.classList.add('invalid');
            diagnosis.classList.add('invalid');
            document.getElementById('chief-complaint-error').textContent = 'Debe completar al menos uno de estos campos';
            document.getElementById('diagnosis-error').textContent = 'Debe completar al menos uno de estos campos';
            return false;
        } else {
            chiefComplaint.classList.remove('invalid');
            diagnosis.classList.remove('invalid');
            document.getElementById('chief-complaint-error').textContent = '';
            document.getElementById('diagnosis-error').textContent = '';
            return true;
        }
    }

    chiefComplaint.addEventListener('input', validateComplaintAndDiagnosis);
    chiefComplaint.addEventListener('blur', validateComplaintAndDiagnosis);
    diagnosis.addEventListener('input', validateComplaintAndDiagnosis);
    diagnosis.addEventListener('blur', validateComplaintAndDiagnosis);

    // View history button click
    viewHistoryBtn.addEventListener('click', function() {
        // Show history list
        historyList.style.display = 'block';

        // Hide modal if open
        recordModal.style.display = 'none';
    });

    // Close record modal
    closeModal.addEventListener('click', function() {
        recordModal.style.display = 'none';
    });

    // Cancel record button
    cancelRecord.addEventListener('click', function() {
        recordModal.style.display = 'none';
    });

    // Validate form fields
    function validateForm() {
        let isValid = true;

        // Validate patient ID (required)
        const patientIdField = document.getElementById('patient-id');
        if (!patientIdField.value) {
            // If patient ID is missing, set it from currentPatientId
            if (currentPatientId) {
                console.log("Setting patient ID from currentPatientId:", currentPatientId);
                patientIdField.value = currentPatientId;
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
            console.log("Doctor ID is set:", doctorId.value);
        }

        // Double-check that currentPatientId is set
        if (!currentPatientId) {
            console.warn("currentPatientId is not set, trying to get it from the form");
            currentPatientId = patientIdField.value;

            // If we have a window.currentPatientId (set from PHP), use that
            if (window.currentPatientId) {
                console.log("Using window.currentPatientId:", window.currentPatientId);
                currentPatientId = window.currentPatientId;
                patientIdField.value = currentPatientId;
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

        // If validation fails, show a more specific error message
        if (!isValid) {
            showAlert('Por favor, complete los campos requeridos: Médico, Fecha y al menos uno de Motivo de Consulta o Diagnóstico', 'error');
        }

        return isValid;
    }

    // Submit record form
    let isSubmitting = false; // Flag to prevent multiple submissions

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
        const originalBtnText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Guardando...';

        const formData = new FormData(recordForm);

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
            const submitBtn = recordForm.querySelector('.save-btn');
            submitBtn.disabled = false;
            submitBtn.textContent = originalBtnText;

            if (data.success) {
                showAlert('Registro guardado correctamente', 'success');
                recordModal.style.display = 'none';

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
            const submitBtn = recordForm.querySelector('.save-btn');
            submitBtn.disabled = false;
            submitBtn.textContent = originalBtnText;
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
