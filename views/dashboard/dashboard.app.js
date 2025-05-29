document.addEventListener("DOMContentLoaded", () => {

  const now = new Date();

  // FILTRO POR DOCTOR
  const doctorSelect = document.getElementById("doctor-select");
  const tableRows = document.querySelectorAll(".chart-placeholder tbody tr");

  generateCalendar(now.getFullYear(), now.getMonth(), doctorSelect.value);

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
    let numerAppointments = 0;
    tableRows.forEach((row) => {
      if (!selectedDoctorId || row.dataset.doctorId === selectedDoctorId) {
        numerAppointments++;
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });

    // Buscar datos del doctor
    const doctor = doctors.find((doctor) => ''+doctor.id === selectedDoctorId);
    console.log({doctors, selectedDoctorId, doctor})

    if (doctor) {
      const now = new Date();
      const firstDayOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
      const lastDayOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0);

      generateCalendar(now.getFullYear(), now.getMonth(), selectedDoctorId);
      // Filtrar citas futuras
      const upcomingAppointments = appointments
        .filter(
          (a) =>
            a.id_doctor == selectedDoctorId &&
            new Date(a.appointment_date) > now &&
            a.status == "A"
        )
        .sort(
          (a, b) => new Date(a.appointment_date) - new Date(b.appointment_date)
        );

      // Filtrar citas del mes actual
      const monthAppointments = appointments
        .filter((a) => {
          const apptDate = new Date(a.appointment_date);
          return (
            a.id_doctor == selectedDoctorId &&
            apptDate >= firstDayOfMonth &&
            apptDate <= lastDayOfMonth &&
            a.status == "A"
          );
        })
        .sort(
          (a, b) => new Date(a.appointment_date) - new Date(b.appointment_date)
        );

      const nextAppointment = upcomingAppointments[0];

      // Mostrar datos del doctor
      document.getElementById("doctor-name").textContent = `${doctor.names} ${
        doctor.last_name
      } ${doctor.last_name2 || ""}`;

      const photoDiv = document.getElementById("doctor-photo");
      photoDiv.style.backgroundImage = `url('/controllers/doctor/mostrar_foto.php?id=${doctor.id}')`;
      photoDiv.style.backgroundSize = "cover";
      photoDiv.style.backgroundPosition = "center";

      document.getElementById("doctor-appointments").textContent =
        numerAppointments;

      // Mostrar próxima cita
      document.getElementById("next-appointments").textContent = nextAppointment
        ? new Date(nextAppointment.appointment_date).toLocaleString()
        : "Sin próximas citas";

      // Mostrar todas las citas del mes actual con ID
      const monthAppointmentsContainer =
        document.getElementById("month-appointments");
      monthAppointmentsContainer.innerHTML = ""; // Limpiar contenido anterior

      if (monthAppointments.length > 0) {
        monthAppointments.forEach((appt) => {
          const apptDate = new Date(appt.appointment_date).toLocaleString();
          const item = document.createElement("li");
          item.textContent = `Cita: ${appt.cita} - Fecha: ${apptDate}`;
          monthAppointmentsContainer.appendChild(item);
        });
      } else {
        monthAppointmentsContainer.innerHTML = "<li>Sin citas este mes</li>";
      }
    }
  });
});

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

    const dayStr = new Date(year, month, day).toISOString().split("T")[0];

    // Doctor seleccionado actual (puedes obtenerlo así para la generación del calendario)
    const selectedDoctorId = document.getElementById("doctor-select")?.value || null;

    // Verificar si hay cita en ese día para el doctor seleccionado
    const hasAppointment = appointments.some((appt) => {
      const apptDateStr = new Date(appt.appointment_date)
        .toISOString()
        .split("T")[0];
      return (
        apptDateStr === dayStr &&
        appt.status === "A" &&
        appt.id_doctor == selectedDoctorId
      );
    });

    if (hasAppointment) {
      dayElement.classList.add("has-appointment"); // Clase para poner fondo amarillo
    }

    dayElement.onclick = () => {
      const selectedDoctorId = document.getElementById("doctor-select").value;
      const selectedDate = new Date(year, month, day);

      // Normalizar a solo YYYY-MM-DD
      const selectedDayStr = selectedDate.toISOString().split("T")[0];

      const filteredAppointments = appointments.filter((appt) => {
        const apptDateStr = new Date(appt.appointment_date)
          .toISOString()
          .split("T")[0];
        return (
          apptDateStr === selectedDayStr &&
          appt.status === "A" &&
          appt.id_doctor == selectedDoctorId
        );
      });

      const container = document.getElementById("day-appointments");
      container.innerHTML = "";

      if (filteredAppointments.length > 0) {
        filteredAppointments.forEach((appt) => {
          const apptItem = document.createElement("li");
          const apptTime = new Date(appt.appointment_date).toLocaleTimeString();
          apptItem.textContent = `Cita: ${appt.cita} - Hora: ${apptTime} - Paciente: ${appt.patient_names}  ${appt.patient_last_name}  ${appt.patient_last_name2}`;
          container.appendChild(apptItem);
        });
      } else {
        container.innerHTML = "<li>Sin citas este día</li>";
      }
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
