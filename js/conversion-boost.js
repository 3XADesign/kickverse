// ============================================
// CONVERSION-BOOST.JS - Funcionalidad de conversión
// ============================================

// ============================================
// 1. STICKY CTA MÓVIL
// ============================================
function initStickyCTA() {
    const stickyCTA = document.querySelector('.sticky-cta');
    if (!stickyCTA) return;

    let scrollThreshold = 800; // Mostrar después de 800px de scroll
    let lastScroll = 0;

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        // Mostrar cuando el usuario scrollea hacia abajo más de threshold
        if (currentScroll > scrollThreshold) {
            stickyCTA.classList.add('visible');
        } else {
            stickyCTA.classList.remove('visible');
        }

        // Ocultar si el usuario está en la sección de planes
        const planesSection = document.getElementById('planes');
        if (planesSection) {
            const planesSectionTop = planesSection.getBoundingClientRect().top;
            const planesSectionBottom = planesSection.getBoundingClientRect().bottom;
            
            if (planesSectionTop < window.innerHeight && planesSectionBottom > 0) {
                stickyCTA.classList.remove('visible');
            }
        }

        lastScroll = currentScroll;
    });
}

// ============================================
// 2. COUNTDOWN TIMER
// ============================================
function initCountdownTimer() {
    const countdown = document.querySelector('.countdown-timer');
    if (!countdown) return;

    // Configurar fecha objetivo (ejemplo: 7 días desde hoy)
    const targetDate = new Date();
    targetDate.setDate(targetDate.getDate() + 7);
    targetDate.setHours(23, 59, 59, 999);

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = targetDate - now;

        if (distance < 0) {
            // El countdown ha terminado
            countdown.innerHTML = '<div class="countdown-ended">¡El drop ha terminado!</div>';
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Actualizar los elementos
        const daysEl = countdown.querySelector('.countdown-days');
        const hoursEl = countdown.querySelector('.countdown-hours');
        const minutesEl = countdown.querySelector('.countdown-minutes');
        const secondsEl = countdown.querySelector('.countdown-seconds');

        if (daysEl) daysEl.textContent = String(days).padStart(2, '0');
        if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
        if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, '0');
        if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, '0');
    }

    // Actualizar cada segundo
    updateCountdown();
    setInterval(updateCountdown, 1000);
}

// ============================================
// 3. STOCK INDICATOR DINÁMICO
// ============================================
function initStockIndicator() {
    const stockIndicators = document.querySelectorAll('.stock-indicator');
    
    stockIndicators.forEach(indicator => {
        const stockBar = indicator.nextElementSibling;
        if (stockBar && stockBar.classList.contains('stock-bar')) {
            const stockFill = stockBar.querySelector('.stock-fill');
            if (stockFill) {
                // Simular actualización de stock (en producción, esto vendría del backend)
                const randomStock = Math.floor(Math.random() * 30) + 10; // Entre 10 y 40
                const percentage = (randomStock / 100) * 100;
                
                setTimeout(() => {
                    stockFill.style.width = `${percentage}%`;
                }, 300);
            }
        }
    });
}

// ============================================
// 4. FAQ ACCORDION
// ============================================
function initFAQ() {
    const faqQuestions = document.querySelectorAll('.faq-question');
    
    faqQuestions.forEach(question => {
        question.addEventListener('click', () => {
            const faqItem = question.parentElement;
            const isActive = faqItem.classList.contains('active');
            
            // Cerrar todos los demás
            document.querySelectorAll('.faq-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Toggle el actual
            if (!isActive) {
                faqItem.classList.add('active');
            }
        });
    });
}

// ============================================
// 5. EXIT-INTENT POPUP
// ============================================
function initExitIntent() {
    const exitPopup = document.querySelector('.exit-popup');
    if (!exitPopup) return;

    let hasShown = false;
    const closeBtn = exitPopup.querySelector('.exit-popup-close');

    // Detectar cuando el mouse sale por arriba de la ventana
    document.addEventListener('mouseleave', (e) => {
        if (e.clientY < 0 && !hasShown) {
            showExitPopup();
        }
    });

    // Cerrar popup
    function hideExitPopup() {
        exitPopup.classList.remove('show');
        document.body.style.overflow = '';
    }

    function showExitPopup() {
        if (hasShown) return;
        
        exitPopup.classList.add('show');
        document.body.style.overflow = 'hidden';
        hasShown = true;

        // Guardar en localStorage para no mostrar de nuevo
        localStorage.setItem('exitPopupShown', 'true');

        // GTM Event
        if (typeof gtag !== 'undefined') {
            gtag('event', 'exit_intent_shown', {
                'event_category': 'engagement'
            });
        }
    }

    // Botón de cerrar
    if (closeBtn) {
        closeBtn.addEventListener('click', hideExitPopup);
    }

    // Cerrar al hacer clic fuera
    exitPopup.addEventListener('click', (e) => {
        if (e.target === exitPopup) {
            hideExitPopup();
        }
    });

    // Cerrar con ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && exitPopup.classList.contains('show')) {
            hideExitPopup();
        }
    });

    // No mostrar si ya se mostró anteriormente
    if (localStorage.getItem('exitPopupShown')) {
        hasShown = true;
    }
}

