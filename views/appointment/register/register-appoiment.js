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
    const workingDays = doctorSchedules
      .map((s) => s.day.toLowerCase())
      .map((day) => dayNameToNumber[day])
      .filter((v, i, a) => a.indexOf(v) === i);

    fp.set("disable", [(date) => !workingDays.includes(date.getDay())]);

    // Clear the date if it's not in the doctor's schedule
    if (appointmentDate.value) {
      const selectedDate = new Date(appointmentDate.value);
      if (!workingDays.includes(selectedDate.getDay())) {
        fp.clear();
      }
    }

    // If no date is selected, automatically select the first available time slot
    if (!appointmentDate.value) {
      // Find the next available working day
      const today = new Date();
      let nextAvailableDate = new Date(today);

      // Try the next 14 days to find a working day
      for (let i = 0; i < 14; i++) {
        if (workingDays.includes(nextAvailableDate.getDay())) {
          break;
        }
        nextAvailableDate.setDate(nextAvailableDate.getDate() + 1);
      }

      // Get the day name for the available date
      const dayName = Object.keys(dayNameToNumber).find(
        (key) => dayNameToNumber[key] === nextAvailableDate.getDay()
      );

      // Find the schedule for this day
      const daySchedule = doctorSchedules.find(
        (s) => s.day.toLowerCase() === dayName
      );

      if (daySchedule) {
        // Set the time to the start time of the doctor's schedule
        const [hours, minutes] = daySchedule.start_time.split(":").map(Number);
        nextAvailableDate.setHours(hours, minutes, 0, 0);

        // Set the date in the flatpickr
        fp.setDate(nextAvailableDate);
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

    const isDuplicate = allAppointments.some(
      (appt) =>
        parseInt(appt.id_doctor) === selectedDoctorId &&
        parseInt(appt.id_patient) === selectedPatientId &&
        appt.id != currentAppointmentId &&
        appt.appointment_date.slice(0, 16) === selectedDateStr // asumiendo formato YYYY-MM-DDTHH:mm
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

    // Actualizar días habilitados (ya existente)
    updateEnabledDays();

    // Obtener el select del doctor y el input hidden
    const doctorSelect = document.getElementById("doctor");
    const nameDoctorInput = document.getElementById("name_doctor");

    if (doctorSelect && nameDoctorInput) {
      const selectedOption = doctorSelect.options[doctorSelect.selectedIndex];
      const fullName = selectedOption.text || "";
      nameDoctorInput.value = fullName.trim();
    }

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

      // Verificar si todos los campos están correctos
      if (this.checkValidity()) {
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
        Swal.fire({
          title: "Error",
          text: "Por favor, complete todos los campos correctamente.",
          icon: "error",
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
      option.text = doctor.doctor_name + " " + doctor.last_name + " " + doctor.last_name2;
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
  const regex = /^[A-Z]{4}\d{6}[HM][A-Z]{5}[A-Z0-9]\d$/;
  return regex.test(curp);
}

function updateMedicalAreaName() {
  const selectedOption = speciality.options[speciality.selectedIndex];
  nameMedicalArea.value = selectedOption.text;
}
