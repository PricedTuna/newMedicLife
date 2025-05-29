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
        if (e.ctrlKey && e.key === 'd') {
            e.preventDefault();
            window.location.href = '/views/dashboard/dashboard.view.php';
        }
        if (e.ctrlKey && e.key === 'm') {
            e.preventDefault();
            window.location.href = '/views/doctor/list/list-doctors.view.php';
        }
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            window.location.href = '/views/patient/list/list-patients.view.php';
        }
        if (e.ctrlKey && e.key === 'u') {
            e.preventDefault();
            window.location.href = '/views/user/list/list-users.view.php';
        }
        if (e.ctrlKey && e.key === 'c') {
            e.preventDefault();
            window.location.href = '/views/appointment/list/list-appointments.view.php';
        }
    });

});
