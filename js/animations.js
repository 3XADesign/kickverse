/**
 * KICKVERSE - Sistema de Animaciones Interactivas
 * Animaciones elegantes y efectos visuales
 */

(function() {
    'use strict';

    // ============================================
    // CARRUSEL AUTOMÁTICO
    // ============================================
    
    class AutoCarousel {
        constructor(containerSelector, options = {}) {
            this.container = document.querySelector(containerSelector);
            if (!this.container) return;
            
            this.track = this.container.querySelector('.carousel-track');
            this.items = this.container.querySelectorAll('.carousel-item');
            this.prevBtn = this.container.querySelector('.carousel-prev');
            this.nextBtn = this.container.querySelector('.carousel-next');
            
            this.currentIndex = 0;
            this.itemsToShow = options.itemsToShow || 5;
            this.autoPlayInterval = options.autoPlayInterval || 3000;
            this.isAutoPlaying = options.autoPlay !== false;
            this.isPaused = false;
            this.autoPlayTimer = null;
            
            this.init();
        }
        
        init() {
            if (!this.track || this.items.length === 0) return;
            
            // Event listeners para botones
            if (this.prevBtn) {
                this.prevBtn.addEventListener('click', () => this.prev());
            }
            if (this.nextBtn) {
                this.nextBtn.addEventListener('click', () => this.next());
            }
            
            // Pausar al hacer hover
            this.container.addEventListener('mouseenter', () => this.pause());
            this.container.addEventListener('mouseleave', () => this.resume());
            
            // Responsive
            this.updateItemsToShow();
            window.addEventListener('resize', () => this.updateItemsToShow());
            
            // Iniciar autoplay
            if (this.isAutoPlaying) {
                this.startAutoPlay();
            }
            
            // Posición inicial
            this.updatePosition();
        }
        
        updateItemsToShow() {
            const width = window.innerWidth;
            if (width < 640) {
                this.itemsToShow = 2;
            } else if (width < 1024) {
                this.itemsToShow = 3;
            } else if (width < 1280) {
                this.itemsToShow = 4;
            } else {
                this.itemsToShow = 5;
            }
            this.updatePosition();
        }
        
        next() {
            const maxIndex = Math.max(0, this.items.length - this.itemsToShow);
            this.currentIndex = (this.currentIndex + 1) % (this.items.length);
            if (this.currentIndex > maxIndex) {
                this.currentIndex = 0;
            }
            this.updatePosition();
            this.resetAutoPlay();
        }
        
        prev() {
            const maxIndex = Math.max(0, this.items.length - this.itemsToShow);
            this.currentIndex--;
            if (this.currentIndex < 0) {
                this.currentIndex = maxIndex;
            }
            this.updatePosition();
            this.resetAutoPlay();
        }
        
        updatePosition() {
            if (!this.track) return;
            
            const itemWidth = 100 / this.itemsToShow;
            const offset = -(this.currentIndex * itemWidth);
            this.track.style.transform = `translateX(${offset}%)`;
        }
        
        startAutoPlay() {
            if (!this.isAutoPlaying) return;
            this.autoPlayTimer = setInterval(() => {
                if (!this.isPaused) {
                    this.next();
                }
            }, this.autoPlayInterval);
        }
        
        stopAutoPlay() {
            if (this.autoPlayTimer) {
                clearInterval(this.autoPlayTimer);
                this.autoPlayTimer = null;
            }
        }
        
        resetAutoPlay() {
            this.stopAutoPlay();
            if (this.isAutoPlaying && !this.isPaused) {
                this.startAutoPlay();
            }
        }
        
        pause() {
            this.isPaused = true;
        }
        
        resume() {
            this.isPaused = false;
        }
    }
    
    // ============================================
    // ESCUDOS FLOTANTES DE FONDO
    // ============================================
    
    class FloatingClubs {
        constructor() {
            this.clubs = [
                'laliga_realmadrid.png',
                'laliga_barcelona.png',
                'premier_manchestercity.png',
                'premier_liverpool.png',
                'seriea_juventus.png',
                'bundesliga_bayernmunchen.png',
                'laliga_atlmadrid.png',
                'premier_arsenal.png',
                'seriea_inter.png',
                'bundesliga_borussiadortmund.png'
            ];
            
            this.init();
        }
        
        init() {
            // Crear contenedor de fondo
            const container = document.createElement('div');
            container.className = 'clubs-background';
            document.body.prepend(container);
            
            // Crear escudos flotantes (solo 6 para no saturar)
            for (let i = 0; i < 6; i++) {
                const img = document.createElement('img');
                img.className = 'club-logo-float';
                img.src = `./img/clubs/${this.clubs[i]}`;
                img.alt = 'Club Logo';
                img.style.animationDuration = `${15 + Math.random() * 10}s`;
                img.style.animationDelay = `${i * 2}s`;
                container.appendChild(img);
            }
        }
    }
    
    // ============================================
    // SCROLL REVEAL - Aparecer al hacer scroll
    // ============================================
    
    class ScrollReveal {
        constructor() {
            this.elements = document.querySelectorAll('.scroll-reveal');
            this.init();
        }
        
        init() {
            // Observador de intersección
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });
            
            this.elements.forEach(el => observer.observe(el));
        }
    }
    
    // ============================================
    // PARALLAX SUAVE
    // ============================================
    
    class ParallaxEffect {
        constructor() {
            this.init();
        }
        
        init() {
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                
                // Parallax para hero
                const hero = document.querySelector('.hero');
                if (hero) {
                    hero.style.transform = `translateY(${scrolled * 0.3}px)`;
                }
                
                // Parallax para escudos flotantes
                const clubs = document.querySelectorAll('.club-logo-float');
                clubs.forEach((club, index) => {
                    const speed = 0.1 + (index * 0.05);
                    club.style.transform = `translateY(${scrolled * speed}px)`;
                });
            });
        }
    }
    
    // ============================================
    // EFECTOS DE HOVER PARA CARDS
    // ============================================
    
    class CardEffects {
        constructor() {
            this.init();
        }
        
        init() {
            // Efecto 3D en cards de planes
            const planCards = document.querySelectorAll('.plan-card');
            planCards.forEach(card => {
                card.addEventListener('mousemove', (e) => {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    
                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;
                    
                    const rotateX = (y - centerY) / 10;
                    const rotateY = (centerX - x) / 10;
                    
                    card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-10px)`;
                });
                
                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateY(0)';
                });
            });
            
            // Efecto similar para feature cards
            const featureCards = document.querySelectorAll('.feature-card');
            featureCards.forEach(card => {
                card.addEventListener('mousemove', (e) => {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    
                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;
                    
                    const rotateX = (y - centerY) / 15;
                    const rotateY = (centerX - x) / 15;
                    
                    card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-8px)`;
                });
                
                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateY(0)';
                });
            });
        }
    }
    
    // ============================================
    // CONTADOR ANIMADO
    // ============================================
    
    class AnimatedCounter {
        constructor(selector) {
            this.elements = document.querySelectorAll(selector);
            this.init();
        }
        
        init() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !entry.target.dataset.animated) {
                        this.animateCounter(entry.target);
                        entry.target.dataset.animated = 'true';
                    }
                });
            }, { threshold: 0.5 });
            
            this.elements.forEach(el => observer.observe(el));
        }
        
        animateCounter(element) {
            const target = parseInt(element.textContent.replace(/[^0-9]/g, ''));
            const duration = 2000;
            const increment = target / (duration / 16);
            let current = 0;
            
            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    element.textContent = Math.floor(current) + (element.textContent.includes('+') ? '+' : '');
                    requestAnimationFrame(updateCounter);
                } else {
                    element.textContent = target + (element.textContent.includes('+') ? '+' : '');
                }
            };
            
            updateCounter();
        }
    }
    
    // ============================================
    // PARTÍCULAS MÁGICAS AL HACER CLIC
    // ============================================
    
    class MagicParticles {
        constructor() {
            this.init();
        }
        
        init() {
            document.addEventListener('click', (e) => {
                // Solo en elementos interactivos
                if (e.target.closest('button, a, .plan-card, .feature-card')) {
                    this.createParticles(e.clientX, e.clientY);
                }
            });
        }
        
        createParticles(x, y) {
            for (let i = 0; i < 6; i++) {
                const particle = document.createElement('div');
                particle.style.position = 'fixed';
                particle.style.left = x + 'px';
                particle.style.top = y + 'px';
                particle.style.width = '4px';
                particle.style.height = '4px';
                particle.style.borderRadius = '50%';
                particle.style.background = `linear-gradient(135deg, #BA51DD, #DC4CB0)`;
                particle.style.pointerEvents = 'none';
                particle.style.zIndex = '9999';
                particle.style.opacity = '1';
                
                document.body.appendChild(particle);
                
                const angle = (Math.PI * 2 * i) / 6;
                const velocity = 2 + Math.random() * 2;
                const tx = Math.cos(angle) * velocity * 30;
                const ty = Math.sin(angle) * velocity * 30;
                
                particle.animate([
                    { transform: 'translate(0, 0) scale(1)', opacity: 1 },
                    { transform: `translate(${tx}px, ${ty}px) scale(0)`, opacity: 0 }
                ], {
                    duration: 600,
                    easing: 'cubic-bezier(0, .9, .57, 1)'
                }).onfinish = () => particle.remove();
            }
        }
    }
    
    // ============================================
    // INICIALIZACIÓN
    // ============================================
    
    function init() {
        // Esperar a que el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAll);
        } else {
            initAll();
        }
    }
    
    function initAll() {
        // Carrusel automático
        new AutoCarousel('.carousel', {
            itemsToShow: 5,
            autoPlay: true,
            autoPlayInterval: 3000
        });
        
        // Escudos flotantes de fondo
        new FloatingClubs();
        
        // Scroll reveal
        new ScrollReveal();
        
        // Parallax
        new ParallaxEffect();
        
        // Efectos de cards
        new CardEffects();
        
        // Contadores animados
        new AnimatedCounter('.hero-stat-number');
        
        // Partículas mágicas
        new MagicParticles();
        
        // Añadir clase scroll-reveal a elementos que deben aparecer
        const elementsToReveal = [
            '.plans-section',
            '.features-section',
            '.faq-section'
        ];
        
        elementsToReveal.forEach(selector => {
            const el = document.querySelector(selector);
            if (el && !el.classList.contains('scroll-reveal')) {
                el.classList.add('scroll-reveal');
            }
        });
    }
    
    // Iniciar
    init();
    
    // Exponer API global (opcional)
    window.KickverseAnimations = {
        AutoCarousel,
        FloatingClubs,
        ScrollReveal,
        ParallaxEffect
    };
    
})();
