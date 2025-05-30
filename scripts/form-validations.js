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