// ============================================
// 6. ANIMACIONES AL SCROLL (Intersection Observer)
// ============================================
function initScrollAnimations() {
    const animatedElements = document.querySelectorAll(
        '.trust-badge, .testimonial-card, .countdown-section, .faq-item'
    );

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '0';
                entry.target.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    entry.target.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, entry.target.dataset.delay || 0);
                
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    animatedElements.forEach((el, index) => {
        el.dataset.delay = index * 100; // Delay escalonado
        observer.observe(el);
    });
}

// ============================================
// 7. LAZY LOADING DE IMÁGENES
// ============================================
function initLazyLoading() {
    const lazyImages = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                imageObserver.unobserve(img);
                
                // Añadir clase cuando se carga
                img.addEventListener('load', () => {
                    img.classList.add('loaded');
                });
            }
        });
    });

    lazyImages.forEach(img => imageObserver.observe(img));
}

// ============================================
// 8. TRACKING DE EVENTOS
// ============================================
function initEventTracking() {
    // Track clicks en CTAs
    document.querySelectorAll('a[href*="suscrib"], a[href*="plan"]').forEach(cta => {
        cta.addEventListener('click', () => {
            const planName = cta.textContent.trim();
            
            if (typeof gtag !== 'undefined') {
                gtag('event', 'cta_click', {
                    'event_category': 'conversion',
                    'event_label': planName,
                    'value': 1
                });
            }
        });
    });

    // Track scroll depth
    let scrollDepthTracked = {
        25: false,
        50: false,
        75: false,
        100: false
    };

    window.addEventListener('scroll', () => {
        const scrollPercent = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
        
        Object.keys(scrollDepthTracked).forEach(depth => {
            if (scrollPercent >= depth && !scrollDepthTracked[depth]) {
                scrollDepthTracked[depth] = true;
                
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'scroll_depth', {
                        'event_category': 'engagement',
                        'event_label': `${depth}%`,
                        'value': parseInt(depth)
                    });
                }
            }
        });
    });

    // Track tiempo en página
    let timeOnPage = 0;
    const timeTracked = {
        30: false,
        60: false,
        120: false,
        300: false
    };

    setInterval(() => {
        timeOnPage += 5;
        
        Object.keys(timeTracked).forEach(time => {
            if (timeOnPage >= time && !timeTracked[time]) {
                timeTracked[time] = true;
                
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'time_on_page', {
                        'event_category': 'engagement',
                        'event_label': `${time}s`,
                        'value': parseInt(time)
                    });
                }
            }
        });
    }, 5000);
}

