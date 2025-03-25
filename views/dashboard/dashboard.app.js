console.log("Dashboard.js cargado correctamente");

function generateCalendar(year, month) {
    const calendar = document.getElementById("calendar");
    if (!calendar) {
        console.error("Elemento #calendar no encontrado");
        return;
    }
    calendar.innerHTML = "";

    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const firstDay = new Date(year, month, 1).getDay();

    const daysOfWeek = ["D", "L", "M", "X", "J", "V", "S"];
    daysOfWeek.forEach(day => {
        let div = document.createElement("div");
        div.textContent = day;
        div.classList.add("header");
        calendar.appendChild(div);
    });

    for (let i = 0; i < firstDay; i++) {
        let emptyCell = document.createElement("div");
        calendar.appendChild(emptyCell);
    }

    for (let day = 1; day <= daysInMonth; day++) {
        let dayCell = document.createElement("div");
        dayCell.textContent = day;
        dayCell.classList.add("day");
        dayCell.onclick = () => alert("Cita para el día " + day);
        calendar.appendChild(dayCell);
    }
}

document.addEventListener("DOMContentLoaded", function() {
    generateCalendar(new Date().getFullYear(), new Date().getMonth());
});