

document.querySelectorAll(".delete-btn").forEach(button => {
    button.addEventListener("click", async function (event) {
        event.preventDefault();

        const result = await Swal.fire({
            title: "¿Estás seguro de que deseas eliminar este doctor?",
            text: "",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Eliminar",
            cancelButtonText: "Cancelar"
        })

        if (result.isConfirmed) {
            const form = this.closest('form');
            form.submit();
        }
    });
});
