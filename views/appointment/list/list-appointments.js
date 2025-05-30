document.addEventListener('DOMContentLoaded', function() {
  // Filtros de búsqueda
  const searchInput = document.getElementById('searchInput');
  const statusFilter = document.getElementById('statusFilter');
  const clearFilters = document.getElementById('clearFilters');
  const rows = document.querySelectorAll('tbody tr');

  function applyFilters() {
    const searchTerm = searchInput.value.toLowerCase();
    const statusValue = statusFilter.value;

    rows.forEach(row => {
      // Solo procesar filas que tienen celdas (no mensajes de "no hay citas")
      if (row.querySelector('[data-label="Paciente"]') && row.querySelector('[data-label="Doctor"]')) {
        const patientName = row.querySelector('[data-label="Paciente"]').textContent.toLowerCase();
        const doctorName = row.querySelector('[data-label="Doctor"]').textContent.toLowerCase();

        // Determinar el estado basado en la tabla contenedora
        let rowStatus = 'A'; // Por defecto activa
        const tableContainer = row.closest('.table-container');
        if (tableContainer) {
          const title = tableContainer.querySelector('h2');
          if (title) {
            if (title.textContent.includes('Terminadas')) rowStatus = 'T';
            else if (title.textContent.includes('Finalizadas')) rowStatus = 'F';
          }
        }

        const matchesSearch = patientName.includes(searchTerm) || doctorName.includes(searchTerm);
        const matchesStatus = statusValue === 'all' || rowStatus === statusValue;

        row.style.display = matchesSearch && matchesStatus ? '' : 'none';
      }
    });
  }

  if (searchInput && statusFilter && clearFilters) {
    searchInput.addEventListener('input', applyFilters);
    statusFilter.addEventListener('change', applyFilters);
    clearFilters.addEventListener('click', function() {
      searchInput.value = '';
      statusFilter.value = 'all';
      applyFilters();
    });
  }

  // Confirmación para eliminar cita
  document.querySelectorAll(".delete-btn").forEach((button) => {
    button.addEventListener("click", async function (event) {
      event.preventDefault();

      const result = await Swal.fire({
        title: "¿Estás seguro de que deseas eliminar esta cita?",
        text: "",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Eliminar",
        cancelButtonText: "Cancelar"
      });

      if (result.isConfirmed) {
        const form = this.closest('form');
        form.submit();
      }
    });
  });

  // Confirmación para finalizar cita
  document.querySelectorAll(".finish-btn").forEach((button) => {
    button.addEventListener("click", async function (event) {
      event.preventDefault();

      const result = await Swal.fire({
        title: "¿Estás seguro de que deseas finalizar esta cita?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Finalizar",
        cancelButtonText: "Cancelar"
      });

      if (result.isConfirmed) {
        window.location.href = this.closest('a').href;
      }
    });
  });
});
