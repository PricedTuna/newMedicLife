document.addEventListener("DOMContentLoaded", function () {
  const appointmentDate = document.getElementById("appointmentDate");
  const dateError = document.getElementById("dateError");
  const doctorSelect = document.getElementById("doctor");
  const patientIdInput = document.getElementById("patientId");
  const curpError = document.getElementById("curpError");
  const curpInput = document.getElementById("CURP");
  const datalist = document.getElementById("curpList");
  const patientName = document.getElementById("patientName");
  const patientEmail = document.getElementById("patientEmail");
  const medicalArea = document.getElementById("speciality");
  const doctorForm = document.getElementById("doctor");
  const nameMedicalArea = document.getElementById("name_medical_area");
  // Funciones para mostrar/ocultar indicador de carga
  function showLoading() {
    const loadingDiv = document.createElement("div");
    loadingDiv.id = "loadingIndicator";
    loadingDiv.innerHTML = `
      <div class="loading-spinner"></div>
      <p>Cargando...</p>
    `;
    document.body.appendChild(loadingDiv);
  }

  function hideLoading() {
    const loadingDiv = document.getElementById("loadingIndicator");
    if (loadingDiv) loadingDiv.remove();
  }

  // Validación y búsqueda paciente por CURP --------------------------------------------------------------------------------------
  curpInput.addEventListener("change", function () {
    showLoading();
    const entrada = curpInput.value.trim().toUpperCase();
    const curp = entrada.split(" - ")[0];

    if (isValidCURP(curp)) {
      const patient = patients.find((p) => p.CURP === curp);

      if (patient) {
        patientIdInput.value = patient.id;
        patientName.value = [
          patient.names,
          patient.last_name, // En este espacio es donde se lleva a cabo la
          patient.last_name2, // validación de la CURP e insertar el valor de id
        ] // paciente en su campo
          .filter(Boolean)
          .join(" ");
        patientEmail.value = patient.email;
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
    hideLoading();
  });

  // Validación onBlur para CURP
  curpInput.addEventListener("blur", function () {
    const entrada = curpInput.value.trim().toUpperCase();
    const curp = entrada.split(" - ")[0];

    if (!curp) {
      curpError.textContent = "El campo CURP es obligatorio.";
      curpError.style.display = "block";
      return;
    }

    if (!isValidCURP(curp)) {
      curpError.textContent = "Formato de CURP inválido.";
      curpError.style.display = "block";
    } else {
      const patient = patients.find((p) => p.CURP === curp);
      if (!patient) {
        curpError.textContent = "No se encontró un paciente con esa CURP.";
        curpError.style.display = "block";
      } else {
        curpError.style.display = "none";
      }
    }
  });
  //--------------------------------------------------------------------------------------------------------------------------------

  // Inicializar flatpickr UNA vez
  let fp = flatpickr("#appointmentDate", {
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    minDate: "today",
    locale: "es",
    disable: [],
    minuteIncrement: 30,
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
      fp.set("disable", [() => true]);
      return;
    }

    const doctorSchedules = schedules.filter(
      (s) => s.id_doctor === selectedDoctorId
    );

    // Obtener días en que el doctor trabaja
    const workingDays = doctorSchedules
      .map((s) => s.day.toLowerCase())
      .map((day) => dayNameToNumber[day])
      .filter((v, i, a) => a.indexOf(v) === i);

    // Habilitar solo los días que trabaja el doctor
    fp.set("disable", [(date) => !workingDays.includes(date.getDay())]);

    // Filtrar horas según el día seleccionado y el horario del doctor
    fp.set("enableTime", true);

    // Obtener horarios para un día específico
    const selectedDate = fp.selectedDates[0] || null;
    if (selectedDate) {
      const dayOfWeek = selectedDate.getDay();

      // Obtener los horarios para ese día
      const daySchedules = doctorSchedules.filter(
        (s) => dayNameToNumber[s.day.toLowerCase()] === dayOfWeek
      );

      // Crear un array de rangos permitidos en formato { from: Date, to: Date }
      const allowedTimeRanges = daySchedules.map((schedule) => {
        // Crear fechas basadas en selectedDate para start y end time
        const [startHour, startMinute] = schedule.start_time
          .split(":")
          .map(Number);
        const [endHour, endMinute] = schedule.end_time.split(":").map(Number);

        const from = new Date(selectedDate);
        from.setHours(startHour, startMinute, 0, 0);

        const to = new Date(selectedDate);
        to.setHours(endHour, endMinute, 0, 0);

        return { from, to };
      });

      // Configurar la función disable para bloquear horas fuera de los rangos permitidos
      fp.set("disable", [
        (date) => {
          // Deshabilitar si no está en el día de trabajo
          if (!workingDays.includes(date.getDay())) return true;

          // Para las horas, si la fecha es igual al día seleccionado
          if (
            date.toDateString() === selectedDate.toDateString() &&
            fp.config.enableTime
          ) {
            // Verificar si la hora está dentro de algún rango permitido
            const inAllowedRange = allowedTimeRanges.some(
              (range) => date >= range.from && date <= range.to
            );
            return !inAllowedRange;
          }
          return false;
        },
      ]);
    }

    // Limpiar fecha si no corresponde al horario del doctor
    if (appointmentDate.value) {
      const selectedDateCheck = new Date(appointmentDate.value);
      if (!workingDays.includes(selectedDateCheck.getDay())) {
        fp.clear();
      }
    }
  }

  updateEnabledDays();

  appointmentDate.addEventListener("change", function () {
    const selectedDate = new Date(appointmentDate.value);
    const selectedDoctorId = parseInt(doctorSelect.value);
    const selectedPatientId = parseInt(patientIdInput.value);

    // Solo continuar si se ha seleccionado doctor y paciente
    if (
      !selectedDoctorId ||
      !selectedPatientId ||
      isNaN(selectedDate.getTime())
    ) {
      dateError.textContent = "Seleccione doctor, paciente y una fecha válida.";
      dateError.style.display = "inline";
      appointmentDate.setCustomValidity("Faltan datos para validar la cita.");
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

    if (!isWithinSchedule) {
      dateError.textContent = "Fecha u hora fuera del horario del doctor.";
      dateError.style.display = "inline";
      appointmentDate.setCustomValidity(
        "Fecha u hora fuera del horario del doctor"
      );
      return;
    }

    const selectedDateStr = appointmentDate.value; // directamente del input (datetime-local)

    const currentAppointmentId = parseInt(
      document.getElementById("appointmentId")?.value || 0
    );

    const selectedDateTime = selectedDateStr.slice(0, 16); // formato YYYY-MM-DDTHH:mm

    // Detectar si el paciente ya tiene una cita a esa hora
    const patientHasConflict = allAppointments.some(
      (appt) =>
        parseInt(appt.id_patient) === selectedPatientId &&
        appt.id != currentAppointmentId &&
        appt.appointment_date.slice(0, 16) === selectedDateTime
    );

    // Detectar si el doctor ya tiene una cita a esa hora
    const doctorHasConflict = allAppointments.some(
      (appt) =>
        parseInt(appt.id_doctor) === selectedDoctorId &&
        appt.id != currentAppointmentId &&
        appt.appointment_date.slice(0, 16) === selectedDateTime
    );

    if (patientHasConflict || doctorHasConflict) {
      if (patientHasConflict && doctorHasConflict) {
        dateError.textContent =
          "Ya existe una cita para este paciente y este doctor en ese horario.";
      } else if (patientHasConflict) {
        dateError.textContent =
          "Este paciente ya tiene una cita a esa hora con otro doctor.";
      } else if (doctorHasConflict) {
        dateError.textContent =
          "Este doctor ya tiene una cita a esa hora con otro paciente.";
      }
      dateError.style.display = "inline";
      appointmentDate.setCustomValidity("Cita en conflicto.");
    } else {
      dateError.style.display = "none";
      appointmentDate.setCustomValidity("");
    }
  });

  let medicalAreaID = getParamsMedical(medicalArea);

  filterDoctorsByArea(medicalAreaID, doctors, doctorForm);

  // Validación onBlur para especialidad
  medicalArea.addEventListener("blur", function () {
    if (!medicalArea.value) {
      const errorSpan = document.createElement("span");
      errorSpan.id = "specialityError";
      errorSpan.style.color = "red";
      errorSpan.textContent = "Debe seleccionar una especialidad.";

      // Eliminar mensaje de error anterior si existe
      const existingError = document.getElementById("specialityError");
      if (existingError) existingError.remove();

      medicalArea.parentNode.appendChild(errorSpan);
    } else {
      const existingError = document.getElementById("specialityError");
      if (existingError) existingError.remove();
    }
  });

  // Validación onBlur para doctor
  doctorForm.addEventListener("blur", function () {
    if (!doctorForm.value) {
      const errorSpan = document.createElement("span");
      errorSpan.id = "doctorError";
      errorSpan.style.color = "red";
      errorSpan.textContent = "Debe seleccionar un doctor.";

      // Eliminar mensaje de error anterior si existe
      const existingError = document.getElementById("doctorError");
      if (existingError) existingError.remove();

      doctorForm.parentNode.appendChild(errorSpan);
    } else {
      const existingError = document.getElementById("doctorError");
      if (existingError) existingError.remove();
    }
  });

  medicalArea.addEventListener("change", function () {
    showLoading();

    medicalAreaID = getParamsMedical(medicalArea);
    doctorSelect.innerHTML = '<option value="">Selecciona un Doctor</option>';
    filterDoctorsByArea(medicalAreaID, doctors, doctorForm);

    // Obtener el texto de la opción seleccionada
    const selectedOptionText =
      medicalArea.options[medicalArea.selectedIndex].text;
    // Poner ese texto en el input oculto
    const nameMedicalAreaInput = document.getElementById("name_medical_area");
    if (nameMedicalAreaInput) {
      nameMedicalAreaInput.value = selectedOptionText;
    }

    hideLoading();
  });

  doctorForm.addEventListener("change", function () {
  showLoading();

  updateEnabledDays();

  const nameDoctorInput = document.getElementById("name_doctor");

  if (doctorSelect && nameDoctorInput) {
    const selectedOption = doctorSelect.options[doctorSelect.selectedIndex];
    const fullName = selectedOption.text || "";
    nameDoctorInput.value = fullName.trim();
  }

  // Mostrar horarios disponibles del médico seleccionado
  const selectedDoctorId = doctorForm.value;
  const scheduleContainer = document.getElementById("doctorScheduleContainer");
  const scheduleList = document.getElementById("doctorScheduleList");
  scheduleList.innerHTML = "";

  if (!selectedDoctorId) {
    scheduleContainer.style.display = "none";
    hideLoading();
    return;
  }

  const doctorSchedules = window.schedules.filter(
    (schedule) => schedule.id_doctor == selectedDoctorId
  );

  // Mapeo de días en inglés a español
  const diasSemana = {
    monday: "Lunes",
    tuesday: "Martes",
    wednesday: "Miércoles",
    thursday: "Jueves",
    friday: "Viernes",
    saturday: "Sábado",
    sunday: "Domingo",
  };

  if (doctorSchedules.length === 0) {
    scheduleList.innerHTML =
      "<li>Este médico no tiene horarios disponibles.</li>";
  } else {
    doctorSchedules.forEach((schedule) => {
      const dia = diasSemana[schedule.day.toLowerCase()] || schedule.day;
      const item = document.createElement("li");
      item.textContent = `${dia} - ${schedule.start_time} a ${schedule.end_time}`;
      scheduleList.appendChild(item);
    });
  }

  scheduleContainer.style.display = "block";

  hideLoading();
});


  if (typeof appointment !== "undefined" && appointment.id_doctor) {
    setTimeout(() => {
      doctorForm.value = appointment.id_doctor;
      updateEnabledDays();
    }, 100);
  }

  if (!medicalArea || !doctorForm) {
    console.warn("No se encontró uno de los selects en el DOM.");
    return;
  }

  // Confirmación al registrar cita
  document
    .getElementById("solicitarCita")
    .addEventListener("submit", function (event) {
      event.preventDefault();

      // Validar todos los campos
      const curpValue = curpInput.value.trim();
      const doctorValue = doctorSelect.value;
      const appointmentValue = appointmentDate.value;
      const medicalAreaValue = medicalArea.value;

      let formIsValid = true;
      let errorMessages = [];

      // Limpiar mensajes anteriores
      curpError.style.display = "none";
      dateError.style.display = "none";

      // Eliminar mensajes de error anteriores
      const previousErrors = document.querySelectorAll("#specialityError, #doctorError");
      previousErrors.forEach(error => error.remove());

      // Validar CURP
      if (!curpValue || !isValidCURP(curpValue.split(" - ")[0])) {
        curpError.textContent = "Por favor ingresa una CURP válida.";
        curpError.style.display = "block";
        curpInput.classList.add("invalid-field");
        formIsValid = false;
        errorMessages.push("CURP inválida o no encontrada");
      } else {
        curpInput.classList.remove("invalid-field");
      }

      // Validar especialidad médica
      if (!medicalAreaValue) {
        const errorSpan = document.createElement("span");
        errorSpan.id = "specialityError";
        errorSpan.style.color = "red";
        errorSpan.textContent = "Debe seleccionar una especialidad.";
        medicalArea.parentNode.appendChild(errorSpan);
        medicalArea.classList.add("invalid-field");
        formIsValid = false;
        errorMessages.push("Debe seleccionar una especialidad");
      } else {
        medicalArea.classList.remove("invalid-field");
      }

      // Validar doctor
      if (!doctorValue) {
        const errorSpan = document.createElement("span");
        errorSpan.id = "doctorError";
        errorSpan.style.color = "red";
        errorSpan.textContent = "Debe seleccionar un doctor.";
        doctorSelect.parentNode.appendChild(errorSpan);
        doctorSelect.classList.add("invalid-field");
        formIsValid = false;
        errorMessages.push("Debe seleccionar un doctor");
      } else {
        doctorSelect.classList.remove("invalid-field");
      }

      // Validar fecha de cita
      if (!appointmentValue) {
        dateError.textContent = "Debe seleccionar una fecha válida.";
        dateError.style.display = "inline";
        appointmentDate.classList.add("invalid-field");
        formIsValid = false;
        errorMessages.push("Debe seleccionar una fecha válida");
      } else if (dateError.style.display === "inline") {
        // Si hay un error específico de fecha mostrado (conflicto de horarios)
        appointmentDate.classList.add("invalid-field");
        formIsValid = false;
        errorMessages.push(dateError.textContent);
      } else {
        appointmentDate.classList.remove("invalid-field");
      }

      // Verificar si todos los campos están correctos
      if (formIsValid && this.checkValidity()) {
        Swal.fire({
          title: "¿Confirmar cita?",
          text: `Paciente: ${patientName.value}\nDoctor: ${
            doctorForm.options[doctorForm.selectedIndex].text
          }\nFecha: ${appointmentDate.value}`,
          icon: "question",
          showCancelButton: true,
          confirmButtonText: "Confirmar",
          cancelButtonText: "Cancelar",
        }).then((result) => {
          if (result.isConfirmed) {
            this.submit();
          }
        });
      } else {
        // Mostrar alerta de error con todos los mensajes
        Swal.fire({
          title: "Error en el formulario",
          html: errorMessages.length > 0
            ? `<div style="text-align: left; color: #ff0000;">Por favor corrija los siguientes errores:<br><ul><li>${errorMessages.join('</li><li>')}</li></ul></div>`
            : "Por favor, complete todos los campos correctamente.",
          icon: "error",
          confirmButtonText: "Entendido"
        });
      }
    });

  updateMedicalAreaName();
});

function getParamsMedical(medicalArea) {
  return medicalArea.value;
}

function filterDoctorsByArea(
  areaID,
  doctors,
  doctorSelect,
  selectedDoctorId = null
) {
  const filtered = doctors.filter((doc) => doc.medical_area_id == areaID);
  doctorSelect.innerHTML = ""; // limpia opciones

  // Verificar si el usuario es un doctor (variable global pasada desde PHP)
  const isUserDoctor = typeof isDoctor !== "undefined" && isDoctor === true;

  // Si el usuario es un doctor, solo mostrar ese doctor
  if (isUserDoctor) {
    // Si hay doctores filtrados, solo agregar el doctor actual
    if (filtered.length > 0) {
      // Agregar solo el primer doctor (que debería ser el doctor actual según la consulta SQL)
      const option = document.createElement("option");
      option.value = filtered[0].doctor_id;
      option.text = filtered[0].doctor_name;
      doctorSelect.appendChild(option);

      // Seleccionar automáticamente
      doctorSelect.value = filtered[0].doctor_id;

      // Disparar el evento change para actualizar los días disponibles
      const event = new Event("change");
      doctorSelect.dispatchEvent(event);
    }
  } else {
    // Para usuarios no doctores, mostrar la opción por defecto
    const defaultOption = document.createElement("option");
    defaultOption.text = "Selecciona un Doctor";
    defaultOption.value = "";
    doctorSelect.appendChild(defaultOption);

    // Agrega todas las opciones filtradas
    filtered.forEach((doctor) => {
      const option = document.createElement("option");
      option.value = doctor.doctor_id;
      option.text =
        doctor.doctor_name + " " + doctor.last_name + " " + doctor.last_name2;
      doctorSelect.appendChild(option);
    });

    // Si hay un doctor seleccionado explícito, úsalo
    if (selectedDoctorId) {
      doctorSelect.value = selectedDoctorId;
    } else {
      doctorSelect.value = "";
    }
  }
}

function isValidCURP(curp) {
  // If we're updating an appointment, the CURP might be combined with the full name
  // Extract just the CURP part (first 18 characters if longer)
  let curpToValidate = curp;

  // If the string contains spaces, it might be a combined CURP and name
  if (curp.includes(' ')) {
    // Try to extract just the CURP part (first word)
    curpToValidate = curp.split(' ')[0];
  }

  // If we have an existing appointment with a patient already selected,
  // and we're not changing the patient, consider it valid
  if (typeof appointment !== 'undefined' && appointment.id && appointment.curp) {
    if (curp.includes(appointment.curp)) {
      return true;
    }
  }

  // Otherwise, validate the CURP format
  const regex = /^[A-Z]{4}\d{6}[HM][A-Z]{5}[A-Z0-9]\d$/;
  return regex.test(curpToValidate);
}

function updateMedicalAreaName() {
  const selectedOption = speciality.options[speciality.selectedIndex];
  nameMedicalArea.value = selectedOption.text;
}

function getAvailableTimeSlotsForDate(date, doctorId) {
  const dayOfWeek = date
    .toLocaleDateString("en-US", { weekday: "long" })
    .toLowerCase();
  const schedulesForDay = schedules.filter(
    (s) =>
      parseInt(s.id_doctor) === doctorId && s.day.toLowerCase() === dayOfWeek
  );

  const appointmentsForDoctor = allAppointments.filter(
    (appt) =>
      parseInt(appt.id_doctor) === doctorId &&
      appt.appointment_date.startsWith(date.toISOString().slice(0, 10))
  );

  const bookedTimes = appointmentsForDoctor.map((appt) =>
    appt.appointment_date.slice(11, 16)
  );

  const timeSlots = [];

  schedulesForDay.forEach((s) => {
    const [startH, startM] = s.start_time.split(":").map(Number);
    const [endH, endM] = s.end_time.split(":").map(Number);

    let start = new Date(date);
    start.setHours(startH, startM, 0, 0);
    let end = new Date(date);
    end.setHours(endH, endM, 0, 0);

    while (start < end) {
      const hours = start.getHours().toString().padStart(2, "0");
      const minutes = start.getMinutes().toString().padStart(2, "0");
      const timeStr = `${hours}:${minutes}`;

      if (!bookedTimes.includes(timeStr)) {
        timeSlots.push(timeStr);
      }

      start.setMinutes(start.getMinutes() + 30);
    }
  });

  return timeSlots;
}
