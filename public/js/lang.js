/**
 * Kickverse Multi-language System
 * ES/EN language switcher with localStorage persistence
 */

(function() {
    'use strict';

    // Default language
    const DEFAULT_LANG = 'es';
    
    // Get current language from localStorage or use default
    function getCurrentLanguage() {
        return localStorage.getItem('kickverse_lang') || DEFAULT_LANG;
    }

    // Set language and update DOM
    function setLanguage(lang) {
        // Validate language
        if (lang !== 'es' && lang !== 'en') {
            lang = DEFAULT_LANG;
        }

        // Save to localStorage
        localStorage.setItem('kickverse_lang', lang);

        // Update HTML lang attribute
        document.documentElement.setAttribute('lang', lang);

        // Show/hide elements based on language
        const allLangElements = document.querySelectorAll('[data-lang]');
        allLangElements.forEach(element => {
            if (element.getAttribute('data-lang') === lang) {
                element.style.display = '';
                element.removeAttribute('hidden');
            } else {
                element.style.display = 'none';
                element.setAttribute('hidden', '');
            }
        });

        // Update language toggle button state
        updateLanguageButton(lang);

        // Trigger custom event for other scripts
        window.dispatchEvent(new CustomEvent('languageChanged', { detail: { lang } }));
    }

    // Update language button visual state
    function updateLanguageButton(lang) {
        const langButtons = document.querySelectorAll('.lang-btn');
        langButtons.forEach(btn => {
            const btnLang = btn.getAttribute('data-lang-btn');
            if (btnLang === lang) {
                btn.classList.add('active');
                btn.setAttribute('aria-pressed', 'true');
            } else {
                btn.classList.remove('active');
                btn.setAttribute('aria-pressed', 'false');
            }
        });
    }

    // Toggle between languages
    function toggleLanguage() {
        const currentLang = getCurrentLanguage();
        const newLang = currentLang === 'es' ? 'en' : 'es';
        setLanguage(newLang);
    }

    // Initialize language system
    function initLanguage() {
        // Set initial language
        const currentLang = getCurrentLanguage();
        setLanguage(currentLang);

        // Add click handlers to language buttons
        const langButtons = document.querySelectorAll('.lang-btn');
        langButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const targetLang = this.getAttribute('data-lang-btn');
                if (targetLang) {
                    setLanguage(targetLang);
                } else {
                    toggleLanguage();
                }
            });
        });

        // Keyboard accessibility
        document.addEventListener('keydown', function(e) {
            // Alt + L to toggle language
            if (e.altKey && e.key === 'l') {
                e.preventDefault();
                toggleLanguage();
            }
        });

        console.log('Kickverse language system initialized. Current language:', currentLang);
    }

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLanguage);
    } else {
        initLanguage();
    }

    // Expose functions globally
    window.KickverseLang = {
        setLanguage: setLanguage,
        getCurrentLanguage: getCurrentLanguage,
        toggleLanguage: toggleLanguage
    };

})();
