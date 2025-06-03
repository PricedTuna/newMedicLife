// ===========================
//  VALIDACIÓN DE INPUTS DE IDENTIFICACIÓN
// ===========================

// Función que valida CURP, RFC, foto, número de afiliación y cédula profesional.
function validateIdentificationInputs() {
  let valid = true;
  // --- Validar CURP ---
  const curpInput = document.getElementById("curp");
  const curp = curpInput.value.trim();
  const curpPattern =
    /^[A-Z]{4}\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]\d$/;
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

  // --- Validar RFC (opcional) ---
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

  // --- Validar archivo de foto ---
  // Ejemplo: Se verifica que exista, sea una imagen y pese menos de 2MB.
  const photoInput = document.getElementById("photo");
  const photoFile = photoInput.files[0];
  const hiddenIdInput = document.querySelector('input[name="id"]');
  const isUpdateMode = hiddenIdInput && hiddenIdInput.value.trim() !== "";

  // Solo requerir foto si es un nuevo doctor (no en modo actualización)
  if (!isUpdateMode && !photoFile) {
    showErrorMessage(photoInput, "Debes seleccionar una imagen.");
    valid = false;
  } else if (photoFile) {
    // Si hay un archivo seleccionado (sea nuevo doctor o actualización), validar el archivo
    if (!photoFile.type.startsWith("image/")) {
      showErrorMessage(photoInput, "El archivo debe ser una imagen.");
      valid = false;
    } else if (photoFile.size > 2 * 1024 * 1024) {
      // 2MB
      showErrorMessage(photoInput, "La imagen no debe pesar más de 2MB.");
      valid = false;
    } else {
      clearErrorMessage(photoInput);
    }
  } else {
    clearErrorMessage(photoInput);
  }
  // Patrón alfanumérico para campos opcionales
  const alphanumericPattern = /^[A-Za-z0-9]+$/;

  // --- Validar Número de Afiliación (opcional) ---
  const affiliationInput = document.getElementById("affiliationNumber");
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

  // --- Validar Cédula Profesional (opcional) ---
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

const stateSelect = document.getElementById("state");
const municipalitySelect = document.getElementById("municipality");
const localitySelect = document.getElementById("locality");

/**
 * Llena el select de estados.
 */
