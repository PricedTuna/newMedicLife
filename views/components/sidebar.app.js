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

  function toggle() {
    if (isActive()) {
      stop();
    } else {
      play();
    }
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

  function actualizarTextoBoton() {
    if (VoiceAssistant.isActive()) {
      btn.textContent = "🔈Activado";
    } else {
      btn.textContent = "🔇Desactivado";
    }
  }

  actualizarTextoBoton();
  menuItems.forEach((item) => {
    const itemPath = item.getAttribute("href").split("/").pop();

    if (itemPath === currentLocation) {
      item.parentElement.classList.add("active"); // Agrega la clase active al <li>
    } else {
      item.parentElement.classList.remove("active"); // Elimina la clase si no coincide
    }
  });

  //  ========= keyboard shortcuts
  document.addEventListener("keydown", function (e) {
    if (e.ctrlKey && e.key === "d") {
      e.preventDefault();
      window.location.href = "/views/dashboard/dashboard.view.php";
    }
    if (e.ctrlKey && e.key === "m") {
      e.preventDefault();
      window.location.href = "/views/doctor/list/list-doctors.view.php";
    }
    if (e.ctrlKey && e.key === "p") {
      e.preventDefault();
      window.location.href = "/views/patient/list/list-patients.view.php";
    }
    if (e.ctrlKey && e.key === "u") {
      e.preventDefault();
      window.location.href = "/views/user/list/list-users.view.php";
    }
    if (e.ctrlKey && e.key === "c") {
      e.preventDefault();
      window.location.href =
        "/views/appointment/list/list-appointments.view.php";
    }
  });

  btn.addEventListener("click", () => {
    VoiceAssistant.toggle();
    actualizarTextoBoton();
  });
});
