let currentStep = 1;
function showStep(step) {
    console.log(`🔄 Intentando mostrar el paso: ${step}`);

    let stepElement = document.getElementById(`step-${step}`);
    if (!stepElement) {
        console.error(`❌ Elemento con ID 'step-${step}' no encontrado.`);
        return;
    }

    // Ocultar todos los pasos
    document.querySelectorAll('.form-step').forEach((formStep) => {
        formStep.style.display = 'none';
    });

    // Mostrar el paso actual
    stepElement.style.display = 'block';
    console.log(`✅ Mostrando paso: ${step}`);

    // Quitar la clase 'step-active' de todos los pasos
    document.querySelectorAll('.step').forEach((stepElement) => {
        stepElement.classList.remove('step-active');
    });

    // Agregar 'step-active' al paso actual
    let activeStep = document.querySelector(`.step[data-step='${step}']`);
    if (activeStep) {
        activeStep.classList.add('step-active');
        console.log(`🎯 Paso ${step} marcado como activo.`);
    } else {
        console.warn(`⚠️ No se encontró el paso con data-step='${step}'`);
    }
}

function prevStep(step) {
    currentStep = step;
    showStep(currentStep);
}

// Función para mostrar mensajes de error dinámicos
function showErrorMessage(input, message) {
    let errorElement = input.nextElementSibling;
    
    if (!errorElement || !errorElement.classList.contains('error-message')) {
        errorElement = document.createElement('span');
        errorElement.classList.add('error-message');
        errorElement.style.color = 'red';
        input.parentNode.appendChild(errorElement);
    }
    
    errorElement.textContent = message;
    input.style.border = '2px solid red';
}

// Función para limpiar mensajes de error
function clearErrorMessage(input) {
    let errorElement = input.nextElementSibling;
    
    if (errorElement && errorElement.classList.contains('error-message')) {
        errorElement.remove();
    }

    input.style.border = '2px solid var(--line-clr)';
}

// Función de validación para el Paso 1
function validateStep1() {
    let valid = true;

    // Validar nombres y apellidos (solo letras y espacios)
    document.querySelectorAll("#step-1 input[type='text']").forEach((input) => {
        let namePattern = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;
        if (!namePattern.test(input.value.trim())) {
            showErrorMessage(input, 'Solo se permiten letras y espacios.');
            valid = false;
        } else {
            clearErrorMessage(input);
        }
    });

    // Validar número telefónico (10 dígitos exactos)
    let phoneInput = document.getElementById('phoneNumber');
    if (!/^\d{10}$/.test(phoneInput.value.trim())) {
        showErrorMessage(phoneInput, 'El número debe tener 10 dígitos.');
        valid = false;
    } else {
        clearErrorMessage(phoneInput);
    }

    // Validar correo electrónico
    let emailInput = document.getElementById('email');
    let emailValue = emailInput.value.trim();
    let emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    
    if (!emailPattern.test(emailValue)) {
        showErrorMessage(emailInput, 'Correo electrónico no válido.');
        valid = false;
    } else {
        clearErrorMessage(emailInput);
    }

    // Validar selección de género
    let genderSelect = document.getElementById('gender');
    if (genderSelect.value === '') {
        showErrorMessage(genderSelect, 'Debe seleccionar un género.');
        valid = false;
    } else {
        clearErrorMessage(genderSelect);
    }

    // Validar fecha de nacimiento (no vacía y menor a la fecha actual)
    let birthDateInput = document.getElementById('birthDate');
    let birthDateValue = birthDateInput.value;
    
    if (!birthDateValue) {
        showErrorMessage(birthDateInput, 'Debe seleccionar una fecha de nacimiento.');
        valid = false;
    } else {
        let birthDate = new Date(birthDateValue);
        let today = new Date();
        if (birthDate >= today) {
            showErrorMessage(birthDateInput, 'Debe ser una fecha pasada.');
            valid = false;
        } else {
            clearErrorMessage(birthDateInput);
        }
    }

    return valid;
}
// Función de validación para el Paso 2
function validateStep2() {
    let valid = true;

    // Validar texto para Calle, Colonia y Número Exterior (solo letras, números y espacios)
    document.querySelectorAll("#step-2 input[type='text']").forEach((input) => {
        let pattern = /^[A-Za-z0-9ÁÉÍÓÚáéíóúÑñ\s]+$/;

        // Excluir Número Interior de validación si está vacío
        if (input.id === 'intNumber' && input.value.trim() === '') {
            clearErrorMessage(input); // No mostrar error si está vacío
            return;
        }

        if (!pattern.test(input.value.trim())) {
            showErrorMessage(input, 'Solo se permiten letras, números y espacios.');
            valid = false;
        } else {
            clearErrorMessage(input);
        }
    });

    // Validar Código Postal (exactamente 5 dígitos numéricos)
    let postalCodeInput = document.getElementById('postalCode');
    if (!/^\d{5}$/.test(postalCodeInput.value.trim())) {
        showErrorMessage(postalCodeInput, 'El código postal debe tener 5 dígitos.');
        valid = false;
    } else {
        clearErrorMessage(postalCodeInput);
    }

    return valid;
}
// Función de validación para el Paso 3
function validateStep3() {
    let valid = true;

    console.log("Validando Paso 3..."); // Depuración

    // Validar CURP (18 caracteres, formato oficial)
    let curpInput = document.getElementById('curp');
    let curpPattern = /^[A-Z]{4}\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]\d$/;
    const curp = curpInput.value.trim();
    if (curp.length !== 18) {
        showErrorMessage(curpInput, 'La CURP debe tener exactamente 18 caracteres.');
        valid = false;
    } else if (!curpPattern.test(curp)) {
        showErrorMessage(curpInput, 'CURP inválida. Revisa el formato.');
        valid = false;
    } else {
        clearErrorMessage(curpInput);
    }
    

    // Validar RFC (12 o 13 caracteres)
    let rfcInput = document.getElementById('rfc');
    let rfcPattern = /^[A-ZÑ&]{3,4}\d{6}[A-Z\d]{3}$/i;
    if (rfcInput.value.trim() !== '' && !rfcPattern.test(rfcInput.value.trim())) {
        showErrorMessage(rfcInput, 'RFC inválido. Debe tener entre 12 y 13 caracteres.');
        valid = false;
    } else {
        clearErrorMessage(rfcInput);
    }

    // Validar Número de Afiliación (solo letras y números, opcional)
    let affiliationInput = document.getElementById('affiliationNumber');
    let textNumberPattern = /^[A-Za-z0-9]+$/;
    if (affiliationInput.value.trim() !== '' && !textNumberPattern.test(affiliationInput.value.trim())) {
        showErrorMessage(affiliationInput, 'El número de afiliación solo puede contener letras y números.');
        valid = false;
    } else {
        clearErrorMessage(affiliationInput);
    }

    // Validar Cédula Profesional (solo letras y números, opcional)
    let licenseInput = document.getElementById('professionalLicense');
    if (licenseInput.value.trim() !== '' && !textNumberPattern.test(licenseInput.value.trim())) {
        showErrorMessage(licenseInput, 'La cédula profesional solo puede contener letras y números.');
        valid = false;
    } else {
        clearErrorMessage(licenseInput);
    }

    // Validar Foto (si es requerida y es un archivo de imagen)
    let photoInput = document.getElementById('photo');
    if (photoInput) {
        if (photoInput.hasAttribute('required') && photoInput.files.length === 0) {
            showErrorMessage(photoInput, 'Debe seleccionar una foto.');
            valid = false;
        } else if (photoInput.files.length > 0) {
            let file = photoInput.files[0];
            if (!file.type.startsWith('image/')) {
                showErrorMessage(photoInput, 'El archivo debe ser una imagen.');
                valid = false;
            } else {
                clearErrorMessage(photoInput);
            }
        }
    }

    console.log("Paso 3 validación:", valid); // Depuración
    return valid;
}

