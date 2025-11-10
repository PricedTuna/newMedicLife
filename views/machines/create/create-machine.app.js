document.addEventListener("DOMContentLoaded", () => {
    // ================================
    // Gestión de ventanas operativas
    // ================================
    const windowsContainer = document.getElementById("operational-windows-container");
    const addWindowBtn = document.getElementById("add-window-btn");
    const defaultWindowsBtn = document.getElementById("default-windows-btn");
    const defaultStartInput = document.getElementById("default-window-start");
    const defaultEndInput = document.getElementById("default-window-end");
    const daysOfWeek = ["Lunes","Martes","Miércoles","Jueves","Viernes","Sábado","Domingo"];

    if (windowsContainer) {
        let counter = windowsContainer.children.length;

        const createWindowRow = (day = "", start = "", end = "") => {
            const div = document.createElement("div");
            div.classList.add("operational-window");
            div.innerHTML = `
                <select name="operational_windows[${counter}][day_of_week]" required>
                    ${daysOfWeek.map(d => `<option value="${d}" ${d === day ? "selected" : ""}>${d}</option>`).join('')}
                </select>
                <input type="time" name="operational_windows[${counter}][start_time]" value="${start}" required>
                <input type="time" name="operational_windows[${counter}][end_time]" value="${end}" required>
                <button type="button" class="remove-window-btn">❌</button>
            `;
            windowsContainer.appendChild(div);
            counter++;
        };

        addWindowBtn?.addEventListener("click", () => createWindowRow());

        windowsContainer.addEventListener("click", e => {
            if (e.target.classList.contains("remove-window-btn")) {
                e.target.parentElement.remove();
            }
        });

        defaultWindowsBtn?.addEventListener("click", () => {
            const startTime = defaultStartInput.value || "08:00";
            const endTime = defaultEndInput.value || "17:00";

            if (startTime >= endTime) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "La hora de inicio debe ser menor que la hora de fin."
                });
                return;
            }

            windowsContainer.innerHTML = "";
            counter = 0;
            daysOfWeek.forEach(day => createWindowRow(day, startTime, endTime));
        });
    }

    // ================================
    // Validación y confirmación de envío
    // ================================
    const forms = document.querySelectorAll(".form-container form");

    forms.forEach(form => {
        form.addEventListener("submit", e => {
            e.preventDefault();
            let errors = [];

            // Validación específica para formulario de máquina
            const name = form.querySelector("#name")?.value.trim();
            const model = form.querySelector("#model")?.value.trim();
            const type = form.querySelector("#id_machine_type")?.value;
            const area = form.querySelector("#id_medical_area")?.value;
            const status = form.querySelector("#operational_status")?.value;

            if (name !== undefined && name.length < 3) errors.push("El nombre de la máquina debe tener al menos 3 caracteres.");
            if (model !== undefined && model.length < 2) errors.push("El modelo es obligatorio.");
            if (type !== undefined && !type) errors.push("Debe seleccionar un tipo de máquina.");
            if (area !== undefined && !area) errors.push("Debe seleccionar un área médica.");
            if (status !== undefined && !status) errors.push("Debe seleccionar un estado operativo.");

            // Validación de ventanas operativas si existen
            const windows = form.querySelectorAll(".operational-window");
            windows.forEach((win, index) => {
                const start = win.querySelector("input[name*='start_time']")?.value;
                const end = win.querySelector("input[name*='end_time']")?.value;
                if (!start || !end) errors.push(`La ventana operativa #${index + 1} debe tener hora de inicio y fin.`);
                if (start && end && start >= end) errors.push(`En la ventana operativa #${index + 1}, la hora de inicio debe ser menor que la hora de fin.`);
            });

            if (errors.length > 0) {
                Swal.fire({ icon: "error", title: "Datos incompletos", html: errors.join("<br>") });
                return false;
            }

            // Confirmación antes de enviar
            Swal.fire({
                icon: "question",
                title: "Confirmar envío",
                text: "¿Desea guardar los cambios?",
                showCancelButton: true,
                confirmButtonText: "Sí, guardar",
                cancelButtonText: "Cancelar"
            }).then(result => {
                if (result.isConfirmed) form.submit();
            });
        });
    });
});
