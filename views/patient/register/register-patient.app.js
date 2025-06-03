let currentStep = 1;

function showStep(step) {
    document.querySelectorAll('.form-step').forEach((formStep) => {
        formStep.style.display = 'none';
    });

    let activeStep = document.getElementById(`step-${step}`);
    if (activeStep) {
        activeStep.style.display = 'block';
    }

    document.querySelectorAll('.step').forEach((stepElement) => {
        stepElement.classList.remove('step-active');
    });

    let currentStepElement = document.querySelector(`.step[data-step='${step}']`);
    if (currentStepElement) {


        currentStepElement.classList.add('step-active');
    }


}

function nextStep(step) {
    if (validateStep(currentStep)) {
        currentStep = step;
        showStep(currentStep);
    } else {
        Swal.fire({
            title: 'Campos incompletos',
            text: 'Por favor, completa todos los campos obligatorios antes de continuar.',
            icon: 'error',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Entendido'
        });
    }
}

function prevStep(step) {
    currentStep = step;
    showStep(currentStep);
}

function validateStep(step) {
    let valid = true;
    document.querySelectorAll(`#step-${step} input[required], #step-${step} select[required]`).forEach((input) => {
        if (!validateField(input)) {
            valid = false;
        }
    });

    // Validar el campo de foto en el paso 1
    if (step === 1 && typeof validatePhotoField === 'function') {
        if (!validatePhotoField()) {
            valid = false;
        }
    }

    return valid;
}

// Función para limpiar mensaje de error
function clearErrorMessage(input) {
    input.style.border = '2px solid var(--line-clr)';

    // Eliminar mensaje de error si existe
    let errorElement = input.nextElementSibling;
    if (errorElement && errorElement.classList.contains('error-message')) {
        errorElement.remove();
    }
}

// Función para validar un campo individual
function validateField(input) {
    // Validar si el campo está vacío
    if (input.hasAttribute('required') && !input.value.trim()) {
        // Obtener el título del campo según su ID para mensajes más específicos
        let fieldTitle = getFieldTitle(input.id);
        showErrorMessage(input, `El campo ${fieldTitle} es obligatorio`);
        return false;
    }
    // Validar que los campos de nombre no contengan números
    else if (['firstName', 'lastName', 'motherLastName', 'contactFirstName', 'contactLastName', 'contactMotherLastName'].includes(input.id) && /\d/.test(input.value)) {
        let fieldTitle = getFieldTitle(input.id);
        showErrorMessage(input, `El ${fieldTitle} no debe contener números`);
        return false;
    }
    // Validar que los campos del segundo paso no contengan caracteres especiales
    else if (['street', 'neighborhood', 'extNumber', 'intNumber'].includes(input.id) && /[^A-Za-z0-9\s]/.test(input.value)) {
        let fieldTitle = getFieldTitle(input.id);
        showErrorMessage(input, `El campo ${fieldTitle} no debe contener caracteres especiales`);
        return false;
    }
    // Validar formato de correo electrónico
    else if (input.id === 'email' && input.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value)) {
        showErrorMessage(input, `El Correo Electrónico no tiene un formato válido`);
        return false;
    }
    // Validar formato de CURP (18 caracteres)
    else if (input.id === 'curp' && input.value.trim() && !/^[A-Z0-9]{18}$/.test(input.value)) {
        showErrorMessage(input, `La CURP debe tener 18 caracteres alfanuméricos`);
        return false;
    }
    // Validar formato de RFC (12 o 13 caracteres)
    else if (input.id === 'rfc' && input.value.trim() && !/^[A-Z0-9]{12,13}$/.test(input.value)) {
        showErrorMessage(input, `El RFC debe tener 12 o 13 caracteres alfanuméricos`);
        return false;
    }
    // Validar número de afiliación (solo alfanumérico)
    else if (input.id === 'affiliationNumber' && input.value.trim() && !/^[A-Za-z0-9]+$/.test(input.value)) {
        showErrorMessage(input, `El Número de Afiliación solo debe contener letras y números`);
        return false;
    } else {
        clearErrorMessage(input);
        return true;
    }
}

