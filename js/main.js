// ============================================
// MAIN.JS - Lógica principal de Kickverse
// ============================================

// Variables globales
let currentStep = 1;
let formData = {
    liga: '',
    ligaDisplay: '',
    equipo: '',
    equipacion: '',
    version: '', // 'fan' o 'player'
    talla: '',
    parches: false,
    personalizar: false,
    nombre: '',
    dorsal: ''
};
let cartItems = [];
let currentProductForCart = null; // Producto actual para añadir al carrito

// ============================================
// BASE DE DATOS DE CAMISETAS DISPONIBLES
// ============================================

const camisetasDisponibles = [
    // LA LIGA
    { liga: 'laliga', equipo: 'Alavés', slug: 'alaves', local: true, visitante: true },
    { liga: 'laliga', equipo: 'Athletic Bilbao', slug: 'bilbao', local: true, visitante: true },
    { liga: 'laliga', equipo: 'Atlético Madrid', slug: 'atletico', local: true, visitante: true },
    { liga: 'laliga', equipo: 'Barcelona', slug: 'barcelona', local: true, visitante: true },
    { liga: 'laliga', equipo: 'Real Betis', slug: 'betis', local: true, visitante: true },
    { liga: 'laliga', equipo: 'Celta de Vigo', slug: 'celta', local: true, visitante: true },
    { liga: 'laliga', equipo: 'Elche', slug: 'elche', local: true, visitante: true },
    { liga: 'laliga', equipo: 'Espanyol', slug: 'espanyol', local: true, visitante: true },
    { liga: 'laliga', equipo: 'Getafe', slug: 'getafe', local: true, visitante: true },
    { liga: 'laliga', equipo: 'Girona', slug: 'girona', local: true, visitante: true },
    { liga: 'laliga', equipo: 'Levante', slug: 'levante', local: true, visitante: true },
    { liga: 'laliga', equipo: 'Mallorca', slug: 'mallorca', local: true, visitante: true },
    { liga: 'laliga', equipo: 'Osasuna', slug: 'osasuna', local: true, visitante: false },
    { liga: 'laliga', equipo: 'Rayo Vallecano', slug: 'rayo', local: true, visitante: true },
    { liga: 'laliga', equipo: 'Real Madrid', slug: 'madrid', local: true, visitante: true },
    { liga: 'laliga', equipo: 'Real Oviedo', slug: 'oviedo', local: true, visitante: true },
    { liga: 'laliga', equipo: 'Real Sociedad', slug: 'realsociedad', local: true, visitante: true },
    { liga: 'laliga', equipo: 'Sevilla', slug: 'sevilla', local: true, visitante: true },
    { liga: 'laliga', equipo: 'Valencia', slug: 'valencia', local: true, visitante: true },
    { liga: 'laliga', equipo: 'Villarreal', slug: 'villareal', local: true, visitante: true },
    
    // PREMIER LEAGUE
    { liga: 'premier', equipo: 'Arsenal', slug: 'arsenal', local: true, visitante: true },
    { liga: 'premier', equipo: 'Aston Villa', slug: 'astonvilla', local: true, visitante: true },
    { liga: 'premier', equipo: 'Chelsea', slug: 'chelsea', local: true, visitante: true },
    { liga: 'premier', equipo: 'Crystal Palace', slug: 'crystalpalace', local: true, visitante: true },
    { liga: 'premier', equipo: 'Everton', slug: 'everton', local: true, visitante: true },
    { liga: 'premier', equipo: 'Liverpool', slug: 'liverpool', local: true, visitante: true },
    { liga: 'premier', equipo: 'Manchester City', slug: 'manchestercity', local: true, visitante: true },
    { liga: 'premier', equipo: 'Manchester United', slug: 'manchesterunited', local: true, visitante: true },
    { liga: 'premier', equipo: 'Newcastle', slug: 'newscastle', local: true, visitante: true },
    { liga: 'premier', equipo: 'Tottenham', slug: 'tottenham', local: true, visitante: true },
    { liga: 'premier', equipo: 'West Ham', slug: 'westham', local: true, visitante: true },
    
    // SERIE A
    { liga: 'seriea', equipo: 'Atalanta', slug: 'atalanta', local: true, visitante: true },
    { liga: 'seriea', equipo: 'Bologna', slug: 'bologna', local: true, visitante: true },
    { liga: 'seriea', equipo: 'Fiorentina', slug: 'fiorentina', local: true, visitante: true },
    { liga: 'seriea', equipo: 'Inter', slug: 'inter', local: true, visitante: true },
    { liga: 'seriea', equipo: 'Juventus', slug: 'juventus', local: true, visitante: true },
    { liga: 'seriea', equipo: 'Lazio', slug: 'lazio', local: true, visitante: true },
    { liga: 'seriea', equipo: 'Milan', slug: 'milan', local: true, visitante: true },
    { liga: 'seriea', equipo: 'Napoli', slug: 'napoli', local: true, visitante: true },
    { liga: 'seriea', equipo: 'Roma', slug: 'roma', local: true, visitante: true },
    { liga: 'seriea', equipo: 'Torino', slug: 'torino', local: true, visitante: true },
    
    // BUNDESLIGA
    { liga: 'bundesliga', equipo: 'Augsburg', slug: 'Augsburg', local: true, visitante: true },
    { liga: 'bundesliga', equipo: 'Bayern München', slug: 'bayern', local: true, visitante: true },
    { liga: 'bundesliga', equipo: 'Borussia Dortmund', slug: 'dortmund', local: true, visitante: true },
    { liga: 'bundesliga', equipo: 'Eintracht Frankfurt', slug: 'Eintracht', local: true, visitante: true },
    { liga: 'bundesliga', equipo: 'SC Freiburg', slug: 'Freiburg', local: true, visitante: true },
    { liga: 'bundesliga', equipo: 'Hamburger SV', slug: 'Hamburger', local: true, visitante: true },
    { liga: 'bundesliga', equipo: 'Heidenheim', slug: 'Heidenheim', local: true, visitante: false },
    { liga: 'bundesliga', equipo: 'Hoffenheim', slug: 'Hoffenheim', local: true, visitante: true },
    { liga: 'bundesliga', equipo: 'FC Köln', slug: 'Köln', local: true, visitante: true },
    { liga: 'bundesliga', equipo: 'RB Leipzig', slug: 'Leipzig', local: true, visitante: true },
    { liga: 'bundesliga', equipo: 'Mainz 05', slug: 'Mainz05', local: true, visitante: true },
    { liga: 'bundesliga', equipo: 'Bayer Leverkusen', slug: 'leverkusen', local: true, visitante: true },
    { liga: 'bundesliga', equipo: 'Borussia Mönchengladbach', slug: 'Mönchengladbach', local: true, visitante: true },
    { liga: 'bundesliga', equipo: 'St. Pauli', slug: 'St.Pauli', local: true, visitante: true },
    { liga: 'bundesliga', equipo: 'VfB Stuttgart', slug: 'Stuttgart', local: true, visitante: true },
    { liga: 'bundesliga', equipo: 'Union Berlin', slug: 'UnionBerlin', local: true, visitante: true },
    { liga: 'bundesliga', equipo: 'Werder Bremen', slug: 'bremen', local: true, visitante: true },
    { liga: 'bundesliga', equipo: 'VfL Wolfsburg', slug: 'wolfburg', local: true, visitante: true },
    
    // LIGUE 1
    { liga: 'ligue1', equipo: 'Olympique Lyon', slug: 'lyon', local: true, visitante: true },
    { liga: 'ligue1', equipo: 'Olympique Marseille', slug: 'marsella', local: true, visitante: true },
    { liga: 'ligue1', equipo: 'AS Monaco', slug: 'monaco', local: true, visitante: true },
    { liga: 'ligue1', equipo: 'Paris Saint-Germain', slug: 'psg', local: true, visitante: true },
    
    // SELECCIONES
    { liga: 'selecciones', equipo: 'Argentina', slug: 'argentina', local: true, visitante: true },
    { liga: 'selecciones', equipo: 'Colombia', slug: 'colombia', local: true, visitante: true },
    { liga: 'selecciones', equipo: 'Japón', slug: 'japon', local: true, visitante: true },
    { liga: 'selecciones', equipo: 'Uruguay', slug: 'uruguay', local: true, visitante: true },
];

// ============================================
// INICIALIZACIÓN
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    initMobileMenu();
    initScrollEffects();
    initModals();
    initCart();
    
    // Inicializar formulario si existe
    if (document.getElementById('form-wizard')) {
        initFormWizard();
    }
    
    // Inicializar catálogo si existe
    if (document.getElementById('catalogo-grid')) {
        initCatalogo();
    }
    
    // Inicializar carrusel si existe
    if (document.getElementById('carousel-track')) {
        initCarousel();
    }
    
    // Cargar carrito desde localStorage
    loadCartFromStorage();
});

// ============================================
// MENÚ MÓVIL
// ============================================

function initMobileMenu() {
    const menuToggle = document.querySelector('.menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    const menuClose = document.querySelector('.mobile-menu-close');
    const menuOverlay = document.querySelector('.menu-overlay');
    const mobileLinks = document.querySelectorAll('.mobile-menu-link');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.add('active');
            menuOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }
    
    if (menuClose) {
        menuClose.addEventListener('click', closeMobileMenu);
    }
    
    if (menuOverlay) {
        menuOverlay.addEventListener('click', closeMobileMenu);
    }
    
    mobileLinks.forEach(link => {
        link.addEventListener('click', () => {
            setTimeout(closeMobileMenu, 300);
        });
    });
}

function closeMobileMenu() {
    const mobileMenu = document.querySelector('.mobile-menu');
    const menuOverlay = document.querySelector('.menu-overlay');
    
    if (mobileMenu) mobileMenu.classList.remove('active');
    if (menuOverlay) menuOverlay.classList.remove('active');
    document.body.style.overflow = '';
}

// ============================================
// EFECTOS DE SCROLL
// ============================================

function initScrollEffects() {
    const header = document.querySelector('.header');
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
}

// ============================================
// MODALES
// ============================================

