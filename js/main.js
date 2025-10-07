// ============================================
// MAIN.JS - Lógica principal de Kickverse
// ============================================

// Variables globales
let currentStep = 1;
let formData = {
    liga: '',
    equipo: '',
    equipacion: '',
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
    const telefono = '34614299735';
    const mensaje = `¡Hola Kickverse! 👋

Quiero comprar:
🏆 Equipo: ${equipo}
👕 Equipación: ${equipacion}
💰 Precio: ${precio}

¿Cuáles son los siguientes pasos?`;
    
    const urlWhatsApp = `https://wa.me/${telefono}?text=${encodeURIComponent(mensaje)}`;
    window.open(urlWhatsApp, '_blank');
}

function generarMensajeWhatsApp(data) {
    const telefono = '34614299735';
    
    let mensaje = `¡Hola Kickverse! 👋

Quiero realizar un pedido:

📋 DETALLES DEL PEDIDO:
🏆 Liga: ${data.liga}
⚽ Equipo: ${data.equipo}
👕 Equipación: ${data.equipacion}
📏 Talla: ${data.talla}
🏅 Parches: ${data.parches ? 'Sí' : 'No'}`;

    if (data.personalizar) {
        mensaje += `\n✏️ Personalización:
   - Nombre: ${data.nombre}
   - Dorsal: ${data.dorsal}`;
    }
    
    mensaje += `\n\n¿Cuál es el precio final y los pasos a seguir?`;
    
    const urlWhatsApp = `https://wa.me/${telefono}?text=${encodeURIComponent(mensaje)}`;
    return urlWhatsApp;
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
    
    if (!stepContainer) return;
    
    let content = '';
    
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
            content = getStep4Content();
            break;
        case 5:
            content = getStep5Content();
            break;
        case 6:
            content = getStep6Content();
            break;
        case 7:
            content = getStep7Content();
            break;
    }
    
    stepContainer.innerHTML = content;
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
    formData.liga = liga;
    nextStep();
}