// Función para obtener el título del campo según su ID
function getFieldTitle(id) {
    switch(id) {
        case 'firstName': return 'Nombre';
        case 'lastName': return 'Apellido Paterno';
        case 'motherLastName': return 'Apellido Materno';
        case 'email': return 'Correo Electrónico';
        case 'curp': return 'CURP';
        case 'rfc': return 'RFC';
        case 'affiliationNumber': return 'Número de Afiliación';
        case 'phoneNumber': return 'Número Telefónico';
        case 'birthDate': return 'Fecha de Nacimiento';
        case 'gender': return 'Sexo';
        case 'street': return 'Calle';
        case 'neighborhood': return 'Colonia';
        case 'postalCode': return 'Código Postal';
        case 'extNumber': return 'Número Exterior';
        case 'intNumber': return 'Número Interior';
        case 'state': return 'Estado';
        case 'municipality': return 'Municipio';
        case 'locality': return 'Localidad';
        case 'bloodType': return 'Tipo de Sangre';
        case 'maritalStatus': return 'Estado Civil';
        case 'weight': return 'Peso';
        case 'height': return 'Altura';
        case 'ethnicGroup': return 'Grupo Étnico';
        case 'religion': return 'Religión';
        case 'contactFirstName': return 'Nombre del Contacto';
        case 'contactLastName': return 'Apellido Paterno del Contacto';
        case 'contactMotherLastName': return 'Apellido Materno del Contacto';
        case 'contactPhone': return 'Número Telefónico del Contacto';
        case 'contactRelation': return 'Relación con el Paciente';
        default: return id; // Si no hay un título específico, usar el ID
    }
}

// Función para mostrar mensaje de error
function showErrorMessage(input, message) {
    input.style.border = '2px solid red';

    // Crear o actualizar mensaje de error
    let errorElement = input.nextElementSibling;
    if (!errorElement || !errorElement.classList.contains('error-message')) {
        errorElement = document.createElement('span');
        errorElement.classList.add('error-message');
        errorElement.style.color = 'red';
        input.parentNode.appendChild(errorElement);
    }
    errorElement.textContent = message;
}

// Función para validar el campo de peso
function validateWeight(input) {
    // Limpiar cualquier mensaje de error previo
    clearErrorMessage(input);

    const value = input.value.trim();

    // Si el campo está vacío, no hacer nada (la validación de campo requerido se encargará)
    if (!value) return;

    // Validar que el valor no exceda el rango permitido
    const numValue = parseFloat(value);

    // Verificar si el valor es negativo
    if (numValue < 0) {
        showErrorMessage(input, "El peso no puede ser negativo");
        input.value = ""; // Limpiar el valor
        return;
    }

    // Verificar si el valor excede el máximo permitido (999.99)
    if (numValue > 999.99) {
        showErrorMessage(input, "El peso no puede exceder 999.99 kg");
        input.value = "999.99"; // Establecer al valor máximo
        return;
    }

    // Verificar el formato (máximo 3 dígitos enteros y 2 decimales)
    const parts = value.split('.');
    const integerPart = parts[0];

    // Si la parte entera tiene más de 3 dígitos
    if (integerPart.length > 3) {
        showErrorMessage(input, "El peso no puede tener más de 3 dígitos enteros");
        input.value = integerPart.substring(0, 3);
        if (parts.length > 1) {
            input.value += "." + parts[1];
        }
    }

    // Si hay parte decimal y tiene más de 2 dígitos
    if (parts.length > 1 && parts[1].length > 2) {
        input.value = integerPart + "." + parts[1].substring(0, 2);
    }
}

// Función para validar el campo de altura
function validateHeight(input) {
    // Limpiar cualquier mensaje de error previo
    clearErrorMessage(input);

    const value = input.value.trim();

    // Si el campo está vacío, no hacer nada (la validación de campo requerido se encargará)
    if (!value) return;

    // Validar que el valor no exceda el rango permitido
    const numValue = parseFloat(value);

    // Verificar si el valor es negativo
    if (numValue < 0) {
        showErrorMessage(input, "La altura no puede ser negativa");
        input.value = ""; // Limpiar el valor
        return;
    }

    // Verificar si el valor excede el máximo permitido (300 cm)
    if (numValue > 300) {
        showErrorMessage(input, "La altura no puede exceder 300 cm");
        input.value = "300"; // Establecer al valor máximo
        return;
    }

    // Verificar el formato (máximo 3 dígitos enteros y 2 decimales)
    const parts = value.split('.');
    const integerPart = parts[0];

    // Si la parte entera tiene más de 3 dígitos
    if (integerPart.length > 3) {
        showErrorMessage(input, "La altura no puede tener más de 3 dígitos enteros");
        input.value = integerPart.substring(0, 3);
        if (parts.length > 1) {
            input.value += "." + parts[1];
        }
    }

    // Si hay parte decimal y tiene más de 2 dígitos
    if (parts.length > 1 && parts[1].length > 2) {
        input.value = integerPart + "." + parts[1].substring(0, 2);
    }
}