function initModals() {
    const modals = document.querySelectorAll('.modal');
    
    modals.forEach(modal => {
        const closeBtn = modal.querySelector('.modal-close');
        
        if (closeBtn) {
            closeBtn.addEventListener('click', () => closeModal(modal));
        }
        
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal(modal);
            }
        });
    });
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modal) {
    if (typeof modal === 'string') {
        modal = document.getElementById(modal);
    }
    
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// ============================================
// WHATSAPP - COMPRA DIRECTA
// ============================================

function comprarWhatsApp(equipo, equipacion, precio) {
    const mensaje = `Hola Kickverse!

Quiero comprar:
Equipo: ${equipo}
Equipacion: ${equipacion}
Precio: ${precio}

¿Cuales son los siguientes pasos?`;
    
    // Abrir modal de dirección en lugar de ir directo a WhatsApp
    abrirModalDireccion(mensaje, 'directo');
}

function generarMensajeWhatsApp(data) {
    let mensaje = `Hola Kickverse!

Quiero realizar un pedido:

DETALLES DEL PEDIDO:
Liga: ${data.liga}
Equipo: ${data.equipo}
Equipacion: ${data.equipacion}
Talla: ${data.talla}
Parches: ${data.parches ? 'Si' : 'No'}`;

    if (data.personalizar) {
        mensaje += `\nPersonalizacion:
   - Nombre: ${data.nombre}
   - Dorsal: ${data.dorsal}`;
    }
    
    mensaje += `\n\n¿Cual es el precio final y los pasos a seguir?`;
    
    // Abrir modal de dirección en lugar de ir directo a WhatsApp
    abrirModalDireccion(mensaje, 'directo');
}

// ============================================
// FORMULARIO DINÁMICO - WIZARD
// ============================================

function initFormWizard() {
    loadStepContent(1);
    updateProgressBar();
}

function loadStepContent(step) {
    currentStep = step;
    const stepContainer = document.getElementById('step-content');
    
    if (!stepContainer) {
        console.error('❌ No se encontró step-content');
        return;
    }
    
    let content = '';
    
    console.log('🔄 Cargando paso:', step, 'Version:', formData.version);
    
    switch(step) {
        case 1:
            content = getStep1Content();
            break;
        case 2:
            content = getStep2Content();
            break;
        case 3:
            content = getStep3Content();
            break;
        case 4:
            content = getStep4Content(); // Versión FAN/PLAYER
            break;
        case 5:
            content = getStep5Content(); // Talla
            break;
        case 6:
            content = getStep6Content(); // Parches
            break;
        case 7:
            content = getStep7Content(); // Personalización
            console.log('✅ HTML generado para paso 7, longitud:', content.length);
            break;
        case 8:
            // Solo FAN con personalización llega aquí
            if (formData.version === 'fan' && formData.personalizar) {
                content = getStep8Content();
            } else {
                content = '<p>Error: Paso 8 no disponible</p>';
            }
            break;
    }
    
    stepContainer.innerHTML = content;

    if (!stepContainer.innerHTML.trim()) {
        console.warn('⚠️ El contenido del paso quedó vacío. Renderizando mensaje de diagnóstico.');
        stepContainer.innerHTML = `
            <div class="form-step">
                <h2 style="color: #fff;">No se pudo cargar el paso ${step}</h2>
                <p class="text-secondary">Revisa la consola del navegador para más detalles.</p>
            </div>
        `;
    }
    console.log('📝 HTML insertado en step-content:', stepContainer.innerHTML.substring(0, 120));
    updateProgressBar();
    
    // Scroll to top suavemente
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function getStep1Content() {
    return `
        <div class="form-step">
            <h2>Paso 1: Elige tu Liga</h2>
            <p class="text-secondary mb-lg">Selecciona la liga de tu equipo favorito</p>
            
            <div class="grid grid-3">
                <div class="option-card" onclick="selectLiga('La Liga')">
                    <img src="./img/leagues/laliga.svg" alt="La Liga" style="width: 80px; height: 80px; margin: 0 auto var(--spacing-md) auto; object-fit: contain; display: block;">
                    <h3>La Liga</h3>
                    <p>España</p>
                </div>
                <div class="option-card" onclick="selectLiga('Premier League')">
                    <img src="./img/leagues/premier.svg" alt="Premier League" style="width: 80px; height: 80px; margin: 0 auto var(--spacing-md) auto; object-fit: contain; display: block;">
                    <h3>Premier League</h3>
                    <p>Inglaterra</p>
                </div>
                <div class="option-card" onclick="selectLiga('Serie A')">
                    <img src="./img/leagues/seriea.svg" alt="Serie A" style="width: 80px; height: 80px; margin: 0 auto var(--spacing-md) auto; object-fit: contain; display: block;">
                    <h3>Serie A</h3>
                    <p>Italia</p>
                </div>
                <div class="option-card" onclick="selectLiga('Bundesliga')">
                    <img src="./img/leagues/bundesliga.svg" alt="Bundesliga" style="width: 80px; height: 80px; margin: 0 auto var(--spacing-md) auto; object-fit: contain; display: block;">
                    <h3>Bundesliga</h3>
                    <p>Alemania</p>
                </div>
                <div class="option-card" onclick="selectLiga('Ligue 1')">
                    <img src="./img/leagues/ligue1.svg" alt="Ligue 1" style="width: 80px; height: 80px; margin: 0 auto var(--spacing-md) auto; object-fit: contain; display: block;">
                    <h3>Ligue 1</h3>
                    <p>Francia</p>
                </div>
                <div class="option-card" onclick="selectLiga('Selecciones')">
                    <i class="fas fa-flag" style="font-size: 3rem; color: var(--color-accent-purple); margin-bottom: var(--spacing-md);"></i>
                    <h3>Selecciones</h3>
                    <p>Nacionales</p>
                </div>
            </div>
        </div>
    `;
}

function selectLiga(liga) {
    // Guardar tanto el nombre de display como la versión normalizada
    formData.ligaDisplay = liga; // Ej: "Premier League"
    formData.liga = normalizarNombreLiga(liga); // Ej: "premier"
    nextStep();
}

function getStep2Content() {
    const equipos = getEquiposPorLiga(formData.ligaDisplay || formData.liga);
    
    let html = `
        <div class="form-step">
            <h2>Paso 2: Elige tu Equipo</h2>
            <p class="text-secondary mb-md">Liga: ${formData.ligaDisplay || formData.liga}</p>
            
            <div class="grid grid-3">
    `;
    
    equipos.forEach(equipo => {
        const logoPath = getEquipoLogo(formData.ligaDisplay || formData.liga, equipo.nombre);
        html += `
            <div class="option-card" onclick="selectEquipo('${equipo.nombre}')">
                <img src="${logoPath}" 
                     alt="${equipo.nombre}" 
                     style="width: 70px; height: 70px; margin: 0 auto var(--spacing-md) auto; object-fit: contain; display: block;"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <i class="fas fa-shield-halved" style="display: none; font-size: 3rem; color: var(--color-accent-purple); margin: 0 auto var(--spacing-md) auto;"></i>
                <h3>${equipo.display}</h3>
            </div>
        `;
    });
    
    html += `
            </div>
            <button class="btn btn-secondary mt-lg" onclick="previousStep()">
                <i class="fas fa-arrow-left"></i> Volver
            </button>
        </div>
    `;
    
    return html;
}

function selectEquipo(equipo) {
    formData.equipo = equipo;
    nextStep();
}

function getStep3Content() {
    return `
        <div class="form-step">
            <h2>Paso 3: Elige la Equipación</h2>
            <p class="text-secondary mb-md">Equipo: ${formData.equipo}</p>
            
            <div class="grid grid-2">
                <div class="option-card" onclick="selectEquipacion('Local')">
                    <i class="fas fa-home"></i>
                    <h3>Primera Equipación</h3>
                    <p>Local</p>
                </div>
                <div class="option-card" onclick="selectEquipacion('Visitante')">
                    <i class="fas fa-plane"></i>
                    <h3>Segunda Equipación</h3>
                    <p>Visitante</p>
                </div>
            </div>
            
            <button class="btn btn-secondary mt-lg" onclick="previousStep()">
                <i class="fas fa-arrow-left"></i> Volver
            </button>
        </div>
    `;
}

function selectEquipacion(equipacion) {
    formData.equipacion = equipacion;
    nextStep();
}

function getStep4Content() {
    return `
        <div class="form-step">
            <h2>Paso 4: Elige la Versión</h2>
            <p class="text-secondary mb-lg">Selecciona el tipo de camiseta que prefieres</p>
            
            <div class="grid grid-2">
                <div class="option-card version-card" onclick="selectVersion('fan')">
                    <div class="version-badge">MÁS POPULAR</div>
                    <i class="fas fa-users"></i>
                    <h3>Versión FAN</h3>
                    <p class="version-description">Ideal para uso casual y aficionados. Mismo diseño oficial, fabricación standard.</p>
                    <ul class="version-features">
                        <li><i class="fas fa-check"></i> Diseño oficial del equipo</li>
                        <li><i class="fas fa-check"></i> Telas ligeras y cómodas</li>
                        <li><i class="fas fa-check"></i> Perfecto para el día a día</li>
                    </ul>
                    <div class="version-price">
                        <span class="price-label">Desde</span>
                        <span class="price-value">24,99€</span>
                    </div>
                    <a href="tallas.html" class="version-info-link" target="_blank" onclick="event.stopPropagation()">
                        <i class="fas fa-info-circle"></i> Ver guía de tallas
                    </a>
                </div>
                
                <div class="option-card version-card version-player" onclick="selectVersion('player')">
                    <div class="version-badge version-badge-premium">CALIDAD PREMIUM</div>
                    <i class="fas fa-trophy"></i>
                    <h3>Versión PLAYER</h3>
                    <p class="version-description">Calidad profesional idéntica a la que usan los jugadores en el campo.</p>
                    <ul class="version-features">
                        <li><i class="fas fa-check"></i> Calidad profesional</li>
                        <li><i class="fas fa-check"></i> Tecnología Dri-FIT</li>
                        <li><i class="fas fa-check"></i> Ajuste ergonómico</li>
                        <li><i class="fas fa-check"></i> <strong>Parches y personalización INCLUIDOS</strong></li>
                    </ul>
                    <div class="version-price version-price-premium">
                        <span class="price-label">Precio fijo</span>
                        <span class="price-value">34,99€</span>
                        <span class="price-note">Todo incluido</span>
                    </div>
                    <a href="tallas.html" class="version-info-link" target="_blank" onclick="event.stopPropagation()">
                        <i class="fas fa-info-circle"></i> Ver guía de tallas
                    </a>
                </div>
            </div>
            
            <button class="btn btn-secondary mt-lg" onclick="previousStep()">
                <i class="fas fa-arrow-left"></i> Volver
            </button>
        </div>
    `;
}

function selectVersion(version) {
    formData.version = version;
    
    // Si es PLAYER, activar automáticamente parches y personalización
    if (version === 'player') {
        formData.parches = true;
        formData.personalizar = true;
        formData.nombre = '';
        formData.dorsal = '';
    } else {
        // Resetear valores adicionales para FAN
        formData.parches = false;
        formData.personalizar = false;
        formData.nombre = '';
        formData.dorsal = '';
    }
    
    nextStep();
}

function getStep5Content() {
    const tallas = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
    
    let html = `
        <div class="form-step">
            <h2>Paso 5: Elige tu Talla</h2>
            <p class="text-secondary mb-md">Versión: <strong>${formData.version === 'fan' ? 'FAN' : 'PLAYER'}</strong></p>
            <a href="tallas.html" target="_blank" class="tallas-guide-link">
                <i class="fas fa-info-circle"></i> Consultar guía de tallas
            </a>
            
            <div class="tallas-buttons">
    `;
    
    tallas.forEach(talla => {
        html += `
            <button class="talla-btn" onclick="selectTalla('${talla}')">
                ${talla}
            </button>
        `;
    });
    
    html += `
            </div>
            <button class="btn btn-secondary mt-lg" onclick="previousStep()">
                <i class="fas fa-arrow-left"></i> Volver
            </button>
        </div>
    `;
    
    return html;
}

function selectTalla(talla) {
    formData.talla = talla;
    nextStep();
}

function getStep6Content() {
    // Versión PLAYER: mostrar confirmación de que los parches están incluidos
    if (formData.version === 'player') {
        formData.parches = true;
        return `
            <div class="form-step">
                <h2>Paso 6: Parches y Detalles Premium</h2>
                <p class="text-secondary mb-lg">La versión PLAYER incluye todos los extras sin coste adicional.</p>
                
                <div class="card mb-lg">
                    <ul class="version-features">
                        <li><i class="fas fa-check"></i> Parches oficiales de liga incluidos</li>
                        <li><i class="fas fa-check"></i> Personalización incluida (nombre y dorsal)</li>
                        <li><i class="fas fa-check"></i> Acabados premium idénticos a los jugadores</li>
                    </ul>
                </div>
                
                <div class="btn-group">
                    <button class="btn btn-secondary" onclick="previousStep()">
                        <i class="fas fa-arrow-left"></i> Volver
                    </button>
                    <button class="btn btn-primary" onclick="nextStep()">
                        <i class="fas fa-arrow-right"></i> Continuar
                    </button>
                </div>
            </div>
        `;
    }
    
    return `
        <div class="form-step">
            <h2>Paso 6: ¿Quieres añadir parches?</h2>
            <p class="text-secondary mb-lg">Parches oficiales de liga (recomendado)</p>
            
            <div class="grid grid-2">
                <div class="option-card" onclick="selectParches(true)">
                    <i class="fas fa-check-circle"></i>
                    <h3>Sí, con parches</h3>
                    <p>+1,99€</p>
                </div>
                <div class="option-card" onclick="selectParches(false)">
                    <i class="fas fa-times-circle"></i>
                    <h3>No, sin parches</h3>
                    <p>Sin coste</p>
                </div>
            </div>
            
            <button class="btn btn-secondary mt-lg" onclick="previousStep()">
                <i class="fas fa-arrow-left"></i> Volver
            </button>
        </div>
    `;
}

function selectParches(conParches) {
    formData.parches = conParches;
    nextStep();
}

function getStep7Content() {
    // PASO 7 para PLAYER: Formulario de personalización (obligatorio)
    if (formData.version === 'player') {
        return `
            <div class="form-step">
                <h2>Paso 7: Personaliza tu Camiseta</h2>
                <p class="text-secondary mb-lg">✨ Personalización incluida en versión PLAYER</p>
                
                <div class="card mb-lg">
                    <div class="form-group mb-lg">
                        <label for="nombre-player" class="form-label">
                            <i class="fas fa-user"></i>
                            Nombre (máximo 12 caracteres) *
                        </label>
                        <input type="text" 
                               id="nombre-player" 
                               class="form-control"
                               placeholder="Ej: MESSI"
                               maxlength="12">
                    </div>
                    
                    <div class="form-group">
                        <label for="dorsal-player" class="form-label">
                            <i class="fas fa-hashtag"></i>
                            Dorsal (0-99) *
                        </label>
                        <input type="number" 
                               id="dorsal-player" 
                               class="form-control"
                               placeholder="Ej: 10"
                               min="0" 
                               max="99">
                    </div>
                </div>
                
                <div class="btn-group">
                    <button class="btn btn-secondary" onclick="previousStep()">
                        <i class="fas fa-arrow-left"></i> Volver
                    </button>
                    <button class="btn btn-primary" onclick="finalizarYAgregarAlCarrito()">
                        <i class="fas fa-shopping-cart"></i> Añadir al Carrito
                    </button>
                </div>
            </div>
        `;
    }
    
    // PASO 7 para FAN: Pregunta si quiere personalizar
    return `
        <div class="form-step">
            <h2>Paso 7: ¿Quieres personalizarla?</h2>
            <p class="text-secondary mb-lg">Añade nombre y dorsal a tu camiseta</p>
            
            <div class="grid grid-2">
                <div class="option-card" onclick="selectPersonalizacion(true)">
                    <i class="fas fa-edit"></i>
                    <h3>Sí, personalizar</h3>
                    <p>+2,99€</p>
                </div>
                <div class="option-card" onclick="selectPersonalizacion(false)">
                    <i class="fas fa-times"></i>
                    <h3>No, sin personalizar</h3>
                    <p>Sin coste</p>
                </div>
            </div>
            
            <button class="btn btn-secondary mt-lg" onclick="previousStep()">
                <i class="fas fa-arrow-left"></i> Volver
            </button>
        </div>
    `;
}

function selectPersonalizacion(personalizar) {
    formData.personalizar = personalizar;
    if (!personalizar) {
        formData.nombre = '';
        formData.dorsal = '';
    }
    
    if (personalizar) {
        // Si quiere personalizar, ir al paso 8 (formulario)
        nextStep();
    } else {
        // Si NO quiere personalizar, añadir directamente al carrito
        finalizarYAgregarAlCarrito();
    }
}

function getStep8Content() {
    // PASO 8 solo para FAN que eligió personalizar
    return `
        <div class="form-step">
            <h2>Paso 8: Datos de Personalización</h2>
            <p class="text-secondary mb-lg">Introduce el nombre y dorsal</p>
            
            <div class="card mb-lg">
                <div class="form-group mb-lg">
                    <label for="nombre-input" class="form-label">
                        <i class="fas fa-user"></i>
                        Nombre (máximo 12 caracteres) *
                    </label>
                    <input type="text" 
                           id="nombre-input" 
                           class="form-control"
                           placeholder="Ej: RODRÍGUEZ" 
                           maxlength="12"
                           value="${formData.nombre || ''}">
                </div>
                
                <div class="form-group">
                    <label for="dorsal-input" class="form-label">
                        <i class="fas fa-hashtag"></i>
                        Dorsal (0-99) *
                    </label>
                    <input type="number" 
                           id="dorsal-input" 
                           class="form-control"
                           placeholder="Ej: 10" 
                           min="0" 
                           max="99"
                           value="${formData.dorsal || ''}">
                </div>
            </div>
            
            <div class="btn-group">
                <button class="btn btn-secondary" onclick="previousStep()">
                    <i class="fas fa-arrow-left"></i> Volver
                </button>
                <button class="btn btn-primary" onclick="guardarYAgregarAlCarrito()">
                    <i class="fas fa-shopping-cart"></i> Añadir al Carrito
                </button>
            </div>
        </div>
    `;
}

function guardarYAgregarAlCarrito() {
    const nombre = document.getElementById('nombre-input').value.trim();
    const dorsal = document.getElementById('dorsal-input').value.trim();
    
    if (!nombre || !dorsal) {
        alert('Por favor, completa todos los campos de personalización');
        return;
    }
    
    formData.nombre = nombre.toUpperCase();
    formData.dorsal = dorsal;
    
    finalizarYAgregarAlCarrito();
}

function finalizarYAgregarAlCarrito() {
    console.log('🛒 Finalizando y agregando al carrito...');
    
    // Para PLAYER: capturar datos del formulario si existen
    if (formData.version === 'player') {
        const nombreInput = document.getElementById('nombre-player');
        const dorsalInput = document.getElementById('dorsal-player');
        
        if (nombreInput && dorsalInput) {
            const nombre = nombreInput.value.trim();
            const dorsal = dorsalInput.value.trim();
            
            if (!nombre || !dorsal) {
                alert('⚠️ Por favor, completa el nombre y el dorsal');
                return;
            }
            
            formData.nombre = nombre.toUpperCase();
            formData.dorsal = dorsal;
            console.log('✅ Datos PLAYER capturados:', formData.nombre, formData.dorsal);
        }
    }
    
    // Validar datos mínimos
    if (!formData.liga || !formData.equipo || !formData.equipacion || !formData.talla) {
        alert('⚠️ Faltan datos. Por favor, completa todos los pasos.');
        return;
    }
    
    // Calcular precio final
    let precioFinal = 0;
    if (formData.version === 'player') {
        precioFinal = 34.99; // Todo incluido
    } else {
        precioFinal = 24.99;
        if (formData.parches) precioFinal += 1.99;
        if (formData.personalizar) precioFinal += 2.99;
    }
    
    console.log('💰 Precio calculado:', precioFinal);
    
    // Construir objeto del item
    const equipoSlug = obtenerSlugEquipo(formData.liga, formData.equipo);
    const tipoEquipacion = formData.equipacion.toLowerCase().includes('primera') || formData.equipacion.toLowerCase().includes('local') ? 'local' : 'visitante';
    const imagenPath = `./img/camisetas/${formData.liga}_${equipoSlug}_${tipoEquipacion}.png`;
    
    const item = {
        liga: formData.ligaDisplay || formData.liga,
        equipo: formData.equipo,
        equipacion: formData.equipacion,
        version: formData.version,
        talla: formData.talla,
        parches: formData.parches,
        nombre: formData.nombre || '',
        dorsal: formData.dorsal || '',
        precio: precioFinal,
        imagen: imagenPath,
        nombreProducto: `${formData.equipo} - ${formData.equipacion} (${formData.version === 'player' ? 'PLAYER' : 'FAN'})`
    };
    
    console.log('📦 Item construido:', item);
    
    // Añadir al carrito
    addToCart(item);
    console.log('✅ Producto añadido al carrito');
    
    // Resetear formulario
    resetForm();
    
    // Mostrar mensaje de éxito
    alert('✅ Producto añadido al carrito correctamente');
    
    // Abrir carrito
    openCart();
}

function mostrarResumen() {
    console.log('🎯 mostrarResumen() iniciado');
    
    const stepContainer = document.getElementById('step-content');
    console.log('📦 stepContainer encontrado:', !!stepContainer);
    console.log('📊 formData completo:', JSON.stringify(formData, null, 2));
    
    // Calcular precio según versión
    let precioBase = 0;
    let precioParches = 0;
    let precioPersonalizacion = 0;
    let precioTotal = 0;
    
    if (formData.version === 'player') {
        // Versión PLAYER: precio fijo de 34,99€ (todo incluido)
        precioBase = 34.99;
        precioTotal = 34.99;
    } else {
        // Versión FAN: 24,99€ + extras
        precioBase = 24.99;
        precioParches = formData.parches ? 1.99 : 0;
        precioPersonalizacion = formData.personalizar ? 2.99 : 0;
        precioTotal = precioBase + precioParches + precioPersonalizacion;
    }
    
    console.log('Precios calculados:', { precioBase, precioParches, precioPersonalizacion, precioTotal });
    
    // Validar que tenemos los datos necesarios
    if (!formData.liga || !formData.equipo || !formData.equipacion) {
        console.error('❌ Faltan datos en formData:', formData);
        alert('Error: Faltan datos del pedido. Por favor, completa todos los pasos.');
        return;
    }
    
    // Obtener la imagen de la camiseta
    // formData.liga ya está normalizada desde selectLiga()
    const equipoSlug = obtenerSlugEquipo(formData.liga, formData.equipo);
    
    if (!equipoSlug) {
        console.error('❌ No se pudo obtener el slug del equipo:', formData.equipo);
    }
    
    const tipoEquipacion = formData.equipacion.toLowerCase().includes('primera') || formData.equipacion.toLowerCase().includes('local') ? 'local' : 'visitante';
    const imagenPath = `./img/camisetas/${formData.liga}_${equipoSlug}_${tipoEquipacion}.png`;
    
    // Debug: mostrar en consola
    console.log('✅ Datos para imagen:', {
        ligaDisplay: formData.ligaDisplay,
        ligaNormalizada: formData.liga,
        equipo: formData.equipo,
        equipoSlug,
        tipoEquipacion,
        imagenPath
    });
    
    let html = `
        <div class="form-step">
            <h2>Resumen de tu Pedido</h2>
            <p class="text-secondary mb-lg">Revisa los detalles antes de continuar</p>
            
            <div class="resumen-container">
                <div class="resumen-preview">
                    <div class="resumen-image-container">
                        <img src="${imagenPath}" 
                             alt="${formData.equipo} - ${formData.equipacion}" 
                             class="resumen-camiseta-image"
                             onerror="this.src='./img/hero-jersey.png'">`;
    
    if (formData.personalizar) {
        html += `
                        <div class="resumen-personalizacion">
                            <span class="resumen-nombre">${formData.nombre || ''}</span>
                            <span class="resumen-dorsal">${formData.dorsal || ''}</span>
                        </div>`;
    }
    
    html += `
                    </div>
                    
                    <div class="resumen-version-badge ${formData.version}">
                        ${formData.version === 'player' ? 'PLAYER' : 'FAN'}
                    </div>
                </div>
                
                <div class="resumen-details">
                    <div class="card">
                        <div class="summary-grid">
                            <div class="summary-item-compact">
                                <span class="summary-label">Liga:</span>
                                <span class="summary-value">${formData.ligaDisplay || formData.liga || 'N/A'}</span>
                            </div>
                            <div class="summary-item-compact">
                                <span class="summary-label">Equipo:</span>
                                <span class="summary-value">${formData.equipo || 'N/A'}</span>
                            </div>
                            <div class="summary-item-compact">
                                <span class="summary-label">Equipación:</span>
                                <span class="summary-value">${formData.equipacion || 'N/A'}</span>
                            </div>
                            <div class="summary-item-compact">
                                <span class="summary-label">Talla:</span>
                                <span class="summary-value">${formData.talla || 'N/A'}</span>
                            </div>
                        </div>
                        
                        <div class="summary-divider"></div>
                        
                        <div class="summary-extras">
                            <div class="summary-extra-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>Parches: ${formData.parches ? (formData.version === 'player' ? 'Incluido' : '+1,99€') : 'No'}</span>
                            </div>`;
    
    if (formData.personalizar) {
        const precioPersonalizacionTexto = formData.version === 'player' ? 'Incluido' : '+2,99€';
        html += `
                            <div class="summary-extra-item">
                                <i class="fas fa-font"></i>
                                <span>${formData.nombre || ''} ${formData.dorsal || ''} (${precioPersonalizacionTexto})</span>
                            </div>`;
    }
    
    html += `
                        </div>
                        
                        <div class="summary-total">
                            <span class="summary-total-label">TOTAL:</span>
                            <span class="summary-total-value">${precioTotal.toFixed(2)}€</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-promo mb-lg">
                <p class="modal-promo-text">
                    <i class="fas fa-gift"></i> ¡Añade 2 más y la 3ª es GRATIS!
                </p>
            </div>
            
            <div class="flex flex-column gap-md">
                <button class="btn btn-whatsapp btn-lg" onclick="finalizarPedidoWhatsApp()">
                    <i class="fab fa-whatsapp"></i>
                    Finalizar Pedido por WhatsApp
                </button>
                <button class="btn btn-secondary" onclick="resetForm()">
                    <i class="fas fa-redo"></i>
                    Añadir Otra Camiseta
                </button>
                <button class="btn btn-outline" onclick="loadStepContent(6)">
                    <i class="fas fa-arrow-left"></i>
                    Modificar Pedido
                </button>
            </div>
        </div>
    `;
    
    console.log('stepContainer:', stepContainer);
    console.log('HTML length:', html.length);
    console.log('HTML preview:', html.substring(0, 200));
    
    if (!stepContainer) {
        console.error('❌ stepContainer no encontrado!');
        alert('Error: No se encontró el contenedor del formulario');
        return;
    }
    
    try {
        stepContainer.innerHTML = html;
        console.log('✅ HTML insertado correctamente');
        console.log('📏 Contenido innerHTML length:', stepContainer.innerHTML.length);
        
        currentStep = 8;
        updateProgressBar();
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch (error) {
        console.error('❌ Error al insertar HTML:', error);
        alert('Error al mostrar el resumen: ' + error.message);
    }
}

function finalizarPedidoWhatsApp() {
    // Primero añadir el pedido actual al carrito si no está ya
    if (formData.equipo && !cartItems.some(item => 
        item.equipo === formData.equipo && 
        item.equipacion === formData.equipacion &&
        item.talla === formData.talla &&
        item.nombre === formData.nombre &&
        item.dorsal === formData.dorsal
    )) {
        // Calcular precio según versión
        let precioFinal = 0;
        if (formData.version === 'player') {
            precioFinal = 34.99; // Precio fijo PLAYER
        } else {
            precioFinal = 24.99; // Base FAN
            if (formData.parches) precioFinal += 1.99;
            if (formData.personalizar) precioFinal += 2.99;
        }
        
        const producto = {
            liga: formData.liga,
            equipo: formData.equipo,
            equipacion: formData.equipacion,
            version: formData.version,
            talla: formData.talla,
            parches: formData.parches,
            personalizar: formData.personalizar,
            nombre: formData.nombre,
            dorsal: formData.dorsal,
            precio: precioFinal,
            nombreProducto: `${formData.equipo} - ${formData.equipacion} (${formData.version === 'player' ? 'PLAYER' : 'FAN'})`
        };
        
        cartItems.push(producto);
        localStorage.setItem('kickverse_cart', JSON.stringify(cartItems));
        updateCartCount();
    }
    
    // Verificar si debe mostrar upsell (exactamente 2 camisetas)
    if (cartItems.length === 2 && !upsellActivado) {
        mostrarModalUpsell();
        return;
    }
    
    // Si no hay upsell o ya se vio, mostrar cross-sell y resumen
    mostrarCrossSellYResumen();
}

function normalizarNombreLiga(ligaNombre) {
    // Convertir el nombre de la liga del formulario al formato interno
    const mapeoLigas = {
        'La Liga': 'laliga',
        'Premier League': 'premier',
        'Serie A': 'seriea',
        'Bundesliga': 'bundesliga',
        'Ligue 1': 'ligue1',
        'Selecciones': 'selecciones'
    };
    return mapeoLigas[ligaNombre] || ligaNombre.toLowerCase().replace(/\s+/g, '');
}

function obtenerSlugEquipo(liga, equipo) {
    // Primero buscar coincidencia exacta
    let camiseta = camisetasDisponibles.find(c => 
        c.liga === liga && c.equipo === equipo
    );
    
    // Si no encuentra, buscar coincidencia parcial (para casos como "FC Barcelona" vs "Barcelona")
    if (!camiseta) {
        const equipoLimpio = equipo.replace(/^(FC|CF|RCD|CA|SSC|AS|AC)\s+/i, '').trim();
        camiseta = camisetasDisponibles.find(c => 
            c.liga === liga && 
            (c.equipo.includes(equipoLimpio) || equipoLimpio.includes(c.equipo))
        );
    }
    
    // Si aún no encuentra, intentar con los equipos del formulario
    if (!camiseta) {
        // Buscar en getEquiposPorLiga usando el nombre del equipo directamente
        // getEquiposPorLiga ya devuelve los slugs correctos
        const mapeoLigasInverso = {
            'laliga': 'La Liga',
            'premier': 'Premier League',
            'seriea': 'Serie A',
            'bundesliga': 'Bundesliga',
            'ligue1': 'Ligue 1',
            'selecciones': 'Selecciones'
        };
        const ligaDisplay = mapeoLigasInverso[liga] || liga;
        const equiposLiga = getEquiposPorLiga(ligaDisplay);
        const equipoForm = equiposLiga.find(e => e.nombre === equipo || e.display === equipo);
        
        if (equipoForm) {
            // Buscar por el slug del formulario
            camiseta = camisetasDisponibles.find(c => 
                c.liga === liga && c.slug === equipoForm.slug
            );
        }
    }
    
    console.log('🔍 Búsqueda de slug:', {
        ligaBuscada: liga,
        equipoBuscado: equipo,
        camisetaEncontrada: camiseta,
        slugResultado: camiseta ? camiseta.slug : 'NO ENCONTRADO'
    });
    
    return camiseta ? camiseta.slug : equipo.toLowerCase().replace(/\s+/g, '');
}

function getTotalSteps() {
    return formData.version === 'player' ? 7 : 8;
}

function nextStep() {
    const totalSteps = getTotalSteps();
    const targetStep = Math.min(currentStep + 1, totalSteps);
    loadStepContent(targetStep);
}

function previousStep() {
    const targetStep = Math.max(currentStep - 1, 1);
    loadStepContent(targetStep);
}

function resetForm() {
    formData = {
        liga: '',
        ligaDisplay: '',
        equipo: '',
        equipacion: '',
        version: '',
        talla: '',
        parches: false,
        personalizar: false,
        nombre: '',
        dorsal: ''
    };
    loadStepContent(1);
}

function updateProgressBar() {
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    
    if (progressBar && progressText) {
        const totalSteps = getTotalSteps();
        const progress = (currentStep / totalSteps) * 100;
        progressBar.style.width = `${progress}%`;
        progressText.textContent = `Paso ${currentStep} de ${totalSteps}`;
    }
}

// ============================================
// DATOS - EQUIPOS POR LIGA
// ============================================

function getEquiposPorLiga(liga) {
    const equipos = {
        'La Liga': [
            { nombre: 'Real Madrid', display: 'Real Madrid', slug: 'madrid' },
            { nombre: 'Barcelona', display: 'FC Barcelona', slug: 'barcelona' },
            { nombre: 'Atlético Madrid', display: 'Atlético Madrid', slug: 'atletico' },
            { nombre: 'Sevilla', display: 'Sevilla FC', slug: 'sevilla' },
            { nombre: 'Valencia', display: 'Valencia CF', slug: 'valencia' },
            { nombre: 'Real Betis', display: 'Real Betis', slug: 'betis' },
            { nombre: 'Athletic Bilbao', display: 'Athletic Bilbao', slug: 'bilbao' },
            { nombre: 'Real Sociedad', display: 'Real Sociedad', slug: 'realsociedad' },
            { nombre: 'Villarreal', display: 'Villarreal CF', slug: 'villareal' },
            { nombre: 'Celta de Vigo', display: 'Celta de Vigo', slug: 'celta' },
            { nombre: 'Espanyol', display: 'RCD Espanyol', slug: 'espanyol' },
            { nombre: 'Getafe', display: 'Getafe CF', slug: 'getafe' },
            { nombre: 'Osasuna', display: 'CA Osasuna', slug: 'osasuna' },
            { nombre: 'Rayo Vallecano', display: 'Rayo Vallecano', slug: 'rayo' },
            { nombre: 'Alavés', display: 'Deportivo Alavés', slug: 'alaves' },
            { nombre: 'Mallorca', display: 'RCD Mallorca', slug: 'mallorca' },
            { nombre: 'Girona', display: 'Girona FC', slug: 'girona' },
            { nombre: 'Real Oviedo', display: 'Real Oviedo', slug: 'oviedo' },
            { nombre: 'Elche', display: 'Elche CF', slug: 'elche' },
            { nombre: 'Levante', display: 'Levante UD', slug: 'levante' }
        ],
        'Premier League': [
            { nombre: 'Manchester United', display: 'Manchester United', slug: 'manchesterunited' },
            { nombre: 'Manchester City', display: 'Manchester City', slug: 'manchestercity' },
            { nombre: 'Liverpool', display: 'Liverpool FC', slug: 'liverpool' },
            { nombre: 'Chelsea', display: 'Chelsea FC', slug: 'chelsea' },
            { nombre: 'Arsenal', display: 'Arsenal FC', slug: 'arsenal' },
            { nombre: 'Tottenham', display: 'Tottenham', slug: 'tottenham' },
            { nombre: 'Newcastle', display: 'Newcastle United', slug: 'newscastle' },
            { nombre: 'West Ham', display: 'West Ham', slug: 'westham' },
            { nombre: 'Aston Villa', display: 'Aston Villa', slug: 'astonvilla' },
            { nombre: 'Everton', display: 'Everton FC', slug: 'everton' },
            { nombre: 'Crystal Palace', display: 'Crystal Palace', slug: 'crystalpalace' }
        ],
        'Serie A': [
            { nombre: 'Juventus', display: 'Juventus', slug: 'juventus' },
            { nombre: 'Milan', display: 'AC Milan', slug: 'milan' },
            { nombre: 'Inter', display: 'Inter de Milán', slug: 'inter' },
            { nombre: 'Roma', display: 'AS Roma', slug: 'roma' },
            { nombre: 'Napoli', display: 'SSC Napoli', slug: 'napoli' },
            { nombre: 'Lazio', display: 'Lazio', slug: 'lazio' },
            { nombre: 'Atalanta', display: 'Atalanta', slug: 'atalanta' },
            { nombre: 'Fiorentina', display: 'Fiorentina', slug: 'fiorentina' },
            { nombre: 'Bologna', display: 'Bologna FC', slug: 'bologna' },
            { nombre: 'Torino', display: 'Torino FC', slug: 'torino' }
        ],
        'Bundesliga': [
            { nombre: 'Bayern München', display: 'Bayern Múnich', slug: 'bayern' },
            { nombre: 'Borussia Dortmund', display: 'Borussia Dortmund', slug: 'dortmund' },
            { nombre: 'RB Leipzig', display: 'RB Leipzig', slug: 'Leipzig' },
            { nombre: 'Bayer Leverkusen', display: 'Bayer Leverkusen', slug: 'leverkusen' },
            { nombre: 'Eintracht Frankfurt', display: 'Eintracht Frankfurt', slug: 'Eintracht' },
            { nombre: 'Werder Bremen', display: 'Werder Bremen', slug: 'bremen' },
            { nombre: 'VfL Wolfsburg', display: 'VfL Wolfsburg', slug: 'wolfburg' },
            { nombre: 'SC Freiburg', display: 'SC Freiburg', slug: 'Freiburg' },
            { nombre: 'VfB Stuttgart', display: 'VfB Stuttgart', slug: 'Stuttgart' },
            { nombre: 'Borussia Mönchengladbach', display: 'Borussia Mönchengladbach', slug: 'Mönchengladbach' },
            { nombre: 'Union Berlin', display: 'Union Berlin', slug: 'UnionBerlin' },
            { nombre: 'FC Köln', display: 'FC Köln', slug: 'Köln' },
            { nombre: 'Hoffenheim', display: 'Hoffenheim', slug: 'Hoffenheim' },
            { nombre: 'Mainz 05', display: 'Mainz 05', slug: 'Mainz05' },
            { nombre: 'Augsburg', display: 'Augsburg', slug: 'Augsburg' },
            { nombre: 'Heidenheim', display: 'Heidenheim', slug: 'Heidenheim' },
            { nombre: 'St. Pauli', display: 'St. Pauli', slug: 'St.Pauli' },
            { nombre: 'Hamburger SV', display: 'Hamburger SV', slug: 'Hamburger' }
        ],
        'Ligue 1': [
            { nombre: 'Paris Saint-Germain', display: 'Paris Saint-Germain', slug: 'psg' },
            { nombre: 'Olympique Marseille', display: 'Olympique de Marsella', slug: 'marsella' },
            { nombre: 'AS Monaco', display: 'AS Monaco', slug: 'monaco' },
            { nombre: 'Olympique Lyon', display: 'Olympique de Lyon', slug: 'lyon' }
        ],
        'Selecciones': [
            { nombre: 'Argentina', display: 'Argentina', slug: 'argentina' },
            { nombre: 'Colombia', display: 'Colombia', slug: 'colombia' },
            { nombre: 'Japón', display: 'Japón', slug: 'japon' },
            { nombre: 'Uruguay', display: 'Uruguay', slug: 'uruguay' }
        ]
    };
    
    return equipos[liga] || [];
}

// Función para mapear slugs de camisetas a slugs de clubs
function mapSlugCamisetaToClub(liga, slugCamiseta) {
    const mapping = {
        'laliga': {
            'madrid': 'realmadrid',
            'atletico': 'atlmadrid',
            'bilbao': 'athletic',
            'villareal': 'villarreal',
            'rayo': 'rayovallecano',
            'oviedo': 'realoviedo'
        },
        'premier': {
            'newscastle': 'newcastle'
        },
        'bundesliga': {
            'bayern': 'bayernmunchen',
            'dortmund': 'borussiadortmund',
            'Leipzig': 'rbleipzig',
            'leverkusen': 'bayerleverkusen',
            'Eintracht': 'eintrachtfrankfurt',
            'bremen': 'werderbremen',
            'wolfburg': 'wolfsburg',
            'Freiburg': 'freiburg',
            'Stuttgart': 'stuttgart',
            'Mönchengladbach': 'bmonchengladbach',
            'UnionBerlin': 'unionberlin',
            'Köln': 'koln',
            'Hoffenheim': 'hoffenheim',
            'Mainz05': 'mainz05',
            'Augsburg': 'augsburgo',
            'Heidenheim': 'heidenheim',
            'St.Pauli': 'st_pauli',
            'Hamburger': 'hamburgo'
        },
        'ligue1': {
            'lyon': 'olympiquelyon',
            'marsella': 'olimpiquemarsella'
        }
    };
    
    // Si existe un mapeo para esta liga y slug, úsalo
    if (mapping[liga] && mapping[liga][slugCamiseta]) {
        return mapping[liga][slugCamiseta];
    }
    
    // Si no, devuelve el slug original
    return slugCamiseta;
}

// Función auxiliar para obtener el logo del equipo
function getEquipoLogo(liga, equipoNombre) {
    const equipos = getEquiposPorLiga(liga);
    const equipo = equipos.find(e => e.nombre === equipoNombre);
    
    if (!equipo) return './img/clubs/default.png';
    
    let prefix = '';
    switch(liga) {
        case 'La Liga':
            prefix = 'laliga';
            break;
        case 'Premier League':
            prefix = 'premier';
            break;
        case 'Serie A':
            prefix = 'seriea';
            break;
        case 'Bundesliga':
            prefix = 'bundesliga';
            break;
        case 'Ligue 1':
            prefix = 'ligue1';
            break;
        case 'Selecciones':
            prefix = 'selecciones';
            break;
    }
    
    // Mapear el slug de camiseta al slug de club
    const slugClub = mapSlugCamisetaToClub(prefix, equipo.slug);
    
    return `./img/clubs/${prefix}_${slugClub}.png`;
}

// ============================================
// CATÁLOGO - FILTROS Y BÚSQUEDA
// ============================================

function initCatalogo() {
    console.log('Catálogo inicializado');
    
    // Generar catálogo completo
    generarCatalogo();
    
    // Event listeners para filtros
    const filterLiga = document.getElementById('filter-liga');
    const filterEquipo = document.getElementById('filter-equipo');
    const filterEquipacion = document.getElementById('filter-equipacion');
    const filterSearch = document.getElementById('filter-search');
    
    if (filterLiga) {
        filterLiga.addEventListener('change', () => {
            actualizarEquipos();
            aplicarFiltros();
        });
    }
    if (filterEquipo) {
        filterEquipo.addEventListener('change', aplicarFiltros);
    }
    if (filterEquipacion) {
        filterEquipacion.addEventListener('change', aplicarFiltros);
    }
    if (filterSearch) {
        filterSearch.addEventListener('input', aplicarFiltros);
    }
    
    // Event listeners para botones de vista
    const viewButtons = document.querySelectorAll('.view-btn');
    viewButtons.forEach((btn, index) => {
        btn.addEventListener('click', () => {
            // Remover active de todos
            viewButtons.forEach(b => b.classList.remove('active'));
            // Añadir active al clickeado
            btn.classList.add('active');
            // Cambiar vista
            cambiarVista(index === 0 ? 'grid' : 'list');
        });
    });
}

function generarCatalogo() {
    const catalogoGrid = document.getElementById('catalogo-grid');
    if (!catalogoGrid) return;
    
    let catalogoHTML = '';
    let totalCamisetas = 0;
    
    camisetasDisponibles.forEach(camiseta => {
        // Generar camiseta local
        if (camiseta.local) {
            catalogoHTML += generarTarjetaCamiseta(camiseta, 'local');
            totalCamisetas++;
        }
        
        // Generar camiseta visitante
        if (camiseta.visitante) {
            catalogoHTML += generarTarjetaCamiseta(camiseta, 'visitante');
            totalCamisetas++;
        }
    });
    
    catalogoGrid.innerHTML = catalogoHTML;
    
    // Actualizar contador total
    const countTotal = document.getElementById('count-total');
    const countShowing = document.getElementById('count-showing');
    if (countTotal) countTotal.textContent = totalCamisetas;
    if (countShowing) countShowing.textContent = totalCamisetas;
}

function generarTarjetaCamiseta(camiseta, tipo) {
    const tipoTexto = tipo === 'local' ? 'Primera Equipación' : 'Segunda Equipación';
    const imagenPath = `./img/camisetas/${camiseta.liga}_${camiseta.slug}_${tipo}.png`;
    const ligaIcon = `./img/leagues/${camiseta.liga}.svg`;
    const escudoPath = getLogoPath(camiseta.liga, camiseta.slug);
    
    // Nombre de liga para mostrar
    const ligaNombre = {
        'laliga': 'La Liga',
        'premier': 'Premier League',
        'seriea': 'Serie A',
        'bundesliga': 'Bundesliga',
        'ligue1': 'Ligue 1',
        'selecciones': 'Selecciones'
    }[camiseta.liga] || camiseta.liga;
    
    return `
        <div class="camiseta-card" data-liga="${camiseta.liga}" data-equipacion="${tipo}" data-equipo="${camiseta.equipo.toLowerCase()}">
            <div class="camiseta-badge badge-discount">-60%</div>
            <div class="camiseta-image-wrapper">
                <div class="camiseta-league">
                    <img src="${ligaIcon}" alt="${ligaNombre}" onerror="this.style.display='none'">
                </div>
                <img src="${imagenPath}" 
                     alt="Camiseta ${camiseta.equipo} ${tipoTexto}" 
                     class="camiseta-image"
                     onerror="this.src='./img/hero-jersey.png'">
            </div>
            <div class="camiseta-content">
                <div class="camiseta-team">
                    <i class="fas fa-shield-halved"></i>
                    <span>${camiseta.equipo}</span>
                </div>
                <h3 class="camiseta-name">${tipoTexto}</h3>
                <div class="camiseta-details">
                    <div class="camiseta-type">
                        <i class="fas fa-tag"></i>
                        <span>${ligaNombre}</span>
                    </div>
                    <div class="camiseta-sizes">
                        <i class="fas fa-ruler"></i>
                        <span>XS-XXL</span>
                    </div>
                </div>
                <div class="camiseta-price">
                    <div class="price-old">79.99€</div>
                    <div class="price-current">24.99€</div>
                </div>
                <button class="btn btn-primary btn-comprar" 
                        onclick="openPersonalizarModal('${camiseta.equipo}', '${tipoTexto}', 24.99, '${imagenPath}')">
                    <i class="fas fa-shopping-cart"></i>
                    Comprar
                </button>
            </div>
        </div>
    `;
}

function getLogoPath(liga, slug) {
    let prefix = '';
    
    switch(liga) {
        case 'laliga':
            prefix = 'laliga';
            break;
        case 'premier':
            prefix = 'premier';
            break;
        case 'seriea':
            prefix = 'seriea';
            break;
        case 'bundesliga':
            prefix = 'bundesliga';
            break;
        case 'ligue1':
            prefix = 'ligue1';
            break;
        case 'selecciones':
            prefix = 'selecciones';
            break;
    }
    
    // Mapear el slug de camiseta al slug de club
    const slugClub = mapSlugCamisetaToClub(prefix, slug);
    
    return `./img/clubs/${prefix}_${slugClub}.png`;
}

function aplicarFiltros() {
    const liga = document.getElementById('filter-liga')?.value.toLowerCase() || '';
    const equipo = document.getElementById('filter-equipo')?.value.toLowerCase() || '';
    const equipacion = document.getElementById('filter-equipacion')?.value.toLowerCase() || '';
    const searchText = document.getElementById('filter-search')?.value.toLowerCase() || '';
    
    const cards = document.querySelectorAll('.camiseta-card');
    let visibleCount = 0;
    
    cards.forEach(card => {
        // Obtener atributos data
        const cardLiga = card.getAttribute('data-liga')?.toLowerCase() || '';
        const cardEquipacion = card.getAttribute('data-equipacion')?.toLowerCase() || '';
        const cardEquipo = card.getAttribute('data-equipo')?.toLowerCase() || '';
        
        // Obtener texto para búsqueda
        const teamElement = card.querySelector('.camiseta-team');
        const teamText = teamElement ? teamElement.textContent.toLowerCase() : '';
        
        let showCard = true;
        
        // Filtrar por liga
        if (liga && cardLiga !== liga) {
            showCard = false;
        }
        
        // Filtrar por equipo
        if (equipo && cardEquipo !== equipo) {
            showCard = false;
        }
        
        // Filtrar por equipación
        if (equipacion && cardEquipacion !== equipacion) {
            showCard = false;
        }
        
        // Filtrar por búsqueda (nombre del equipo)
        if (searchText && !teamText.includes(searchText) && !cardEquipo.includes(searchText)) {
            showCard = false;
        }
        
        // Mostrar u ocultar tarjeta
        if (showCard) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Actualizar contador
    const countShowing = document.getElementById('count-showing');
    if (countShowing) {
        countShowing.textContent = visibleCount;
    }
}

function limpiarFiltros() {
    const filterLiga = document.getElementById('filter-liga');
    const filterEquipo = document.getElementById('filter-equipo');
    const filterEquipacion = document.getElementById('filter-equipacion');
    const filterSearch = document.getElementById('filter-search');
    
    if (filterLiga) filterLiga.value = '';
    if (filterEquipo) filterEquipo.value = '';
    if (filterEquipacion) filterEquipacion.value = '';
    if (filterSearch) filterSearch.value = '';
    
    // Mostrar todas las tarjetas
    const cards = document.querySelectorAll('.camiseta-card');
    cards.forEach(card => {
        card.style.display = '';
    });
    
    // Actualizar contador
    const countShowing = document.getElementById('count-showing');
    if (countShowing) {
        countShowing.textContent = cards.length;
    }
}

function cambiarVista(vista) {
    const catalogoGrid = document.getElementById('catalogo-grid');
    if (!catalogoGrid) return;
    
    if (vista === 'list') {
        catalogoGrid.classList.add('catalogo-list');
        catalogoGrid.classList.remove('catalogo-grid');
    } else {
        catalogoGrid.classList.add('catalogo-grid');
        catalogoGrid.classList.remove('catalogo-list');
    }
}

function actualizarEquipos() {
    const filterLiga = document.getElementById('filter-liga');
    const filterEquipo = document.getElementById('filter-equipo');
    
    if (!filterLiga || !filterEquipo) return;
    
    const ligaSeleccionada = filterLiga.value;
    
    // Limpiar opciones actuales excepto la primera
    filterEquipo.innerHTML = '<option value="">Todos los equipos</option>';
    
    if (!ligaSeleccionada) {
        // Si no hay liga seleccionada, mostrar todos los equipos
        const todosEquipos = new Set();
        camisetasDisponibles.forEach(camiseta => {
            todosEquipos.add(camiseta.equipo);
        });
        
        Array.from(todosEquipos).sort().forEach(equipo => {
            const option = document.createElement('option');
            option.value = equipo.toLowerCase();
            option.textContent = equipo;
            filterEquipo.appendChild(option);
        });
    } else {
        // Filtrar equipos por liga seleccionada
        const equiposPorLiga = camisetasDisponibles
            .filter(camiseta => camiseta.liga === ligaSeleccionada)
            .map(camiseta => camiseta.equipo)
            .sort();
        
        equiposPorLiga.forEach(equipo => {
            const option = document.createElement('option');
            option.value = equipo.toLowerCase();
            option.textContent = equipo;
            filterEquipo.appendChild(option);
        });
    }
}

// ============================================
// CARRITO DE COMPRAS
// ============================================

function initCart() {
    updateCartBadge();
}

function loadCartFromStorage() {
    const savedCart = localStorage.getItem('kickverse_cart');
    if (savedCart) {
        cartItems = JSON.parse(savedCart);
        updateCartBadge();
    }
}

function saveCartToStorage() {
    localStorage.setItem('kickverse_cart', JSON.stringify(cartItems));
    updateCartBadge();
}

function updateCartBadge() {
    const badge = document.getElementById('cart-badge');
    if (badge) {
        const itemCount = cartItems.reduce((sum, item) => sum + item.cantidad, 0);
        badge.textContent = itemCount;
        badge.style.display = itemCount > 0 ? 'flex' : 'none';
    }
}

function addToCart(item) {
    // Buscar si ya existe en el carrito (mismo producto con mismas características)
    const existingIndex = cartItems.findIndex(cartItem => 
        cartItem.equipo === item.equipo && 
        cartItem.equipacion === item.equipacion && 
        cartItem.talla === item.talla &&
        cartItem.parches === item.parches &&
        cartItem.nombre === item.nombre &&
        cartItem.dorsal === item.dorsal
    );
    
    if (existingIndex >= 0) {
        // Si existe exactamente el mismo producto, aumentar cantidad
        cartItems[existingIndex].cantidad++;
    } else {
        // Si no existe, añadir nuevo item con ID único
        cartItems.push({
            ...item,
            cantidad: 1,
            id: Date.now() + Math.random() // ID único más robusto
        });
    }
    
    saveCartToStorage();
    renderCart(); // Re-renderizar el carrito para actualizar el contador de promoción
    showCartNotification('Producto añadido al carrito');
}

function removeFromCart(itemId) {
    console.log('Eliminando producto:', itemId);
    // Convertir ambos a string para comparación consistente
    const idToRemove = String(itemId);
    cartItems = cartItems.filter(item => String(item.id) !== idToRemove);
    saveCartToStorage();
    renderCart();
    showCartNotification('Producto eliminado del carrito');
}

function updateCartItemQuantity(itemId, newQuantity) {
    // Convertir a string para comparación consistente
    const idToFind = String(itemId);
    const item = cartItems.find(item => String(item.id) === idToFind);
    if (item) {
        if (newQuantity <= 0) {
            removeFromCart(itemId);
        } else {
            item.cantidad = newQuantity;
            saveCartToStorage();
            renderCart();
        }
    }
}

function clearCart() {
    cartItems = [];
    saveCartToStorage();
    renderCart();
}

// Hacer las funciones accesibles globalmente para onclick
window.removeFromCart = removeFromCart;
window.updateCartItemQuantity = updateCartItemQuantity;
window.clearCart = clearCart;

function renderCart() {
    const cartContainer = document.getElementById('cart-items-container');
    const cartEmpty = document.getElementById('cart-empty');
    const cartContent = document.getElementById('cart-content');
    const cartTotal = document.getElementById('cart-total');
    
    if (!cartContainer) return;
    
    if (cartItems.length === 0) {
        if (cartEmpty) cartEmpty.style.display = 'block';
        if (cartContent) cartContent.style.display = 'none';
        return;
    }
    
    if (cartEmpty) cartEmpty.style.display = 'none';
    if (cartContent) cartContent.style.display = 'block';
    
    let html = '';
    let total = 0;
    
    cartItems.forEach(item => {
        const itemTotal = item.precio * item.cantidad;
        total += itemTotal;
        
        // Convertir ID a string y escapar para uso seguro en atributos
        const itemId = String(item.id);
        const safeId = itemId.replace(/'/g, "\\'");
        
        html += `
            <div class="cart-item" data-id="${itemId}">
                <div class="cart-item-image">
                    <img src="${item.imagen}" alt="${item.equipo}">
                </div>
                <div class="cart-item-details">
                    <h4>${item.equipo}</h4>
                    <p>${item.equipacion} - Talla: ${item.talla}</p>
                    ${item.parches ? '<span class="badge badge-sm">Con parches</span>' : ''}
                    ${item.nombre ? `<span class="badge badge-primary badge-sm">${item.nombre} #${item.dorsal}</span>` : ''}
                </div>
                <div class="cart-item-quantity">
                    <button type="button" onclick="updateCartItemQuantity('${safeId}', ${item.cantidad - 1})" class="btn-quantity">-</button>
                    <span>${item.cantidad}</span>
                    <button type="button" onclick="updateCartItemQuantity('${safeId}', ${item.cantidad + 1})" class="btn-quantity">+</button>
                </div>
                <div class="cart-item-price">
                    <span>${itemTotal.toFixed(2)}€</span>
                </div>
                <button type="button" onclick="removeFromCart('${safeId}')" class="cart-item-remove" aria-label="Eliminar producto">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
    });
    
    cartContainer.innerHTML = html;
    
    // Calcular descuento si hay cupón aplicado
    const discount = calculateDiscount(total);
    const finalTotal = total - discount;
    
    // Actualizar subtotal
    const subtotalElement = document.getElementById('cart-subtotal');
    if (subtotalElement) {
        subtotalElement.textContent = `${total.toFixed(2)}€`;
    }
    
    // Mostrar/ocultar fila de descuento
    const discountRow = document.getElementById('discount-row');
    const discountAmount = document.getElementById('cart-discount');
    if (discountRow && discountAmount) {
        if (discount > 0) {
            discountAmount.textContent = `-${discount.toFixed(2)}€`;
            discountRow.style.display = 'flex';
        } else {
            discountRow.style.display = 'none';
        }
    }
    
    // Actualizar total final
    if (cartTotal) {
        cartTotal.textContent = `${finalTotal.toFixed(2)}€`;
    }
    
    // Mostrar promoción descuento primera compra
    const promoMessage = document.getElementById('cart-promo-message');
    if (promoMessage) {
        // Verificar si es primera compra (esto se podría mejorar con localStorage o backend)
        const esPrimeraCompra = !localStorage.getItem('kickverse_compra_realizada');
        
        if (esPrimeraCompra) {
            promoMessage.innerHTML = '<i class="fas fa-percent"></i> ¡10% de descuento en tu primera compra aplicado!';
            promoMessage.style.display = 'block';
            promoMessage.className = 'cart-promo-message success';
        } else {
            promoMessage.style.display = 'none';
        }
    }
}

function showCartNotification(message) {
    // Crear notificación temporal
    const notification = document.createElement('div');
    notification.className = 'cart-notification';
    notification.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

function openCart() {
    renderCart();
    openModal('cart-modal');
}

function finalizarCompraCarrito() {
    if (cartItems.length === 0) {
        alert('El carrito está vacío');
        return;
    }
    
    let mensaje = `Hola Kickverse!\n\nQuiero realizar un pedido:\n\n`;
    
    cartItems.forEach((item, index) => {
        mensaje += `PRODUCTO ${index + 1}:\n`;
        mensaje += `Equipo: ${item.equipo}\n`;
        mensaje += `Equipacion: ${item.equipacion}\n`;
        mensaje += `Talla: ${item.talla}\n`;
        mensaje += `Parches: ${item.parches ? 'Si' : 'No'}\n`;
        if (item.nombre) {
            mensaje += `Personalizacion: ${item.nombre} #${item.dorsal}\n`;
        }
        mensaje += `Cantidad: ${item.cantidad}\n`;
        mensaje += `Precio: ${(item.precio * item.cantidad).toFixed(2)}€\n\n`;
    });
    
    const subtotal = cartItems.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
    mensaje += `Subtotal: ${subtotal.toFixed(2)}€\n`;
    
    // Añadir descuento si existe
    if (appliedCoupon) {
        const discount = calculateDiscount(subtotal);
        mensaje += `Descuento (${appliedCoupon.code}): -${discount.toFixed(2)}€\n`;
        mensaje += `TOTAL: ${(subtotal - discount).toFixed(2)}€\n\n`;
    } else {
        mensaje += `TOTAL: ${subtotal.toFixed(2)}€\n\n`;
    }
    
    mensaje += `¿Cual es el siguiente paso?`;
    
    // Abrir modal de dirección en lugar de ir directo a WhatsApp
    abrirModalDireccion(mensaje, 'carrito');
}

// ============================================
// MODAL DE PERSONALIZACIÓN (CATÁLOGO)
// ============================================

function openPersonalizarModal(equipo, equipacion, precio, imagenSrc) {
    currentProductForCart = {
        equipo: equipo,
        equipacion: equipacion,
        precio: parseFloat(precio),
        imagen: imagenSrc,
        liga: 'La Liga' // Default, se puede mejorar
    };
    
    // Mostrar imagen de la camiseta
    const modalJerseyImage = document.getElementById('modal-jersey-image');
    if (modalJerseyImage) {
        modalJerseyImage.src = imagenSrc;
        modalJerseyImage.onerror = function() {
            this.src = './img/hero-jersey.png';
        };
    }
    
    // Resetear badge a FAN
    const modalVersionBadge = document.getElementById('modal-version-badge');
    if (modalVersionBadge) {
        modalVersionBadge.className = 'modal-version-badge fan';
        modalVersionBadge.textContent = 'FAN';
    }
    
    // Resetear formulario
    document.getElementById('personalizar-talla').value = '';
    document.getElementById('personalizar-parches').checked = false;
    document.getElementById('personalizar-custom').checked = false;
    document.getElementById('personalizar-nombre').value = '';
    document.getElementById('personalizar-dorsal').value = '';
    document.getElementById('custom-fields').style.display = 'none';
    
    // Resetear versión a FAN por defecto
    const versionInput = document.getElementById('modal-version-selected');
    if (versionInput) {
        versionInput.value = 'fan';
    }
    
    // Marcar card FAN como seleccionada por defecto
    const cards = document.querySelectorAll('.version-card-modal');
    cards.forEach(card => {
        if (card.getAttribute('data-version') === 'fan') {
            card.classList.add('selected');
        } else {
            card.classList.remove('selected');
        }
    });
    
    // Resetear estado de tallas
    const tallaButtons = document.querySelectorAll('.tallas-buttons-modal .talla-btn-modal');
    tallaButtons.forEach(btn => btn.classList.remove('selected'));
    
    // Mostrar grupos de parches y personalización (para FAN por defecto)
    const modalParchesGroup = document.getElementById('modal-parches-group');
    const modalPersonalizacionGroup = document.getElementById('modal-personalizacion-group');
    if (modalParchesGroup) modalParchesGroup.style.display = 'block';
    if (modalPersonalizacionGroup) modalPersonalizacionGroup.style.display = 'block';
    
    // Actualizar título del modal
    const modalTitle = document.querySelector('#personalizar-modal .modal-title');
    if (modalTitle) {
        modalTitle.textContent = `Personalizar ${equipo}`;
    }
    
    openModal('personalizar-modal');
}

function selectModalVersion(version) {
    // Actualizar el input oculto
    const versionInput = document.getElementById('modal-version-selected');
    if (versionInput) {
        versionInput.value = version;
    }
    
    // Actualizar badge de versión
    const modalVersionBadge = document.getElementById('modal-version-badge');
    if (modalVersionBadge) {
        modalVersionBadge.className = `modal-version-badge ${version}`;
        modalVersionBadge.textContent = version === 'player' ? 'PLAYER' : 'FAN';
    }
    
    // Actualizar estado visual de las cards
    const cards = document.querySelectorAll('.version-card-modal');
    cards.forEach(card => {
        if (card.getAttribute('data-version') === version) {
            card.classList.add('selected');
        } else {
            card.classList.remove('selected');
        }
    });
    
    // Obtener los elementos
    const modalParchesGroup = document.getElementById('modal-parches-group');
    const modalPersonalizacionGroup = document.getElementById('modal-personalizacion-group');
    
    if (version === 'player') {
        // PLAYER: Ocultar opciones de parches y personalización (ya incluidos)
        if (modalParchesGroup) modalParchesGroup.style.display = 'none';
        if (modalPersonalizacionGroup) modalPersonalizacionGroup.style.display = 'none';
        
        // Auto-activar parches y personalización para PLAYER
        const parchesCheckbox = document.getElementById('personalizar-parches');
        const customCheckbox = document.getElementById('personalizar-custom');
        if (parchesCheckbox) parchesCheckbox.checked = true;
        if (customCheckbox) {
            customCheckbox.checked = true;
            toggleCustomFields(); // Mostrar campos de personalización
        }
    } else {
        // FAN: Mostrar opciones opcionales
        if (modalParchesGroup) modalParchesGroup.style.display = 'block';
        if (modalPersonalizacionGroup) modalPersonalizacionGroup.style.display = 'block';
        
        // Desactivar por defecto
        const parchesCheckbox = document.getElementById('personalizar-parches');
        const customCheckbox = document.getElementById('personalizar-custom');
        if (parchesCheckbox) parchesCheckbox.checked = false;
        if (customCheckbox) {
            customCheckbox.checked = false;
            toggleCustomFields(); // Ocultar campos
        }
    }
}

function selectModalTalla(talla) {
    // Actualizar el input oculto
    const tallaInput = document.getElementById('personalizar-talla');
    if (tallaInput) {
        tallaInput.value = talla;
    }
    
    // Actualizar estado visual de los botones
    const buttons = document.querySelectorAll('.tallas-buttons-modal .talla-btn');
    buttons.forEach(btn => {
        if (btn.textContent.trim() === talla) {
            btn.classList.add('selected');
        } else {
            btn.classList.remove('selected');
        }
    });
}

function toggleCustomFields() {
    const checkbox = document.getElementById('personalizar-custom');
    const fields = document.getElementById('custom-fields');
    
    if (checkbox.checked) {
        fields.style.display = 'block';
    } else {
        fields.style.display = 'none';
        document.getElementById('personalizar-nombre').value = '';
        document.getElementById('personalizar-dorsal').value = '';
    }
}

function agregarAlCarrito() {
    // Obtener versión seleccionada desde el input oculto
    const versionInput = document.getElementById('modal-version-selected');
    const version = versionInput ? versionInput.value : 'fan';
    
    const talla = document.getElementById('personalizar-talla').value;
    const parches = document.getElementById('personalizar-parches').checked;
    const personalizar = document.getElementById('personalizar-custom').checked;
    const nombre = document.getElementById('personalizar-nombre').value.trim().toUpperCase();
    const dorsal = document.getElementById('personalizar-dorsal').value.trim();
    
    if (!version) {
        alert('Por favor selecciona una versión (FAN o PLAYER)');
        return;
    }
    
    if (!talla) {
        alert('Por favor selecciona una talla');
        return;
    }
    
    if (personalizar && (!nombre || !dorsal)) {
        alert('Por favor completa el nombre y dorsal');
        return;
    }
    
    // Calcular precio según versión
    let precioFinal;
    if (version === 'player') {
        // PLAYER: Precio fijo 34.99€ (todo incluido)
        precioFinal = 34.99;
    } else {
        // FAN: Precio base 24.99€ + extras opcionales
        precioFinal = 24.99;
        if (parches) precioFinal += 1.99;
        if (personalizar) precioFinal += 2.99;
    }
    
    const item = {
        ...currentProductForCart,
        version: version,
        talla: talla,
        parches: parches,
        nombre: personalizar ? nombre : '',
        dorsal: personalizar ? dorsal : '',
        precio: precioFinal
    };
    
    addToCart(item);
    closeModal('personalizar-modal');
    
    // Opcional: abrir el carrito
    // openCart();
}

// ============================================
// CARRUSEL DE CAMISETAS
// ============================================

let currentSlide = 0;
let carouselInterval = null;
const CAROUSEL_INTERVAL_TIME = 5000; // 5 segundos

// Camisetas destacadas para el carrusel (las más vendidas)
const featuredJerseys = [
    { liga: 'laliga', equipo: 'Real Madrid', slug: 'madrid', tipo: 'local' },
    { liga: 'laliga', equipo: 'Barcelona', slug: 'barcelona', tipo: 'local' },
    { liga: 'premier', equipo: 'Manchester City', slug: 'manchestercity', tipo: 'local' },
    { liga: 'premier', equipo: 'Liverpool', slug: 'liverpool', tipo: 'local' },
    { liga: 'seriea', equipo: 'Juventus', slug: 'juventus', tipo: 'local' },
    { liga: 'bundesliga', equipo: 'Bayern München', slug: 'bayern', tipo: 'local' },
    { liga: 'ligue1', equipo: 'Paris Saint-Germain', slug: 'psg', tipo: 'local' }
];

function initCarousel() {
    const carouselTrack = document.getElementById('carousel-track');
    const indicatorsContainer = document.getElementById('carousel-indicators');
    
    if (!carouselTrack || !indicatorsContainer) return;
    
    // Generar items del carrusel
    let carouselHTML = '';
    featuredJerseys.forEach((jersey, index) => {
        carouselHTML += generateCarouselItem(jersey, index);
    });
    carouselTrack.innerHTML = carouselHTML;
    
    // Generar indicadores
    let indicatorsHTML = '';
    featuredJerseys.forEach((_, index) => {
        indicatorsHTML += `<div class="carousel-indicator ${index === 0 ? 'active' : ''}" onclick="goToSlide(${index})"></div>`;
    });
    indicatorsContainer.innerHTML = indicatorsHTML;
    
    // Añadir event listeners a los botones de comprar del carrusel
    const carouselBuyButtons = document.querySelectorAll('.btn-comprar-carousel');
    carouselBuyButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const equipo = this.getAttribute('data-equipo');
            const equipacion = this.getAttribute('data-equipacion');
            const precio = parseFloat(this.getAttribute('data-precio'));
            const imagen = this.getAttribute('data-imagen');
            const liga = this.getAttribute('data-liga');
            
            // Actualizar el producto actual con la liga correcta
            currentProductForCart = {
                equipo: equipo,
                equipacion: equipacion,
                precio: precio,
                imagen: imagen,
                liga: liga
            };
            
            // Resetear formulario
            document.getElementById('personalizar-talla').value = '';
            document.getElementById('personalizar-parches').checked = false;
            document.getElementById('personalizar-custom').checked = false;
            document.getElementById('personalizar-nombre').value = '';
            document.getElementById('personalizar-dorsal').value = '';
            document.getElementById('custom-fields').style.display = 'none';
            
            // Actualizar título del modal
            const modalTitle = document.querySelector('#personalizar-modal .modal-title');
            if (modalTitle) {
                modalTitle.textContent = `Personalizar ${equipo}`;
            }
            
            // Abrir modal
            openModal('personalizar-modal');
        });
    });
    
    // Iniciar autoplay
    startCarouselAutoplay();
    
    // Pausar autoplay al hover
    const carouselContainer = document.querySelector('.carousel-container');
    if (carouselContainer) {
        carouselContainer.addEventListener('mouseenter', stopCarouselAutoplay);
        carouselContainer.addEventListener('mouseleave', startCarouselAutoplay);
    }
}

function generateCarouselItem(jersey, index) {
    const tipoTexto = jersey.tipo === 'local' ? 'Primera Equipación' : 'Segunda Equipación';
    const imagenPath = `./img/camisetas/${jersey.liga}_${jersey.slug}_${jersey.tipo}.png`;
    const ligaIcon = `./img/leagues/${jersey.liga}.svg`;
    
    const ligaNombre = {
        'laliga': 'La Liga',
        'premier': 'Premier League',
        'seriea': 'Serie A',
        'bundesliga': 'Bundesliga',
        'ligue1': 'Ligue 1'
    }[jersey.liga] || jersey.liga;
    
    return `
        <div class="carousel-item" data-index="${index}">
            <div class="camiseta-card">
                <div class="camiseta-badge badge-discount">-60%</div>
                <div class="camiseta-image-wrapper">
                    <div class="camiseta-league">
                        <img src="${ligaIcon}" alt="${ligaNombre}" onerror="this.style.display='none'">
                    </div>
                    <img src="${imagenPath}" 
                         alt="Camiseta ${jersey.equipo} ${tipoTexto}" 
                         class="camiseta-image"
                         onerror="this.src='./img/hero-jersey.png'">
                </div>
                <div class="camiseta-content">
                    <div class="camiseta-team">
                        <i class="fas fa-shield-halved"></i>
                        <span>${jersey.equipo}</span>
                    </div>
                    <h3 class="camiseta-name">${tipoTexto}</h3>
                    <div class="camiseta-details">
                        <div class="camiseta-type">
                            <i class="fas fa-tag"></i>
                            <span>${ligaNombre}</span>
                        </div>
                        <div class="camiseta-sizes">
                            <i class="fas fa-ruler"></i>
                            <span>XS-XXL</span>
                        </div>
                    </div>
                    <div class="camiseta-price">
                        <div class="price-old">79.99€</div>
                        <div class="price-current">24.99€</div>
                    </div>
                    <button class="btn btn-primary btn-comprar-carousel" 
                            data-equipo="${jersey.equipo}" 
                            data-equipacion="${tipoTexto}" 
                            data-precio="24.99" 
                            data-imagen="${imagenPath}"
                            data-liga="${ligaNombre}">
                        <i class="fas fa-shopping-cart"></i>
                        Comprar
                    </button>
                </div>
            </div>
        </div>
    `;
}

function moveCarousel(direction) {
    const totalSlides = featuredJerseys.length;
    const carouselTrack = document.getElementById('carousel-track');
    
    // Calcular cuántos slides se muestran según el ancho de pantalla
    const slidesVisible = getSlidesVisible();
    const maxSlide = Math.max(0, totalSlides - slidesVisible);
    
    currentSlide += direction;
    
    // Limitar el rango
    if (currentSlide < 0) {
        currentSlide = maxSlide;
    } else if (currentSlide > maxSlide) {
        currentSlide = 0;
    }
    
    updateCarouselPosition();
    updateIndicators();
    
    // Reiniciar autoplay
    stopCarouselAutoplay();
    startCarouselAutoplay();
}

function goToSlide(slideIndex) {
    currentSlide = slideIndex;
    updateCarouselPosition();
    updateIndicators();
    
    // Reiniciar autoplay
    stopCarouselAutoplay();
    startCarouselAutoplay();
}

function updateCarouselPosition() {
    const carouselTrack = document.getElementById('carousel-track');
    if (!carouselTrack) return;
    
    const slideWidth = carouselTrack.children[0].offsetWidth;
    const gap = 24; // var(--spacing-lg) = 24px
    const offset = -(currentSlide * (slideWidth + gap));
    
    carouselTrack.style.transform = `translateX(${offset}px)`;
}

function updateIndicators() {
    const indicators = document.querySelectorAll('.carousel-indicator');
    indicators.forEach((indicator, index) => {
        if (index === currentSlide) {
            indicator.classList.add('active');
        } else {
            indicator.classList.remove('active');
        }
    });
}

function getSlidesVisible() {
    const width = window.innerWidth;
    if (width > 1024) return 3;
    if (width > 768) return 2;
    return 1;
}

function startCarouselAutoplay() {
    if (carouselInterval) return; // Ya está corriendo
    
    carouselInterval = setInterval(() => {
        moveCarousel(1);
    }, CAROUSEL_INTERVAL_TIME);
}

function stopCarouselAutoplay() {
    if (carouselInterval) {
        clearInterval(carouselInterval);
        carouselInterval = null;
    }
}

// Actualizar posición en resize
window.addEventListener('resize', () => {
    if (document.getElementById('carousel-track')) {
        updateCarouselPosition();
    }
});

// ============================================
// SISTEMA DE CUPONES Y DESCUENTOS
// ============================================

// Definición de cupones disponibles
const AVAILABLE_COUPONS = {
    'WELCOME5': {
        type: 'fixed', // descuento fijo
        value: 5,
        minPurchase: 60,
        description: 'Descuento de 5€ en compras superiores a 60€'
    },
    'NOTBETTING10': {
        type: 'percentage', // porcentaje
        value: 10,
        maxDiscount: 5,
        minPurchase: 0,
        description: 'Descuento del 10% (máximo 5€)'
    },
    'TOPBONUS10': {
        type: 'percentage',
        value: 10,
        maxDiscount: 5,
        minPurchase: 0,
        description: 'Descuento del 10% (máximo 5€)'
    },
    'KICKVERSE10': {
        type: 'percentage',
        value: 10,
        maxDiscount: 5,
        minPurchase: 0,
        description: 'Descuento del 10% (máximo 5€)'
    }
};

// Variable global para el cupón aplicado
let appliedCoupon = null;

// Mostrar popup de bienvenida (solo primera visita)
function showWelcomePopup() {
    const hasVisited = localStorage.getItem('kickverse_visited');
    
    if (!hasVisited) {
        setTimeout(() => {
            const popup = document.getElementById('welcome-popup');
            if (popup) {
                popup.classList.add('active');
            }
        }, 2000); // Mostrar después de 2 segundos
        
        localStorage.setItem('kickverse_visited', 'true');
    }
}

// Cerrar popup de bienvenida
function closeWelcomePopup() {
    const popup = document.getElementById('welcome-popup');
    if (popup) {
        popup.classList.remove('active');
    }
}

// Copiar código de cupón
function copyCouponCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        const btn = event.target.closest('.btn-copy');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> ¡Copiado!';
        btn.style.background = 'var(--primary-color)';
        btn.style.color = 'white';
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.style.background = '';
            btn.style.color = '';
        }, 2000);
    }).catch(err => {
        console.error('Error al copiar:', err);
    });
}

