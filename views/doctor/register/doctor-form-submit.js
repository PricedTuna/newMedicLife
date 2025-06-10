document.addEventListener('DOMContentLoaded', function() {
    // Add event listener for the submit button
    const submitBtn = document.getElementById('submit-doctor-btn');
    const doctorForm = document.getElementById('doctor-form');
    
    if (submitBtn && doctorForm) {
        const isUpdateMode = document.querySelector('input[name="id"]') && document.querySelector('input[name="id"]').value;

        submitBtn.addEventListener('click', function() {
            // Validate the form first
            if (!doctorForm.checkValidity()) {
                // If the form is not valid, trigger the browser's validation
                doctorForm.reportValidity();
                return;
            }
            
            if (!isUpdateMode) {
                // Show confirmation dialog for new doctors
                Swal.fire({
                    title: '¿Crear usuario para este doctor?',
                    text: '¿Desea crear un usuario en la sección de usuarios para este doctor?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, crear usuario',
                    cancelButtonText: 'No, solo registrar doctor'
                }).then((result) => {
                    // Add a hidden field to the form with the result
                    let createUserInput = document.querySelector('input[name="create_user"]');
                    if (!createUserInput) {
                        createUserInput = document.createElement('input');
                        createUserInput.type = 'hidden';
                        createUserInput.name = 'create_user';
                        doctorForm.appendChild(createUserInput);
                    }
                    createUserInput.value = result.isConfirmed ? '1' : '0';

                    // Submit the form
                    doctorForm.submit();
                });
            } else {
                // For update mode, just submit the form
                doctorForm.submit();
            }
        });
    }
});