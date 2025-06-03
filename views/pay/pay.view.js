document.addEventListener('DOMContentLoaded', () => {
  const efectivoRadio = document.getElementById('efectivo');
  const paypalRadio = document.getElementById('paypal');
  const paypalFields = document.getElementById('paypal-fields');
  const montoInput = document.getElementById('monto');
  const paypalName = document.getElementById('paypal_name');
  const paypalCountry = document.getElementById('paypal_country');
  const form = document.getElementById('payment-form');

  function toggleFields() {
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

  efectivoRadio.addEventListener('change', toggleFields);
  paypalRadio.addEventListener('change', toggleFields);
  toggleFields();

  form.addEventListener('submit', (e) => {
    const selected = document.querySelector('input[name="metodo_pago"]:checked');
    if (!selected) {
      alert('Por favor, selecciona un método de pago.');
      e.preventDefault();
      return;
    }

    if (!montoInput.value || parseFloat(montoInput.value) <= 0) {
      alert('Ingresa un monto válido.');
      montoInput.focus();
      e.preventDefault();
      return;
    }

    if (selected.value === 'paypal') {
      if (!paypalName.value.trim()) {
        alert('Ingresa tu nombre completo.');
        paypalName.focus();
        e.preventDefault();
        return;
      }

      if (!paypalCountry.value) {
        alert('Selecciona un país.');
        paypalCountry.focus();
        e.preventDefault();
        return;
      }
    }
  });
});
