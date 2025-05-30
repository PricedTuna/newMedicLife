document.addEventListener('DOMContentLoaded', function() {
  // Filtros de búsqueda
  const searchInput = document.getElementById('searchInput');
  const genderFilter = document.getElementById('genderFilter');
  const clearFilters = document.getElementById('clearFilters');
  const rows = document.querySelectorAll('tbody tr');

  function applyFilters() {
    const searchTerm = searchInput.value.toLowerCase();
    const genderValue = genderFilter.value;

    rows.forEach(row => {
      // Solo procesar filas que tienen celdas (no mensajes de "no hay médicos")
      if (row.querySelector('[data-label="Nombre"]') && row.querySelector('[data-label="Sexo"]')) {
        const name = row.querySelector('[data-label="Nombre"]').textContent.toLowerCase();
        const lastName = row.querySelector('[data-label="Apellidos"]').textContent.toLowerCase();
        const curp = row.querySelector('[data-label="CURP"]').textContent.toLowerCase();
        const gender = row.querySelector('[data-label="Sexo"]').textContent.trim();

        const matchesSearch = name.includes(searchTerm) ||
                             lastName.includes(searchTerm) ||
                             curp.includes(searchTerm);
        const matchesGender = genderValue === 'all' || gender === genderValue;

        row.style.display = matchesSearch && matchesGender ? '' : 'none';
      }
    });
  }

  if (searchInput && genderFilter && clearFilters) {
    searchInput.addEventListener('input', applyFilters);
    genderFilter.addEventListener('change', applyFilters);
    clearFilters.addEventListener('click', function() {
      searchInput.value = '';
      genderFilter.value = 'all';
      applyFilters();
    });
  }
});

document.querySelectorAll(".delete-btn").forEach(button => {
    button.addEventListener("click", async function (event) {
        event.preventDefault();

        const result = await Swal.fire({
            title: "¿Estás seguro de que deseas eliminar este doctor?",
            text: "",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Eliminar",
            cancelButtonText: "Cancelar"
        })

        if (result.isConfirmed) {
            const form = this.closest('form');
            form.submit();
        }
    });
});
