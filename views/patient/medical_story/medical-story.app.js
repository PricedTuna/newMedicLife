document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const searchForm = document.getElementById('searchForm');
    const patientDetails = document.getElementById('patientDetails');
    const successMessage = document.getElementById('successMessage');
    const patientNameElement = document.getElementById('patientName');
    const patientBirthDateElement = document.getElementById('patientBirthDate');
    const patientEmailElement = document.getElementById('patientEmail');
    const confirmButton = document.getElementById('confirmButton');
    const cancelButton = document.getElementById('cancelButton');
    const newSearchButton = document.getElementById('newSearchButton');

    let currentPatientId = null;

    // Manejar envío del formulario de búsqueda
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const curp = document.getElementById('curp').value.trim();

        if (!curp) {
            showAlert('Por favor, ingrese un CURP para buscar', 'error');
            return;
        }

        // Realizar la búsqueda mediante AJAX
        const formData = new FormData();
        formData.append('action', 'search');
        formData.append('curp', curp);

        fetch('/controllers/patient/medical-story.controller.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log({response})
            console.log(JSON.stringify(response))
            return response.json()
        })
        .then(data => {
            if (data.success) {
                // Mostrar detalles del paciente para confirmación
                const patient = data.patient;
                currentPatientId = patient.id;

                // Formatear el nombre completo
                const fullName = `${patient.names} ${patient.last_name} ${patient.last_name2}`;

                // Formatear la fecha de nacimiento
                const birthDate = formatDate(patient.birth_date);

                // Mostrar los detalles
                patientNameElement.textContent = fullName;
                patientBirthDateElement.textContent = birthDate;
                patientEmailElement.textContent = patient.email || 'No disponible';

                // Mostrar sección de confirmación
                searchForm.style.display = 'none';
                patientDetails.style.display = 'block';
                successMessage.style.display = 'none';
            } else {
                // Mostrar mensaje de error
                showAlert(data.message || 'No se encontró ningún paciente con el CURP proporcionado', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Ocurrió un error al buscar el paciente. Por favor, intente de nuevo.', 'error');
        });
    });

    // Manejar clic en botón de confirmación
    confirmButton.addEventListener('click', function() {
        if (!currentPatientId) {
            showAlert('No se ha seleccionado ningún paciente', 'error');
            return;
        }

        // Enviar confirmación mediante AJAX
        const formData = new FormData();
        formData.append('action', 'confirm');
        formData.append('patientId', currentPatientId);

        fetch('/controllers/patient/medical-story.controller.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mostrar mensaje de éxito
                patientDetails.style.display = 'none';
                successMessage.style.display = 'block';

                // Si se envió un correo, mostrar un mensaje adicional
                if (data.emailSent) {
                    showAlert('Se ha enviado un correo electrónico con tu historial médico', 'success');
                }
            } else {
                // Mostrar mensaje de error
                showAlert(data.message || 'Ocurrió un error al confirmar la identidad del paciente', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Ocurrió un error al confirmar la identidad del paciente. Por favor, intente de nuevo.', 'error');
        });
    });

    // Manejar clic en botón de cancelación
    cancelButton.addEventListener('click', function() {
        // Volver al formulario de búsqueda
        patientDetails.style.display = 'none';
        searchForm.style.display = 'block';
        currentPatientId = null;
    });

    // Manejar clic en botón de nueva búsqueda
    newSearchButton.addEventListener('click', function() {
        // Volver al formulario de búsqueda
        successMessage.style.display = 'none';
        searchForm.style.display = 'block';
        document.getElementById('curp').value = '';
        currentPatientId = null;
    });

    // Función para mostrar alertas usando SweetAlert2
    function showAlert(message, type) {
        Swal.fire({
            text: message,
            icon: type,
            confirmButtonText: 'Aceptar'
        });
    }

    // Función para formatear fechas
    function formatDate(dateString) {
        if (!dateString) return 'No disponible';

        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        const date = new Date(dateString);

        return date.toLocaleDateString('es-ES', options);
    }
});
