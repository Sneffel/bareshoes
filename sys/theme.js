// script.js
const themeSwitcher = document.getElementById('theme-switcher');
const themeIcon = document.getElementById('theme-icon');
const logos = document.querySelectorAll('#logo, .logo');

// Check the saved theme preference
const currentTheme = localStorage.getItem('theme');
if (currentTheme) {
    document.documentElement.setAttribute('data-bs-theme', currentTheme);
    updateThemeIcon(currentTheme);
    invertLogoColors(currentTheme);
}

// Theme switcher function
themeSwitcher.addEventListener('click', () => {
    const isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    const newTheme = isDarkMode ? '' : 'dark';
    
    localStorage.setItem('theme', newTheme);
    document.documentElement.setAttribute('data-bs-theme', newTheme);
    updateThemeIcon(newTheme);
    invertLogoColors(newTheme);
});

// Function to update theme icon based on the current theme
function updateThemeIcon(theme) {
    themeIcon.className = theme === 'dark' ? 'bi bi-moon' : 'bi bi-sun';
}

// Function to invert logo colors
function invertLogoColors(theme) {
    logos.forEach(logo => {
        logo.style.filter = theme === 'dark' ? 'invert(1)' : 'invert(0)';
    });
}