// Aplicar cupón
function applyCoupon() {
    const input = document.getElementById('coupon-input');
    const couponCode = input.value.trim().toUpperCase();
    const messageDiv = document.getElementById('coupon-message');
    
    if (!couponCode) {
        showCouponMessage('Por favor, introduce un código de cupón', 'error');
        return;
    }
    
    const coupon = AVAILABLE_COUPONS[couponCode];
    
    if (!coupon) {
        showCouponMessage('Cupón no válido o expirado', 'error');
        input.value = '';
        return;
    }
    
    // Calcular subtotal
    const subtotal = cartItems.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
    
    // Verificar compra mínima
    if (subtotal < coupon.minPurchase) {
        showCouponMessage(`Este cupón requiere una compra mínima de ${coupon.minPurchase}€`, 'error');
        return;
    }
    
    // Aplicar cupón
    appliedCoupon = {
        code: couponCode,
        ...coupon
    };
    
    input.value = '';
    showCouponMessage(`¡Cupón aplicado! ${coupon.description}`, 'success');
    showAppliedCoupon(couponCode);
    renderCart(); // Recalcular totales
}

// Eliminar cupón
function removeCoupon() {
    appliedCoupon = null;
    
    const appliedDiv = document.getElementById('applied-coupon');
    if (appliedDiv) {
        appliedDiv.style.display = 'none';
    }
    
    const messageDiv = document.getElementById('coupon-message');
    if (messageDiv) {
        messageDiv.style.display = 'none';
    }
    
    renderCart(); // Recalcular totales
}

