document.addEventListener('DOMContentLoaded', () => {
    const diasEnEspanol = {
        monday: "Lunes",
        tuesday: "Martes",
        wednesday: "Miércoles",
        thursday: "Jueves",
        friday: "Viernes",
        saturday: "Sábado",
        sunday: "Domingo",
    };

    const doctorSelect = document.getElementById('doctor');
    const horariosContainer = document.getElementById('horariosDisponiblesContainer');
    const horariosList = document.getElementById('horariosDisponibles');
    const appointmentDateContainer = document.getElementById('appointmentDateContainer');

    doctorSelect.addEventListener('change', () => {
        const doctorId = parseInt(doctorSelect.value);
        horariosList.innerHTML = '';

        // Ocultar por defecto
        horariosContainer.style.display = 'none';
        appointmentDateContainer.style.display = 'none';

        if (!doctorId || isNaN(doctorId)) {
            return;
        }

        const horarios = window.schedules.filter(s => s.id_doctor === doctorId);

        if (horarios.length > 0) {
            horariosContainer.style.display = 'block';
            appointmentDateContainer.style.display = 'block'; // Mostrar campo de fecha

            horarios.forEach(horario => {
                const li = document.createElement('li');
                const diaTraducido = diasEnEspanol[horario.day.toLowerCase()] || horario.day;
                li.textContent = `Día: ${diaTraducido} | De ${horario.start_time} a ${horario.end_time}`;
                horariosList.appendChild(li);
            });
        } else {
            horariosContainer.style.display = 'block';
            horariosList.innerHTML = '<li>Este médico no tiene horarios registrados.</li>';
        }
    });
});
