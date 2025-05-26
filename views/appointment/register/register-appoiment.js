document.addEventListener("DOMContentLoaded", function () {
  if (
    typeof doctors !== "undefined" &&
    typeof patients !== "undefined" &&
    typeof schedules !== "undefined"
  ) {
    console.log("Variables cargadas");
    console.log(allAppointments);

    if (typeof appointment !== "undefined") {
      console.log("Appointment:", appointment);
    } else {
      console.log("Appointment no definido");
    }
  } else {
    if (typeof appointment !== "undefined" && appointment === null) {
      console.log("Appointment es null, inicializando sin cita previa");
      // Código alternativo
    } else {
      console.warn("Variables faltantes o appointment indefinido");
    }
    return;
  }

  const appointmentDate = document.getElementById("appointmentDate");
  const doctorSelect = document.getElementById("doctor");
  const dateError = document.getElementById("dateError");

  const patientIdInput = document.getElementById("patientId");
  const curpError = document.getElementById("curpError");
  const curpInput = document.getElementById("CURP");
  const datalist = document.getElementById("curpList");
  const patientName = document.getElementById("patientName");
  const medicalArea = document.getElementById("speciality");
  const doctorForm = document.getElementById("doctor");

  if (curpInput.value.trim() === "") {
    curpInput.value = ""; // Limpia espacios sobrantes
  }

  // Validación y búsqueda paciente por CURP --------------------------------------------------------------------------------------
  curpInput.addEventListener("change", function () {
    const entrada = curpInput.value.trim().toUpperCase();
    const curp = entrada.split(" - ")[0];

    console.log("Se entro al validador de curp");

    if (isValidCURP(curp)) {
      const patient = patients.find((p) => p.CURP === curp);

      if (patient) {
        patientIdInput.value = patient.id;
        patientName.value = [
          patient.names,
          patient.last_name,                                                // En este espacio es donde se lleva a cabo la 
          patient.last_name2,                                               // validación de la CURP e insertar el valor de id
        ]                                                                   // paciente en su campo
          .filter(Boolean)  
          .join(" ");
        curpError.style.display = "none";
      } else {
        patientIdInput.value = "";
        patientName.value = "";
        curpError.textContent = "No se encontró un paciente con esa CURP.";
        curpError.style.display = "block";
      }
    } else {
      patientIdInput.value = "";
      patientName.value = "";
      curpError.textContent = "Formato de CURP inválido.";
      curpError.style.display = "block";
    }
  });
  //--------------------------------------------------------------------------------------------------------------------------------

  if (typeof appointment !== "undefined" && appointment.id_doctor) {
    setTimeout(() => {
      doctorForm.value = appointment.id_doctor;
      updateEnabledDays();
    }, 100);
  }
  

  // Inicializar flatpickr UNA vez
  let fp = flatpickr("#appointmentDate", {
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    minDate: "today",
    locale: "es",
    disable: [],
  });

  // Rellenar datalist de CURP
  patients.forEach((p) => {
    const option = document.createElement("option");
    option.value =
      p.CURP + " - " + p.names + " " + p.last_name + " " + (p.last_name2 || "");
    datalist.appendChild(option);
  });

  const dayNameToNumber = {
    sunday: 0,
    monday: 1,
    tuesday: 2,
    wednesday: 3,
    thursday: 4,
    friday: 5,
    saturday: 6,
  };

  
  function updateEnabledDays() {
    const selectedDoctorId = parseInt(doctorSelect.value);
    if (!selectedDoctorId) {
      fp.set("disable", [(date) => true]);
      return;
    }

    const workingDays = schedules
      .filter((s) => s.id_doctor === selectedDoctorId)
      .map((s) => s.day.toLowerCase())
      .map((day) => dayNameToNumber[day])
      .filter((v, i, a) => a.indexOf(v) === i);

    fp.set("disable", [(date) => !workingDays.includes(date.getDay())]);

    if (appointmentDate.value) {
      const selectedDate = new Date(appointmentDate.value);
      if (!workingDays.includes(selectedDate.getDay())) {
        fp.clear();
      }
    }
  }

  updateEnabledDays();

  doctorSelect.addEventListener("change", updateEnabledDays);

  appointmentDate.addEventListener("change", function () {
    const selectedDate = new Date(appointmentDate.value);
    const selectedDoctorId = parseInt(doctorSelect.value);
    const selectedPatientId = parseInt(patientIdInput.value);

    if (!selectedDoctorId || isNaN(selectedDate.getTime())) {
      console.warn("Doctor no seleccionado o fecha inválida");
      return;
    }

    const dayOfWeek = selectedDate
      .toLocaleDateString("en-US", { weekday: "long" })
      .toLowerCase();
    const hourMinutes = selectedDate.toTimeString().slice(0, 5);

    const doctorSchedules = schedules.filter(
      (s) =>
        parseInt(s.id_doctor) === selectedDoctorId &&
        s.day.toLowerCase() === dayOfWeek
    );

    const isWithinSchedule = doctorSchedules.some((s) => {
      const [selH, selM] = hourMinutes.split(":").map(Number);
      const selectedMinutes = selH * 60 + selM;

      const [startH, startM] = s.start_time.split(":").map(Number);
      const startMinutes = startH * 60 + startM;

      const [endH, endM] = s.end_time.split(":").map(Number);
      const endMinutes = endH * 60 + endM;

      return selectedMinutes >= startMinutes && selectedMinutes <= endMinutes;
    });

    // Validación de horario
    if (!isWithinSchedule) {
      dateError.textContent = "Fecha u hora fuera del horario del doctor.";
      dateError.style.display = "inline";
      appointmentDate.setCustomValidity(
        "Fecha u hora fuera del horario del doctor"
      );
      return;
    }

    // Validación de duplicado con allAppointments
    const selectedDateStr = selectedDate.toISOString().slice(0, 16); // YYYY-MM-DDTHH:mm

    const currentAppointmentId = parseInt(
      document.getElementById("appointmentId")?.value || 0
    );

    const isDuplicate = allAppointments.some(
      (appt) =>
        parseInt(appt.id_doctor) === selectedDoctorId &&
        parseInt(appt.id_patient) === selectedPatientId &&
        appt.id != currentAppointmentId && // Excluir la cita actual si se está editando
        new Date(appt.appointment_date).toISOString().slice(0, 16) ===
          selectedDateStr
    );

    if (isDuplicate) {
      dateError.textContent =
        "Ya existe una cita en ese horario para este paciente y doctor.";
      dateError.style.display = "inline";
      appointmentDate.setCustomValidity("Cita duplicada.");
    } else {
      dateError.style.display = "none";
      appointmentDate.setCustomValidity("");
    }
  });

  if (!medicalArea || !doctorForm) {
    console.warn("No se encontró uno de los selects en el DOM.");
    return;
  }

  let medicalAreaID = getParamsMedical(medicalArea);
  filterDoctorsByArea(medicalAreaID, doctors, doctorForm);

  medicalArea.addEventListener("change", function () {
    medicalAreaID = getParamsMedical(medicalArea);
    console.log(medicalAreaID);
    console.log(doctors);
    console.log(doctorForm);
    filterDoctorsByArea(medicalAreaID, doctors, doctorForm);
  });
});

function getParamsMedical(medicalArea) {
  return medicalArea.value;
}

function filterDoctorsByArea(areaID, doctors, doctorSelect) {
  const filtered = doctors.filter((doc) => doc.medical_area_id == areaID);
  console.log(areaID);
  console.log(doctors);
  console.log(doctorSelect);

  doctorSelect.innerHTML = "";

  const defaultOption = document.createElement("option");
  defaultOption.text = "-- Selecciona un doctor --";
  defaultOption.value = "";
  doctorSelect.appendChild(defaultOption);

  filtered.forEach((doctor) => {
    const option = document.createElement("option");
    option.value = doctor.doctor_id;
    option.text = doctor.doctor_name;
    doctorSelect.appendChild(option);
  });
}

function isValidCURP(curp) {
  const regex = /^[A-Z]{4}\d{6}[HM][A-Z]{5}[A-Z0-9]\d$/;
  return regex.test(curp);
}
