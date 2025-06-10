// Font Size Adjuster Module
const FontSizeAdjuster = (() => {
  // Default font size in pixels
  const DEFAULT_FONT_SIZE = 16;
  
  // Min and max font sizes
  const MIN_FONT_SIZE = 12;
  const MAX_FONT_SIZE = 24;
  
  // Step size for font size adjustments
  const FONT_SIZE_STEP = 2;
  
  // LocalStorage key for font size
  const STORAGE_KEY = 'fontSizePreference';
  
  // Get the current font size preference from localStorage or use default
  function getCurrentFontSize() {
    const storedSize = localStorage.getItem(STORAGE_KEY);
    return storedSize ? parseInt(storedSize) : DEFAULT_FONT_SIZE;
  }
  
  // Save font size preference to localStorage
  function saveFontSize(size) {
    localStorage.setItem(STORAGE_KEY, size.toString());
  }
  
  // Apply font size to the html element
  function applyFontSize(size) {
    document.documentElement.style.fontSize = `${size}px`;
  }
  
  // Increase font size
  function increase() {
    const currentSize = getCurrentFontSize();
    const newSize = Math.min(currentSize + FONT_SIZE_STEP, MAX_FONT_SIZE);
    saveFontSize(newSize);
    applyFontSize(newSize);
    showFontSizeToast(newSize);
    return newSize;
  }
  
  // Decrease font size
  function decrease() {
    const currentSize = getCurrentFontSize();
    const newSize = Math.max(currentSize - FONT_SIZE_STEP, MIN_FONT_SIZE);
    saveFontSize(newSize);
    applyFontSize(newSize);
    showFontSizeToast(newSize);
    return newSize;
  }
  
  // Reset font size to default
  function reset() {
    saveFontSize(DEFAULT_FONT_SIZE);
    applyFontSize(DEFAULT_FONT_SIZE);
    showFontSizeToast(DEFAULT_FONT_SIZE);
    return DEFAULT_FONT_SIZE;
  }
  
  // Initialize font size from stored preference
  function initialize() {
    const currentSize = getCurrentFontSize();
    applyFontSize(currentSize);
    return currentSize;
  }
  
  // Show a toast notification with the current font size
  function showFontSizeToast(size) {
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: `Tamaño de letra: ${size}px`,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        icon: 'info'
      });
    }
  }
  
  // Public API
  return {
    increase,
    decrease,
    reset,
    initialize,
    getCurrentFontSize
  };
})();

// Initialize font size when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
  // Initialize font size from stored preference
  FontSizeAdjuster.initialize();
  
  // Add event listeners to font size buttons if they exist
  const increaseFontBtn = document.getElementById('increaseFontBtn');
  const decreaseFontBtn = document.getElementById('decreaseFontBtn');
  const resetFontBtn = document.getElementById('resetFontBtn');
  
  if (increaseFontBtn) {
    increaseFontBtn.addEventListener('click', FontSizeAdjuster.increase);
  }
  
  if (decreaseFontBtn) {
    decreaseFontBtn.addEventListener('click', FontSizeAdjuster.decrease);
  }
  
  if (resetFontBtn) {
    resetFontBtn.addEventListener('click', FontSizeAdjuster.reset);
  }
});