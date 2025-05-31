// ===========================
// Estado Global
// ===========================

let currentStep = 1; // Guarda el paso actual del formulario

// ===========================
// Funciones de Visualización
// ===========================

/**
 * Muestra el paso especificado del formulario.
 * Oculta todos los pasos y resalta el indicador del paso activo.
 *
 * @param {number} step - Número del paso a mostrar.
 */
function showStep(step) {
  const stepElement = document.getElementById(`step-${step}`);
  if (!stepElement) {
    console.error(`❌ Elemento con ID 'step-${step}' no encontrado.`);
    return;
  }

  // Ocultar todos los pasos
  document.querySelectorAll(".form-step").forEach((formStep) => {
    formStep.style.display = "none";
  });

  // Mostrar el paso actual
  stepElement.style.display = "block";

  // Remover la clase activa de todos los indicadores
  document
    .querySelectorAll(".step")
    .forEach((el) => el.classList.remove("step-active"));

  // Resaltar el indicador del paso actual
  const activeIndicator = document.querySelector(`.step[data-step='${step}']`);
  if (activeIndicator) {
    activeIndicator.classList.add("step-active");
  } else {
    console.warn(`⚠️ No se encontró el paso con data-step='${step}'`);
  }
}

/**
 * Retrocede al paso indicado.
 *
 * @param {number} step - Número del paso al que se desea regresar.
 */
function prevStep(step) {
  currentStep = step;
  showStep(currentStep);
}

// ===========================
// Funciones de Manejo de Errores
// ===========================

/**
 * Muestra un mensaje de error dinámico junto a un input.
 *
 * @param {HTMLElement} input - Elemento input al que se le asigna el error.
 * @param {string} message - Mensaje de error a mostrar.
 */
function showErrorMessage(input, message) {
  let errorElement = input.nextElementSibling;
  if (!errorElement || !errorElement.classList.contains("error-message")) {
    errorElement = document.createElement("span");
    errorElement.classList.add("error-message");
    errorElement.style.color = "red";
    input.parentNode.appendChild(errorElement);
  }
  errorElement.textContent = message;
  input.style.border = "2px solid red";
}

/**
 * Limpia el mensaje de error mostrado en un input.
 *
 * @param {HTMLElement} input - Elemento input del cual se elimina el error.
 */
function clearErrorMessage(input) {
  let errorElement = input.nextElementSibling;
  if (errorElement && errorElement.classList.contains("error-message")) {
    errorElement.remove();
  }
  input.style.border = "2px solid var(--line-clr)";
}

function validateScheduleDays() {
  let hasValidSchedule = false;

  days.forEach((day) => {
    const checkbox = document.querySelector(`#${day}_active`);
    const startInput = document.querySelector(`#${day}_start`);
    const endInput = document.querySelector(`#${day}_end`);

    if (
      checkbox.checked &&
      startInput.value &&
      endInput.value &&
      startInput.value < endInput.value
    ) {
      hasValidSchedule = true;
    }
  });

  if (!hasValidSchedule) {
    alert("Debe ingresar al menos un horario válido para poder continuar.");
    return false;
  }

  return true;
}

// ===========================
// Función Genérica de Validación de Inputs de Texto
// ===========================

/**
 * Valida inputs de tipo texto dentro de un contenedor dado usando una expresión regular.
 *
 * @param {string} containerId - ID del contenedor que agrupa los inputs.
 * @param {RegExp} pattern - Expresión regular para validar el contenido.
 * @param {Function} errorMsgCallback - Función que retorna el mensaje de error.
 * @param {Function} [skipCondition] - Función opcional para determinar si se debe omitir la validación de un input.
 * @returns {boolean} Verdadero si todos los inputs son válidos.
 */
function validateTextInputs(
  containerId,
  pattern,
  errorMsgCallback,
  skipCondition
) {
  let valid = true;
  document
    .querySelectorAll(`#${containerId} input[type='text']`)
    .forEach((input) => {
      if (skipCondition && skipCondition(input)) {
        clearErrorMessage(input);
        return;
      }
      if (!pattern.test(input.value.trim())) {
        showErrorMessage(input, errorMsgCallback());
        valid = false;
      } else {
        clearErrorMessage(input);
      }
    });
  return valid;
}

