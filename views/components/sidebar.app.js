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

    try {
      const response = await fetch(audioFile, { method: "HEAD" });
      if (!response.ok) {
        console.error(
          `Audio no encontrado para el título: "${document.title}" (ruta: "${audioFile}")`
        );
        setActive(false);
        return;
      }
    } catch (error) {
      console.error("Error al verificar archivo de audio:", error);
      setActive(false);
      return;
    }

    if (currentAudio) {
      currentAudio.pause();
      currentAudio.currentTime = 0;
    }

    currentAudio = new Audio(audioFile);
    currentAudio.play();
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
    if (e.ctrlKey && e.key === "i") {
      e.preventDefault();
      showShortcutToast('Navegando al panel principal...');
      setTimeout(() => window.location.href = "/views/dashboard/dashboard.view.php", 500);
    }
    if (e.ctrlKey && e.key === "m") {
      e.preventDefault();
      showShortcutToast('Navegando a Médicos...');
      setTimeout(() => window.location.href = "/views/doctor/main/main-doctor.view.php", 500);
    }
    if (e.ctrlKey && e.key === "p") {
      e.preventDefault();
      showShortcutToast('Navegando a Pacientes...');
      setTimeout(() => window.location.href = "/views/patient/main/main-patient.view.php", 500);
    }
    if (e.ctrlKey && e.key === "u") {
      e.preventDefault();
      showShortcutToast('Navegando a Usuarios...');
      setTimeout(() => window.location.href = "/views/user/list/list-users.view.php", 500);
    }
    if (e.ctrlKey && e.key === "c") {
      e.preventDefault();
      showShortcutToast('Navegando a Citas...');
      setTimeout(() => window.location.href = "/views/appointment/list/list-appointments.view.php", 500);
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
