/**
 * JavaScript for Payment Summary View
 */

// Function to handle date filter change
function changeDate(date) {
    // Redirect to the same page with the new date parameter
    window.location.href = `/views/pay/payment-summary.view.php?date=${date}`;
}

// Initialize any tooltips or interactive elements
document.addEventListener('DOMContentLoaded', function() {
    // You can add initialization code here if needed
    
    // Example: Add click event to summary cards to highlight them
    const summaryCards = document.querySelectorAll('.summary-card');
    summaryCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove highlight from all cards
            summaryCards.forEach(c => c.classList.remove('active'));
            // Add highlight to clicked card
            this.classList.add('active');
        });
    });
});