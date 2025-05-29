document.querySelectorAll('.delete-btn').forEach((button) => {
  button.addEventListener('click', async function (event) {

    event.preventDefault();

    const result = await Swal.fire({
      title: '¿Estás seguro de que deseas eliminar este paciente?',
      text: '',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Eliminar',
      cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
      // Get the form that contains this button
      const form = this.closest('form');

      // Submit the form
      form.submit();
    }
  });
});

// Control botón para activar/desactivar asistente de voz
document.getElementById('voiceToggleBtn').addEventListener('click', () => {
  VoiceAssistant.toggle();

  const btn = document.getElementById('voiceToggleBtn');
  if (VoiceAssistant.isActive()) {
    btn.textContent = 'Desactivar Asistente de Voz';
  } else {
    btn.textContent = 'Activar Asistente de Voz';
  }
});