const fillStates = () => {
  stateSelect.innerHTML = '<option value="">Seleccione...</option>';
  states.forEach((s) => {
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
  localitySelect.innerHTML =
    '<option value="">Seleccione un municipio primero...</option>';

  if (selectedState) {
    municipalities
      .filter((m) => m.id_state == selectedState)
      .forEach((m) => {
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
      .filter(
        (l) =>
          l.id_state == selectedState &&
          l.id_municipality == selectedMunicipality
      )
      .forEach((l) => {
        localitySelect.innerHTML += `<option value="${l.id}">${l.name}</option>`;
      });

    if (preselected.locality) {
      localitySelect.value = preselected.locality;
    }
  }
};

// Evento DOMContentLoaded para ejecutar todo en orden
document.addEventListener("DOMContentLoaded", function () {
  fillStates(); // Llenamos estados
  updateMunicipalities(); // Llenamos municipios
  updateLocalities(); // Llenamos localidades

  // Asociar eventos luego de rellenar selects
  stateSelect.addEventListener("change", () => {
    preselected.municipality = null;
    preselected.locality = null;
    updateMunicipalities();
  });

  municipalitySelect.addEventListener("change", () => {
    preselected.locality = null;
    updateLocalities();
  });
});

// ===========================
// ACTUALIZACIÓN DE LA ETIQUETA DE FOTO
// ===========================
document.getElementById("photo").addEventListener("change", (event) => {
  const fileName = event.target.files[0]
    ? event.target.files[0].name
    : "Subir Foto";
  document.getElementById("photo-label").textContent = fileName;
});

// ===========================
// VALIDACIÓN EN TIEMPO REAL (onBlur)
// ===========================

// Añadir validación onBlur para los campos de identificación
document.addEventListener("DOMContentLoaded", () => {
  // Validar CURP al perder el foco
  const curpInput = document.getElementById("curp");
  if (curpInput) {
    curpInput.addEventListener("blur", () => {
      const curp = curpInput.value.trim();
      const curpPattern =
        /^[A-Z]{4}\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]\d$/;
      if (curp.length !== 18) {
        showErrorMessage(
          curpInput,
          "La CURP debe tener exactamente 18 caracteres."
        );
      } else if (!curpPattern.test(curp)) {
        showErrorMessage(curpInput, "CURP inválida. Revisa el formato.");
      } else {
        clearErrorMessage(curpInput);
      }
    });
  }

  // Validar RFC al perder el foco
  const rfcInput = document.getElementById("rfc");
  if (rfcInput) {
    rfcInput.addEventListener("blur", () => {
      const rfcPattern = /^[A-ZÑ&]{3,4}\d{6}[A-Z\d]{3}$/i;
      if (
        rfcInput.value.trim() !== "" &&
        !rfcPattern.test(rfcInput.value.trim())
      ) {
        showErrorMessage(
          rfcInput,
          "RFC inválido. Debe tener entre 12 y 13 caracteres."
        );
      } else {
        clearErrorMessage(rfcInput);
      }
    });
  }

  // Validar Número de Afiliación al perder el foco
  const affiliationInput = document.getElementById("affiliationNumber");
  if (affiliationInput) {
    affiliationInput.addEventListener("blur", () => {
      const alphanumericPattern = /^[A-Za-z0-9]+$/;
      if (
        affiliationInput.value.trim() !== "" &&
        !alphanumericPattern.test(affiliationInput.value.trim())
      ) {
        showErrorMessage(
          affiliationInput,
          "El número de afiliación solo puede contener letras y números."
        );
      } else {
        clearErrorMessage(affiliationInput);
      }
    });
  }

  // Validar Cédula Profesional al perder el foco
  const licenseInput = document.getElementById("professionalLicense");
  if (licenseInput) {
    licenseInput.addEventListener("blur", () => {
      const alphanumericPattern = /^[A-Za-z0-9]+$/;
      if (
        licenseInput.value.trim() !== "" &&
        !alphanumericPattern.test(licenseInput.value.trim())
      ) {
        showErrorMessage(
          licenseInput,
          "La cédula profesional solo puede contener letras y números."
        );
      } else {
        clearErrorMessage(licenseInput);
      }
    });
  }
});

// ===========================
// MANEJO DE HORARIOS
// ===========================

document.addEventListener("DOMContentLoaded", () => {
  const days = [
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
    "Sunday",
  ];
  const scheduleErrorMessage = document.getElementById(
    "schedule-error-message"
  );
  const form = document.querySelector("form");

  // Función para mostrar/ocultar inputs según el checkbox
  function toggleInputs(day) {
    const checkbox = document.getElementById(`${day}_active`);
    const startInput = document.getElementById(`${day}_start`);
    const endInput = document.getElementById(`${day}_end`);
    const errorDiv = document.getElementById(`error_${day}`);

    if (checkbox.checked) {
      startInput.disabled = false;
      endInput.disabled = startInput.value === "";
      if (startInput.value === "") endInput.value = "";
    } else {
      startInput.disabled = true;
      endInput.disabled = true;
      startInput.value = "";
      endInput.value = "";
      errorDiv.style.display = "none";
    }
  }

  // Validación por día
  function validateDay(day) {
    const checkbox = document.getElementById(`${day}_active`);
    const startInput = document.getElementById(`${day}_start`);
    const endInput = document.getElementById(`${day}_end`);
    const errorDiv = document.getElementById(`error_${day}`);

    if (!checkbox.checked) {
      errorDiv.style.display = "none";
      return true;
    }

    const start = startInput.value;
    const end = endInput.value;

    if (!start || !end) {
      errorDiv.textContent = "Debe ingresar hora de inicio y fin.";
      errorDiv.style.display = "block";
      errorDiv.style.color = "orange";
      return false;
    }

    if (start >= end) {
      errorDiv.textContent = "La hora de fin debe ser mayor que la de inicio.";
      errorDiv.style.display = "block";
      errorDiv.style.color = "red";
      return false;
    }

    errorDiv.textContent = "Hora válida.";
    errorDiv.style.display = "block";
    errorDiv.style.color = "green";
    return true;
  }

  // Función para clonar horario a todos los días
  function cloneScheduleToAllDays(day) {
    const startTimeInput = document.getElementById(`${day}_start`);
    const endTimeInput = document.getElementById(`${day}_end`);

    if (!startTimeInput.value || !endTimeInput.value) {
      return; // No clonar si no hay horario completo
    }

    days.forEach((otherDay) => {
      if (otherDay !== day) {
        const otherStartInput = document.getElementById(`${otherDay}_start`);
        const otherEndInput = document.getElementById(`${otherDay}_end`);
        const otherActiveCheckbox = document.getElementById(`${otherDay}_active`);

        if (otherStartInput && otherEndInput && otherActiveCheckbox) {
          otherActiveCheckbox.checked = true;
          otherStartInput.value = startTimeInput.value;
          otherEndInput.value = endTimeInput.value;
          toggleInputs(otherDay);
          validateDay(otherDay);
        }
      }
    });
  }

  // Función para crear el botón de clonar horario
  function createCloneButton(day) {
    // Buscar el checkbox del día para encontrar el contenedor
    const checkbox = document.getElementById(`${day}_active`);
    if (!checkbox) {
      console.error(`No se encontró el checkbox para el día ${day}`);
      return;
    }

    // Intentar encontrar el contenedor del día de varias maneras
    let container = null;

    // Método 1: Buscar el elemento .schedule-row que contiene el checkbox
    container = checkbox.closest('.schedule-row');

    // Método 2: Si no se encuentra, buscar el elemento padre que contiene los inputs de tiempo
    if (!container) {
      const startInput = document.getElementById(`${day}_start`);
      if (startInput) {
        container = startInput.closest('.schedule-row');
      }
    }

    // Método 3: Si aún no se encuentra, buscar cualquier elemento que contenga los inputs del día
    if (!container) {
      // Buscar un elemento común que contenga tanto el checkbox como los inputs
      let parent = checkbox.parentElement;
      while (parent && !container) {
        if (parent.querySelector(`#${day}_start`) && parent.querySelector(`#${day}_end`)) {
          container = parent;
          break;
        }
        parent = parent.parentElement;
      }
    }

    // Si no se encuentra ningún contenedor, usar el padre directo del checkbox
    if (!container) {
      container = checkbox.parentElement;
      console.warn(`No se encontró un contenedor adecuado para el día ${day}, usando el padre directo del checkbox`);
    }

    // Verificar si ya existe un botón de clonar
    const existingButton = document.querySelector(`.clone-button[data-day="${day}"]`);
    if (existingButton) {
      console.log(`Ya existe un botón de clonar para el día ${day}`);
      return;
    }

    const cloneButton = document.createElement('button');
    cloneButton.type = 'button';
    cloneButton.className = 'clone-button';
    cloneButton.setAttribute('data-day', day);
    cloneButton.innerHTML = '<i class="fas fa-copy"></i> Clonar a todos los días';
    cloneButton.style.marginLeft = '10px';
    cloneButton.style.padding = '5px 10px';
    cloneButton.style.backgroundColor = '#3498db';
    cloneButton.style.color = 'white';
    cloneButton.style.border = 'none';
    cloneButton.style.borderRadius = '4px';
    cloneButton.style.cursor = 'pointer';
    cloneButton.style.fontSize = '12px';
    cloneButton.style.display = 'inline-block'; // Asegurar que sea visible

    cloneButton.addEventListener('click', (e) => {
      e.preventDefault();
      const startInput = document.getElementById(`${day}_start`);
      const endInput = document.getElementById(`${day}_end`);

      if (!startInput.value || !endInput.value) {
        Swal.fire({
          title: "Error",
          text: "Debe ingresar hora de inicio y fin antes de clonar.",
          icon: "error",
          confirmButtonColor: "#3085d6",
          confirmButtonText: "Entendido"
        });
        return;
      }

      if (!validateDay(day)) {
        Swal.fire({
          title: "Error",
          text: "El horario no es válido. Corrija los errores antes de clonar.",
          icon: "error",
          confirmButtonColor: "#3085d6",
          confirmButtonText: "Entendido"
        });
        return;
      }

      Swal.fire({
        title: "¿Clonar horario?",
        text: `¿Desea aplicar este horario (${startInput.value} - ${endInput.value}) a todos los demás días?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, aplicar",
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          cloneScheduleToAllDays(day);
          Swal.fire({
            title: "¡Horario clonado!",
            text: "El horario se ha aplicado a todos los días.",
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Entendido"
          });
        }
      });
    });

    // Intentar agregar el botón en diferentes lugares

    // Opción 1: Agregar al contenedor de inputs de tiempo
    const timeInputsContainer = container.querySelector('.schedule-time-inputs');
    if (timeInputsContainer) {
      timeInputsContainer.appendChild(cloneButton);
      console.log(`Botón de clonar agregado al contenedor de inputs de tiempo para el día ${day}`);
      return;
    }

    // Opción 2: Agregar junto al input de fin
    const endInput = document.getElementById(`${day}_end`);
    if (endInput && endInput.parentElement) {
      endInput.parentElement.appendChild(cloneButton);
      console.log(`Botón de clonar agregado junto al input de fin para el día ${day}`);
      return;
    }

    // Opción 3: Agregar al contenedor principal
    container.appendChild(cloneButton);
    console.log(`Botón de clonar agregado al contenedor principal para el día ${day}`);
  }

  // Inicializar eventos para cada día
  days.forEach((day) => {
    const checkbox = document.getElementById(`${day}_active`);
    const startInput = document.getElementById(`${day}_start`);
    const endInput = document.getElementById(`${day}_end`);
    const errorDiv = document.getElementById(`error_${day}`);

    toggleInputs(day);

    // Crear el botón de clonar para este día
    createCloneButton(day);

    checkbox.addEventListener("change", () => {
      toggleInputs(day);
      errorDiv.style.display = "none";
      if (checkbox.checked && startInput.value && endInput.value) {
        validateDay(day);
      }
    });

    startInput.addEventListener("input", () => {
      toggleInputs(day);
      validateDay(day);
    });

    endInput.addEventListener("input", () => {
      validateDay(day);
    });
  });

  // Validación al enviar el formulario
  form.addEventListener("submit", (event) => {
    let hasAtLeastOneValidSchedule = false;
    let valid = true;

    days.forEach((day) => {
      const checkbox = document.getElementById(`${day}_active`);
      if (checkbox.checked) {
        const isValid = validateDay(day);
        if (isValid) hasAtLeastOneValidSchedule = true;
        else valid = false;
      }
    });

    if (!hasAtLeastOneValidSchedule) {
      event.preventDefault();
      scheduleErrorMessage.style.display = "block";
    } else {
      scheduleErrorMessage.style.display = "none";
    }

    if (!valid) {
      event.preventDefault();
    }
  });
});
// ===========================
// MANEJO DEL ENVÍO DEL FORMULARIO
// ===========================
document.querySelector("form").addEventListener("submit", (e) => {
  if (!validateIdentificationInputs()) {
    e.preventDefault();
  }
});
