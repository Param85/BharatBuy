/**
 * BharatBuy Platform Features & UX Enhancements
 * Includes:
 * 1. Dark mode sync
 * 2. Cookie banner consent
 * 3. Global site search shortcuts (Ctrl+K, /)
 * 4. Back to top button
 * 5. Mobile navigation drawer
 * 6. Top loader & button loading states
 * 8. Scroll progress bar
 * 9. Reusable copy to clipboard button
 * 14. UTM parameter client-side propagation
 * 17. Reusable accessible confirmation modal
 */

(function () {
    'use strict';

    // -------------------------------------------------------------------------
    // 8. Scroll Progress Bar & 4. Back to Top Button
    // -------------------------------------------------------------------------
    const scrollProgress = document.getElementById('bb-scroll-progress');
    const backToTopBtn = document.getElementById('back-to-top-btn');

    function handleScroll() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;

        if (scrollProgress && scrollHeight > 0) {
            const percentage = (scrollTop / scrollHeight) * 100;
            scrollProgress.style.width = Math.min(percentage, 100) + '%';
        }

        if (backToTopBtn) {
            if (scrollTop > 300) {
                backToTopBtn.classList.add('visible');
            } else {
                backToTopBtn.classList.remove('visible');
            }
        }
    }

    window.addEventListener('scroll', handleScroll, { passive: true });

    if (backToTopBtn) {
        backToTopBtn.addEventListener('click', function () {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // -------------------------------------------------------------------------
    // 5. Mobile Navigation Drawer
    // -------------------------------------------------------------------------
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileDrawerClose = document.getElementById('mobile-drawer-close');
    const mobileNavDrawer = document.getElementById('mobile-nav-drawer');
    const mobileNavOverlay = document.getElementById('mobile-nav-overlay');

    function openMobileMenu() {
        if (mobileNavDrawer && mobileNavOverlay) {
            mobileNavDrawer.classList.add('open');
            mobileNavOverlay.classList.add('open');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeMobileMenu() {
        if (mobileNavDrawer && mobileNavOverlay) {
            mobileNavDrawer.classList.remove('open');
            mobileNavOverlay.classList.remove('open');
            document.body.style.overflow = '';
        }
    }

    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', openMobileMenu);
    }
    if (mobileDrawerClose) {
        mobileDrawerClose.addEventListener('click', closeMobileMenu);
    }
    if (mobileNavOverlay) {
        mobileNavOverlay.addEventListener('click', closeMobileMenu);
    }

    // -------------------------------------------------------------------------
    // 1. Dark Mode Sync across Mobile & Desktop
    // -------------------------------------------------------------------------
    const mobileThemeToggle = document.getElementById('mobile-theme-toggle');
    const desktopThemeToggle = document.getElementById('theme-toggle');

    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
        const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', nextTheme);
        localStorage.setItem('theme', nextTheme);

        const themeBtnText = document.getElementById('theme-btn-text');
        if (themeBtnText) {
            themeBtnText.innerText = nextTheme === 'dark' ? 'Light Mode' : 'Dark Mode';
        }
        const mobileThemeText = document.getElementById('mobile-theme-text');
        if (mobileThemeText) {
            mobileThemeText.innerText = nextTheme === 'dark' ? 'Light Mode' : 'Dark Mode';
        }
    }

    if (mobileThemeToggle) {
        mobileThemeToggle.addEventListener('click', toggleTheme);
    }

    // Direct header 1-click theme toggle if present
    const navDirectThemeBtn = document.getElementById('nav-direct-theme-btn');
    if (navDirectThemeBtn) {
        navDirectThemeBtn.addEventListener('click', toggleTheme);
    }

    // -------------------------------------------------------------------------
    // 2. Simple Cookie Banner
    // -------------------------------------------------------------------------
    const cookieBanner = document.getElementById('bb-cookie-banner');
    const cookieAcceptAll = document.getElementById('cookie-accept-all');
    const cookieAcceptEssential = document.getElementById('cookie-accept-essential');

    if (cookieBanner) {
        const consent = localStorage.getItem('bb_cookie_consent');
        if (!consent) {
            setTimeout(function () {
                cookieBanner.classList.add('show');
            }, 800);
        }

        if (cookieAcceptAll) {
            cookieAcceptAll.addEventListener('click', function () {
                localStorage.setItem('bb_cookie_consent', 'all');
                cookieBanner.classList.remove('show');
            });
        }

        if (cookieAcceptEssential) {
            cookieAcceptEssential.addEventListener('click', function () {
                localStorage.setItem('bb_cookie_consent', 'essential');
                cookieBanner.classList.remove('show');
            });
        }
    }

    // -------------------------------------------------------------------------
    // 3. Global Site Search Shortcut (Ctrl+K or /)
    // -------------------------------------------------------------------------
    document.addEventListener('keydown', function (e) {
        const activeTag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
        const isEditing = activeTag === 'input' || activeTag === 'textarea' || (document.activeElement && document.activeElement.isContentEditable);

        if ((e.ctrlKey && e.key === 'k') || (e.key === '/' && !isEditing)) {
            e.preventDefault();
            const homeSearchInput = document.getElementById('homeSearchInput') || document.querySelector('input[name="q"]') || document.querySelector('.search-container input');
            if (homeSearchInput) {
                homeSearchInput.focus();
                homeSearchInput.select();
            } else {
                window.location.href = 'dashboard.php';
            }
        }
    });

    // -------------------------------------------------------------------------
    // 9. Reusable Copy Button
    // -------------------------------------------------------------------------
    document.addEventListener('click', function (e) {
        const copyBtn = e.target.closest('[data-copy], .btn-copy');
        if (!copyBtn) return;

        const textToCopy = copyBtn.getAttribute('data-copy') || window.location.href;
        if (!textToCopy) return;

        navigator.clipboard.writeText(textToCopy).then(function () {
            const originalHTML = copyBtn.innerHTML;
            copyBtn.classList.add('copied');
            copyBtn.innerHTML = '<i class="fa-solid fa-check"></i> Copied!';

            setTimeout(function () {
                copyBtn.classList.remove('copied');
                copyBtn.innerHTML = originalHTML;
            }, 1800);
        }).catch(function (err) {
            console.error('Failed to copy to clipboard:', err);
        });
    });

    // -------------------------------------------------------------------------
    // 13. Password Visibility Toggle
    // -------------------------------------------------------------------------
    document.querySelectorAll('.password-toggle-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const wrap = btn.closest('.password-input-wrap') || btn.parentElement;
            const input = wrap ? wrap.querySelector('input[type="password"], input[type="text"]') : null;
            if (!input) return;

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            const icon = btn.querySelector('i');
            if (icon) {
                icon.className = isPassword ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
            }
        });
    });

    // -------------------------------------------------------------------------
    // 17. Reusable Accessible Confirmation Modal
    // -------------------------------------------------------------------------
    const confirmModal = document.getElementById('bb-confirm-modal');
    const modalTitle = document.getElementById('bb-modal-title');
    const modalMessage = document.getElementById('bb-modal-message');
    const modalCancel = document.getElementById('bb-modal-cancel');
    const modalConfirm = document.getElementById('bb-modal-confirm');
    let currentConfirmCallback = null;

    window.bbConfirm = function (title, message, callback) {
        if (!confirmModal) {
            if (window.confirm(message)) {
                if (typeof callback === 'function') callback();
            }
            return;
        }

        if (modalTitle) modalTitle.innerText = title || 'Confirm Action';
        if (modalMessage) modalMessage.innerText = message || 'Are you sure?';
        currentConfirmCallback = callback;

        confirmModal.classList.add('show');
    };

    function hideConfirmModal() {
        if (confirmModal) {
            confirmModal.classList.remove('show');
            currentConfirmCallback = null;
        }
    }

    if (modalCancel) {
        modalCancel.addEventListener('click', hideConfirmModal);
    }
    if (confirmModal) {
        confirmModal.addEventListener('click', function (e) {
            if (e.target === confirmModal) hideConfirmModal();
        });
    }
    if (modalConfirm) {
        modalConfirm.addEventListener('click', function () {
            const cb = currentConfirmCallback;
            hideConfirmModal();
            if (typeof cb === 'function') cb();
        });
    }

    // -------------------------------------------------------------------------
    // 6. Global Top Loader API
    // -------------------------------------------------------------------------
    const topLoader = document.getElementById('bb-top-loader');
    window.bbShowLoader = function () {
        if (topLoader) topLoader.style.display = 'block';
    };
    window.bbHideLoader = function () {
        if (topLoader) topLoader.style.display = 'none';
    };

    // -------------------------------------------------------------------------
    // 14. UTM Attribution Persistence
    // -------------------------------------------------------------------------
    try {
        const urlParams = new URLSearchParams(window.location.search);
        const utmKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
        const foundUtm = {};
        utmKeys.forEach(function (key) {
            if (urlParams.has(key)) {
                foundUtm[key] = urlParams.get(key);
            }
        });
        if (Object.keys(foundUtm).length > 0) {
            localStorage.setItem('bb_utm_params', JSON.stringify(foundUtm));
        }
    } catch (e) {
        // Fallback silently
    }
})();