function getStep2Content() {
    const equipos = getEquiposPorLiga(formData.liga);
    
    let html = `
        <div class="form-step">
            <h2>Paso 2: Elige tu Equipo</h2>
            <p class="text-secondary mb-md">Liga: ${formData.liga}</p>
            
            <div class="grid grid-3">
    `;
    
    equipos.forEach(equipo => {
        const logoPath = getEquipoLogo(formData.liga, equipo.nombre);
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
    const tallas = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
    
    let html = `
        <div class="form-step">
            <h2>Paso 4: Elige tu Talla</h2>
            <p class="text-secondary mb-lg">Selecciona la talla que mejor te quede</p>
            
            <div class="grid grid-3">
    `;
    
    tallas.forEach(talla => {
        html += `
            <div class="option-card" onclick="selectTalla('${talla}')">
                <i class="fas fa-ruler"></i>
                <h3>${talla}</h3>
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

function selectTalla(talla) {
    formData.talla = talla;
    nextStep();
}

function getStep5Content() {
    return `
        <div class="form-step">
            <h2>Paso 5: ¿Quieres añadir parches?</h2>
            <p class="text-secondary mb-lg">Parches oficiales de liga (recomendado)</p>
            
            <div class="grid grid-2">
                <div class="option-card" onclick="selectParches(true)">
                    <i class="fas fa-check-circle"></i>
                    <h3>Sí, con parches</h3>
                    <p>+5€</p>
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

function getStep6Content() {
    return `
        <div class="form-step">
            <h2>Paso 6: ¿Quieres personalizarla?</h2>
            <p class="text-secondary mb-lg">Añade nombre y dorsal a tu camiseta</p>
            
            <div class="grid grid-2">
                <div class="option-card" onclick="selectPersonalizacion(true)">
                    <i class="fas fa-edit"></i>
                    <h3>Sí, personalizar</h3>
                    <p>+10€</p>
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
    
    if (personalizar) {
        nextStep();
    } else {
        // Si no quiere personalizar, mostramos resumen
        mostrarResumen();
    }
}

function getStep7Content() {
    return `
        <div class="form-step">
            <h2>Paso 7: Datos de personalización</h2>
            <p class="text-secondary mb-lg">Introduce el nombre y dorsal</p>
            
            <div class="form-group">
                <label for="nombre-input">Nombre</label>
                <input type="text" 
                       id="nombre-input" 
                       placeholder="Ej: RODRÍGUEZ" 
                       maxlength="12"
                       value="${formData.nombre}">
            </div>
            
            <div class="form-group">
                <label for="dorsal-input">Dorsal</label>
                <input type="number" 
                       id="dorsal-input" 
                       placeholder="Ej: 10" 
                       min="1" 
                       max="99"
                       value="${formData.dorsal}">
            </div>
            
            <div class="flex gap-md mt-lg">
                <button class="btn btn-secondary" onclick="previousStep()">
                    <i class="fas fa-arrow-left"></i> Volver
                </button>
                <button class="btn btn-primary" onclick="guardarPersonalizacion()">
                    <i class="fas fa-check"></i> Continuar
                </button>
            </div>
        </div>
    `;
}

function guardarPersonalizacion() {
    const nombre = document.getElementById('nombre-input').value.trim();
    const dorsal = document.getElementById('dorsal-input').value.trim();
    
    if (!nombre || !dorsal) {
        alert('Por favor, completa todos los campos de personalización');
        return;
    }
    
    formData.nombre = nombre.toUpperCase();
    formData.dorsal = dorsal;
    
    mostrarResumen();
}

function mostrarResumen() {
    const stepContainer = document.getElementById('step-content');
    
    let precioBase = 29.99;
    let precioParches = formData.parches ? 5 : 0;
    let precioPersonalizacion = formData.personalizar ? 10 : 0;
    let precioTotal = precioBase + precioParches + precioPersonalizacion;
    
    let html = `
        <div class="form-step">
            <h2>Resumen de tu Pedido</h2>
            <p class="text-secondary mb-xl">Revisa los detalles antes de continuar</p>
            
            <div class="card mb-lg">
                <div class="summary-item">
                    <span class="summary-label">Liga:</span>
                    <span class="summary-value">${formData.liga}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Equipo:</span>
                    <span class="summary-value">${formData.equipo}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Equipación:</span>
                    <span class="summary-value">${formData.equipacion}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Talla:</span>
                    <span class="summary-value">${formData.talla}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Parches:</span>
                    <span class="summary-value">${formData.parches ? 'Sí (+5€)' : 'No'}</span>
                </div>
    `;
    
    if (formData.personalizar) {
        html += `
                <div class="summary-item">
                    <span class="summary-label">Personalización:</span>
                    <span class="summary-value">${formData.nombre} #${formData.dorsal} (+10€)</span>
                </div>
        `;
    }
    
    html += `
                <div class="summary-total">
                    <div class="summary-item" style="border: none;">
                        <span class="summary-label">TOTAL:</span>
                        <span class="summary-value">${precioTotal.toFixed(2)}€</span>
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
    
    stepContainer.innerHTML = html;
    currentStep = 7;
    updateProgressBar();
}

function finalizarPedidoWhatsApp() {
    const url = generarMensajeWhatsApp(formData);
    window.open(url, '_blank');
}

function nextStep() {
    if (currentStep < 7) {
        loadStepContent(currentStep + 1);
    }
}

function previousStep() {
    if (currentStep > 1) {
        loadStepContent(currentStep - 1);
    }
}

function resetForm() {
    formData = {
        liga: '',
        equipo: '',
        equipacion: '',
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
        const progress = (currentStep / 7) * 100;
        progressBar.style.width = `${progress}%`;
        progressText.textContent = `Paso ${currentStep} de 7`;
    }
}

// ============================================
// DATOS - EQUIPOS POR LIGA
// ============================================

function getEquiposPorLiga(liga) {
    const equipos = {
        'La Liga': [
            { nombre: 'Real Madrid', display: 'Real Madrid', slug: 'realmadrid' },
            { nombre: 'FC Barcelona', display: 'FC Barcelona', slug: 'barcelona' },
            { nombre: 'Atlético de Madrid', display: 'Atlético de Madrid', slug: 'atlmadrid' },
            { nombre: 'Sevilla FC', display: 'Sevilla FC', slug: 'sevilla' },
            { nombre: 'Valencia CF', display: 'Valencia CF', slug: 'valencia' },
            { nombre: 'Real Betis', display: 'Real Betis', slug: 'betis' },
            { nombre: 'Athletic Bilbao', display: 'Athletic Bilbao', slug: 'athletic' },
            { nombre: 'Real Sociedad', display: 'Real Sociedad', slug: 'realsociedad' },
            { nombre: 'Villarreal CF', display: 'Villarreal CF', slug: 'villarreal' },
            { nombre: 'Celta de Vigo', display: 'Celta de Vigo', slug: 'celta' },
            { nombre: 'RCD Espanyol', display: 'RCD Espanyol', slug: 'espanyol' },
            { nombre: 'Getafe CF', display: 'Getafe CF', slug: 'getafe' },
            { nombre: 'CA Osasuna', display: 'CA Osasuna', slug: 'osasuna' },
            { nombre: 'Rayo Vallecano', display: 'Rayo Vallecano', slug: 'rayovallecano' },
            { nombre: 'Deportivo Alavés', display: 'Deportivo Alavés', slug: 'alaves' },
            { nombre: 'RCD Mallorca', display: 'RCD Mallorca', slug: 'mallorca' },
            { nombre: 'Girona FC', display: 'Girona FC', slug: 'girona' }
        ],
        'Premier League': [
            { nombre: 'Manchester United', display: 'Manchester United', slug: 'manchesterunited' },
            { nombre: 'Manchester City', display: 'Manchester City', slug: 'manchestercity' },
            { nombre: 'Liverpool FC', display: 'Liverpool FC', slug: 'liverpool' },
            { nombre: 'Chelsea FC', display: 'Chelsea FC', slug: 'chelsea' },
            { nombre: 'Arsenal FC', display: 'Arsenal FC', slug: 'arsenal' },
            { nombre: 'Tottenham', display: 'Tottenham', slug: 'tottenham' },
            { nombre: 'Newcastle United', display: 'Newcastle United', slug: 'newcastle' },
            { nombre: 'West Ham', display: 'West Ham', slug: 'westham' },
            { nombre: 'Aston Villa', display: 'Aston Villa', slug: 'astonvilla' }
        ],
        'Serie A': [
            { nombre: 'Juventus', display: 'Juventus', slug: 'juventus' },
            { nombre: 'AC Milan', display: 'AC Milan', slug: 'milan' },
            { nombre: 'Inter de Milán', display: 'Inter de Milán', slug: 'inter' },
            { nombre: 'AS Roma', display: 'AS Roma', slug: 'roma' },
            { nombre: 'SSC Napoli', display: 'SSC Napoli', slug: 'napoli' },
            { nombre: 'Lazio', display: 'Lazio', slug: 'lazio' }
        ],
        'Bundesliga': [
            { nombre: 'Bayern Múnich', display: 'Bayern Múnich', slug: 'bayern' },
            { nombre: 'Borussia Dortmund', display: 'Borussia Dortmund', slug: 'dortmund' },
            { nombre: 'RB Leipzig', display: 'RB Leipzig', slug: 'leipzig' },
            { nombre: 'Bayer Leverkusen', display: 'Bayer Leverkusen', slug: 'leverkusen' },
            { nombre: 'Eintracht Frankfurt', display: 'Eintracht Frankfurt', slug: 'frankfurt' }
        ],
        'Ligue 1': [
            { nombre: 'Paris Saint-Germain', display: 'Paris Saint-Germain', slug: 'psg' },
            { nombre: 'Olympique de Marsella', display: 'Olympique de Marsella', slug: 'olimpiquemarsella' },
            { nombre: 'AS Monaco', display: 'AS Monaco', slug: 'monaco' },
            { nombre: 'Olympique de Lyon', display: 'Olympique de Lyon', slug: 'olympiquelyon' },
            { nombre: 'Lille OSC', display: 'Lille OSC', slug: 'lille' }
        ],
        'Selecciones': [
            { nombre: 'España', display: 'España', slug: 'espana' },
            { nombre: 'Brasil', display: 'Brasil', slug: 'brasil' },
            { nombre: 'Argentina', display: 'Argentina', slug: 'argentina' },
            { nombre: 'Francia', display: 'Francia', slug: 'francia' },
            { nombre: 'Alemania', display: 'Alemania', slug: 'alemania' },
            { nombre: 'Italia', display: 'Italia', slug: 'italia' },
            { nombre: 'Portugal', display: 'Portugal', slug: 'portugal' },
            { nombre: 'Inglaterra', display: 'Inglaterra', slug: 'inglaterra' },
            { nombre: 'Países Bajos', display: 'Países Bajos', slug: 'paisesbajos' }
        ]
    };
    
    return equipos[liga] || [];
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
    
    return `./img/clubs/${prefix}_${equipo.slug}.png`;
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
        filterLiga.addEventListener('change', aplicarFiltros);
    }
    if (filterEquipacion) {
        filterEquipacion.addEventListener('change', aplicarFiltros);
    }
    if (filterSearch) {
        filterSearch.addEventListener('input', aplicarFiltros);
    }
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
                    <div class="price-old">99.99€</div>
                    <div class="price-current">39.99€</div>
                </div>
                <button class="btn btn-primary btn-comprar" 
                        onclick="openPersonalizarModal('${camiseta.equipo}', '${tipoTexto}', 39.99, '${imagenPath}')">
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
    
    return `./img/clubs/${prefix}_${slug}.png`;
}

function aplicarFiltros() {
    const liga = document.getElementById('filter-liga').value.toLowerCase();
    const equipacion = document.getElementById('filter-equipacion').value.toLowerCase();
    const searchText = document.getElementById('filter-search').value.toLowerCase();
    
    const cards = document.querySelectorAll('.camiseta-card');
    let visibleCount = 0;
    
    cards.forEach(card => {
        const teamElement = card.querySelector('.camiseta-team');
        const nameElement = card.querySelector('.camiseta-name');
        const typeElement = card.querySelector('.camiseta-type');
        
        if (!teamElement || !nameElement) return;
        
        const teamText = teamElement.textContent.toLowerCase();
        const nameText = nameElement.textContent.toLowerCase();
        const typeText = typeElement ? typeElement.textContent.toLowerCase() : '';
        
        let showCard = true;
        
        // Filtrar por búsqueda
        if (searchText && !teamText.includes(searchText) && !nameText.includes(searchText)) {
            showCard = false;
        }
        
        // Filtrar por equipación
        if (equipacion) {
            if (equipacion === 'local' && !typeText.includes('primera') && !typeText.includes('local')) {
                showCard = false;
            }
            if (equipacion === 'visitante' && !typeText.includes('segunda') && !typeText.includes('visitante')) {
                showCard = false;
            }
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
    document.getElementById('filter-liga').value = '';
    document.getElementById('filter-equipo').value = '';
    document.getElementById('filter-equipacion').value = '';
    document.getElementById('filter-search').value = '';
    
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
    // Buscar si ya existe en el carrito
    const existingIndex = cartItems.findIndex(cartItem => 
        cartItem.equipo === item.equipo && 
        cartItem.equipacion === item.equipacion && 
        cartItem.talla === item.talla &&
        cartItem.nombre === item.nombre &&
        cartItem.dorsal === item.dorsal
    );
    
    if (existingIndex >= 0) {
        // Si existe, aumentar cantidad
        cartItems[existingIndex].cantidad++;
    } else {
        // Si no existe, añadir nuevo item
        cartItems.push({
            ...item,
            cantidad: 1,
            id: Date.now() // ID único
        });
    }
    
    saveCartToStorage();
    showCartNotification('Producto añadido al carrito');
}

function removeFromCart(itemId) {
    cartItems = cartItems.filter(item => item.id !== itemId);
    saveCartToStorage();
    renderCart();
}

function updateCartItemQuantity(itemId, newQuantity) {
    const item = cartItems.find(item => item.id === itemId);
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
        
        html += `
            <div class="cart-item" data-id="${item.id}">
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
                    <button onclick="updateCartItemQuantity(${item.id}, ${item.cantidad - 1})" class="btn-quantity">-</button>
                    <span>${item.cantidad}</span>
                    <button onclick="updateCartItemQuantity(${item.id}, ${item.cantidad + 1})" class="btn-quantity">+</button>
                </div>
                <div class="cart-item-price">
                    <span>${itemTotal.toFixed(2)}€</span>
                </div>
                <button onclick="removeFromCart(${item.id})" class="cart-item-remove">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
    });
    
    cartContainer.innerHTML = html;
    
    if (cartTotal) {
        cartTotal.textContent = `${total.toFixed(2)}€`;
    }
    
    // Mostrar promoción 3x2
    const promoMessage = document.getElementById('cart-promo-message');
    if (promoMessage) {
        const totalItems = cartItems.reduce((sum, item) => sum + item.cantidad, 0);
        if (totalItems >= 2 && totalItems < 3) {
            promoMessage.innerHTML = '<i class="fas fa-gift"></i> ¡Añade 1 más y la tercera es GRATIS!';
            promoMessage.style.display = 'block';
        } else if (totalItems >= 3) {
            promoMessage.innerHTML = '<i class="fas fa-check-circle"></i> ¡Promoción 3x2 aplicada!';
            promoMessage.style.display = 'block';
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
    
    const telefono = '34614299735';
    let mensaje = `¡Hola Kickverse! 👋\n\nQuiero realizar un pedido:\n\n`;
    
    cartItems.forEach((item, index) => {
        mensaje += `📦 PRODUCTO ${index + 1}:\n`;
        mensaje += `⚽ Equipo: ${item.equipo}\n`;
        mensaje += `👕 Equipación: ${item.equipacion}\n`;
        mensaje += `📏 Talla: ${item.talla}\n`;
        mensaje += `🏅 Parches: ${item.parches ? 'Sí' : 'No'}\n`;
        if (item.nombre) {
            mensaje += `✏️ Personalización: ${item.nombre} #${item.dorsal}\n`;
        }
        mensaje += `🔢 Cantidad: ${item.cantidad}\n`;
        mensaje += `💰 Precio: ${(item.precio * item.cantidad).toFixed(2)}€\n\n`;
    });
    
    const total = cartItems.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
    mensaje += `💵 TOTAL: ${total.toFixed(2)}€\n\n`;
    mensaje += `¿Cuál es el siguiente paso?`;
    
    const urlWhatsApp = `https://wa.me/${telefono}?text=${encodeURIComponent(mensaje)}`;
    window.open(urlWhatsApp, '_blank');
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
    
    openModal('personalizar-modal');
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
    const talla = document.getElementById('personalizar-talla').value;
    const parches = document.getElementById('personalizar-parches').checked;
    const personalizar = document.getElementById('personalizar-custom').checked;
    const nombre = document.getElementById('personalizar-nombre').value.trim().toUpperCase();
    const dorsal = document.getElementById('personalizar-dorsal').value.trim();
    
    if (!talla) {
        alert('Por favor selecciona una talla');
        return;
    }
    
    if (personalizar && (!nombre || !dorsal)) {
        alert('Por favor completa el nombre y dorsal');
        return;
    }
    
    // Calcular precio
    let precioFinal = currentProductForCart.precio;
    if (parches) precioFinal += 5;
    if (personalizar) precioFinal += 10;
    
    const item = {
        ...currentProductForCart,
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
                        <div class="price-old">99.99€</div>
                        <div class="price-current">39.99€</div>
                    </div>
                    <button class="btn btn-primary btn-comprar-carousel" 
                            data-equipo="${jersey.equipo}" 
                            data-equipacion="${tipoTexto}" 
                            data-precio="39.99" 
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
