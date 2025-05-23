document.addEventListener('DOMContentLoaded', function () {
    if (typeof doctors !== "undefined" && typeof patients !== "undefined" && typeof schedules !== "undefined") {
        console.log("Las variables se ha cargado correctamente");
        console.log(doctors);
        console.log(patients);
        console.log(schedules);
    } else {
        console.warn("Error al cargar la variable 'doctors'");
        return;
    }

    const appointmentDate = document.getElementById("appointmentDate");
    const doctorSelect = document.getElementById("doctor"); // ya lo tenías como doctorForm, usa uno solo
    const dateError = document.getElementById("dateError"); // asegúrate de tener este ID en el HTML

    const patientIdInput = document.getElementById("patientId");
    const curpError = document.getElementById("curpError");
    let curpInput = document.getElementById("CURP");
    let datalist = document.getElementById("curpList");
    let patientName = document.getElementById("patientName");
    const medicalArea = document.getElementById("speciality");
    const doctorForm = document.getElementById("doctor");
    // Logica de seleccion de horarios para la cita con disponibilidad para los medicos

    flatpickr("#appointmentDate", {
        enableTime: true,          // permite seleccionar hora
        dateFormat: "Y-m-d H:i",   // formato fecha y hora
        minDate: "today",          // fecha mínima (hoy)
        locale: "es"
        // aquí puedes añadir más configuraciones según necesites
    });

    // Rellenar el datalist con CURP y nombres
    patients.forEach(p => {
        const option = document.createElement("option");
        option.value = p.CURP + " - " + p.names + " " + p.last_name + " " + (p.last_name2 || "");
        datalist.appendChild(option);
    });


    const dayNameToNumber = {
        sunday: 0,
        monday: 1,
        tuesday: 2,
        wednesday: 3,
        thursday: 4,
        friday: 5,
        saturday: 6
    };

    let fp = flatpickr("#appointmentDate", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        minDate: "today",
        locale: "es", // <- Esto activa el idioma español
        disable: [] // vacío al principio
    });

    function updateEnabledDays() {
        const selectedDoctorId = parseInt(doctorSelect.value);
        if (!selectedDoctorId) {
            fp.set('disable', [date => true]); // Deshabilita todo si no hay doctor
            return;
        }

        const workingDays = schedules
            .filter(s => s.id_doctor === selectedDoctorId)
            .map(s => s.day.toLowerCase())
            .map(day => dayNameToNumber[day])
            .filter((v, i, a) => a.indexOf(v) === i);

        fp.set('disable', [
            date => !workingDays.includes(date.getDay())
        ]);

        // Limpiar fecha si seleccionada no es válida
        if (appointmentDate.value) {
            const selectedDate = new Date(appointmentDate.value);
            if (!workingDays.includes(selectedDate.getDay())) {
                fp.clear();
            }
        }
    }

    // Ejecuta al cargar para configurar flatpickr con el doctor por defecto (si hay)
    updateEnabledDays();

    // Ejecuta cada vez que cambie el doctor
    doctorSelect.addEventListener("change", updateEnabledDays);


    appointmentDate.addEventListener("change", function () {
        const selectedDate = new Date(appointmentDate.value);
        const selectedDoctorId = parseInt(doctorSelect.value);

        if (!selectedDoctorId || isNaN(selectedDate.getTime())) {
            console.warn("Doctor no seleccionado o fecha inválida");
            return;
        }

        const dayOfWeek = selectedDate.toLocaleDateString("en-US", { weekday: "long" }).toLowerCase(); // ej: "monday"
        const hourMinutes = selectedDate.toTimeString().slice(0, 5); // "HH:MM"

        console.log(dayOfWeek);


        const doctorSchedules = schedules.filter(s => parseInt(s.id_doctor) === selectedDoctorId && s.day.toLowerCase() === dayOfWeek);

        console.log(doctorSchedules);
        console.log(schedules);


        const isValid = doctorSchedules.some(s => {
            // Extraemos la hora y minutos seleccionados
            const [selH, selM] = hourMinutes.split(":").map(Number);
            const selectedMinutes = selH * 60 + selM;

            // Extraemos la hora y minutos de inicio del horario del doctor
            const [startH, startM] = s.start_time.split(":").map(Number);
            const startMinutes = startH * 60 + startM;

            // Extraemos la hora y minutos de fin del horario del doctor
            const [endH, endM] = s.end_time.split(":").map(Number);
            const endMinutes = endH * 60 + endM;
            console.log("Hora seleccionada (minutos):", selectedMinutes);
            console.log("Inicio:", startMinutes, "Fin:", endMinutes);

            // Comparamos si la hora seleccionada está dentro del rango permitido
            return selectedMinutes >= startMinutes && selectedMinutes <= endMinutes;
        });



        if (!isValid) {
            dateError.style.display = "inline";
            appointmentDate.setCustomValidity("Fecha u hora fuera del horario del doctor");
            console.warn("La fecha/hora está fuera del horario permitido");
        } else {
            dateError.style.display = "none";
            appointmentDate.setCustomValidity(""); // válida
        }
    });
    //-----------------------------------------------------------------------------------------------------------------------------------------------
    //Logica de busqueda de pacientes:


    curpInput.addEventListener("change", function () {
        const entrada = curpInput.value.trim().toUpperCase();
        const curp = entrada.split(" - ")[0]; // Extrae solo el CURP

        if (isValidCURP(curp)) {
            const patient = patients.find(p => p.CURP === curp);

            if (patient) {
                patientIdInput.value = patient.id;
                patientName.value = [patient.names, patient.last_name, patient.last_name2]
                    .filter(Boolean).join(" ");
                curpError.style.display = "none";
                console.log("Paciente encontrado:", patient);
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
    })

    //'----------------------------------------------------------------------------------------------------------------------------

    //logica de seleccion de areas medicas y medicos disponibles


    if (!medicalArea || !doctorForm) {
        console.warn("No se encontró uno de los selects en el DOM.");
        return;
    }

    // Inicial: al cargar la página
    let medicalAreaID = getParamsMedical(medicalArea);
    filterDoctorsByArea(medicalAreaID, doctors, doctorForm);

    // Cada vez que se cambia de especialidad
    medicalArea.addEventListener("change", function () {
        medicalAreaID = getParamsMedical(medicalArea);
        filterDoctorsByArea(medicalAreaID, doctors, doctorForm);
    });
});

function getParamsMedical(medicalArea) {
    const medicalAreaID = medicalArea.value;
    console.log("Área médica seleccionada:", medicalAreaID);
    return medicalAreaID;
}

function filterDoctorsByArea(areaID, doctors, doctorSelect) {
    // Filtrar los doctores por área médica
    const filtered = doctors.filter(doc => doc.medical_area_id == areaID);

    console.log("Doctores filtrados:", filtered);

    // Limpiar select
    doctorSelect.innerHTML = "";

    // Añadir opción por defecto
    const defaultOption = document.createElement("option");
    defaultOption.text = "-- Selecciona un doctor --";
    defaultOption.value = "";
    doctorSelect.appendChild(defaultOption);

    // Insertar doctores filtrados como opciones
    filtered.forEach(doctor => {
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