// ===========================
// Validaciones de Cada Paso
// ===========================

/**
 * Valida los campos del Paso 1: nombres, teléfono, correo, género y fecha de nacimiento.
 *
 * @returns {boolean} Verdadero si la validación es exitosa.
 */
function validateStep1() {
  let valid = true;

  // Validar nombres y apellidos: solo letras y espacios.
  const namePattern = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;
  valid =
    validateTextInputs(
      "step-1",
      namePattern,
      () => "Solo se permiten letras y espacios."
    ) && valid;

  // Validar número telefónico: exactamente 10 dígitos.
  const phoneInput = document.getElementById("phoneNumber");
  if (!/^\d{10}$/.test(phoneInput.value.trim())) {
    showErrorMessage(phoneInput, "El número debe tener 10 dígitos.");
    valid = false;
  } else {
    clearErrorMessage(phoneInput);
  }

  // Validar correo electrónico.
  const emailInput = document.getElementById("email");
  const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
  if (!emailPattern.test(emailInput.value.trim())) {
    showErrorMessage(emailInput, "Correo electrónico no válido.");
    valid = false;
  } else {
    clearErrorMessage(emailInput);
  }

  // Validar selección de género.
  const genderSelect = document.getElementById("gender");
  if (genderSelect.value === "") {
    showErrorMessage(genderSelect, "Debe seleccionar un género.");
    valid = false;
  } else {
    clearErrorMessage(genderSelect);
  }

  // Validar fecha de nacimiento: no vacía y debe ser en el pasado.
  const birthDateInput = document.getElementById("birthDate");
  if (!birthDateInput.value) {
    showErrorMessage(
      birthDateInput,
      "Debe seleccionar una fecha de nacimiento."
    );
    valid = false;
  } else {
    const birthDate = new Date(birthDateInput.value);
    const today = new Date();
    if (birthDate >= today) {
      showErrorMessage(birthDateInput, "Debe ser una fecha pasada.");
      valid = false;
    } else {
      clearErrorMessage(birthDateInput);
    }
  }

  return valid;
}

/**
 * Valida los campos del Paso 2: dirección y código postal.
 *
 * @returns {boolean} Verdadero si la validación es exitosa.
 */
function validateStep2() {
  let valid = true;

  // Validar campos de dirección: solo letras, números y espacios.
  const pattern = /^[A-Za-z0-9ÁÉÍÓÚáéíóúÑñ\s]+$/;
  valid =
    validateTextInputs(
      "step-2",
      pattern,
      () => "Solo se permiten letras, números y espacios.",
      (input) => input.id === "intNumber" && input.value.trim() === "" // Salta validación para "Número Interior" si está vacío
    ) && valid;

  // Validar Código Postal: exactamente 5 dígitos numéricos.
  const postalCodeInput = document.getElementById("postalCode");
  if (!/^\d{5}$/.test(postalCodeInput.value.trim())) {
    showErrorMessage(postalCodeInput, "El código postal debe tener 5 dígitos.");
    valid = false;
  } else {
    clearErrorMessage(postalCodeInput);
  }

  return valid;
}

/**
 * Valida los campos del Paso 3: CURP, RFC, número de afiliación, cédula profesional y foto.
 *
 * @returns {boolean} Verdadero si la validación es exitosa.
 */
