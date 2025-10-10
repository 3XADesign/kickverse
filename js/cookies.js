/**
 * Cookie Consent Manager
 * Gestiona el consentimiento de cookies según normativa GDPR
 */

class CookieConsent {
    constructor() {
        this.cookieName = 'kickverse_cookie_consent';
        this.cookieDuration = 365; // días
        this.preferences = {
            necessary: true, // Siempre activadas
            analytics: false,
            marketing: false,
            preferences: false
        };
        
        this.init();
    }

    init() {
        // Verificar si ya existe consentimiento
        const savedConsent = this.getCookie(this.cookieName);
        
        if (!savedConsent) {
            // Mostrar banner si no hay consentimiento previo
            this.showBanner();
        } else {
            // Cargar preferencias guardadas
            this.preferences = JSON.parse(savedConsent);
            this.applyCookiePreferences();
        }

        // Configurar event listeners
        this.setupEventListeners();
    }

    showBanner() {
        const banner = document.getElementById('cookieConsent');
        if (banner) {
            setTimeout(() => {
                banner.classList.add('show');
            }, 1000);
        }
    }

    hideBanner() {
        const banner = document.getElementById('cookieConsent');
        if (banner) {
            banner.classList.remove('show');
        }
    }

    showSettings() {
        const modal = document.getElementById('cookieSettingsModal');
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
            
            // Cargar preferencias actuales en el modal
            this.loadSettingsToModal();
        }
    }

    hideSettings() {
        const modal = document.getElementById('cookieSettingsModal');
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    loadSettingsToModal() {
        // Cargar el estado actual de las preferencias en los toggles
        for (const [key, value] of Object.entries(this.preferences)) {
            const toggle = document.getElementById(`cookie-${key}`);
            if (toggle) {
                toggle.checked = value;
            }
        }
    }

    setupEventListeners() {
        // Botón Aceptar Todo
        const acceptBtn = document.getElementById('acceptCookies');
        if (acceptBtn) {
            acceptBtn.addEventListener('click', () => this.acceptAll());
        }

        // Botón Rechazar Todo
        const rejectBtn = document.getElementById('rejectCookies');
        if (rejectBtn) {
            rejectBtn.addEventListener('click', () => this.rejectAll());
        }

        // Botón Configurar
        const settingsBtn = document.getElementById('cookieSettings');
        if (settingsBtn) {
            settingsBtn.addEventListener('click', () => this.showSettings());
        }

        // Botón Cerrar Modal
        const closeBtn = document.getElementById('closeSettings');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.hideSettings());
        }

        // Botón Guardar Preferencias
        const saveBtn = document.getElementById('savePreferences');
        if (saveBtn) {
            saveBtn.addEventListener('click', () => this.savePreferences());
        }

        // Cerrar modal al hacer clic fuera
        const modal = document.getElementById('cookieSettingsModal');
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.hideSettings();
                }
            });
        }
    }

    acceptAll() {
        this.preferences = {
            necessary: true,
            analytics: true,
            marketing: true,
            preferences: true
        };
        this.saveConsent();
        this.hideBanner();
        this.applyCookiePreferences();
    }

    rejectAll() {
        this.preferences = {
            necessary: true,
            analytics: false,
            marketing: false,
            preferences: false
        };
        this.saveConsent();
        this.hideBanner();
        this.applyCookiePreferences();
    }

    savePreferences() {
        // Leer valores de los toggles
        const analyticsToggle = document.getElementById('cookie-analytics');
        const marketingToggle = document.getElementById('cookie-marketing');
        const preferencesToggle = document.getElementById('cookie-preferences');

        this.preferences = {
            necessary: true,
            analytics: analyticsToggle ? analyticsToggle.checked : false,
            marketing: marketingToggle ? marketingToggle.checked : false,
            preferences: preferencesToggle ? preferencesToggle.checked : false
        };

        this.saveConsent();
        this.hideSettings();
        this.hideBanner();
        this.applyCookiePreferences();
    }

    saveConsent() {
        const consentData = JSON.stringify(this.preferences);
        this.setCookie(this.cookieName, consentData, this.cookieDuration);
    }

    applyCookiePreferences() {
        // Aquí puedes activar/desactivar diferentes servicios según las preferencias
        
        // Ejemplo: Google Analytics
        if (this.preferences.analytics) {
            this.enableAnalytics();
        } else {
            this.disableAnalytics();
        }

        // Ejemplo: Marketing (Facebook Pixel, etc.)
        if (this.preferences.marketing) {
            this.enableMarketing();
        }

        // Ejemplo: Cookies de preferencias (idioma, tema, etc.)
        if (this.preferences.preferences) {
            this.enablePreferences();
        }

        console.log('Preferencias de cookies aplicadas:', this.preferences);
    }

    enableAnalytics() {
        // Implementar código de Google Analytics si está configurado
        if (typeof gtag !== 'undefined') {
            gtag('consent', 'update', {
                'analytics_storage': 'granted'
            });
        }
    }

    disableAnalytics() {
        if (typeof gtag !== 'undefined') {
            gtag('consent', 'update', {
                'analytics_storage': 'denied'
            });
        }
    }

    enableMarketing() {
        if (typeof gtag !== 'undefined') {
            gtag('consent', 'update', {
                'ad_storage': 'granted',
                'ad_user_data': 'granted',
                'ad_personalization': 'granted'
            });
        }
    }

    enablePreferences() {
        // Las cookies de preferencias ya están permitidas
    }

    setCookie(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = "expires=" + date.toUTCString();
        document.cookie = `${name}=${value};${expires};path=/;SameSite=Lax`;
    }

    getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    // Método público para revisar consentimiento desde otros scripts
    hasConsent(type) {
        return this.preferences[type] === true;
    }

    // Método para abrir configuración desde enlaces externos
    openSettings() {
        this.showSettings();
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.cookieConsent = new CookieConsent();
});
