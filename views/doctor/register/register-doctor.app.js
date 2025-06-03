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
  // Primero, eliminar cualquier mensaje de error o éxito existente
  clearAllMessages(input);

  // Crear y agregar el nuevo mensaje de error
  const errorElement = document.createElement("span");
  errorElement.classList.add("error-message");
  errorElement.style.color = "red";
  errorElement.textContent = message;
  input.parentNode.appendChild(errorElement);

  // Cambiar el borde del input según el tipo de mensaje
  if (message === "Verificando disponibilidad...") {
    input.style.border = "2px solid #FFA500"; // Naranja para verificación
  } else {
    input.style.border = "2px solid red"; // Rojo para error
  }
}

/**
 * Limpia el mensaje de error mostrado en un input.
 *
 * @param {HTMLElement} input - Elemento input del cual se elimina el error.
 */
function clearErrorMessage(input) {
  clearAllMessages(input);
  input.style.border = "2px solid var(--line-clr)";
}

/**
 * Limpia todos los mensajes (error y éxito) asociados a un input.
 *
 * @param {HTMLElement} input - Elemento input del cual se eliminan los mensajes.
 */
function clearAllMessages(input) {
  // Eliminar mensajes de error
  const errorMessages = input.parentNode.querySelectorAll(".error-message");
  errorMessages.forEach(element => element.remove());

  // Eliminar mensajes de éxito
  const successMessages = input.parentNode.querySelectorAll(".success-message");
  successMessages.forEach(element => element.remove());
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
 * Valida que la CURP sea coherente con la fecha de nacimiento y el género.
 *
 * @param {string} curp - CURP a validar
 * @param {string} birthDate - Fecha de nacimiento en formato YYYY-MM-DD
 * @param {string} gender - Género (M o F)
 * @returns {Object} Objeto con propiedades isValid y message
 */
function validateCURPCoherence(curp, birthDate, gender) {
  if (!curp || !birthDate || !gender) {
    return { isValid: false, message: "Faltan datos para validar la CURP." };
  }

  // Extraer fecha de nacimiento de la CURP (posiciones 4-9)
  const curpYear = curp.substring(4, 6);
  const curpMonth = curp.substring(6, 8);
  const curpDay = curp.substring(8, 10);

  // Extraer fecha de nacimiento del input
  const birthDateObj = new Date(birthDate);
  const inputYear = birthDateObj.getFullYear().toString().substring(2);
  const inputMonth = String(birthDateObj.getMonth() + 1).padStart(2, '0');
  const inputDay = String(birthDateObj.getUTCDate()).padStart(2, '0');

  // Extraer género de la CURP (posición 10)
  const curpGender = curp.charAt(10);

  // Validar coherencia de fecha
  const dateIsCoherent = curpYear === inputYear &&
                         curpMonth === inputMonth &&
                         curpDay === inputDay;

  console.log({curpYear, inputYear, curpMonth, inputMonth, curpDay, inputDay})

  // Validar coherencia de género
  const genderIsCoherent = (curpGender === 'H' && gender === 'M') ||
                           (curpGender === 'M' && gender === 'F');

  if (!dateIsCoherent && !genderIsCoherent) {
    return {
      isValid: false,
      message: "La CURP no coincide con la fecha de nacimiento ni con el género proporcionados."
    };
  } else if (!dateIsCoherent) {
    return {
      isValid: false,
      message: "La CURP no coincide con la fecha de nacimiento proporcionada."
    };
  } else if (!genderIsCoherent) {
    return {
      isValid: false,
      message: "La CURP no coincide con el género proporcionado (H para masculino, M para femenino)."
    };
  }

  return { isValid: true, message: "CURP coherente con los datos proporcionados." };
}

/**
 * Valida la coherencia entre CURP y RFC.
 *
 * @param {string} curp - CURP a validar
 * @param {string} rfc - RFC a validar
 * @returns {Object} Objeto con propiedades isValid y message
 */
function validateCURPRFCCoherence(curp, rfc) {
  if (!curp || !rfc) {
    return { isValid: true, message: "No se puede validar la coherencia sin ambos valores." };
  }

  // El RFC debe coincidir con los primeros 10 caracteres de la CURP
  // excepto que el RFC puede tener 3 o 4 letras al inicio
  const curpStart = curp.substring(0, 4);
  const rfcStart = rfc.substring(0, 4);

  // Si el RFC tiene 13 caracteres, comparamos los primeros 4 caracteres
  // Si tiene 12, comparamos los primeros 3 de la CURP con los primeros 3 del RFC
  let nameCoherent = false;

  if (rfc.length === 13) {
    nameCoherent = curpStart === rfcStart;
  } else if (rfc.length === 12) {
    nameCoherent = curp.substring(0, 3) === rfc.substring(0, 3);
  }

  // Comparar fecha de nacimiento (posiciones 4-9 en CURP, 4-9 o 3-8 en RFC)
  const curpDate = curp.substring(4, 10);
  const rfcDate = rfc.length === 13 ? rfc.substring(4, 10) : rfc.substring(3, 9);

  const dateCoherent = curpDate === rfcDate;

  if (!nameCoherent && !dateCoherent) {
    return {
      isValid: false,
      message: "El RFC no coincide con la CURP en nombre ni en fecha de nacimiento."
    };
  } else if (!nameCoherent) {
    return {
      isValid: false,
      message: "El RFC no coincide con la CURP en las iniciales del nombre."
    };
  } else if (!dateCoherent) {
    return {
      isValid: false,
      message: "El RFC no coincide con la CURP en la fecha de nacimiento."
    };
  }

  return { isValid: true, message: "RFC coherente con la CURP." };
}

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
    showErrorMessage(phoneInput, "El número debe tener exactamente 10 dígitos numéricos. Ejemplo: 5512345678");
    valid = false;
  } else {
    clearErrorMessage(phoneInput);
  }

  // Validar correo electrónico.
  const emailInput = document.getElementById("email");
  const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
  if (!emailPattern.test(emailInput.value.trim())) {
    showErrorMessage(emailInput, "Correo electrónico no válido. Debe tener formato usuario@dominio.com");
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

  // Validar fecha de nacimiento: no vacía, debe ser en el pasado, no más de 200 años atrás y mayor a 21 años.
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
    const twoHundredYearsAgo = new Date();
    twoHundredYearsAgo.setFullYear(today.getFullYear() - 200);

    // Calcular edad
    const minAgeDate = new Date();
    minAgeDate.setFullYear(today.getFullYear() - 21);

    if (birthDate >= today) {
      showErrorMessage(birthDateInput, "Debe ser una fecha pasada.");
      valid = false;
    } else if (birthDate < twoHundredYearsAgo) {
      showErrorMessage(birthDateInput, "La fecha no puede ser mayor a 200 años.");
      valid = false;
    } else if (birthDate > minAgeDate) {
      showErrorMessage(birthDateInput, "El doctor debe tener al menos 21 años de edad para registrarse.");
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
    showErrorMessage(postalCodeInput, "El código postal debe tener exactamente 5 dígitos numéricos. Ejemplo: 01234");
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
      "La CURP debe tener exactamente 18 caracteres. Verifique que no falten caracteres."
    );
    valid = false;
  } else if (!curpPattern.test(curp)) {
    showErrorMessage(
      curpInput,
      "CURP inválida. Revise que el formato sea correcto y que los caracteres sean válidos."
    );
    valid = false;
  } else {
    // Validar coherencia de CURP con fecha de nacimiento y género
    const birthDateInput = document.getElementById("birthDate");
    const genderSelect = document.getElementById("gender");

    if (birthDateInput.value && genderSelect.value) {
      const coherenceResult = validateCURPCoherence(
        curp,
        birthDateInput.value,
        genderSelect.value
      );

      if (!coherenceResult.isValid) {
        showErrorMessage(curpInput, coherenceResult.message);
        valid = false;
      } else {
        clearErrorMessage(curpInput);
      }
    } else {
      clearErrorMessage(curpInput);
    }
  }

  // Validar RFC: opcional, pero si se llena debe cumplir el formato.
  const rfcInput = document.getElementById("rfc");
  const rfcPattern = /^[A-ZÑ&]{3,4}\d{6}[A-Z\d]{3}$/i;
  if (rfcInput.value.trim() !== "" && !rfcPattern.test(rfcInput.value.trim())) {
    showErrorMessage(
      rfcInput,
      "RFC inválido. Debe tener entre 12 y 13 caracteres y seguir el formato correcto."
    );
    valid = false;
  } else if (rfcInput.value.trim() !== "") {
    // Validar coherencia entre CURP y RFC
    if (curpInput.value.trim() !== "") {
      const coherenceResult = validateCURPRFCCoherence(
        curpInput.value.trim(),
        rfcInput.value.trim()
      );

      if (!coherenceResult.isValid) {
        showErrorMessage(rfcInput, coherenceResult.message);
        valid = false;
      } else {
        clearErrorMessage(rfcInput);
      }
    } else {
      clearErrorMessage(rfcInput);
    }
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
    if (photoInput.hasAttribute("required") && photoInput.files.length === 0) {
      showErrorMessage(photoInput, "Debe seleccionar una foto.");
      valid = false;
    } else if (photoInput.files.length > 0) {
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
    Swal.fire({
      title: "Error de validación",
      text: "⚠️ Corrige los errores antes de continuar.",
      icon: "warning",
      confirmButtonColor: "#3085d6",
      confirmButtonText: "Entendido"
    });
    return;
  }
  if (currentStep === 2 && !validateStep2()) {
    Swal.fire({
      title: "Error de validación",
      text: "⚠️ Corrige los errores antes de continuar.",
      icon: "warning",
      confirmButtonColor: "#3085d6",
      confirmButtonText: "Entendido"
    });
    return;
  }
  if (currentStep === 3 && !validateStep3()) {
    Swal.fire({
      title: "Error de validación",
      text: "⚠️ Corrige los errores antes de continuar.",
      icon: "warning",
      confirmButtonColor: "#3085d6",
      confirmButtonText: "Entendido"
    });
    return;
  }

  currentStep = step;
  showStep(currentStep);
};

