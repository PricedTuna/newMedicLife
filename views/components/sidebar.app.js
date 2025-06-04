const sidebarToggleButton = document.getElementById("toggle-btn");

// voiceAssistant.js (debe estar antes o separado, fuera del DOMContentLoaded)
const VoiceAssistant = (() => {
  let currentAudio = null;

  // Obtiene la ruta del audio basándose en el título de la página
  function getAudioForPath() {
    // Obtiene el título de la página, por ejemplo "Lista de Usuarios"
    const title = document.title || "home";

    // Normaliza el título para usarlo en la ruta del archivo de audio
    const name = title
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "") // Quita acentos
      .replace(/\s+/g, "-") // Reemplaza espacios por guiones
      .replace(/[^\w-]/g, ""); // Quita caracteres no alfanuméricos excepto guiones

    // Construye la ruta del audio
    const audioPath = `/audio/${name}.mp3`;
    console.log(audioPath);


    return audioPath;
  }

  function isActive() {
    return localStorage.getItem("voiceAssistantActive") === "true";
  }

  function setActive(value) {
    localStorage.setItem("voiceAssistantActive", value ? "true" : "false");
  }

  async function play() {
    setActive(true);

    const audioFile = getAudioForPath();
    let audioToPlay = audioFile;

    try {
      // Verificar si el archivo de audio existe
      const response = await fetch(audioFile, { method: "HEAD" });
      if (!response.ok) {
        console.warn(
          `Audio no encontrado para el título: "${document.title}" (ruta: "${audioFile}"). Intentando usar audio por defecto.`
        );

        // Intentar usar un audio por defecto (dashboard.mp3)
        const defaultAudio = "/audio/dashboard.mp3";
        const defaultResponse = await fetch(defaultAudio, { method: "HEAD" });

        if (defaultResponse.ok) {
          console.log("Usando audio por defecto:", defaultAudio);
          audioToPlay = defaultAudio;
        } else {
          console.error("No se encontró el audio por defecto.");
          return; // No reproducir nada si no hay audio por defecto
        }
      }

      if (currentAudio) {
        currentAudio.pause();
        currentAudio.currentTime = 0;
      }

      currentAudio = new Audio(audioToPlay);
      currentAudio.play();
    } catch (error) {
      console.error("Error al verificar archivo de audio:", error);
      // No desactivamos el asistente si hay un error
      // Solo continuamos sin reproducir audio
    }
  }

  function stop() {
    setActive(false);

    if (currentAudio) {
      currentAudio.pause();
      currentAudio.currentTime = 0;
    }
  }

  function showVoiceStatus(isActive) {
    Swal.fire({
      title: isActive ? 'Asistente de voz activado' : 'Asistente de voz desactivado',
      text: isActive ? 'El asistente de voz te guiará por la aplicación' : 'Has desactivado el asistente de voz',
      icon: isActive ? 'success' : 'info',
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });
  }

  function toggle() {
    const wasActive = isActive();
    if (wasActive) {
      stop();
    } else {
      play();
    }
    showVoiceStatus(!wasActive);
  }

  return {
    play,
    stop,
    toggle,
    isActive,
  };
})();

const sidebar = document.getElementById("sidebar");

function toggleSidebar() {
  sidebar.classList.toggle("close");
  sidebarToggleButton.classList.toggle("rotate");
}

