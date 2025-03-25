
console.log("LO ESTA CARGANDO")
document.querySelectorAll(".delete-btn").forEach(button => {
    button.addEventListener("click", function () {
        const doctorId = this.getAttribute("data-id");
        console.log("ID del doctor a eliminar:", doctorId); // 🔴 Verifica si el botón tiene el ID correcto

        if (confirm("¿Estás seguro de que deseas eliminar este doctor?")) {
            fetch("/controllers/doctor/delete-doctor.controller.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `doctor_id=${doctorId}`
            })
            .then(response => {
                console.log("Respuesta recibida:", response); // 🔴 Verifica si la respuesta llega
                return response.json();
            })
            .then(data => {
                console.log("Datos recibidos:", data); // 🔴 Verifica la respuesta JSON
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => console.error("Error en la petición:", error));
        }
    });
});