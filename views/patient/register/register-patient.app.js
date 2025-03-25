let currentStep = 1;

function showStep(step) {
    document.querySelectorAll('.form-step').forEach((formStep) => {
        formStep.style.display = 'none';
    });

    let activeStep = document.getElementById(`step-${step}`);
    if (activeStep) {
        activeStep.style.display = 'block';
    }

    document.querySelectorAll('.step').forEach((stepElement) => {
        stepElement.classList.remove('step-active');
    });

    let currentStepElement = document.querySelector(`.step[data-step='${step}']`);
    if (currentStepElement) {
        console.log({ currentStepElement })
        console.log(currentStepElement)
        currentStepElement.classList.add('step-active');
    }

    console.log(`Mostrando paso: ${step}`); // Debugging
}

function nextStep(step) {
    if (validateStep(currentStep)) {
        currentStep = step;
        showStep(currentStep);
    } else {
        alert('Por favor, completa todos los campos obligatorios antes de continuar.');
    }
}

function prevStep(step) {
    currentStep = step;
    showStep(currentStep);
}

function validateStep(step) {
    let valid = true;
    document.querySelectorAll(`#step-${step} input[required], #step-${step} select[required]`).forEach((input) => {
        if (!input.value.trim()) {
            valid = false;
            input.style.border = '2px solid red';
        } else {
            input.style.border = '2px solid var(--line-clr)';
        }
    });



    return valid;
}

document.getElementById('curp').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});


document.addEventListener('DOMContentLoaded', () => {
    showStep(currentStep);

    document.getElementById('patient-form').addEventListener('submit', (event) => {
        if (!validateStep(currentStep)) {
            event.preventDefault();
            alert('Por favor, completa todos los campos antes de enviar.');
        } else {
            alert('Registro exitoso');
        }
    });
});