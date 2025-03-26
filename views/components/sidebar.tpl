<link rel="stylesheet" href="{$base_path}/components/sidebar.styles.css">
<script src="{$base_path}/components/sidebar.app.js" defer></script>
<nav id="sidebar">
    <ul>
        <li>
            <span class="logo">
                MedicLife</span>

            <button onclick="toggleSidebar()" id="toggle-btn">
                <img src="./icons/left-double-arrow.svg" alt="Dashboard icon" width="25" height="25">
            </button>
        </li>
        <li class="active">
            <a href="{$base_path}/views/dashboard/dashboard.view.php">
                <img src="./icons/dashboard.svg" alt="Dashboard icon" width="25" height="25">
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="{$base_path}/views/patient/register/register-patient.view.php">
                <img src="./icons/patients.svg" alt="Dashboard icon" width="25" height="25">
                <span>Pacientes</span>
            </a>
        </li>

        <li>
            <a href="{$base_path}/views/doctor/main/main-doctor.view.php">
                <img src="./icons/doctors.svg" alt="Dashboard icon" width="25" height="25">
                <span>Medicos</span>
            </a>
        </li>
        <li>
            <a href="usuario.html">
                <img src="./icons/account.svg" alt="Dashboard icon" width="25" height="25">
                <span>Usuario</span>
            </a>
        </li>
        <li>
            <a href="{$base_path}/index.php">
                <img src="./icons/logout.svg" alt="Dashboard icon" width="25" height="25">
                <span>Cerrar Sesión</span>
            </a>
        </li>
    </ul>
</nav>