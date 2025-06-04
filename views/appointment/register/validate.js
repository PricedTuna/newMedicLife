// Este archivo complementa la validación del formulario de citas
// Funciona en conjunto con register-appoiment.js

document.addEventListener("DOMContentLoaded", function() {
  // Referencia al formulario
  const form = document.getElementById("solicitarCita");

  if (!form) return;

  // Función para validar que todos los campos requeridos estén completos
  function validateRequiredFields() {
    const requiredFields = form.querySelectorAll("[required]");
    let allValid = true;

    requiredFields.forEach(field => {
      if (!field.value.trim()) {
        allValid = false;
        // Marcar visualmente el campo como inválido
        field.classList.add("invalid-field");
      } else {
        field.classList.remove("invalid-field");
      }
    });

    return allValid;
  }

  // Agregar validación adicional a los campos cuando pierden el foco
  const requiredFields = form.querySelectorAll("[required]");
  requiredFields.forEach(field => {
    field.addEventListener("blur", function() {
      if (!this.value.trim()) {
        this.classList.add("invalid-field");
      } else {
        this.classList.remove("invalid-field");
      }
    });

    // Eliminar marca de error cuando el usuario comienza a escribir
    field.addEventListener("input", function() {
      this.classList.remove("invalid-field");
    });
  });

  // Agregar estilos CSS para campos inválidos si no existen
  if (!document.getElementById("validation-styles")) {
    const styleEl = document.createElement("style");
    styleEl.id = "validation-styles";
    styleEl.textContent = `
      .invalid-field {
        border: 2px solid #ff0000 !important;
        background-color: rgba(255, 0, 0, 0.05) !important;
      }
    `;
    document.head.appendChild(styleEl);
  }
});