function validateStep3() {
  let valid = true;

  // Validar CURP: debe tener 18 caracteres y formato oficial.
  const curpInput = document.getElementById("curp");
  const curpPattern =
    /^[A-Z]{4}\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]\d$/;
  const curp = curpInput.value.trim();
  if (curp.length !== 18) {
    showErrorMessage(
      curpInput,
      "La CURP debe tener exactamente 18 caracteres."
    );
    valid = false;
  } else if (!curpPattern.test(curp)) {
    showErrorMessage(curpInput, "CURP inválida. Revisa el formato.");
    valid = false;
  } else {
    clearErrorMessage(curpInput);
  }

  // Validar RFC: opcional, pero si se llena debe cumplir el formato.
  const rfcInput = document.getElementById("rfc");
  const rfcPattern = /^[A-ZÑ&]{3,4}\d{6}[A-Z\d]{3}$/i;
  if (rfcInput.value.trim() !== "" && !rfcPattern.test(rfcInput.value.trim())) {
    showErrorMessage(
      rfcInput,
      "RFC inválido. Debe tener entre 12 y 13 caracteres."
    );
    valid = false;
  } else {
    clearErrorMessage(rfcInput);
  }

  // Validar Número de Afiliación: opcional, solo alfanumérico.
  const affiliationInput = document.getElementById("affiliationNumber");
  const alphanumericPattern = /^[A-Za-z0-9]+$/;
  if (
    affiliationInput.value.trim() !== "" &&
    !alphanumericPattern.test(affiliationInput.value.trim())
  ) {
    showErrorMessage(
      affiliationInput,
      "El número de afiliación solo puede contener letras y números."
    );
    valid = false;
  } else {
    clearErrorMessage(affiliationInput);
  }

  // Validar Cédula Profesional: opcional, solo alfanumérico.
  const licenseInput = document.getElementById("professionalLicense");
  if (
    licenseInput.value.trim() !== "" &&
    !alphanumericPattern.test(licenseInput.value.trim())
  ) {
    showErrorMessage(
      licenseInput,
      "La cédula profesional solo puede contener letras y números."
    );
    valid = false;
  } else {
    clearErrorMessage(licenseInput);
  }

  // Validar Foto: si es requerida, debe ser un archivo de imagen.
  const photoInput = document.getElementById("photo");
  if (photoInput) {
    if (photoInput.files.length === 0) {
      valid = false;
    } else {
      const file = photoInput.files[0];
      if (!file.type.startsWith("image/")) {
        showErrorMessage(photoInput, "El archivo debe ser una imagen.");
        valid = false;
      } else {
        clearErrorMessage(photoInput);
      }
    }
  }

  return valid;
}

// ===========================
// Función para Avanzar de Paso
// ===========================

/**
 * Valida el paso actual y, si es correcto, avanza al siguiente.
 *
 * @param {number} step - Número del siguiente paso.
 */
window.nextStep = function (step) {
  // Validar el paso actual antes de avanzar
  if (currentStep === 1 && !validateStep1()) {
    alert("⚠️ Corrige los errores antes de continuar.");
    return;
  }
  if (currentStep === 2 && !validateStep2()) {
    alert("⚠️ Corrige los errores antes de continuar.");
    return;
  }
  if (currentStep === 3 && !validateStep3()) {
    alert("⚠️ Corrige los errores antes de continuar.");
    return;
  }

  currentStep = step;
  showStep(currentStep);
};

// ===========================
// Inicialización al Cargar el DOM
// ===========================

