/**
 * JavaScript for the medications list view
 */
document.addEventListener('DOMContentLoaded', function() {
    // Check if we have success or error messages from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const message = urlParams.get('message');
    
    // Show alert if we have a message
    if (message) {
        if (success === '1') {
            Swal.fire({
                title: '¡Éxito!',
                text: message,
                icon: 'success',
                confirmButtonText: 'Aceptar'
            });
        } else {
            Swal.fire({
                title: 'Error',
                text: message,
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
        
        // Remove parameters from URL without reloading
        const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
        window.history.replaceState({path: newUrl}, '', newUrl);
    }
    
    // Set active tab based on URL hash or default to medications
    const setActiveTab = () => {
        const hash = window.location.hash.substring(1) || 'medications';
        const tab = document.querySelector(`.tab[data-tab="${hash}"]`);
        if (tab) {
            // Remove active class from all tabs and contents
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            tab.classList.add('active');
            document.getElementById(`${hash}-tab`).classList.add('active');
        }
    };
    
    // Initial tab setup
    setActiveTab();
    
    // Update hash when tab is clicked
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', () => {
            const tabId = tab.getAttribute('data-tab');
            window.location.hash = tabId;
        });
    });
    
    // Listen for hash changes
    window.addEventListener('hashchange', setActiveTab);
    
    // Handle type filter change
    const typeFilter = document.getElementById('typeFilter');
    if (typeFilter) {
        // Set initial value from URL params
        const typeParam = urlParams.get('type');
        if (typeParam) {
            typeFilter.value = typeParam;
        }
    }
    
    // Handle search input
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        // Set initial value from URL params
        const searchParam = urlParams.get('search');
        if (searchParam) {
            searchInput.value = searchParam;
        }
        
        // Add event listener for enter key
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.querySelector('.filter-container form').submit();
            }
        });
    }
});