// Mostrar mensaje de cupón
function showCouponMessage(message, type) {
    const messageDiv = document.getElementById('coupon-message');
    if (!messageDiv) return;
    
    messageDiv.className = `coupon-message ${type}`;
    messageDiv.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}`;
    messageDiv.style.display = 'block';
    
    if (type === 'error') {
        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, 4000);
    }
}

// Mostrar cupón aplicado
function showAppliedCoupon(code) {
    const appliedDiv = document.getElementById('applied-coupon');
    const codeSpan = document.getElementById('applied-coupon-code');
    
    if (appliedDiv && codeSpan) {
        codeSpan.textContent = code;
        appliedDiv.style.display = 'flex';
    }
}

// Calcular descuento
function calculateDiscount(subtotal) {
    let discount = 0;
    
    // Descuento de primera compra (10%)
    const esPrimeraCompra = !localStorage.getItem('kickverse_compra_realizada');
    if (esPrimeraCompra) {
        discount = (subtotal * 10) / 100;
    }
    
    // Si hay cupón aplicado, usar el mayor descuento
    if (appliedCoupon) {
        let couponDiscount = 0;
        
        if (appliedCoupon.type === 'fixed') {
            couponDiscount = appliedCoupon.value;
        } else if (appliedCoupon.type === 'percentage') {
            couponDiscount = (subtotal * appliedCoupon.value) / 100;
            
            // Aplicar descuento máximo si existe
            if (appliedCoupon.maxDiscount && couponDiscount > appliedCoupon.maxDiscount) {
                couponDiscount = appliedCoupon.maxDiscount;
            }
        }
        
        // Usar el mayor descuento (no se acumulan)
        discount = Math.max(discount, couponDiscount);
    }
    
    // El descuento no puede ser mayor que el subtotal
    return Math.min(discount, subtotal);
}

// Inicializar popup de bienvenida al cargar la página
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', showWelcomePopup);
} else {
    showWelcomePopup();
}

// ============================================
// SISTEMA DE DIRECCIÓN DE ENVÍO
// ============================================

// Variable temporal para almacenar el mensaje pendiente
let mensajePendiente = null;
let tipoPedidoPendiente = null; // 'carrito' o 'directo'

// Cargar dirección guardada
function cargarDireccionGuardada() {
    const direccionGuardada = localStorage.getItem('kickverse_direccion');
    if (!direccionGuardada) return null;
    
    try {
        return JSON.parse(direccionGuardada);
    } catch (e) {
        console.error('Error al cargar dirección:', e);
        return null;
    }
}

// Guardar dirección
function guardarDireccion(direccion) {
    localStorage.setItem('kickverse_direccion', JSON.stringify(direccion));
}

// Pre-llenar formulario con dirección guardada
function preLlenarFormulario() {
    const direccion = cargarDireccionGuardada();
    if (!direccion) return;
    
    const form = document.getElementById('direccion-form');
    if (!form) return;
    
    Object.keys(direccion).forEach(key => {
        const input = form.querySelector(`[name="${key}"]`);
        if (input && direccion[key]) {
            input.value = direccion[key];
        }
    });
}

// Obtener datos del formulario
function obtenerDatosDireccion() {
    const form = document.getElementById('direccion-form');
    if (!form) return null;
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return null;
    }
    
    const formData = new FormData(form);
    const direccion = {};
    
    formData.forEach((value, key) => {
        direccion[key] = value;
    });
    
    return direccion;
}

// Abrir modal de dirección
function abrirModalDireccion(mensaje, tipoPedido = 'carrito') {
    mensajePendiente = mensaje;
    tipoPedidoPendiente = tipoPedido;
    
    preLlenarFormulario();
    openModal('direccion-modal');
}

// Confirmar dirección y enviar
function confirmarDireccionYEnviar() {
    const direccion = obtenerDatosDireccion();
    if (!direccion) return;
    
    // Guardar dirección para futuras compras
    guardarDireccion(direccion);
    
    // Añadir dirección al mensaje
    let mensajeCompleto = mensajePendiente + `\n\nDIRECCION DE ENVIO:\n`;
    mensajeCompleto += `Nombre: ${direccion.nombre}\n`;
    mensajeCompleto += `Telefono: ${direccion.telefono}\n`;
    if (direccion.email) {
        mensajeCompleto += `Email: ${direccion.email}\n`;
    }
    mensajeCompleto += `Direccion: ${direccion.calle}\n`;
    mensajeCompleto += `${direccion.ciudad}, ${direccion.provincia}\n`;
    mensajeCompleto += `CP: ${direccion.cp}\n`;
    mensajeCompleto += `Pais: ${direccion.pais}\n`;
    if (direccion.notas) {
        mensajeCompleto += `Notas: ${direccion.notas}\n`;
    }
    
    // Enviar por WhatsApp
    const telefono = '34614299735';
    const urlWhatsApp = `https://wa.me/${telefono}?text=${encodeURIComponent(mensajeCompleto)}`;
    window.open(urlWhatsApp, '_blank');
    
    // Cerrar modal
    closeModal('direccion-modal');
    
    // Si es del carrito, limpiarlo
    if (tipoPedidoPendiente === 'carrito') {
        clearCart();
        closeModal('cart-modal');
    }
    
    // Resetear variables
    mensajePendiente = null;
    tipoPedidoPendiente = null;
}

