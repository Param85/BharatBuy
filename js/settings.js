document.addEventListener('DOMContentLoaded', () => {
    const settingsToggle = document.getElementById('settings-toggle');
    const settingsDropdown = document.getElementById('settings-dropdown');
    
    // Toggle settings dropdown visibility
    if (settingsToggle && settingsDropdown) {
        settingsToggle.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            settingsDropdown.classList.toggle('active');
        });
        
        // Hide dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!settingsDropdown.contains(e.target) && e.target !== settingsToggle && !settingsToggle.contains(e.target)) {
                settingsDropdown.classList.remove('active');
            }
        });
    }

    // Settings elements
    const darkModeBtn = document.getElementById('dark-mode-btn');
    const langSelect = document.getElementById('lang-select');
    const currencySelect = document.getElementById('currency-select');
    const alertBtn = document.getElementById('alert-btn');
    const searchSelect = document.getElementById('search-select');

    // Utility to get/set cookies
    function setCookie(name, value, days = 30) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = `${name}=${value};expires=${date.toUTCString()};path=/`;
    }

    function getCookie(name) {
        const matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|%\{\}\[\]\\\/\+\^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }

    // 1. Dark Mode Logic
    let isDarkMode = getCookie('dark_mode') === 'true';
    applyDarkMode(isDarkMode);

    if (darkModeBtn) {
        darkModeBtn.addEventListener('click', () => {
            isDarkMode = !isDarkMode;
            setCookie('dark_mode', isDarkMode);
            applyDarkMode(isDarkMode);
        });
    }

    function applyDarkMode(enabled) {
        if (enabled) {
            document.body.classList.add('dark-mode');
            if (darkModeBtn) {
                darkModeBtn.classList.add('enabled');
                darkModeBtn.innerHTML = '<i class="fa-solid fa-sun"></i> LIGHT MODE';
            }
        } else {
            document.body.classList.remove('dark-mode');
            if (darkModeBtn) {
                darkModeBtn.classList.remove('enabled');
                darkModeBtn.innerHTML = '<i class="fa-solid fa-moon"></i> DARK MODE';
            }
        }
    }

    // 2. Language Selection
    if (langSelect) {
        langSelect.addEventListener('change', () => {
            setCookie('lang', langSelect.value);
            window.location.reload();
        });
    }

    // 3. Currency Selection
    if (currencySelect) {
        currencySelect.addEventListener('change', () => {
            setCookie('currency', currencySelect.value);
            window.location.reload();
        });
    }

    // 4. Price Alerts Toggle
    let alertsEnabled = getCookie('price_alerts') !== 'false'; // default true
    applyPriceAlerts(alertsEnabled);

    if (alertBtn) {
        alertBtn.addEventListener('click', () => {
            alertsEnabled = !alertsEnabled;
            setCookie('price_alerts', alertsEnabled);
            applyPriceAlerts(alertsEnabled);
        });
    }

    function applyPriceAlerts(enabled) {
        if (enabled) {
            if (alertBtn) {
                alertBtn.classList.add('enabled');
                alertBtn.innerHTML = '<i class="fa-solid fa-bell"></i> ENABLED';
            }
        } else {
            if (alertBtn) {
                alertBtn.classList.remove('enabled');
                alertBtn.innerHTML = '<i class="fa-solid fa-bell-slash"></i> DISABLED';
            }
        }
    }

    // 5. Search Mode
    if (searchSelect) {
        searchSelect.addEventListener('change', () => {
            setCookie('search_mode', searchSelect.value);
        });
    }
});
