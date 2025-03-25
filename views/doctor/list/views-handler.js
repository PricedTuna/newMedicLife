document.addEventListener("DOMContentLoaded", function() {
    console.log("QUE ROLLO CHIKUELO");
    const contentDiv = document.getElementById("dynamic-content");

    if (!contentDiv) {
        console.error("Error: No se encontró el elemento #dynamic-content.");
        return;
    }

    function loadView(view) {
        fetch(view)
            .then(response => response.ok ? response.text() : Promise.reject("Error al cargar la vista"))
            .then(html => {
                // Insertar el HTML en el contenedor
                contentDiv.innerHTML = html;
                // Extraer y ejecutar los scripts contenidos en el HTML cargado
                const scriptElements = contentDiv.querySelectorAll("script");
                scriptElements.forEach(oldScript => {
                    const newScript = document.createElement("script");
                    if (oldScript.src) {
                        newScript.src = oldScript.src;
                    } else {
                        newScript.textContent = oldScript.textContent;
                    }
                    // Copiar atributos importantes, si es necesario
                    if(oldScript.type) newScript.type = oldScript.type;
                    if(oldScript.defer) newScript.defer = true;
                    document.body.appendChild(newScript);
                });
            })
            .catch(error => {
                console.error(error);
                contentDiv.innerHTML = "<p style='color: red;'>Error al cargar la vista.</p>";
            });
    }

    document.getElementById("listar-btn").addEventListener("click", () => loadView("/views/doctor/list/list-doctors.view.php"));
    document.getElementById("crear-btn").addEventListener("click", () => loadView("/views/doctor/register/register-doctor.view.php"));

    // Cargar la vista predeterminada al iniciar
    loadView("/views/doctor/list/list-doctors.view.php");
});