// ============================================
// 9. NOTIFICACIONES DE SOCIAL PROOF
// ============================================
function initSocialProofNotifications() {
    const notifications = [
        { name: 'Carlos M.', location: 'Madrid', plan: 'Plan PRO', time: '5 minutos' },
        { name: 'Laura G.', location: 'Barcelona', plan: 'Plan Premium Random', time: '12 minutos' },
        { name: 'Javier R.', location: 'Valencia', plan: 'Plan Fan', time: '23 minutos' },
        { name: 'Ana P.', location: 'Sevilla', plan: 'Plan Retro TOP', time: '1 hora' },
        { name: 'Miguel S.', location: 'Bilbao', plan: 'Plan PRO', time: '2 horas' }
    ];

    let currentIndex = 0;
    let notificationElement = null;

    function createNotificationElement() {
        const div = document.createElement('div');
        div.className = 'social-proof-notification';
        div.innerHTML = `
            <div class="notification-content">
                <div class="notification-icon">✓</div>
                <div class="notification-text">
                    <strong class="notification-name"></strong>
                    <span class="notification-detail"></span>
                </div>
            </div>
        `;
        document.body.appendChild(div);
        return div;
    }

    function showNotification() {
        if (!notificationElement) {
            notificationElement = createNotificationElement();
        }

        const notification = notifications[currentIndex];
        const nameEl = notificationElement.querySelector('.notification-name');
        const detailEl = notificationElement.querySelector('.notification-detail');

        nameEl.textContent = notification.name;
        detailEl.textContent = `de ${notification.location} se suscribió al ${notification.plan} hace ${notification.time}`;

        notificationElement.classList.add('show');

        setTimeout(() => {
            notificationElement.classList.remove('show');
        }, 5000);

        currentIndex = (currentIndex + 1) % notifications.length;
    }

    // Agregar estilos para las notificaciones
    const style = document.createElement('style');
    style.textContent = `
        .social-proof-notification {
            position: fixed;
            bottom: 80px;
            left: 20px;
            background: white;
            color: #1a1a1a;
            padding: 16px 20px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            transform: translateX(-120%);
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 9998;
            max-width: 320px;
        }

        .social-proof-notification.show {
            transform: translateX(0);
        }

        .notification-content {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .notification-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }

        .notification-text {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .notification-name {
            font-weight: 700;
            font-size: 14px;
        }

        .notification-detail {
            font-size: 13px;
            color: #666;
        }

        @media (max-width: 768px) {
            .social-proof-notification {
                left: 50%;
                transform: translateX(-50%) translateY(120%);
                bottom: 100px;
                max-width: calc(100% - 40px);
            }

            .social-proof-notification.show {
                transform: translateX(-50%) translateY(0);
            }
        }
    `;
    document.head.appendChild(style);

    // Mostrar primera notificación después de 10 segundos
    setTimeout(showNotification, 10000);

    // Luego mostrar cada 25 segundos
    setInterval(showNotification, 25000);
}

// ============================================
// 10. PRICE COMPARISON TOOLTIP
// ============================================
function initPriceComparison() {
    const priceElements = document.querySelectorAll('.plan-price');
    
    priceElements.forEach(priceEl => {
        const tooltip = document.createElement('div');
        tooltip.className = 'price-tooltip';
        tooltip.innerHTML = '💡 Precio por camiseta más bajo que comprarla individual';
        
        priceEl.addEventListener('mouseenter', () => {
            priceEl.appendChild(tooltip);
            setTimeout(() => tooltip.classList.add('show'), 10);
        });
        
        priceEl.addEventListener('mouseleave', () => {
            tooltip.classList.remove('show');
            setTimeout(() => tooltip.remove(), 300);
        });
    });

    // Estilos del tooltip
    const style = document.createElement('style');
    style.textContent = `
        .price-tooltip {
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%) translateY(-10px);
            background: linear-gradient(135deg, #a855f7 0%, #ec4899 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            transition: all 0.3s ease;
            pointer-events: none;
            margin-bottom: 8px;
        }

        .price-tooltip.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        .price-tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 6px solid transparent;
            border-top-color: #ec4899;
        }
    `;
    document.head.appendChild(style);
}

// ============================================
// INICIALIZACIÓN
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 Kickverse - Inicializando mejoras de conversión...');
    
    initStickyCTA();
    initCountdownTimer();
    initStockIndicator();
    initFAQ();
    initExitIntent();
    initScrollAnimations();
    initLazyLoading();
    initEventTracking();
    initSocialProofNotifications();
    initPriceComparison();
    
    console.log('✅ Todas las mejoras de conversión activas');
});

// ============================================
// PERFORMANCE MONITORING
// ============================================
if ('PerformanceObserver' in window) {
    const perfObserver = new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {
            if (entry.entryType === 'largest-contentful-paint') {
                const lcp = entry.startTime;
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'lcp', {
                        'event_category': 'performance',
                        'value': Math.round(lcp)
                    });
                }
            }
        }
    });

    try {
        perfObserver.observe({ entryTypes: ['largest-contentful-paint'] });
    } catch (e) {
        console.log('Performance Observer not supported');
    }
}