document.addEventListener('DOMContentLoaded', () => {
    showStep(currentStep);

    // Añadir validación onBlur para todos los campos requeridos
    document.querySelectorAll('input[required], select[required]').forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
    });

    document.getElementById('patient-form').addEventListener('submit', (event) => {
        if (!validateStep(currentStep)) {
            event.preventDefault();
            Swal.fire({
                title: 'Campos incompletos',
                text: 'Por favor, completa todos los campos antes de enviar.',
                icon: 'error',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Entendido'
            });
        } else {
            event.preventDefault();
            Swal.fire({
                title: 'Registro exitoso',
                text: 'Los datos se han guardado correctamente.',
                icon: 'success',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Continuar'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit();
                }
            });
        }
    });

    // Añadir validación en tiempo real para campos específicos

    // Validación para campos de nombre (no deben contener números y máximo 30 caracteres)
    ['firstName', 'lastName', 'motherLastName', 'contactFirstName', 'contactLastName', 'contactMotherLastName'].forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            // Validar en tiempo real mientras el usuario escribe
            input.addEventListener('input', function(e) {
                if (/\d/.test(this.value)) {
                    let fieldTitle = getFieldTitle(this.id);
                    showErrorMessage(this, `El ${fieldTitle} no debe contener números`);
                    // Mantener el foco en este campo si contiene números
                    this.focus();
                } else if (this.value.length > 30) {
                    let fieldTitle = getFieldTitle(this.id);
                    showErrorMessage(this, `El ${fieldTitle} no debe exceder los 30 caracteres`);
                    // Truncar el valor a 30 caracteres
                    this.value = this.value.slice(0, 30);
                } else {
                    clearErrorMessage(this);
                }
            });

            // Capitaliza los nombres al perder el foco
            input.addEventListener('blur', function() {
                // Solo capitalizar si no hay errores
                if (!this.nextElementSibling || !this.nextElementSibling.classList.contains('error-message')) {
                    this.value = this.value
                        .toLowerCase()
                        .split(" ")
                        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                        .join(" ");
                }
            });
        }
    });

    // Validación para correo electrónico
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('input', function() {
            if (this.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value)) {
                showErrorMessage(this, `El Correo Electrónico no tiene un formato válido`);
            } else {
                clearErrorMessage(this);
            }
        });

        // Validar unicidad de email al perder el foco
        emailInput.addEventListener('blur', function() {
            if (this.value.trim() && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value)) {
                const email = this.value.trim();
                const patientIdInput = document.querySelector('input[name="patient_id"]');
                const patientId = patientIdInput ? patientIdInput.value : '';

                // Mostrar indicador de carga
                showErrorMessage(this, "Verificando disponibilidad...");
                this.style.border = "2px solid #FFA500"; // Naranja para indicar verificación en progreso

                const formData = new FormData();
                formData.append('field', 'email');
                formData.append('value', email);
                if (patientId) {
                    formData.append('patientId', patientId);
                }

                fetch('/controllers/patient/check-field-uniqueness.controller.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        clearErrorMessage(this);
                        // Mostrar mensaje de éxito
                        const successElement = document.createElement("span");
                        successElement.classList.add("success-message");
                        successElement.style.color = "green";
                        successElement.textContent = "Email disponible";
                        this.parentNode.appendChild(successElement);
                        // Eliminar el mensaje después de 3 segundos
                        setTimeout(() => {
                            const successMsg = this.parentNode.querySelector('.success-message');
                            if (successMsg) successMsg.remove();
                        }, 3000);
                    } else {
                        showErrorMessage(this, data.message || "Este email ya está registrado");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    clearErrorMessage(this);
                });
            }
        });
    }

    // Validación para número telefónico
    const phoneInput = document.getElementById('phoneNumber');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            // Limitar a 10 dígitos
            if (this.value.length > 10) {
                this.value = this.value.slice(0, 10);
            }

            // Validar que solo contenga dígitos
            if (!/^\d*$/.test(this.value)) {
                showErrorMessage(this, "Solo se permiten números.");
                // Eliminar caracteres no numéricos
                this.value = this.value.replace(/\D/g, '');
            } else if (this.value.length > 0 && this.value.length < 10) {
                showErrorMessage(this, "El número debe tener 10 dígitos.");
            } else if (this.value.length === 10) {
                clearErrorMessage(this);
            }
        });

        // Validar unicidad de número telefónico al perder el foco
        phoneInput.addEventListener('blur', function() {
            if (this.value.trim() && this.value.length === 10) {
                const phone = this.value.trim();
                const patientIdInput = document.querySelector('input[name="patient_id"]');
                const patientId = patientIdInput ? patientIdInput.value : '';

                // Mostrar indicador de carga
                showErrorMessage(this, "Verificando disponibilidad...");
                this.style.border = "2px solid #FFA500"; // Naranja para indicar verificación en progreso

                const formData = new FormData();
                formData.append('field', 'phone');
                formData.append('value', phone);
                if (patientId) {
                    formData.append('patientId', patientId);
                }

                fetch('/controllers/patient/check-field-uniqueness.controller.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        clearErrorMessage(this);
                        // Mostrar mensaje de éxito
                        const successElement = document.createElement("span");
                        successElement.classList.add("success-message");
                        successElement.style.color = "green";
                        successElement.textContent = "Número de teléfono disponible";
                        this.parentNode.appendChild(successElement);
                        // Eliminar el mensaje después de 3 segundos
                        setTimeout(() => {
                            const successMsg = this.parentNode.querySelector('.success-message');
                            if (successMsg) successMsg.remove();
                        }, 3000);
                    } else {
                        showErrorMessage(this, data.message || "Este número de teléfono ya está registrado");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    clearErrorMessage(this);
                });
            }
        });
    }

    // Validación para CURP
    const curpInput = document.getElementById('curp');
    if (curpInput) {
        curpInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
            if (this.value.trim() && !/^[A-Z0-9]{18}$/.test(this.value)) {
                showErrorMessage(this, `La CURP debe tener 18 caracteres alfanuméricos`);
            } else {
                clearErrorMessage(this);
            }
        });

        // Validar unicidad de CURP al perder el foco
        curpInput.addEventListener('blur', function() {
            if (this.value.trim() && /^[A-Z0-9]{18}$/.test(this.value)) {
                const curp = this.value.trim();
                const patientIdInput = document.querySelector('input[name="patient_id"]');
                const patientId = patientIdInput ? patientIdInput.value : '';

                // Mostrar indicador de carga
                showErrorMessage(this, "Verificando disponibilidad...");
                this.style.border = "2px solid #FFA500"; // Naranja para indicar verificación en progreso

                const formData = new FormData();
                formData.append('field', 'CURP');
                formData.append('value', curp);
                if (patientId) {
                    formData.append('patientId', patientId);
                }

                fetch('/controllers/patient/check-field-uniqueness.controller.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        clearErrorMessage(this);
                        // Mostrar mensaje de éxito
                        const successElement = document.createElement("span");
                        successElement.classList.add("success-message");
                        successElement.style.color = "green";
                        successElement.textContent = "CURP disponible";
                        this.parentNode.appendChild(successElement);
                        // Eliminar el mensaje después de 3 segundos
                        setTimeout(() => {
                            const successMsg = this.parentNode.querySelector('.success-message');
                            if (successMsg) successMsg.remove();
                        }, 3000);
                    } else {
                        showErrorMessage(this, data.message || "Esta CURP ya está registrada");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    clearErrorMessage(this);
                });
            }
        });
    }

    // Validación para RFC
    const rfcInput = document.getElementById('rfc');
    if (rfcInput) {
        rfcInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
            if (this.value.trim() && !/^[A-Z0-9]{12,13}$/.test(this.value)) {
                showErrorMessage(this, `El RFC debe tener 12 o 13 caracteres alfanuméricos`);
            } else {
                clearErrorMessage(this);
            }
        });

        // Validar unicidad de RFC al perder el foco
        rfcInput.addEventListener('blur', function() {
            if (this.value.trim() && /^[A-Z0-9]{12,13}$/.test(this.value)) {
                const rfc = this.value.trim();
                const patientIdInput = document.querySelector('input[name="patient_id"]');
                const patientId = patientIdInput ? patientIdInput.value : '';

                // Mostrar indicador de carga
                showErrorMessage(this, "Verificando disponibilidad...");
                this.style.border = "2px solid #FFA500"; // Naranja para indicar verificación en progreso

                const formData = new FormData();
                formData.append('field', 'RFC');
                formData.append('value', rfc);
                if (patientId) {
                    formData.append('patientId', patientId);
                }

                fetch('/controllers/patient/check-field-uniqueness.controller.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        clearErrorMessage(this);
                        // Mostrar mensaje de éxito
                        const successElement = document.createElement("span");
                        successElement.classList.add("success-message");
                        successElement.style.color = "green";
                        successElement.textContent = "RFC disponible";
                        this.parentNode.appendChild(successElement);
                        // Eliminar el mensaje después de 3 segundos
                        setTimeout(() => {
                            const successMsg = this.parentNode.querySelector('.success-message');
                            if (successMsg) successMsg.remove();
                        }, 3000);
                    } else {
                        showErrorMessage(this, data.message || "Este RFC ya está registrado");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    clearErrorMessage(this);
                });
            }
        });
    }

    // Validación para número de afiliación
    const affiliationInput = document.getElementById('affiliationNumber');
    if (affiliationInput) {
        affiliationInput.addEventListener('input', function() {
            if (this.value.trim() && !/^[A-Za-z0-9]+$/.test(this.value)) {
                showErrorMessage(this, `El Número de Afiliación solo debe contener letras y números`);
            } else {
                clearErrorMessage(this);
            }
        });

        // Validar unicidad de número de afiliación al perder el foco
        affiliationInput.addEventListener('blur', function() {
            if (this.value.trim() && /^[A-Za-z0-9]+$/.test(this.value)) {
                const affiliationNumber = this.value.trim();
                const patientIdInput = document.querySelector('input[name="patient_id"]');
                const patientId = patientIdInput ? patientIdInput.value : '';

                // Mostrar indicador de carga
                showErrorMessage(this, "Verificando disponibilidad...");
                this.style.border = "2px solid #FFA500"; // Naranja para indicar verificación en progreso

                const formData = new FormData();
                formData.append('field', 'insurance_number');
                formData.append('value', affiliationNumber);
                if (patientId) {
                    formData.append('patientId', patientId);
                }

                fetch('/controllers/patient/check-field-uniqueness.controller.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        clearErrorMessage(this);
                        // Mostrar mensaje de éxito
                        const successElement = document.createElement("span");
                        successElement.classList.add("success-message");
                        successElement.style.color = "green";
                        successElement.textContent = "Número de afiliación disponible";
                        this.parentNode.appendChild(successElement);
                        // Eliminar el mensaje después de 3 segundos
                        setTimeout(() => {
                            const successMsg = this.parentNode.querySelector('.success-message');
                            if (successMsg) successMsg.remove();
                        }, 3000);
                    } else {
                        showErrorMessage(this, data.message || "Este número de afiliación ya está registrado");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    clearErrorMessage(this);
                });
            }
        });
    }

    // Validación para campos del segundo paso (no deben contener caracteres especiales)
    ['street', 'neighborhood', 'extNumber', 'intNumber'].forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            // Validar en tiempo real mientras el usuario escribe
            input.addEventListener('input', function(e) {
                if (/[^A-Za-z0-9\s]/.test(this.value)) {
                    let fieldTitle = getFieldTitle(this.id);
                    showErrorMessage(this, `El campo ${fieldTitle} no debe contener caracteres especiales`);
                    // Mantener el foco en este campo si contiene caracteres especiales
                    this.focus();
                } else {
                    clearErrorMessage(this);
                }
            });
        }
    });
});
