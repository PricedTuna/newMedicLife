// Font Size Initializer
// This script initializes the font size from localStorage preference
// It should be included in all pages to apply the font size setting

document.addEventListener('DOMContentLoaded', () => {
  // Default font size in pixels
  const DEFAULT_FONT_SIZE = 16;
  
  // LocalStorage key for font size
  const STORAGE_KEY = 'fontSizePreference';
  
  // Get the current font size preference from localStorage or use default
  function getCurrentFontSize() {
    const storedSize = localStorage.getItem(STORAGE_KEY);
    return storedSize ? parseInt(storedSize) : DEFAULT_FONT_SIZE;
  }
  
  // Apply font size to the html element
  function applyFontSize(size) {
    document.documentElement.style.fontSize = `${size}px`;
  }
  
  // Initialize font size from stored preference
  function initialize() {
    const currentSize = getCurrentFontSize();
    applyFontSize(currentSize);
    return currentSize;
  }
  
  // Initialize font size when the DOM is loaded
  initialize();
});