document.addEventListener("DOMContentLoaded", () => {
  // Capitaliza los nombres al perder el foco
  ["firstName", "lastName", "motherLastName"].forEach((id) => {
    const input = document.getElementById(id);
    if (input) {
      input.addEventListener("blur", () => {
        input.value = input.value
          .toLowerCase()
          .split(" ")
          .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
          .join(" ");
      });
    }
  });

  // Añadir validación onBlur para los campos del paso 1
  document
    .querySelectorAll("#step-1 input, #step-1 select")
    .forEach((input) => {
      input.addEventListener("blur", () => {
        // Validar el campo específico que perdió el foco
        if (input.id === "phoneNumber") {
          if (!/^\d{10}$/.test(input.value.trim())) {
            showErrorMessage(input, "El número debe tener 10 dígitos.");
          } else {
            clearErrorMessage(input);
          }
        } else if (input.id === "email") {
          const emailPattern =
            /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
          if (!emailPattern.test(input.value.trim())) {
            showErrorMessage(input, "Correo electrónico no válido.");
          } else {
            clearErrorMessage(input);
          }
        } else if (input.id === "gender") {
          if (input.value === "") {
            showErrorMessage(input, "Debe seleccionar un género.");
          } else {
            clearErrorMessage(input);
          }
        } else if (input.id === "birthDate") {
          if (!input.value) {
            showErrorMessage(
              input,
              "Debe seleccionar una fecha de nacimiento."
            );
          } else {
            const birthDate = new Date(input.value);
            const today = new Date();
            if (birthDate >= today) {
              showErrorMessage(input, "Debe ser una fecha pasada.");
            } else {
              clearErrorMessage(input);
            }
          }
        } else if (input.type === "text") {
          // Para otros campos de texto (nombres, etc.)
          const namePattern = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;
          if (!namePattern.test(input.value.trim())) {
            showErrorMessage(input, "Solo se permiten letras y espacios.");
          } else {
            clearErrorMessage(input);
          }
        }
      });
    });

  // Añadir validación onBlur para los campos del paso 2
  document.querySelectorAll("#step-2 input").forEach((input) => {
    input.addEventListener("blur", () => {
      if (input.id === "postalCode") {
        if (!/^\d{5}$/.test(input.value.trim())) {
          showErrorMessage(input, "El código postal debe tener 5 dígitos.");
        } else {
          clearErrorMessage(input);
        }
      } else if (input.id !== "intNumber" || input.value.trim() !== "") {
        // Para otros campos de dirección, excepto número interior vacío
        const pattern = /^[A-Za-z0-9ÁÉÍÓÚáéíóúÑñ\s]+$/;
        if (!pattern.test(input.value.trim())) {
          showErrorMessage(
            input,
            "Solo se permiten letras, números y espacios."
          );
        } else {
          clearErrorMessage(input);
        }
      }
    });
  });

  // Añadir validación onBlur para los campos del paso 3
  document.querySelectorAll("#step-3 input").forEach((input) => {
    input.addEventListener("blur", () => {
      if (input.id === "curp") {
        const curpPattern =
          /^[A-Z]{4}\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]\d$/;
        const curp = input.value.trim();
        if (curp.length !== 18) {
          showErrorMessage(
            input,
            "La CURP debe tener exactamente 18 caracteres."
          );
        } else if (!curpPattern.test(curp)) {
          showErrorMessage(input, "CURP inválida. Revisa el formato.");
        } else {
          clearErrorMessage(input);
        }
      } else if (input.id === "rfc" && input.value.trim() !== "") {
        const rfcPattern = /^[A-ZÑ&]{3,4}\d{6}[A-Z\d]{3}$/i;
        if (!rfcPattern.test(input.value.trim())) {
          showErrorMessage(
            input,
            "RFC inválido. Debe tener entre 12 y 13 caracteres."
          );
        } else {
          clearErrorMessage(input);
        }
      } else if (
        (input.id === "affiliationNumber" ||
          input.id === "professionalLicense") &&
        input.value.trim() !== ""
      ) {
        const alphanumericPattern = /^[A-Za-z0-9]+$/;
        if (!alphanumericPattern.test(input.value.trim())) {
          showErrorMessage(input, "Solo puede contener letras y números.");
        } else {
          clearErrorMessage(input);
        }
      } else if (input.id === "photo" && input.files.length > 0) {
        const file = input.files[0];
        if (!file.type.startsWith("image/")) {
          showErrorMessage(input, "El archivo debe ser una imagen.");
        } else {
          clearErrorMessage(input);
        }
      }
    });
  });

  const days = [
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
    "Sunday",
  ];

  let valid = true;

  days.forEach((day) => {
    const checkbox = document.querySelector(`#${day}_active`);
    const startInput = document.querySelector(`#${day}_start`);
    const endInput = document.querySelector(`#${day}_end`);
    const errorDiv = document.querySelector(`#error_${day}`);

    function toggleInputs() {
      const isActive = checkbox.checked;
      startInput.disabled = !isActive;

      if (!isActive) {
        startInput.value = "";
        endInput.value = "";
        endInput.disabled = true;
        errorDiv.style.display = "none";
      } else {
        endInput.disabled = startInput.value === "";
        if (startInput.value === "") {
          endInput.value = "";
        }
      }
    }

    function validateDay() {
      if (!checkbox.checked) {
        errorDiv.style.display = "none";
        return true;
      }

      const start = startInput.value;
      const end = endInput.value;

      endInput.disabled = start === "";
      if (start === "") {
        endInput.value = "";
        errorDiv.style.display = "none";
        return false;
      }

      if (!end) {
        errorDiv.textContent = "Por favor ingrese la hora final.";
        errorDiv.style.display = "block";
        errorDiv.style.color = "orange";
        return false;
      }

      if (start >= end) {
        endInput.value = "";
        errorDiv.textContent =
          "La hora de fin debe ser mayor que la hora de inicio. Se ha borrado la hora fin.";
        errorDiv.style.display = "block";
        errorDiv.style.color = "red";
        return false;
      }

      // Hora válida
      errorDiv.textContent = "Hora ingresada válida.";
      errorDiv.style.display = "block";
      errorDiv.style.color = "green";
      return true;
    }

    // Inicializar estado
    toggleInputs();

    checkbox.addEventListener("change", () => {
      toggleInputs();
      errorDiv.style.display = "none";
    });

    startInput.addEventListener("input", () => {
      toggleInputs();
      validateDay();
    });

    endInput.addEventListener("input", validateDay);
  });

  // Validación al enviar formulario
  const form = document.querySelector("form"); // o usa un ID si lo prefieres
  form.addEventListener("submit", (event) => {
    let hasAtLeastOneValidSchedule = false;
    valid = true;

    days.forEach((day) => {
      const checkbox = document.querySelector(`#${day}_active`);
      const startInput = document.querySelector(`#${day}_start`);
      const endInput = document.querySelector(`#${day}_end`);
      const errorDiv = document.querySelector(`#error_${day}`);

      if (checkbox.checked) {
        const dayValid = validateSchedule(startInput, endInput, errorDiv);
        if (dayValid) hasAtLeastOneValidSchedule = true;
        else valid = false;
      }
    });

    if (!hasAtLeastOneValidSchedule) {
      event.preventDefault();
      valid = false;
    }

    if (!valid) {
      event.preventDefault(); // Detiene el envío si algún día no es válido
    }
  });

  function validateSchedule(startInput, endInput, errorDiv) {
    const start = startInput.value;
    const end = endInput.value;

    if (!start || !end) {
      errorDiv.textContent = "Debe ingresar hora de inicio y fin.";
      errorDiv.style.display = "block";
      errorDiv.style.color = "orange";
      return false;
    }

    if (start >= end) {
      endInput.value = "";
      errorDiv.textContent =
        "La hora de fin debe ser mayor que la hora de inicio.";
      errorDiv.style.display = "block";
      errorDiv.style.color = "red";
      return false;
    }

    errorDiv.style.display = "none";
    return true;
  }

  const scheduleErrorMessage = document.getElementById(
    "schedule-error-message"
  );

  form.addEventListener("submit", (event) => {
    let hasAtLeastOneValidSchedule = false;
    valid = true;

    days.forEach((day) => {
      const checkbox = document.querySelector(`#${day}_active`);
      const startInput = document.querySelector(`#${day}_start`);
      const endInput = document.querySelector(`#${day}_end`);
      const errorDiv = document.querySelector(`#error_${day}`);

      if (checkbox.checked) {
        const dayValid = validateSchedule(startInput, endInput, errorDiv);
        if (dayValid) hasAtLeastOneValidSchedule = true;
        else valid = false;
      }
    });

    if (!hasAtLeastOneValidSchedule) {
      event.preventDefault();
      scheduleErrorMessage.style.display = "block"; // Muestra el mensaje general en rojo
      valid = false;
    } else {
      scheduleErrorMessage.style.display = "none"; // Oculta el mensaje si está todo OK
    }

    if (!valid) {
      event.preventDefault(); // Detiene el envío si algún día no es válido
    }
  });

  // Inicializar formulario y validar al enviar
  const doctorForm = document.getElementById("doctor-form");
  if (doctorForm) {
    showStep(currentStep);

    doctorForm.addEventListener("submit", (event) => {
      if (!validateStep1() || !validateStep2() || !validateStep3()) {
        event.preventDefault();
      }
    });
  } else {
    console.error("Formulario 'doctor-form' no encontrado en el DOM.");
  }
});
