const sidebarToggleButton = document.getElementById('toggle-btn')
const sidebar = document.getElementById('sidebar')

function toggleSidebar() {
    sidebar.classList.toggle('close')
    sidebarToggleButton.classList.toggle('rotate')
}

document.addEventListener("DOMContentLoaded", function() {
    const currentLocation = window.location.pathname.split("/").pop();

    const menuItems = document.querySelectorAll("#sidebar ul li a"); // Selecciona todos los enlaces del sidebar

    menuItems.forEach((item) => {
        const itemPath = item.getAttribute("href").split("/").pop();

        if (itemPath === currentLocation) {
            item.parentElement.classList.add("active"); // Agrega la clase active al <li>
        } else {
            item.parentElement.classList.remove("active"); // Elimina la clase si no coincide
        }
    });

    //  ========= keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault(); // Evita que se abra el guardado del navegador
            alert('¡Atajo Ctrl + S activado!');
        }
    });

});
