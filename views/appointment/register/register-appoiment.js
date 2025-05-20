document.addEventListener('DOMContentLoaded', function () {
    if (typeof doctors !== "undefined" && typeof patients !== "undefined") {
        console.log("Las variables se ha cargado correctamente");
        console.log(doctors);
        console.log(patients);
    } else {
        console.warn("Error al cargar la variable 'doctors'");
        return;
    }

    //-----------------------------------------------------------------------------------------------------------------------------------------------
    //Logica de busqueda de pacientes:
    let curpInput = document.getElementById("CURP");
    const patientIdInput = document.getElementById("patientId");
    const curpError = document.getElementById("curpError");
    let patientName = document.getElementById("patientName");

    curpInput.addEventListener("blur", function () {
        const curp = curpInput.value.trim().toUpperCase();

        if (isValidCURP(curp)) {
            const patient = patients.find(p => p.CURP === curp);

            if (patient) {
                patientIdInput.value = patient.id;
                patientName.value = [patient.names, patient.last_name, patient.last_name2]
                    .filter(Boolean)
                    .join(" ");
                curpError.style.display = "none"; // Oculta error si todo está bien
                console.log("Paciente encontrado:", patient);
            } else {
                patientIdInput.value = "";
                patientName.value = "";
                curpError.textContent = "No se encontró un paciente con esa CURP.";
                curpError.style.display = "block";
                console.warn("No se encontró un paciente con esa CURP.");
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
    const medicalArea = document.getElementById("speciality");
    const doctorForm = document.getElementById("doctor");

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