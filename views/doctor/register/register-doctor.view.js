// ===========================
//  VALIDACIÓN DE INPUTS DE IDENTIFICACIÓN
// ===========================

// Función que valida CURP, RFC, foto, número de afiliación y cédula profesional.
function validateIdentificationInputs() {
    let valid = true;
    // --- Validar CURP ---
    const curpInput = document.getElementById('curp');
    const curp = curpInput.value.trim();
    const curpPattern = /^[A-Z]{4}\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]\d$/;
    if (curp.length !== 18) {
        showErrorMessage(curpInput, 'La CURP debe tener exactamente 18 caracteres.');
        valid = false;
    } else if (!curpPattern.test(curp)) {
        showErrorMessage(curpInput, 'CURP inválida. Revisa el formato.');
        valid = false;
    } else {
        clearErrorMessage(curpInput);
    }

    // --- Validar RFC (opcional) ---
    const rfcInput = document.getElementById('rfc');
    const rfcPattern = /^[A-ZÑ&]{3,4}\d{6}[A-Z\d]{3}$/i;
    if (rfcInput.value.trim() !== '' && !rfcPattern.test(rfcInput.value.trim())) {
        showErrorMessage(rfcInput, 'RFC inválido. Debe tener entre 12 y 13 caracteres.');
        valid = false;
    } else {
        clearErrorMessage(rfcInput);
    }

    // --- Validar archivo de foto ---
    // Ejemplo: Se verifica que exista, sea una imagen y pese menos de 2MB.
    const photoInput = document.getElementById('photo');
    const photoFile = photoInput.files[0];
    if (!photoFile) {
        showErrorMessage(photoInput, 'Debes seleccionar una imagen.');
        valid = false;
    } else if (!photoFile.type.startsWith('image/')) {
        showErrorMessage(photoInput, 'El archivo debe ser una imagen.');
        valid = false;
    } else if (photoFile.size > 2 * 1024 * 1024) { // 2MB
        showErrorMessage(photoInput, 'La imagen no debe pesar más de 2MB.');
        valid = false;
    } else {
        clearErrorMessage(photoInput);
    }
    doctor
    // Patrón alfanumérico para campos opcionales
    const alphanumericPattern = /^[A-Za-z0-9]+$/;

    // --- Validar Número de Afiliación (opcional) ---
    const affiliationInput = document.getElementById('affiliationNumber');
    if (affiliationInput.value.trim() !== '' && !alphanumericPattern.test(affiliationInput.value.trim())) {
        showErrorMessage(affiliationInput, 'El número de afiliación solo puede contener letras y números.');
        valid = false;
    } else {
        clearErrorMessage(affiliationInput);
    }

    // --- Validar Cédula Profesional (opcional) ---
    const licenseInput = document.getElementById('professionalLicense');
    if (licenseInput.value.trim() !== '' && !alphanumericPattern.test(licenseInput.value.trim())) {
        showErrorMessage(licenseInput, 'La cédula profesional solo puede contener letras y números.');
        valid = false;
    } else {
        clearErrorMessage(licenseInput);
    }

    return valid;
}

// ===========================
// ACTUALIZACIÓN DE SELECTS DE UBICACIÓN
// ===========================






// ===========================
// ACTUALIZACIÓN DE SELECTS DE UBICACIÓN
// ===========================

const municipalities = window.municipalities;
const localities = window.localities;
const states = window.states;
const preselected = window.preselectedDoctorData || {};

const stateSelect = document.getElementById('state');
const municipalitySelect = document.getElementById('municipality');
const localitySelect = document.getElementById('locality');

/**
 * Llena el select de estados.
 */
const fillStates = () => {
    stateSelect.innerHTML = '<option value="">Seleccione...</option>';
    states.forEach(s => {
        stateSelect.innerHTML += `<option value="${s.id}">${s.name}</option>`;
    });

    if (preselected.state) {
        stateSelect.value = preselected.state;
    }
};

/**
 * Llena municipios según el estado seleccionado.
 */
const updateMunicipalities = () => {
    const selectedState = stateSelect.value;
    municipalitySelect.innerHTML = '<option value="">Seleccione...</option>';
    localitySelect.innerHTML = '<option value="">Seleccione un municipio primero...</option>';

    if (selectedState) {
        municipalities
            .filter(m => m.id_state == selectedState)
            .forEach(m => {
                municipalitySelect.innerHTML += `<option value="${m.id}">${m.name}</option>`;
            });

        if (preselected.municipality) {
            municipalitySelect.value = preselected.municipality;
        }
    }
};

