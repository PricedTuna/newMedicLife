console.log("LO ESTA CARGANDO");

// Escuchar clics en botones de borrar paciente
document.querySelectorAll(".delete-btn").forEach((button) => {
  button.addEventListener("click", async function (event) {
    const patientId = this.getAttribute("data-id"); // Cambié doctorId por patientId para que coincida con el body
    console.log("ID del paciente a eliminar:", patientId);
      event.preventDefault(); // Prevent form submission

      const result = await Swal.fire({
          title: "¿Estás seguro de que deseas eliminar este paciente?",
          text: "",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Eliminar",
          cancelButtonText: "Cancelar"
      });

    if (result.isConfirmed) {
      fetch("/controllers/patient/delete-patient.controller.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `patient_id=${encodeURIComponent(patientId)}`,
      })
        .then((response) => {
          console.log("Respuesta recibida:", response);
          return response.json();
        })
        .then((data) => {
          console.log("Datos recibidos:", data);
          if (data.success) {
            alert(data.message);
            location.reload();
          } else {
            alert("Error: " + data.message);
          }
        })
        .catch((error) => console.error("Error en la petición:", error));
    }
  });
});

// Control botón para activar/desactivar asistente de voz
document.getElementById("voiceToggleBtn").addEventListener("click", () => {
  VoiceAssistant.toggle();

  const btn = document.getElementById("voiceToggleBtn");
  if (VoiceAssistant.isActive()) {
    btn.textContent = "Desactivar Asistente de Voz";
  } else {
    btn.textContent = "Activar Asistente de Voz";
  }
});
