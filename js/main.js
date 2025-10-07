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

// ============================================
// INICIALIZACIÓN
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    initMobileMenu();
    initScrollEffects();
    initModals();
    
    // Inicializar formulario si existe
    if (document.getElementById('form-wizard')) {
        initFormWizard();
    }
    
    // Inicializar catálogo si existe
    if (document.getElementById('catalogo-grid')) {
        initCatalogo();
    }
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
    // Aquí se puede añadir lógica de filtros cuando sea necesario
    console.log('Catálogo inicializado');
}
