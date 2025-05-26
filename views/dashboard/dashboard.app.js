document.addEventListener("DOMContentLoaded", () => {
  if (typeof doctors !== "undefined") {
    console.log("Variables cargadas");
    console.log(doctors);
  } else {
    console.log("variable de doctor no cargada ");
  }

  const now = new Date();
  generateCalendar(now.getFullYear(), now.getMonth());

  // FILTRO POR DOCTOR
  const doctorSelect = document.getElementById("doctor-select");
  const tableRows = document.querySelectorAll(".chart-placeholder tbody tr");

  doctorSelect.addEventListener("change", () => {
    const selectedDoctorId = doctorSelect.value;

    tableRows.forEach((row) => {
      // Mostrar todas si no hay doctor seleccionado
      if (!selectedDoctorId || row.dataset.doctorId === selectedDoctorId) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });
  });

  doctorSelect.addEventListener("change", () => {
    const selectedDoctorId = doctorSelect.value;

    // Mostrar/ocultar filas por doctor
    tableRows.forEach((row) => {
      if (!selectedDoctorId || row.dataset.doctorId === selectedDoctorId) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });

    // Buscar datos del doctor
    const doctor = doctors.find((d) => d.id == selectedDoctorId);

    if (doctor) {
      // Mostrar datos del doctor
      document.getElementById("doctor-name").textContent = `${doctor.names} ${
        doctor.last_name
      } ${doctor.last_name2 || ""}`;

      // Cargar foto desde PHP por ID
      const photoDiv = document.getElementById("doctor-photo");
      photoDiv.style.backgroundImage = `url('/controllers/doctor/mostrar_foto.php?id=${doctor.id}')`;
      photoDiv.style.backgroundSize = "cover";
      photoDiv.style.backgroundPosition = "center";

      // Ejemplo de campos que puedes haber precargado
      document.getElementById("doctor-appointments").textContent =
        doctor.totalAppointments || 0;
        
      // Opcional: podrías cargar citas futuras y del mes aquí si ya están disponibles
    }
  });
});

console.log("Dashboard.js cargado correctamente");

function generateCalendar(year, month) {
  const calendar = document.getElementById("calendar");
  if (!calendar) {
    console.error("Elemento #calendar no encontrado");
    return;
  }

  const monthNames = [
    "Enero",
    "Febrero",
    "Marzo",
    "Abril",
    "Mayo",
    "Junio",
    "Julio",
    "Agosto",
    "Septiembre",
    "Octubre",
    "Noviembre",
    "Diciembre",
  ];

  // Limpiar calendario
  calendar.innerHTML = "";

  // Crear encabezado del mes
  const header = document.createElement("div");
  header.className = "calendar-header";
  header.innerHTML = `
        <button id="prevMonth" class="nav-button">←</button>
        <h3>${monthNames[month]} ${year}</h3>
        <button id="nextMonth" class="nav-button">→</button>
    `;
  calendar.appendChild(header);

  // Días de la semana
  const daysOfWeek = ["D", "L", "M", "X", "J", "V", "S"];
  const daysContainer = document.createElement("div");
  daysContainer.className = "calendar-days";
  daysOfWeek.forEach((day) => {
    const dayElement = document.createElement("div");
    dayElement.className = "day-name";
    dayElement.textContent = day;
    daysContainer.appendChild(dayElement);
  });

  const daysInMonth = new Date(year, month + 1, 0).getDate();
  const firstDay = new Date(year, month, 1).getDay();

  // Espacios vacíos al inicio
  for (let i = 0; i < firstDay; i++) {
    const empty = document.createElement("div");
    empty.className = "day empty";
    daysContainer.appendChild(empty);
  }

  // Días del mes
  for (let day = 1; day <= daysInMonth; day++) {
    const dayElement = document.createElement("div");
    dayElement.className = "day";
    dayElement.textContent = day;
    dayElement.onclick = () => {
      alert(`Cita para el ${day} de ${monthNames[month]} de ${year}`);
    };
    daysContainer.appendChild(dayElement);
  }

  calendar.appendChild(daysContainer);

  // Navegación
  document.getElementById("prevMonth").onclick = () => {
    const newDate = new Date(year, month - 1);
    generateCalendar(newDate.getFullYear(), newDate.getMonth());
  };

  document.getElementById("nextMonth").onclick = () => {
    const newDate = new Date(year, month + 1);
    generateCalendar(newDate.getFullYear(), newDate.getMonth());
  };
}
