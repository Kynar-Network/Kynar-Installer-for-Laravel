"use strict";

$(document).ready(function() {
// Theme Toggle Logic ()
var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
var themeToggleBtn = document.getElementById('theme-toggle');

// Shared theme logic
function toggleTheme(darkIcon, lightIcon, btn) {
    darkIcon.classList.toggle('hidden');
    lightIcon.classList.toggle('hidden');

    if (localStorage.getItem('color-theme')) {
        if (localStorage.getItem('color-theme') === 'light') {
            document.documentElement.classList.add('dark');
            localStorage.setItem('color-theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('color-theme', 'light');
        }
    } else {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('color-theme', 'light');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('color-theme', 'dark');
        }
    }
}

// Initial theme setup (shared)
if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark');
    themeToggleLightIcon.classList.remove('hidden');
} else {
    document.documentElement.classList.remove('dark');
    themeToggleDarkIcon.classList.remove('hidden');
}

// Event listeners
themeToggleBtn.addEventListener('click', function() {
    toggleTheme(themeToggleDarkIcon, themeToggleLightIcon, themeToggleBtn);
});
});
