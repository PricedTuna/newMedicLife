document.addEventListener('DOMContentLoaded', function() {
  // Validación para campos de texto requeridos
  document.querySelectorAll('input[type="text"][required], input[type="email"][required], textarea[required]').forEach(input => {
    input.addEventListener('blur', function() {
      validateRequiredField(this);
    });
  });

  // Validación para selects requeridos
  document.querySelectorAll('select[required]').forEach(select => {
    select.addEventListener('blur', function() {
      validateSelect(this);
    });
  });

  // Validación para emails
  document.querySelectorAll('input[type="email"]').forEach(input => {
    input.addEventListener('blur', function() {
      validateEmail(this);
    });
  });

  // Validación para teléfonos (10 dígitos)
  document.querySelectorAll('input[name="phone"], input[id*="phone"], input[id*="telefono"]').forEach(input => {
    input.addEventListener('blur', function() {
      validatePhone(this);
    });

    // También validar mientras se escribe para limitar a solo números
    input.addEventListener('input', function() {
      this.value = this.value.replace(/[^0-9]/g, '');
      if (this.value.length > 10) {
        this.value = this.value.substring(0, 10);
      }
    });
  });

  // Validación para códigos postales (5 caracteres)
  document.querySelectorAll('input[name="CP"], input[id*="cp"], input[id*="codigo-postal"]').forEach(input => {
    input.addEventListener('blur', function() {
      validatePostalCode(this);
    });

    // También validar mientras se escribe para limitar a solo números
    input.addEventListener('input', function() {
      this.value = this.value.replace(/[^0-9]/g, '');
      if (this.value.length > 5) {
        this.value = this.value.substring(0, 5);
      }
    });
  });

  // Validación para números exteriores e interiores (solo números)
  document.querySelectorAll('input[name="external_number"], input[name="internal_number"], input[id*="exterior"], input[id*="interior"]').forEach(input => {
    input.addEventListener('blur', function() {
      validateNumberOnly(this);
    });

    // También validar mientras se escribe para limitar a solo números
    input.addEventListener('input', function() {
      this.value = this.value.replace(/[^0-9]/g, '');
    });
  });
});

function validateRequiredField(field) {
  const errorId = `${field.id}Error`;
  let errorElement = document.getElementById(errorId);

  if (!errorElement) {
    errorElement = document.createElement('small');
    errorElement.id = errorId;
    errorElement.style.color = 'red';
    errorElement.style.display = 'none';
    field.parentNode.appendChild(errorElement);
  }

  if (!field.value.trim()) {
    errorElement.textContent = 'Este campo es obligatorio';
    errorElement.style.display = 'block';
    field.classList.add('invalid-input');
    return false;
  } else {
    errorElement.style.display = 'none';
    field.classList.remove('invalid-input');
    return true;
  }
}

function validateSelect(select) {
  const errorId = `${select.id}Error`;
  let errorElement = document.getElementById(errorId);

  if (!errorElement) {
    errorElement = document.createElement('small');
    errorElement.id = errorId;
    errorElement.style.color = 'red';
    errorElement.style.display = 'none';
    select.parentNode.appendChild(errorElement);
  }

  if (!select.value) {
    errorElement.textContent = 'Debe seleccionar una opción';
    errorElement.style.display = 'block';
    select.classList.add('invalid-input');
    return false;
  } else {
    errorElement.style.display = 'none';
    select.classList.remove('invalid-input');
    return true;
  }
}

