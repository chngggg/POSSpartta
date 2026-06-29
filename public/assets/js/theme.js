// // =====================================================
// // THEME TOGGLE - DARK / LIGHT MODE
// // =====================================================

// (function () {
//     "use strict";

//     // Check for saved theme preference
//     const getStoredTheme = () => {
//         return localStorage.getItem("spartta-theme") || "dark";
//     };

//     const setStoredTheme = (theme) => {
//         localStorage.setItem("spartta-theme", theme);
//     };

//     const getPreferredTheme = () => {
//         const storedTheme = getStoredTheme();
//         if (storedTheme) {
//             return storedTheme;
//         }
//         // Check system preference
//         return window.matchMedia("(prefers-color-scheme: light)").matches
//             ? "light"
//             : "dark";
//     };

//     const setTheme = (theme) => {
//         const body = document.body;
//         const icon = document.getElementById("themeIcon");

//         if (theme === "light") {
//             body.classList.add("light-mode");
//             if (icon) {
//                 icon.className = "fas fa-sun";
//             }
//         } else {
//             body.classList.remove("light-mode");
//             if (icon) {
//                 icon.className = "fas fa-moon";
//             }
//         }

//         setStoredTheme(theme);

//         // Trigger custom event for other components
//         document.dispatchEvent(
//             new CustomEvent("themeChanged", {
//                 detail: { theme: theme },
//             }),
//         );
//     };

//     // Initialize theme on page load
//     const initTheme = () => {
//         const theme = getPreferredTheme();
//         setTheme(theme);
//     };

//     // Toggle theme on button click
//     const toggleTheme = () => {
//         const currentTheme = getStoredTheme();
//         const newTheme = currentTheme === "light" ? "dark" : "light";
//         setTheme(newTheme);
//     };

//     // Setup event listeners when DOM is ready
//     document.addEventListener("DOMContentLoaded", function () {
//         // Initialize theme
//         initTheme();

//         // Setup toggle button
//         const toggleBtn = document.getElementById("themeToggle");
//         if (toggleBtn) {
//             toggleBtn.addEventListener("click", toggleTheme);
//         }

//         // Listen for system theme changes
//         window
//             .matchMedia("(prefers-color-scheme: light)")
//             .addEventListener("change", (e) => {
//                 if (!localStorage.getItem("spartta-theme")) {
//                     // Only change if user hasn't manually set a preference
//                     setTheme(e.matches ? "light" : "dark");
//                 }
//             });
//     });
// })();
