document.querySelectorAll(".delete-btn").forEach((button) => {
  button.addEventListener("click", async function () {
    const appointmentId = this.getAttribute("data-id");

      const result = await Swal.fire({
          title: "¿Estás seguro de que deseas eliminar esta cita?",
          text: "",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Eliminar",
          cancelButtonText: "Cancelar"
      });

    if (result.isConfirmed) {
      fetch("/controllers/appoiment/delete-appointment.controller.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `appointment_id=${appointmentId}`,
      })
        .then((response) => {
          
          return response.json();
        })
        .then((data) => {
          
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
  const btn = document.getElementById("voiceBtn");
  btn.addEventListener("click", () => {
    VoiceAssistant.toggle();
    btn.textContent = VoiceAssistant.isActive()
      ? "Desactivar asistente de voz"
      : "Activar asistente de voz";
  });
});