function validateEmail(field) {
  const errorId = `${field.id}Error`;
  let errorElement = document.getElementById(errorId);

  if (!errorElement) {
    errorElement = document.createElement('small');
    errorElement.id = errorId;
    errorElement.style.color = 'red';
    errorElement.style.display = 'none';
    field.parentNode.appendChild(errorElement);
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  if (!field.value.trim()) {
    if (field.hasAttribute('required')) {
      errorElement.textContent = 'El email es obligatorio';
      errorElement.style.display = 'block';
      field.classList.add('invalid-input');
      return false;
    }
  } else if (!emailRegex.test(field.value.trim())) {
    errorElement.textContent = 'Formato de email inválido';
    errorElement.style.display = 'block';
    field.classList.add('invalid-input');
    return false;
  } else {
    errorElement.style.display = 'none';
    field.classList.remove('invalid-input');
    return true;
  }

  return true;
}

/**
 * Valida que un campo de teléfono tenga exactamente 10 dígitos
 * @param {HTMLElement} field - El campo de entrada a validar
 * @returns {boolean} - true si es válido, false si no
 */
function validatePhone(field) {
  const errorId = `${field.id}Error`;
  let errorElement = document.getElementById(errorId);

  if (!errorElement) {
    errorElement = document.createElement('small');
    errorElement.id = errorId;
    errorElement.style.color = 'red';
    errorElement.style.display = 'none';
    field.parentNode.appendChild(errorElement);
  }

  const phoneRegex = /^\d{10}$/;

  if (!field.value.trim()) {
    if (field.hasAttribute('required')) {
      errorElement.textContent = 'El número de teléfono es obligatorio';
      errorElement.style.display = 'block';
      field.classList.add('invalid-input');
      return false;
    }
  } else if (!phoneRegex.test(field.value.trim())) {
    errorElement.textContent = 'El número de teléfono debe tener exactamente 10 dígitos';
    errorElement.style.display = 'block';
    field.classList.add('invalid-input');
    return false;
  } else {
    errorElement.style.display = 'none';
    field.classList.remove('invalid-input');
    return true;
  }

  return true;
}

/**
 * Valida que un campo de código postal tenga exactamente 5 dígitos
 * @param {HTMLElement} field - El campo de entrada a validar
 * @returns {boolean} - true si es válido, false si no
 */
function validatePostalCode(field) {
  const errorId = `${field.id}Error`;
  let errorElement = document.getElementById(errorId);

  if (!errorElement) {
    errorElement = document.createElement('small');
    errorElement.id = errorId;
    errorElement.style.color = 'red';
    errorElement.style.display = 'none';
    field.parentNode.appendChild(errorElement);
  }

  const postalCodeRegex = /^\d{5}$/;

  if (!field.value.trim()) {
    if (field.hasAttribute('required')) {
      errorElement.textContent = 'El código postal es obligatorio';
      errorElement.style.display = 'block';
      field.classList.add('invalid-input');
      return false;
    }
  } else if (!postalCodeRegex.test(field.value.trim())) {
    errorElement.textContent = 'El código postal debe tener exactamente 5 dígitos';
    errorElement.style.display = 'block';
    field.classList.add('invalid-input');
    return false;
  } else {
    errorElement.style.display = 'none';
    field.classList.remove('invalid-input');
    return true;
  }

  return true;
}

/**
 * Valida que un campo contenga solo números
 * @param {HTMLElement} field - El campo de entrada a validar
 * @returns {boolean} - true si es válido, false si no
 */
function validateNumberOnly(field) {
  const errorId = `${field.id}Error`;
  let errorElement = document.getElementById(errorId);

  if (!errorElement) {
    errorElement = document.createElement('small');
    errorElement.id = errorId;
    errorElement.style.color = 'red';
    errorElement.style.display = 'none';
    field.parentNode.appendChild(errorElement);
  }

  const numberRegex = /^\d+$/;

  if (!field.value.trim()) {
    if (field.hasAttribute('required')) {
      errorElement.textContent = 'Este campo es obligatorio';
      errorElement.style.display = 'block';
      field.classList.add('invalid-input');
      return false;
    }
  } else if (!numberRegex.test(field.value.trim())) {
    errorElement.textContent = 'Este campo debe contener solo números';
    errorElement.style.display = 'block';
    field.classList.add('invalid-input');
    return false;
  } else {
    errorElement.style.display = 'none';
    field.classList.remove('invalid-input');
    return true;
  }

  return true;
}
