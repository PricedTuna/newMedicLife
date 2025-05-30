

// Handle dropdown toggle for mobile
document.addEventListener('DOMContentLoaded', function () {
  // Filtros de búsqueda
  const searchInput = document.getElementById('searchInput');
  const roleFilter = document.getElementById('roleFilter');
  const statusFilter = document.getElementById('statusFilter');
  const clearFilters = document.getElementById('clearFilters');
  const rows = document.querySelectorAll('tbody tr');

  function applyFilters() {
    const searchTerm = searchInput.value.toLowerCase();
    const roleValue = roleFilter.value;
    const statusValue = statusFilter.value;

    rows.forEach(row => {
      // Solo procesar filas que tienen celdas (no mensajes de "no hay usuarios")
      if (row.querySelector('[data-label="Nombre"]') && row.querySelector('[data-label="Rol"]')) {
        const name = row.querySelector('[data-label="Nombre"]').textContent.toLowerCase();
        const email = row.querySelector('[data-label="Correo"]').textContent.toLowerCase();

        // Obtener el valor del rol (S, A, D) del texto mostrado
        const roleTd = row.querySelector('[data-label="Rol"]');
        let role = '';
        if (roleTd.textContent.includes('Administración')) role = 'S';
        else if (roleTd.textContent.includes('Administrador')) role = 'A';
        else if (roleTd.textContent.includes('Doctor')) role = 'D';

        // Obtener el estado (AC, IN) del texto mostrado
        const statusTd = row.querySelector('[data-label="Estado"]');
        let status = '';
        if (statusTd.textContent.includes('Activo')) status = 'AC';
        else status = statusTd.textContent.trim();

        const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
        const matchesRole = roleValue === 'all' || role === roleValue;
        const matchesStatus = statusValue === 'all' || status === statusValue;

        row.style.display = matchesSearch && matchesRole && matchesStatus ? '' : 'none';
      }
    });
  }

  if (searchInput && roleFilter && statusFilter && clearFilters) {
    searchInput.addEventListener('input', applyFilters);
    roleFilter.addEventListener('change', applyFilters);
    statusFilter.addEventListener('change', applyFilters);
    clearFilters.addEventListener('click', function() {
      searchInput.value = '';
      roleFilter.value = 'all';
      statusFilter.value = 'all';
      applyFilters();
    });
  }
  // Toggle dropdown on click for mobile devices
  const dropdownBtns = document.querySelectorAll('.dropdown-btn');

  dropdownBtns.forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();

      // Close all other dropdowns
      document.querySelectorAll('.dropdown-content').forEach(content => {
        if (content !== this.nextElementSibling) {
          content.classList.remove('show');
        }
      });

      // Toggle current dropdown
      const dropdownContent = this.nextElementSibling;
      dropdownContent.classList.toggle('show');
    });
  });

  // Close dropdowns when clicking outside
  document.addEventListener('click', function (e) {
    if (!e.target.matches('.dropdown-btn') && !e.target.closest('.dropdown-content')) {
      document.querySelectorAll('.dropdown-content').forEach(content => {
        content.classList.remove('show');
      });
    }
  });
});

document.querySelectorAll('.delete-btn').forEach(button => {
  button.addEventListener('click', async function (event) {
    event.preventDefault();

    const result = await Swal.fire({
      title: '¿Estás seguro de que deseas eliminar este usuario?',
      text: '',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Eliminar',
      cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
      const form = this.closest('form');
      form.submit();
    }
  });
});