/**
 * Llena localidades según el municipio seleccionado.
 */
const updateLocalities = () => {
    const selectedState = stateSelect.value;
    const selectedMunicipality = municipalitySelect.value;
    localitySelect.innerHTML = '<option value="">Seleccione...</option>';

    if (selectedMunicipality) {
        localities
            .filter(l => l.id_state == selectedState && l.id_municipality == selectedMunicipality)
            .forEach(l => {
                localitySelect.innerHTML += `<option value="${l.id}">${l.name}</option>`;
            });

        if (preselected.locality) {
            localitySelect.value = preselected.locality;
        }
    }
};

// Evento DOMContentLoaded para ejecutar todo en orden
document.addEventListener("DOMContentLoaded", function () {
    fillStates();            // Llenamos estados
    updateMunicipalities();  // Llenamos municipios
    updateLocalities();      // Llenamos localidades

    // Asociar eventos luego de rellenar selects
    stateSelect.addEventListener('change', () => {
        preselected.municipality = null;
        preselected.locality = null;
        updateMunicipalities();
    });

    municipalitySelect.addEventListener('change', () => {
        preselected.locality = null;
        updateLocalities();
    });
});































// ===========================
// ACTUALIZACIÓN DE LA ETIQUETA DE FOTO
// ===========================
document.getElementById('photo').addEventListener('change', event => {
    const fileName = event.target.files[0] ? event.target.files[0].name : 'Subir Foto';
    document.getElementById('photo-label').textContent = fileName;
});

// ===========================
// VALIDACIÓN EN TIEMPO REAL (onBlur)
// ===========================

// Añadir validación onBlur para los campos de identificación
document.addEventListener('DOMContentLoaded', () => {
    // Validar CURP al perder el foco
    const curpInput = document.getElementById('curp');
    if (curpInput) {
        curpInput.addEventListener('blur', () => {
            const curp = curpInput.value.trim();
            const curpPattern = /^[A-Z]{4}\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]\d$/;
            if (curp.length !== 18) {
                showErrorMessage(curpInput, 'La CURP debe tener exactamente 18 caracteres.');
            } else if (!curpPattern.test(curp)) {
                showErrorMessage(curpInput, 'CURP inválida. Revisa el formato.');
            } else {
                clearErrorMessage(curpInput);
            }
        });
    }

    // Validar RFC al perder el foco
    const rfcInput = document.getElementById('rfc');
    if (rfcInput) {
        rfcInput.addEventListener('blur', () => {
            const rfcPattern = /^[A-ZÑ&]{3,4}\d{6}[A-Z\d]{3}$/i;
            if (rfcInput.value.trim() !== '' && !rfcPattern.test(rfcInput.value.trim())) {
                showErrorMessage(rfcInput, 'RFC inválido. Debe tener entre 12 y 13 caracteres.');
            } else {
                clearErrorMessage(rfcInput);
            }
        });
    }

    // Validar Número de Afiliación al perder el foco
    const affiliationInput = document.getElementById('affiliationNumber');
    if (affiliationInput) {
        affiliationInput.addEventListener('blur', () => {
            const alphanumericPattern = /^[A-Za-z0-9]+$/;
            if (affiliationInput.value.trim() !== '' && !alphanumericPattern.test(affiliationInput.value.trim())) {
                showErrorMessage(affiliationInput, 'El número de afiliación solo puede contener letras y números.');
            } else {
                clearErrorMessage(affiliationInput);
            }
        });
    }

    // Validar Cédula Profesional al perder el foco
    const licenseInput = document.getElementById('professionalLicense');
    if (licenseInput) {
        licenseInput.addEventListener('blur', () => {
            const alphanumericPattern = /^[A-Za-z0-9]+$/;
            if (licenseInput.value.trim() !== '' && !alphanumericPattern.test(licenseInput.value.trim())) {
                showErrorMessage(licenseInput, 'La cédula profesional solo puede contener letras y números.');
            } else {
                clearErrorMessage(licenseInput);
            }
        });
    }
});

// ===========================
// MANEJO DEL ENVÍO DEL FORMULARIO
// ===========================
document.querySelector('form').addEventListener('submit', e => {
    if (!validateIdentificationInputs()) {
        e.preventDefault();
    }
});
