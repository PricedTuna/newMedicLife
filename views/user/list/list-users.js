console.log("Loading user deletion script");
document.querySelectorAll(".delete-btn").forEach(button => {
    button.addEventListener("click", async function(event) {
        event.preventDefault(); // Prevent form submission

        const userId = this.getAttribute("data-id");
        console.log("ID del usuario a eliminar:", userId);

        const result = await Swal.fire({
            title: "¿Estás seguro de que deseas eliminar este usuario?",
            text: "",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Eliminar",
            cancelButtonText: "Cancelar"
        });

        if (result.isConfirmed) {
            // Get the form that contains this button
            const form = this.closest('form');

            // Submit the form
            form.submit();
        }
    });
});
