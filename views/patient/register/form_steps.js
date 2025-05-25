document.addEventListener('DOMContentLoaded', function () {
    // Validar si las variables de JavaScript tienen datos
    if (!window.municipalities || !window.localities || !window.states) {
        console.error('Error: Las variables no se han cargado correctamente.');
        alert('Error: Las variables necesarias no se han cargado correctamente. Por favor, intente recargar la página.');
        return; // Detener la ejecución si las variables no se cargaron correctamente
    }

    // Ahora que sabemos que las variables están disponibles, podemos usar el código que manipula esas variables
    const stateSelect = document.getElementById('state');
    const municipalitySelect = document.getElementById('municipality');
    const localitySelect = document.getElementById('locality');

    stateSelect.addEventListener('change', () => {
        const selectedState = stateSelect.value;

        municipalitySelect.innerHTML = '<option value="">Seleccione...</option>';
        localitySelect.innerHTML = '<option value="">Seleccione un municipio primero...</option>';

        if (selectedState) {
            const filteredMunicipalities = window.municipalities.filter(m => m.id_state == selectedState);
            filteredMunicipalities.forEach(m => {
                municipalitySelect.innerHTML += `<option value="${m.id}">${m.name}</option>`;
            });
        }
    });

    municipalitySelect.addEventListener('change', () => {
        const selectedState = stateSelect.value;
        const selectedMunicipality = municipalitySelect.value;

        localitySelect.innerHTML = '<option value="">Seleccione...</option>';

        if (selectedMunicipality) {
            const filteredLocalities = window.localities.filter(l =>
                l.id_state == selectedState && l.id_municipality == selectedMunicipality
            );
            filteredLocalities.forEach(l => {
                localitySelect.innerHTML += `<option value="${l.id}">${l.name}</option>`;
            });
        }
    });

    const photoInput = document.getElementById('photo');
    if (photoInput) {
        if (photoInput.hasAttribute('required') && photoInput.files.length === 0) {
            showErrorMessage(photoInput, 'Debe seleccionar una foto.');
            valid = false;
        } else if (photoInput.files.length > 0) {
            const file = photoInput.files[0];
            if (!file.type.startsWith('image/')) {
                showErrorMessage(photoInput, 'El archivo debe ser una imagen.');
                valid = false;
            } else {
                clearErrorMessage(photoInput);
            }
        }
    }

    document.getElementById('photo').addEventListener('change', event => {
    const fileName = event.target.files[0] ? event.target.files[0].name : 'Subir Foto';
    document.getElementById('photo-label').textContent = fileName;
});
});

function clearErrorMessage(input) {
    let errorElement = input.nextElementSibling;
    if (errorElement && errorElement.classList.contains('error-message')) {
        errorElement.remove();
    }
    input.style.border = '2px solid var(--line-clr)';
}
