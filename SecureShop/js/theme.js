// Theme management
(function() {
    // Load saved theme or default to light
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    
    // Update theme toggle button
    function updateThemeToggle() {
        const toggle = document.querySelector('.theme-toggle');
        if (toggle) {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            toggle.textContent = currentTheme === 'light' ? '🌙' : '☀️';
        }
    }
    
    // Initialize theme toggle on page load
    document.addEventListener('DOMContentLoaded', updateThemeToggle);
    
    // Global theme toggle function
    window.toggleTheme = function() {
        const html = document.documentElement;
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        
        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeToggle();
    };
})();