// ============================================
// UPSELLING Y CROSS-SELLING
// ============================================

// Variables para upselling/cross-selling
let upsellActivado = false;
let crosssellItems = [];

/**
 * Verificar si se debe mostrar el modal de promoción primera compra
 * Ahora solo se muestra si es primera compra (sin lógica de 3x2)
 */
function verificarUpsell() {
    // Ya no se usa el modal de upsell para 3x2
    // El descuento de primera compra se aplica automáticamente
    return false;
}

/**
 * Mostrar el modal de promoción (ya no se usa para upsell 3x2)
 */
function mostrarModalUpsell() {
    // Función mantenida para compatibilidad pero ya no se activa
    const modal = document.getElementById('upsell-modal');
    if (modal) {
        modal.classList.add('active');
        upsellActivado = true;
    }
}

/**
 * Cerrar el modal de upsell
 */
function cerrarModalUpsell() {
    const modal = document.getElementById('upsell-modal');
    if (modal) {
        modal.classList.remove('active');
    }
}

/**
 * Usuario acepta el upsell - vuelve al paso 1 para añadir otra camiseta
 */
function aceptarUpsell() {
    cerrarModalUpsell();
    
    // Volver al paso 1 del formulario
    loadStepContent(1);
    
    // Scroll suave al formulario
    const formWizard = document.getElementById('form-wizard');
    if (formWizard) {
        formWizard.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

/**
 * Usuario rechaza el upsell - continuar con cross-sell y resumen
 */
function rechazarUpsell() {
    cerrarModalUpsell();
    
    // Mostrar cross-sell y resumen final
    mostrarCrossSellYResumen();
}

/**
 * Detectar equipo elegido y generar cross-sell contextual
 */
function generarCrossSellContextual() {
    const equipoElegido = formData.equipo;
    const ligaElegida = formData.liga;
    
    crosssellItems = [];
    
    // Cross-sell específico por equipo
    const crosssellData = {
        'Real Madrid': {
            camiseta: {
                nombre: '2.ª Equipación Real Madrid',
                descripcion: 'Camiseta negra alternativa temporada 2024/25',
                imagen: './img/camisetas/laliga_real-madrid_visitante.png',
                precioOriginal: 79.99,
                precioOferta: 24.99,
                tipo: 'camiseta'
            },
            accesorio: {
                nombre: 'Gorra Real Madrid Blanca',
                descripcion: 'Gorra oficial con escudo bordado',
                imagen: './img/icons/gorra.svg',
                precioOriginal: 19.99,
                precioOferta: 7.99,
                tipo: 'accesorio'
            }
        },
        'FC Barcelona': {
            camiseta: {
                nombre: '2.ª Equipación FC Barcelona',
                descripcion: 'Camiseta azul oscura alternativa temporada 2024/25',
                imagen: './img/camisetas/laliga_barcelona_visitante.png',
                precioOriginal: 79.99,
                precioOferta: 24.99,
                tipo: 'camiseta'
            },
            accesorio: {
                nombre: 'Gorra FC Barcelona Azulgrana',
                descripcion: 'Gorra oficial con escudo bordado',
                imagen: './img/icons/gorra.svg',
                precioOriginal: 19.99,
                precioOferta: 7.99,
                tipo: 'accesorio'
            }
        },
        'Atlético de Madrid': {
            camiseta: {
                nombre: '2.ª Equipación Atlético de Madrid',
                descripcion: 'Camiseta azul alternativa temporada 2024/25',
                imagen: './img/camisetas/laliga_atletico_visitante.png',
                precioOriginal: 79.99,
                precioOferta: 24.99,
                tipo: 'camiseta'
            },
            accesorio: {
                nombre: 'Gorra Atlético de Madrid',
                descripcion: 'Gorra oficial con escudo bordado',
                imagen: './img/icons/gorra.svg',
                precioOriginal: 19.99,
                precioOferta: 7.99,
                tipo: 'accesorio'
            }
        }
    };
    
    // Si existe cross-sell específico para el equipo elegido
    if (crosssellData[equipoElegido]) {
        const items = crosssellData[equipoElegido];
        
        // Añadir segunda equipación si la elegida fue la local
        if (formData.equipacion === 'Local' && items.camiseta) {
            crosssellItems.push(items.camiseta);
        }
        
        // Siempre añadir el accesorio
        if (items.accesorio) {
            crosssellItems.push(items.accesorio);
        }
    } else {
        // Cross-sell genérico: solo accesorio
        crosssellItems.push({
            nombre: `Gorra ${equipoElegido}`,
            descripcion: 'Gorra oficial con escudo bordado',
            imagen: './img/icons/gorra.svg',
            precioOriginal: 19.99,
            precioOferta: 7.99,
            tipo: 'accesorio'
        });
    }
    
    return crosssellItems;
}

/**
 * Renderizar el cross-sell en el DOM
 */
function renderizarCrossSell() {
    const items = generarCrossSellContextual();
    
    if (items.length === 0) return '';
    
    let html = `
        <div class="crosssell-container">
            <div class="crosssell-header">
                <h3 class="crosssell-title">
                    <i class="fas fa-plus-circle"></i>
                    También disponible
                </h3>
                <p class="crosssell-subtitle">Completa tu pedido con estos productos especiales</p>
            </div>
            <div class="crosssell-grid">
    `;
    
    items.forEach((item, index) => {
        const ahorro = item.precioOriginal - item.precioOferta;
        const porcentajeDescuento = Math.round((ahorro / item.precioOriginal) * 100);
        
        html += `
            <div class="crosssell-card">
                <span class="crosssell-badge">-${porcentajeDescuento}%</span>
                <img src="${item.imagen}" alt="${item.nombre}" class="crosssell-image" onerror="this.src='./img/hero-jersey.png'">
                <div class="crosssell-info">
                    <h4 class="crosssell-name">${item.nombre}</h4>
                    <p class="crosssell-desc">${item.descripcion}</p>
                    <div class="crosssell-pricing">
                        <span class="crosssell-price-old">${item.precioOriginal.toFixed(2)}€</span>
                        <span class="crosssell-price-new">${item.precioOferta.toFixed(2)}€</span>
                    </div>
                    <span class="crosssell-savings">Ahorras ${ahorro.toFixed(2)}€</span>
                    <button class="crosssell-btn" onclick="añadirCrossSell(${index})">
                        <i class="fas fa-cart-plus"></i>
                        Añadir al pedido
                    </button>
                </div>
            </div>
        `;
    });
    
    html += `
            </div>
        </div>
    `;
    
    return html;
}

/**
 * Añadir item de cross-sell al pedido
 */
function añadirCrossSell(index) {
    const item = crosssellItems[index];
    
    if (!item) return;
    
    // Crear objeto de producto para el carrito
    const producto = {
        liga: formData.liga,
        equipo: formData.equipo,
        equipacion: item.tipo === 'camiseta' ? 'Visitante' : 'Accesorio',
        talla: item.tipo === 'camiseta' ? formData.talla : 'Única',
        parches: false,
        personalizar: false,
        nombre: '',
        dorsal: '',
        precio: item.precioOferta,
        nombreProducto: item.nombre
    };
    
    // Añadir al carrito
    cartItems.push(producto);
    
    // Guardar en localStorage
    localStorage.setItem('kickverse_cart', JSON.stringify(cartItems));
    
    // Feedback visual
    const btn = event.target.closest('.crosssell-btn');
    if (btn) {
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Añadido';
        btn.style.background = 'linear-gradient(135deg, var(--accent-green), #10b981)';
        btn.disabled = true;
        
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.style.background = '';
            btn.disabled = false;
        }, 2000);
    }
    
    // Actualizar contador del carrito
    updateCartCount();
}