document.addEventListener("DOMContentLoaded", function () {
  const currentLocation = window.location.pathname.split("/").pop();
  const btn = document.getElementById("voiceToggleBtn");
  const menuItems = document.querySelectorAll("#sidebar ul li a"); // Selecciona todos los enlaces del sidebar

  if (VoiceAssistant.isActive()) {
    VoiceAssistant.play(); // Reproduce en la nueva ruta automáticamente
  }

  // Actualiza el texto del botón solo si existe
  if (btn) {
    function actualizarTextoBoton() {
      if (VoiceAssistant.isActive()) {
        btn.textContent = "🔈Activado";
      } else {
        btn.textContent = "🔇Desactivado";
      }
    }

    actualizarTextoBoton();
  }

  // Siempre actualiza la navegación activa, independientemente del botón
  menuItems.forEach((item) => {
    const itemPath = item.getAttribute("href").split("/").pop();

    if (itemPath === currentLocation) {
      item.parentElement.classList.add("active"); // Agrega la clase active al <li>
    } else {
      item.parentElement.classList.remove("active"); // Elimina la clase si no coincide
    }
  });

  // Función para mostrar toast de atajos de teclado
  function showShortcutToast(text) {
    const toast = document.createElement('div');
    toast.className = 'shortcut-toast';
    toast.textContent = text;
    document.body.appendChild(toast);

    setTimeout(() => {
      toast.classList.add('show');
      setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
      }, 2000);
    }, 10);
  }

  //  ========= keyboard shortcuts
  document.addEventListener("keydown", function (e) {
    // Navegación principal
    if (e.altKey && e.key === "d") {
      e.preventDefault();
      showShortcutToast('Navegando al Dashboard...');
      setTimeout(() => window.location.href = "/views/dashboard/dashboard.view.php", 500);
    }
    if (e.altKey && e.key === "m") {
      e.preventDefault();
      showShortcutToast('Navegando a Médicos...');
      setTimeout(() => window.location.href = "/views/doctor/main/main-doctor.view.php", 500);
    }
    if (e.altKey && e.key === "p") {
      e.preventDefault();
      showShortcutToast('Navegando a Pacientes...');
      setTimeout(() => window.location.href = "/views/patient/main/main-patient.view.php", 500);
    }
    if (e.altKey && e.key === "u") {
      e.preventDefault();
      showShortcutToast('Navegando a Usuarios...');
      setTimeout(() => window.location.href = "/views/user/list/list-users.view.php", 500);
    }
    if (e.altKey && e.key === "c") {
      e.preventDefault();
      showShortcutToast('Navegando a Citas...');
      setTimeout(() => window.location.href = "/views/appointment/list/list-appointments.view.php", 500);
    }

    // Acceso directo a formularios de registro
    if (e.altKey && e.shiftKey && e.key === "M") {
      e.preventDefault();
      showShortcutToast('Accediendo al formulario de registro de médicos...');
      setTimeout(() => window.location.href = "/views/doctor/register/register-doctor.view.php", 500);
    }
    if (e.altKey && e.shiftKey && e.key === "P") {
      e.preventDefault();
      showShortcutToast('Accediendo al formulario de registro de pacientes...');
      setTimeout(() => window.location.href = "/views/patient/register/register-patient.view.php", 500);
    }
    if (e.altKey && e.shiftKey && e.key === "C") {
      e.preventDefault();
      showShortcutToast('Accediendo al formulario de registro de citas...');
      setTimeout(() => window.location.href = "/views/appointment/register/register-appoiment.php", 500);
    }
    if (e.altKey && e.shiftKey && e.key === "U") {
      e.preventDefault();
      showShortcutToast('Accediendo al formulario de registro de usuarios...');
      setTimeout(() => window.location.href = "/views/user/register/register-user.view.php", 500);
    }
    if (e.altKey && e.key === "k") {
      e.preventDefault();
      showShortcutToast('Mostrando atajos de teclado...');
      const shortcutsSection = document.querySelector('.shortcuts-info-section');
      if (shortcutsSection) {
        shortcutsSection.scrollIntoView({ behavior: 'smooth' });
        shortcutsSection.classList.add('highlight');
        setTimeout(() => shortcutsSection.classList.remove('highlight'), 2000);
      } else {
        // If shortcuts section is not found, navigate to the settings page
        setTimeout(() => window.location.href = "/views/settings/settings.view.php", 500);
      }
    }
  });

  // Solo agrega el event listener si el botón existe
  if (btn) {
    btn.addEventListener("click", () => {
      VoiceAssistant.toggle();
      actualizarTextoBoton();
    });
  }
});
