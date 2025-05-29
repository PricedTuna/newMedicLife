

// Handle dropdown toggle for mobile
document.addEventListener('DOMContentLoaded', function () {
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

