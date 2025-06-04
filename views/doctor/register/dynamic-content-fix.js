// Fix for views-handler.js error
document.addEventListener('DOMContentLoaded', function() {
    // Check if #dynamic-content exists, if not create it
    if (!document.getElementById('dynamic-content')) {
        const dynamicContent = document.createElement('div');
        dynamicContent.id = 'dynamic-content';
        dynamicContent.style.display = 'none'; // Hide it as it's not needed
        document.body.appendChild(dynamicContent);
        console.log('Created #dynamic-content element for views-handler.js');
    }
});