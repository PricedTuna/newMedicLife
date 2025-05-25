document.addEventListener('DOMContentLoaded', () => {
    const efectivoRadio = document.getElementById('efectivo');
    const paypalRadio = document.getElementById('paypal');
    const cashFields = document.getElementById('cash-fields');
    const paypalFields = document.getElementById('paypal-fields');
    const montoInput = document.getElementById('monto');
    const pagaConInput = document.getElementById('paga_con');
    const restanteDisplay = document.getElementById('restante');

    function toggleFields() {
        if (efectivoRadio.checked) {
            cashFields.classList.remove('hidden');
            paypalFields.classList.add('hidden');
            // Hacer required solo los campos de efectivo
            montoInput.required = true;
            pagaConInput.required = true;

            // Quitar required a campos de PayPal
            document.getElementById('paypal_name').required = false;
            document.getElementById('paypal_email').required = false;
            document.getElementById('paypal_country').required = false;
        } else if (paypalRadio.checked) {
            cashFields.classList.add('hidden');
            paypalFields.classList.remove('hidden');
            // Quitar required a campos de efectivo
            montoInput.required = false;
            pagaConInput.required = false;

            // Hacer required solo los campos de PayPal
            document.getElementById('paypal_name').required = true;
            document.getElementById('paypal_email').required = true;
            document.getElementById('paypal_country').required = true;
        }
    }

    function calcularRestante() {
        const monto = parseFloat(montoInput.value) || 0;
        const pagaCon = parseFloat(pagaConInput.value) || 0;
        const cambio = pagaCon - monto;
        restanteDisplay.textContent = `Restante: $${cambio.toFixed(2)}`;
    }

    efectivoRadio.addEventListener('change', toggleFields);
    paypalRadio.addEventListener('change', toggleFields);

    montoInput.addEventListener('input', calcularRestante);
    pagaConInput.addEventListener('input', calcularRestante);

    // Inicializa bien los campos según el radio seleccionado (si uno ya viene seleccionado)
    toggleFields();
});
