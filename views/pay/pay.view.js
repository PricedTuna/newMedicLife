document.addEventListener('DOMContentLoaded', () => {
  const efectivoRadio = document.getElementById('efectivo');
  const paypalRadio = document.getElementById('paypal');
  const cardRadio = document.getElementById('card');
  const transferRadio = document.getElementById('transfer');
  const efectivoOption = document.getElementById('efectivo-option');
  const paypalOption = document.getElementById('paypal-option');
  const cardOption = document.getElementById('card-option');
  const transferOption = document.getElementById('transfer-option');
  const paypalFields = document.getElementById('paypal-fields');
  const cardFields = document.getElementById('card-fields');
  const transferFields = document.getElementById('transfer-fields');
  const montoInput = document.getElementById('monto');
  const paypalName = document.getElementById('paypal_name');
  const paypalCountry = document.getElementById('paypal_country');
  const form = document.getElementById('payment-form');
  const cancelBtn = document.getElementById('cancel-btn');

  // Function to toggle payment-specific fields
  function toggleFields() {
    // Update visual selection state
    if (efectivoRadio.checked) {
      efectivoOption.classList.add('selected');
      paypalOption.classList.remove('selected');
      cardOption.classList.remove('selected');
      transferOption.classList.remove('selected');
    } else if (paypalRadio.checked) {
      paypalOption.classList.add('selected');
      efectivoOption.classList.remove('selected');
      cardOption.classList.remove('selected');
      transferOption.classList.remove('selected');
    } else if (cardRadio.checked) {
      cardOption.classList.add('selected');
      efectivoOption.classList.remove('selected');
      paypalOption.classList.remove('selected');
      transferOption.classList.remove('selected');
    } else if (transferRadio.checked) {
      transferOption.classList.add('selected');
      efectivoOption.classList.remove('selected');
      paypalOption.classList.remove('selected');
      cardOption.classList.remove('selected');
    }

    // Hide all fields first
    paypalFields.classList.add('hidden');
    cardFields.classList.add('hidden');
    transferFields.classList.add('hidden');

    // Reset all field requirements
    paypalName.required = false;
    paypalCountry.required = false;
    document.getElementById('card_number').required = false;
    document.getElementById('card_expiry').required = false;
    document.getElementById('card_cvv').required = false;
    document.getElementById('card_name').required = false;
    document.getElementById('transfer_bank').required = false;
    document.getElementById('transfer_reference').required = false;
    document.getElementById('transfer_date').required = false;

    // Show fields based on selected payment method
    if (paypalRadio.checked) {
      paypalFields.classList.remove('hidden');
      paypalName.required = true;
      paypalCountry.required = true;
    } else if (cardRadio.checked) {
      cardFields.classList.remove('hidden');
      document.getElementById('card_number').required = true;
      document.getElementById('card_expiry').required = true;
      document.getElementById('card_cvv').required = true;
      document.getElementById('card_name').required = true;
    } else if (transferRadio.checked) {
      transferFields.classList.remove('hidden');
      document.getElementById('transfer_bank').required = true;
      document.getElementById('transfer_reference').required = true;
      document.getElementById('transfer_date').required = true;
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

  cardOption.addEventListener('click', () => {
    cardRadio.checked = true;
    toggleFields();
  });

  transferOption.addEventListener('click', () => {
    transferRadio.checked = true;
    toggleFields();
  });

  // Also keep the original radio button change listeners
  efectivoRadio.addEventListener('change', toggleFields);
  paypalRadio.addEventListener('change', toggleFields);
  cardRadio.addEventListener('change', toggleFields);
  transferRadio.addEventListener('change', toggleFields);

  // Initialize fields state
  toggleFields();

  // Handle cancel button click
  cancelBtn.addEventListener('click', (e) => {
    e.preventDefault();
    Swal.fire({
      title: '¿Estás seguro?',
      text: '¿Realmente deseas cancelar el proceso de pago?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, cancelar',
      cancelButtonText: 'No, continuar'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = '/views/appointment/list/list-appointments.view.php';
      }
    });
  });

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

    // Validate Credit Card specific fields
    if (selected.value === 'card') {
      const cardNumber = document.getElementById('card_number');
      const cardExpiry = document.getElementById('card_expiry');
      const cardCvv = document.getElementById('card_cvv');
      const cardName = document.getElementById('card_name');

      // Validate card number (simple validation for demo)
      if (!cardNumber.value.trim() || cardNumber.value.trim().length < 13) {
        Swal.fire({
          title: 'Error',
          text: 'Ingresa un número de tarjeta válido.',
          icon: 'error',
          confirmButtonText: 'Entendido'
        });
        cardNumber.focus();
        return;
      }

      // Validate expiry date (format MM/YY)
      const expiryRegex = /^(0[1-9]|1[0-2])\/([0-9]{2})$/;
      if (!cardExpiry.value.trim() || !expiryRegex.test(cardExpiry.value.trim())) {
        Swal.fire({
          title: 'Error',
          text: 'Ingresa una fecha de expiración válida (MM/AA).',
          icon: 'error',
          confirmButtonText: 'Entendido'
        });
        cardExpiry.focus();
        return;
      }

      // Validate CVV (3-4 digits)
      const cvvRegex = /^[0-9]{3,4}$/;
      if (!cardCvv.value.trim() || !cvvRegex.test(cardCvv.value.trim())) {
        Swal.fire({
          title: 'Error',
          text: 'Ingresa un código CVV válido (3 o 4 dígitos).',
          icon: 'error',
          confirmButtonText: 'Entendido'
        });
        cardCvv.focus();
        return;
      }

      // Validate cardholder name
      if (!cardName.value.trim()) {
        Swal.fire({
          title: 'Error',
          text: 'Ingresa el nombre como aparece en la tarjeta.',
          icon: 'error',
          confirmButtonText: 'Entendido'
        });
        cardName.focus();
        return;
      }
    }

    // Validate Bank Transfer specific fields
    if (selected.value === 'transfer') {
      const transferBank = document.getElementById('transfer_bank');
      const transferReference = document.getElementById('transfer_reference');
      const transferDate = document.getElementById('transfer_date');

      // Validate bank selection
      if (!transferBank.value) {
        Swal.fire({
          title: 'Error',
          text: 'Selecciona el banco desde el que realizaste la transferencia.',
          icon: 'error',
          confirmButtonText: 'Entendido'
        });
        transferBank.focus();
        return;
      }

      // Validate reference number
      if (!transferReference.value.trim()) {
        Swal.fire({
          title: 'Error',
          text: 'Ingresa el número de referencia de la transferencia.',
          icon: 'error',
          confirmButtonText: 'Entendido'
        });
        transferReference.focus();
        return;
      }

      // Validate transfer date
      if (!transferDate.value) {
        Swal.fire({
          title: 'Error',
          text: 'Selecciona la fecha en que realizaste la transferencia.',
          icon: 'error',
          confirmButtonText: 'Entendido'
        });
        transferDate.focus();
        return;
      }
    }

    // Show confirmation dialog
    let paymentMethod = '';
    if (selected.value === 'efectivo') {
      paymentMethod = 'efectivo';
    } else if (selected.value === 'paypal') {
      paymentMethod = 'PayPal';
    } else if (selected.value === 'card') {
      paymentMethod = 'tarjeta de crédito/débito';
    } else if (selected.value === 'transfer') {
      paymentMethod = 'transferencia bancaria';
    }

    Swal.fire({
      title: '¿Confirmar pago?',
      text: `¿Estás seguro de que deseas proceder con el pago de $${montoInput.value} utilizando ${paymentMethod}?`,
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
