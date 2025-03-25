const usersDB = [
    {
        email: "test@test.com",
        password: "test"
    }
];

// Función para validar el correo y la contraseña
function validateLogin(event) {
    event.preventDefault(); // Prevenir el envío del formulario

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    const user = usersDB.find(user => user.email === email && user.password === password);

    if (user) {
        // Redirigir a dashboard.html si el usuario es válido
        window.location.href = 'dashboard/dashboard.html';
    } else {
        alert('Correo o contraseña incorrectos.');
    }
}

// Añadir evento de envío al formulario
document.querySelector('form').addEventListener('submit', validateLogin);