window.nextStep = function (step) {
    console.log("Paso actual antes de validar:", currentStep); // 🛠 Depuración

    if (currentStep === 1 && !validateStep1()) {
        alert('⚠️ Corrige los errores antes de continuar.');
        return;
    }

    if (currentStep === 2 && !validateStep2()) {
        alert('⚠️ Corrige los errores antes de continuar.');
        return;
    }

    if (currentStep === 3 && !validateStep3()) {
        console.log("Entrando a validateStep3()..."); // 🛠 Depuración
        alert('⚠️ Corrige los errores antes de continuar.');
        return;
    }

    // 📌 Actualizar currentStep antes de cambiar el paso
    console.log("Cambiando de paso:", currentStep, "➡️", step);
    currentStep = step;

    // 📌 Mostrar el nuevo paso en la interfaz
    showStep(currentStep);
};


// Validación antes de enviar el formulario
document.addEventListener('DOMContentLoaded', () => {
    const firstNameInput = document.getElementById('firstName');

    firstNameInput.addEventListener('blur', () => {
      const words = firstNameInput.value.toLowerCase().split(" ");
      firstNameInput.value = words.map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(" ");
    });

    const lastNameInput = document.getElementById('lastName');

    lastNameInput.addEventListener('blur', () => {
      const words = lastNameInput.value.toLowerCase().split(" ");
      lastNameInput.value = words.map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(" ");
    });

    const motherLastNameInput = document.getElementById('motherLastName');

    motherLastNameInput.addEventListener('blur', () => {
      const words = motherLastNameInput.value.toLowerCase().split(" ");
      motherLastNameInput.value = words.map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(" ");
    });


    if (document.getElementById('doctor-form')) {
        showStep(currentStep);

        document.getElementById('doctor-form').addEventListener('submit', (event) => {
            console.log("Enviando formulario..."); // Depuración

            if (!validateStep1() || !validateStep2() || !validateStep3()) {
                event.preventDefault();
                alert('Por favor, completa todos los campos antes de enviar.');
            }
        });
    } else {
        console.error("Formulario 'doctor-form' no encontrado en el DOM.");
    }
});