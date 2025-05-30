console.log("Se esta cargando el login")

document.addEventListener("DOMContentLoaded", () => {
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");
    const errorBox = document.getElementById("form-error");

    // Validaciones al perder el foco (onblur)
    emailInput.addEventListener("blur", () => {
        const email = emailInput.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!email) {
            showError("El correo electrónico es obligatorio.");
        } else if (!emailRegex.test(email)) {
            showError("El formato del correo electrónico no es válido.");
        } else {
            clearError();
        }
    });

    passwordInput.addEventListener("blur", () => {
        const password = passwordInput.value.trim();

        if (!password) {
            showError("La contraseña es obligatoria.");
        } else {
            clearError();
        }
    });

    function showError(message) {
        errorBox.textContent = message;
        errorBox.style.display = "block";
    }

    function clearError() {
        errorBox.textContent = "";
        errorBox.style.display = "none";
    }
});
