// JavaScript for appointment list functionality

/**
 * Shows a confirmation dialog when deleting an appointment
 * @param {HTMLElement} button - The delete button that was clicked
 */
function confirmDeleteAppointment(button) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Realmente deseas eliminar esta cita? Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit the form if confirmed
            button.closest('form').submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const clearFiltersBtn = document.getElementById('clearFilters');

    if (searchInput && statusFilter && clearFiltersBtn) {
        // Search filter
        searchInput.addEventListener('input', filterAppointments);

        // Status filter
        statusFilter.addEventListener('change', filterAppointments);

        // Clear filters
        clearFiltersBtn.addEventListener('click', function() {
            searchInput.value = '';
            statusFilter.value = 'all';
            filterAppointments();
        });
    }

    // Function to filter appointments based on search input and status
    function filterAppointments() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;

        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const patientName = row.querySelector('td[data-label="Paciente"]')?.textContent.toLowerCase() || '';
            const doctorName = row.querySelector('td[data-label="Médico"]')?.textContent.toLowerCase() || '';
            const rowStatus = row.getAttribute('data-status');

            const matchesSearch = patientName.includes(searchTerm) || doctorName.includes(searchTerm);
            const matchesStatus = statusValue === 'all' || rowStatus === statusValue;

            row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
        });
    }

    // Add event listeners to all delete buttons
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            confirmDeleteAppointment(this);
        });
    });
});
