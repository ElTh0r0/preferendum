/**
 * Thanks & kudos to: https://whitep4nth3r.com/blog/best-light-dark-mode-theme-toggle-javascript/
 */

function updateButton({ buttonEl, isDark }) {
  buttonEl.classList = isDark
  ? "themeToggle"
  : "themeToggle themeToggle--light";
}

function calculateSettingAsThemeString({
    localStorageTheme,
    systemSettingDark,
}) {
    if (localStorageTheme !== null) {
      return localStorageTheme;
    }
  
    if (systemSettingDark.matches) {
      return "dark";
    }
  
    return "light";
  }
 
function updateThemeOnHtmlEl({ theme }) {
    document.querySelector("html").setAttribute("data-theme", theme);
}
  
const button = document.querySelector("[data-theme-toggle]");
const localStorageTheme = localStorage.getItem("theme");
const systemSettingDark = window.matchMedia("(prefers-color-scheme: dark)");
  
let currentThemeSetting = calculateSettingAsThemeString({
    localStorageTheme,
    systemSettingDark,
});
  
updateButton({ buttonEl: button, isDark: currentThemeSetting === "dark" });
updateThemeOnHtmlEl({ theme: currentThemeSetting });
  
button.addEventListener("click", (event) => {
    const newTheme = currentThemeSetting === "dark" ? "light" : "dark";
  
    localStorage.setItem("theme", newTheme);
    updateButton({ buttonEl: button, isDark: newTheme === "dark" });
    updateThemeOnHtmlEl({ theme: newTheme });
  
    currentThemeSetting = newTheme;
});