/**
 * Mostrar cross-sell y resumen final antes de WhatsApp
 */
function mostrarCrossSellYResumen() {
    const stepContent = document.getElementById('step-content');
    
    if (!stepContent) return;
    
    // Calcular totales
    let totalCarrito = 0;
    cartItems.forEach(item => {
        totalCarrito += item.precio || 27.99;
    });
    
    // Renderizar cross-sell y resumen
    const crosssellHTML = renderizarCrossSell();
    
    let html = `
        <div class="step-content">
            <h2><i class="fas fa-check-circle"></i> ¡Casi listo!</h2>
            <p class="step-subtitle">Revisa tu pedido antes de finalizarlo</p>
            
            ${crosssellHTML}
            
            <div class="final-summary">
                <div class="summary-header">
                    <h3 class="summary-title">
                        <i class="fas fa-receipt"></i>
                        Resumen del Pedido
                    </h3>
                </div>
                
                <div class="summary-items">
    `;
    
    // Listar items del carrito
    cartItems.forEach((item, index) => {
        const nombreItem = item.nombreProducto || `${item.equipo} - ${item.equipacion}`;
        const precioItem = item.precio || 24.99;
        
        html += `
            <div class="summary-item">
                <span class="summary-item-name">
                    <i class="fas fa-shirt"></i>
                    ${nombreItem}
                    ${item.personalizar ? ` (${item.nombre} #${item.dorsal})` : ''}
                </span>
                <span class="summary-item-price">${precioItem.toFixed(2)}€</span>
            </div>
        `;
    });
    
    html += `
                </div>
                
                <div class="summary-total">
                    <span class="summary-total-label">
                        <i class="fas fa-calculator"></i>
                        Total del Pedido
                    </span>
                    <span class="summary-total-price">${totalCarrito.toFixed(2)}€</span>
                </div>
            </div>
            
            <div class="step-actions">
                <button class="btn btn-secondary btn-lg" onclick="loadStepContent(1)">
                    <i class="fas fa-arrow-left"></i>
                    Volver al Inicio
                </button>
                <button class="btn btn-whatsapp btn-lg" onclick="finalizarConCrossSell()">
                    <i class="fab fa-whatsapp"></i>
                    Finalizar y Enviar a WhatsApp
                </button>
            </div>
        </div>
    `;
    
    stepContent.innerHTML = html;
    
    // Scroll al inicio del contenido
    stepContent.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/**
 * Finalizar pedido incluyendo items de cross-sell
 */
function finalizarConCrossSell() {
    // Si no hay items en el carrito, usar formData actual
    if (cartItems.length === 0 && formData.equipo) {
        // Construir mensaje con los datos del formulario actual
        generarMensajeWhatsApp(formData);
    } else {
        // Usar función de finalizar carrito que ya maneja múltiples items
        finalizarCompraCarrito();
    }
}