// ===========================
// Inicialización al Cargar el DOM
// ===========================

document.addEventListener("DOMContentLoaded", () => {
  // Capitaliza los nombres al perder el foco y valida en tiempo real
  ["firstName", "lastName", "motherLastName"].forEach((id) => {
    const input = document.getElementById(id);
    if (input) {
      // Capitaliza al perder el foco
      input.addEventListener("blur", () => {
        // Capitalizar cada palabra (primera letra mayúscula, resto minúsculas)
        input.value = input.value
          .toLowerCase()
          .split(" ")
          .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
          .join(" ");
      });

      // Valida en tiempo real (onChange) para no permitir caracteres especiales ni números
      input.addEventListener("input", () => {
        const namePattern = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]*$/;
        if (!namePattern.test(input.value)) {
          showErrorMessage(input, "Solo se permiten letras, espacios, acentos y ñ.");
          // Eliminar el último carácter ingresado si no es válido
          input.value = input.value.slice(0, -1);
        } else {
          clearErrorMessage(input);
        }
      });
    }
  });

  // Validar número de teléfono en tiempo real (onChange)
  const phoneInput = document.getElementById("phoneNumber");
  if (phoneInput) {
    phoneInput.addEventListener("input", () => {
      // Limitar a 10 dígitos
      if (phoneInput.value.length > 10) {
        phoneInput.value = phoneInput.value.slice(0, 10);
      }

      // Validar que solo contenga dígitos
      if (!/^\d*$/.test(phoneInput.value)) {
        showErrorMessage(phoneInput, "Solo se permiten números.");
        // Eliminar caracteres no numéricos
        phoneInput.value = phoneInput.value.replace(/\D/g, '');
      } else if (phoneInput.value.length > 0 && phoneInput.value.length < 10) {
        showErrorMessage(phoneInput, "El número debe tener 10 dígitos.");
      } else if (phoneInput.value.length === 10) {
        clearErrorMessage(phoneInput);
      }
    });
  }

  // Añadir validación onBlur para los campos del paso 1
  document
    .querySelectorAll("#step-1 input, #step-1 select")
    .forEach((input) => {
      input.addEventListener("blur", () => {
        // Validar el campo específico que perdió el foco
        if (input.id === "phoneNumber") {
          if (!/^\d{10}$/.test(input.value.trim())) {
            showErrorMessage(input, "El número debe tener exactamente 10 dígitos numéricos. Ejemplo: 5512345678");
          } else {
            // Verificar unicidad del número de teléfono
            const phone = input.value.trim();
            const doctorIdInput = document.querySelector('input[name="doctor_id"]');
            const doctorId = doctorIdInput ? doctorIdInput.value : '';

            // Mostrar indicador de carga
            showErrorMessage(input, "Verificando disponibilidad...");
            input.style.border = "2px solid #FFA500"; // Naranja para indicar verificación en progreso

            const formData = new FormData();
            formData.append('field', 'phone');
            formData.append('value', phone);
            if (doctorId) {
              formData.append('doctorId', doctorId);
            }

            fetch('/controllers/doctor/check-field-uniqueness.controller.php', {
              method: 'POST',
              body: formData
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                clearErrorMessage(input);
                // Mostrar mensaje de éxito
                const successElement = document.createElement("span");
                successElement.classList.add("success-message");
                successElement.style.color = "green";
                successElement.textContent = "Número de teléfono disponible";
                input.parentNode.appendChild(successElement);
                // Eliminar el mensaje después de 3 segundos
                setTimeout(() => clearAllMessages(input), 3000);
              } else {
                showErrorMessage(input, data.message);
              }
            })
            .catch(error => {
              console.error('Error:', error);
              clearErrorMessage(input);
            });
          }
        } else if (input.id === "email") {
          const emailPattern =
            /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
          if (!emailPattern.test(input.value.trim())) {
            showErrorMessage(input, "Correo electrónico no válido. Debe tener formato usuario@dominio.com");
          } else {
            // Verificar unicidad del email
            const email = input.value.trim();
            const doctorIdInput = document.querySelector('input[name="doctor_id"]');
            const doctorId = doctorIdInput ? doctorIdInput.value : '';

            // Mostrar indicador de carga
            showErrorMessage(input, "Verificando disponibilidad...");
            input.style.border = "2px solid #FFA500"; // Naranja para indicar verificación en progreso

            const formData = new FormData();
            formData.append('field', 'email');
            formData.append('value', email);
            if (doctorId) {
              formData.append('doctorId', doctorId);
            }

            fetch('/controllers/doctor/check-field-uniqueness.controller.php', {
              method: 'POST',
              body: formData
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                clearErrorMessage(input);
                // Mostrar mensaje de éxito
                const successElement = document.createElement("span");
                successElement.classList.add("success-message");
                successElement.style.color = "green";
                successElement.textContent = "Email disponible";
                input.parentNode.appendChild(successElement);
                // Eliminar el mensaje después de 3 segundos
                setTimeout(() => clearAllMessages(input), 3000);
              } else {
                showErrorMessage(input, data.message);
              }
            })
            .catch(error => {
              console.error('Error:', error);
              clearErrorMessage(input);
            });
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
            const twoHundredYearsAgo = new Date();
            twoHundredYearsAgo.setFullYear(today.getFullYear() - 200);

            // Calcular edad mínima (21 años)
            const minAgeDate = new Date();
            minAgeDate.setFullYear(today.getFullYear() - 21);

            if (birthDate >= today) {
              showErrorMessage(input, "Debe ser una fecha pasada. Seleccione una fecha anterior al día de hoy.");
            } else if (birthDate < twoHundredYearsAgo) {
              showErrorMessage(input, "La fecha no puede ser mayor a 200 años. Verifique el año ingresado.");
            } else if (birthDate > minAgeDate) {
              showErrorMessage(input, "El doctor debe tener al menos 21 años de edad para registrarse. Verifique la fecha ingresada.");
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
          showErrorMessage(input, "El código postal debe tener exactamente 5 dígitos numéricos. Ejemplo: 01234");
        } else {
          clearErrorMessage(input);
        }
      } else if (input.id !== "intNumber" || input.value.trim() !== "") {
        // Para otros campos de dirección, excepto número interior vacío
        const pattern = /^[A-Za-z0-9ÁÉÍÓÚáéíóúÑñ\s]+$/;
        if (!pattern.test(input.value.trim())) {
          showErrorMessage(
            input,
            "Solo se permiten letras, números y espacios. Evite caracteres especiales como @, #, $, etc."
          );
        } else {
          clearErrorMessage(input);

          // Normalizar campos de dirección (capitalizar primera letra de cada palabra)
          if (["street", "neighborhood"].includes(input.id)) {
            input.value = input.value
              .toLowerCase()
              .split(" ")
              .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
              .join(" ");
          }
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
          // Verificar unicidad de la CURP
          const doctorIdInput = document.querySelector('input[name="doctor_id"]');
          const doctorId = doctorIdInput ? doctorIdInput.value : '';

          // Mostrar indicador de carga
          showErrorMessage(input, "Verificando disponibilidad...");
          input.style.border = "2px solid #FFA500"; // Naranja para indicar verificación en progreso

          const formData = new FormData();
          formData.append('field', 'CURP');
          formData.append('value', curp);
          if (doctorId) {
            formData.append('doctorId', doctorId);
          }

          fetch('/controllers/doctor/check-field-uniqueness.controller.php', {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              clearErrorMessage(input);
              // Mostrar mensaje de éxito
              const successElement = document.createElement("span");
              successElement.classList.add("success-message");
              successElement.style.color = "green";
              successElement.textContent = "CURP disponible";
              input.parentNode.appendChild(successElement);
              // Eliminar el mensaje después de 3 segundos
              setTimeout(() => clearAllMessages(input), 3000);
            } else {
              showErrorMessage(input, data.message);
            }
          })
          .catch(error => {
            console.error('Error:', error);
            clearErrorMessage(input);
          });
        }
      } else if (input.id === "rfc" && input.value.trim() !== "") {
        const rfcPattern = /^[A-ZÑ&]{3,4}\d{6}[A-Z\d]{3}$/i;
        if (!rfcPattern.test(input.value.trim())) {
          showErrorMessage(
            input,
            "RFC inválido. Debe tener entre 12 y 13 caracteres."
          );
        } else {
          // Verificar unicidad del RFC
          const rfc = input.value.trim();
          const doctorIdInput = document.querySelector('input[name="doctor_id"]');
          const doctorId = doctorIdInput ? doctorIdInput.value : '';

          // Mostrar indicador de carga
          showErrorMessage(input, "Verificando disponibilidad...");
          input.style.border = "2px solid #FFA500"; // Naranja para indicar verificación en progreso

          const formData = new FormData();
          formData.append('field', 'RFC');
          formData.append('value', rfc);
          if (doctorId) {
            formData.append('doctorId', doctorId);
          }

          fetch('/controllers/doctor/check-field-uniqueness.controller.php', {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              clearErrorMessage(input);
              // Mostrar mensaje de éxito
              const successElement = document.createElement("span");
              successElement.classList.add("success-message");
              successElement.style.color = "green";
              successElement.textContent = "RFC disponible";
              input.parentNode.appendChild(successElement);
              // Eliminar el mensaje después de 3 segundos
              setTimeout(() => clearAllMessages(input), 3000);
            } else {
              showErrorMessage(input, data.message);
            }
          })
          .catch(error => {
            console.error('Error:', error);
            clearErrorMessage(input);
          });
        }
      } else if (input.id === "affiliationNumber" && input.value.trim() !== "") {
        const alphanumericPattern = /^[A-Za-z0-9]+$/;
        if (!alphanumericPattern.test(input.value.trim())) {
          showErrorMessage(input, "Solo puede contener letras y números.");
        } else {
          // Verificar unicidad del número de afiliación
          const affiliationNumber = input.value.trim();
          const doctorIdInput = document.querySelector('input[name="doctor_id"]');
          const doctorId = doctorIdInput ? doctorIdInput.value : '';

          // Mostrar indicador de carga
          showErrorMessage(input, "Verificando disponibilidad...");
          input.style.border = "2px solid #FFA500"; // Naranja para indicar verificación en progreso

          const formData = new FormData();
          formData.append('field', 'insurance_number');
          formData.append('value', affiliationNumber);
          if (doctorId) {
            formData.append('doctorId', doctorId);
          }

          fetch('/controllers/doctor/check-field-uniqueness.controller.php', {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              clearErrorMessage(input);
              // Mostrar mensaje de éxito
              const successElement = document.createElement("span");
              successElement.classList.add("success-message");
              successElement.style.color = "green";
              successElement.textContent = "Número de afiliación disponible";
              input.parentNode.appendChild(successElement);
              // Eliminar el mensaje después de 3 segundos
              setTimeout(() => clearAllMessages(input), 3000);
            } else {
              showErrorMessage(input, data.message);
            }
          })
          .catch(error => {
            console.error('Error:', error);
            clearErrorMessage(input);
          });
        }
      } else if (input.id === "professionalLicense" && input.value.trim() !== "") {
        const alphanumericPattern = /^[A-Za-z0-9]+$/;
        if (!alphanumericPattern.test(input.value.trim())) {
          showErrorMessage(input, "Solo puede contener letras y números.");
        } else {
          // Verificar unicidad de la cédula profesional
          const professionalLicense = input.value.trim();
          const doctorIdInput = document.querySelector('input[name="doctor_id"]');
          const doctorId = doctorIdInput ? doctorIdInput.value : '';

          // Mostrar indicador de carga
          showErrorMessage(input, "Verificando disponibilidad...");
          input.style.border = "2px solid #FFA500"; // Naranja para indicar verificación en progreso

          const formData = new FormData();
          formData.append('field', 'professional_id');
          formData.append('value', professionalLicense);
          if (doctorId) {
            formData.append('doctorId', doctorId);
          }

          fetch('/controllers/doctor/check-field-uniqueness.controller.php', {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              clearErrorMessage(input);
              // Mostrar mensaje de éxito
              const successElement = document.createElement("span");
              successElement.classList.add("success-message");
              successElement.style.color = "green";
              successElement.textContent = "Cédula profesional disponible";
              input.parentNode.appendChild(successElement);
              // Eliminar el mensaje después de 3 segundos
              setTimeout(() => clearAllMessages(input), 3000);
            } else {
              showErrorMessage(input, data.message);
            }
          })
          .catch(error => {
            console.error('Error:', error);
            clearErrorMessage(input);
          });
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



  // Inicializar formulario y validar al enviar
  const doctorForm = document.getElementById("doctor-form");
  if (doctorForm) {
    showStep(currentStep);

    doctorForm.addEventListener("submit", (event) => {
      if (!validateStep1() || !validateStep2() || !validateStep3()) {
        event.preventDefault();
        Swal.fire({
          title: "Formulario inválido",
          text: "Por favor, revisa todos los campos y corrígelos antes de enviar de nuevo.",
          icon: "error",
          confirmButtonColor: "#3085d6",
          confirmButtonText: "Entendido"
        });
      }
    });
  } else {
    console.error("Formulario 'doctor-form' no encontrado en el DOM.");
  }
});
