document.addEventListener('DOMContentLoaded', () => {
  const efectivoRadio = document.getElementById('efectivo');
  const paypalRadio = document.getElementById('paypal');
  const efectivoOption = document.getElementById('efectivo-option');
  const paypalOption = document.getElementById('paypal-option');
  const paypalFields = document.getElementById('paypal-fields');
  const montoInput = document.getElementById('monto');
  const paypalName = document.getElementById('paypal_name');
  const paypalCountry = document.getElementById('paypal_country');
  const form = document.getElementById('payment-form');

  // Function to toggle payment-specific fields
  function toggleFields() {
    // Update visual selection state
    if (efectivoRadio.checked) {
      efectivoOption.classList.add('selected');
      paypalOption.classList.remove('selected');
    } else if (paypalRadio.checked) {
      paypalOption.classList.add('selected');
      efectivoOption.classList.remove('selected');
    }

    // Toggle PayPal fields
    if (paypalRadio.checked) {
      paypalFields.classList.remove('hidden');
      paypalName.required = true;
      paypalCountry.required = true;
    } else {
      paypalFields.classList.add('hidden');
      paypalName.required = false;
      paypalCountry.required = false;
    }
  }

  // Add click handlers to the entire payment option divs
  efectivoOption.addEventListener('click', () => {
    efectivoRadio.checked = true;
    toggleFields();
  });

  paypalOption.addEventListener('click', () => {
    paypalRadio.checked = true;
    toggleFields();
  });

  // Also keep the original radio button change listeners
  efectivoRadio.addEventListener('change', toggleFields);
  paypalRadio.addEventListener('change', toggleFields);

  // Initialize fields state
  toggleFields();

  // Format the amount input to show currency symbol
  montoInput.addEventListener('focus', function() {
    if (this.value === '') {
      this.value = '';
    }
  });

  montoInput.addEventListener('blur', function() {
    if (this.value === '') {
      this.value = '';
    }
  });

  // Form submission handler with validation
  form.addEventListener('submit', (e) => {
    e.preventDefault();

    // Validate payment method selection
    const selected = document.querySelector('input[name="metodo_pago"]:checked');
    if (!selected) {
      Swal.fire({
        title: 'Error',
        text: 'Por favor, selecciona un método de pago.',
        icon: 'error',
        confirmButtonText: 'Entendido'
      });
      return;
    }

    // Validate amount
    if (!montoInput.value || parseFloat(montoInput.value) <= 0) {
      Swal.fire({
        title: 'Error',
        text: 'Ingresa un monto válido mayor a cero.',
        icon: 'error',
        confirmButtonText: 'Entendido'
      });
      montoInput.focus();
      return;
    }

    // Validate PayPal specific fields
    if (selected.value === 'paypal') {
      if (!paypalName.value.trim()) {
        Swal.fire({
          title: 'Error',
          text: 'Ingresa tu nombre completo.',
          icon: 'error',
          confirmButtonText: 'Entendido'
        });
        paypalName.focus();
        return;
      }

      if (!paypalCountry.value) {
        Swal.fire({
          title: 'Error',
          text: 'Selecciona un país.',
          icon: 'error',
          confirmButtonText: 'Entendido'
        });
        paypalCountry.focus();
        return;
      }
    }

    // Show confirmation dialog
    Swal.fire({
      title: '¿Confirmar pago?',
      text: `¿Estás seguro de que deseas proceder con el pago de $${montoInput.value} utilizando ${selected.value === 'efectivo' ? 'efectivo' : 'PayPal'}?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sí, continuar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        form.submit();
      }
    });
  });
});
