// ============================================
// KICKVERSE - LÓGICA DEL FORMULARIO
// ============================================

// Datos de los equipos por liga
const teamsData = {
    laliga: [
        'Real Madrid', 'FC Barcelona', 'Atlético Madrid', 'Sevilla FC',
        'Valencia CF', 'Real Betis', 'Athletic Club', 'Real Sociedad',
        'Villarreal CF', 'Getafe CF'
    ],
    premier: [
        'Manchester United', 'Manchester City', 'Liverpool FC', 'Chelsea FC',
        'Arsenal FC', 'Tottenham Hotspur', 'Newcastle United', 'Aston Villa',
        'Brighton & Hove Albion', 'West Ham United'
    ],
    selecciones: [
        'España', 'Argentina', 'Brasil', 'Francia', 'Alemania',
        'Portugal', 'Inglaterra', 'Italia', 'Países Bajos', 'Bélgica',
        'Uruguay', 'Colombia', 'México', 'Croacia', 'Marruecos'
    ],
    otras: [
        'Paris Saint-Germain', 'Bayern Munich', 'Borussia Dortmund',
        'Inter Milan', 'AC Milan', 'Juventus', 'Benfica', 'Porto',
        'Ajax', 'Celtic FC'
    ]
};

// Estado del formulario
let formData = {
    league: '',
    team: '',
    size: '',
    patches: '',
    playerName: '',
    playerNumber: '',
    price: 29.99
};

// Carrito de items
let cart = [];

// Paso actual
let currentStep = 1;
const totalSteps = 6;

// ============================================
// INICIALIZACIÓN
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    updateProgressBar();
    setupEventListeners();
    setupCart();
    updateCartDisplay();
});

// ============================================
// EVENT LISTENERS
// ============================================
function setupEventListeners() {
    // Step 1: Liga
    const leagueButtons = document.querySelectorAll('#step1 .option-card');
    leagueButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            formData.league = this.getAttribute('data-value');
            selectOption(this);
            populateTeams();
            setTimeout(() => nextStep(), 300);
        });
    });

    // Step 3: Talla
    const sizeButtons = document.querySelectorAll('#step3 .option-card');
    sizeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            formData.size = this.getAttribute('data-value');
            selectOption(this);
            setTimeout(() => nextStep(), 300);
        });
    });

    // Step 4: Parches
    const patchButtons = document.querySelectorAll('#step4 .option-card');
    patchButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            formData.patches = this.getAttribute('data-value');
            selectOption(this);
            setTimeout(() => nextStep(), 300);
        });
    });

    // Step 2: Equipo (select)
    const teamSelect = document.getElementById('teamSelect');
    if (teamSelect) {
        teamSelect.addEventListener('change', function() {
            formData.team = this.value;
        });
    }

    // Step 5: Personalización
    const playerName = document.getElementById('playerName');
    const playerNumber = document.getElementById('playerNumber');
    
    if (playerName) {
        playerName.addEventListener('input', function() {
            formData.playerName = this.value.trim();
        });
    }
    
    if (playerNumber) {
        playerNumber.addEventListener('input', function() {
            formData.playerNumber = this.value.trim();
        });
    }
}

// ============================================
// NAVEGACIÓN ENTRE PASOS
// ============================================
function nextStep() {
    // Validación
    if (!validateCurrentStep()) {
        return;
    }

    // Ocultar paso actual
    document.getElementById(`step${currentStep}`).classList.remove('active');
    
    // Incrementar paso
    currentStep++;
    
    // Mostrar siguiente paso
    document.getElementById(`step${currentStep}`).classList.add('active');
    
    // Si es el último paso, mostrar resumen
    if (currentStep === totalSteps) {
        displaySummary();
    }
    
    // Actualizar barra de progreso
    updateProgressBar();
    
    // Scroll al top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function previousStep() {
    // Ocultar paso actual
    document.getElementById(`step${currentStep}`).classList.remove('active');
    
    // Decrementar paso
    currentStep--;
    
    // Mostrar paso anterior
    document.getElementById(`step${currentStep}`).classList.add('active');
    
    // Actualizar barra de progreso
    updateProgressBar();
    
    // Scroll al top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ============================================
// VALIDACIONES
// ============================================
function validateCurrentStep() {
    switch(currentStep) {
        case 1:
            if (!formData.league) {
                alert('Por favor, selecciona una liga');
                return false;
            }
            break;
        case 2:
            if (!formData.team) {
                alert('Por favor, selecciona un equipo');
                return false;
            }
            break;
        case 3:
            if (!formData.size) {
                alert('Por favor, selecciona una talla');
                return false;
            }
            break;
        case 4:
            if (!formData.patches) {
                alert('Por favor, indica si deseas parches');
                return false;
            }
            break;
    }
    return true;
}

// ============================================
// HELPERS
// ============================================
function selectOption(element) {
    // Remover selección anterior
    const siblings = element.parentElement.querySelectorAll('.option-card');
    siblings.forEach(sib => sib.classList.remove('selected'));
    
    // Seleccionar actual
    element.classList.add('selected');
}

function populateTeams() {
    const teamSelect = document.getElementById('teamSelect');
    const teams = teamsData[formData.league] || [];
    
    // Limpiar opciones anteriores
    teamSelect.innerHTML = '<option value="">Selecciona un equipo...</option>';
    
    // Añadir nuevas opciones
    teams.forEach(team => {
        const option = document.createElement('option');
        option.value = team;
        option.textContent = team;
        teamSelect.appendChild(option);
    });
}

function updateProgressBar() {
    const progressBar = document.getElementById('progressBar');
    const progress = (currentStep / totalSteps) * 100;
    progressBar.style.width = `${progress}%`;
}

// ============================================
// RESUMEN Y PRECIO
// ============================================
function displaySummary() {
    // Calcular precio
    let totalPrice = 29.99;
    
    if (formData.patches === 'si') {
        totalPrice += 5;
    }
    
    if (formData.playerName || formData.playerNumber) {
        totalPrice += 5;
    }
    
    formData.price = totalPrice;
    
    // Mostrar datos en resumen
    document.getElementById('summaryLeague').textContent = getLeagueName(formData.league);
    document.getElementById('summaryTeam').textContent = formData.team;
    document.getElementById('summarySize').textContent = formData.size;
    document.getElementById('summaryPatches').textContent = formData.patches === 'si' ? 'Sí' : 'No';
    
    // Imagen de la camiseta
    const imagePath = `img/camisetas/${formData.league}_${formatTeamName(formData.team)}.png`;
    document.getElementById('summaryImage').src = imagePath;
    document.getElementById('summaryImage').alt = formData.team;
    
    // Personalización
    const personalizationDetail = document.getElementById('personalizationDetail');
    const personalizationSummary = document.getElementById('summaryPersonalization');
    
    if (formData.playerName || formData.playerNumber) {
        personalizationDetail.style.display = 'flex';
        personalizationSummary.textContent = `${formData.playerName || ''} ${formData.playerNumber || ''}`.trim();
        document.getElementById('personalizationPrice').style.display = 'flex';
    } else {
        personalizationDetail.style.display = 'none';
        document.getElementById('personalizationPrice').style.display = 'none';
    }
    
    // Mostrar precio de parches
    if (formData.patches === 'si') {
        document.getElementById('patchesPrice').style.display = 'flex';
    } else {
        document.getElementById('patchesPrice').style.display = 'none';
    }
    
    // Precio total
    document.getElementById('totalPrice').textContent = `${totalPrice.toFixed(2)} €`;
    
    // Actualizar upsell según items en carrito
    updateUpsell();
}

function getLeagueName(leagueCode) {
    const names = {
        'laliga': 'LaLiga',
        'premier': 'Premier League',
        'selecciones': 'Selecciones Internacionales',
        'otras': 'Otras Ligas'
    };
    return names[leagueCode] || leagueCode;
}

function formatTeamName(teamName) {
    return teamName
        .toLowerCase()
        .replace(/\s+/g, '_')
        .replace(/[^a-z0-9_]/g, '');
}

// ============================================
// WHATSAPP
// ============================================
function sendToWhatsApp() {
    // Añadir item actual al carrito
    cart.push({...formData});
    
    // Construir mensaje
    let message = '¡Hola! Quiero comprar ';
    
    if (cart.length === 1) {
        message += 'una camiseta en Kickverse con estas características:\n\n';
    } else {
        message += `${cart.length} camisetas en Kickverse:\n\n`;
    }
    
    // Añadir cada item del carrito
    cart.forEach((item, index) => {
        message += `📌 Camiseta ${index + 1}:\n`;
        message += `- Liga: ${getLeagueName(item.league)}\n`;
        message += `- Equipo: ${item.team}\n`;
        message += `- Talla: ${item.size}\n`;
        message += `- Parches: ${item.patches === 'si' ? 'Sí' : 'No'}\n`;
        
        if (item.playerName || item.playerNumber) {
            message += `- Personalización: ${item.playerName || ''} ${item.playerNumber || ''}`.trim() + '\n';
        }
        
        message += `- Precio: ${item.price.toFixed(2)} €\n\n`;
    });
    
    // Calcular total y descuento si aplica
    let total = cart.reduce((sum, item) => sum + item.price, 0);
    
    if (cart.length >= 3) {
        const cheapest = Math.min(...cart.map(item => item.price));
        total -= cheapest;
        message += `🎁 ¡3ª camiseta GRATIS! Descuento: -${cheapest.toFixed(2)} €\n`;
    }
    
    message += `💰 Total: ${total.toFixed(2)} €\n\n`;
    message += '¿Me confirmáis disponibilidad? 🙌';
    
    // Codificar mensaje para URL
    const encodedMessage = encodeURIComponent(message);
    
    // Número de WhatsApp
    const phoneNumber = '34614299735';
    
    // Abrir WhatsApp
    const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodedMessage}`;
    window.open(whatsappUrl, '_blank');
}

// ============================================
// UPSELL Y CARRITO
// ============================================
function updateUpsell() {
    const upsellBox = document.getElementById('upsellBox');
    const cartCounter = document.getElementById('cartCounter');
    const itemCount = document.getElementById('itemCount');
    
    if (cart.length > 0) {
        cartCounter.style.display = 'block';
        itemCount.textContent = cart.length;
    }
    
    if (cart.length === 2) {
        upsellBox.innerHTML = `
            <p class="upsell-text">🔥 <strong>¡Ya tienes 2 camisetas! Si añades una tercera, te sale totalmente GRATIS.</strong></p>
            <button class="btn btn-outline" onclick="addAnotherItem()">
                ➕ Añadir tercera camiseta
            </button>
        `;
    } else if (cart.length >= 3) {
        upsellBox.innerHTML = `
            <p class="upsell-text">✅ <strong>¡Felicidades! Tu 3ª camiseta es GRATIS.</strong></p>
            <p style="margin-top: 1rem; color: var(--text-secondary);">La camiseta más barata se descontará automáticamente.</p>
        `;
    }
}

function addAnotherItem() {
    // Guardar item actual en el carrito
    cart.push({...formData});
    
    // Actualizar UI del carrito
    updateCartDisplay();
    updateCartCounter();
    
    // Reiniciar formulario
    resetForm();
    
    // Volver al paso 1
    document.getElementById(`step${currentStep}`).classList.remove('active');
    currentStep = 1;
    document.getElementById('step1').classList.add('active');
    updateProgressBar();
    
    // Scroll al top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function updateCartCounter() {
    const cartCounter = document.getElementById('cartCounter');
    const itemCount = document.getElementById('itemCount');
    
    cartCounter.style.display = 'block';
    itemCount.textContent = cart.length;
}

function resetForm() {
    formData = {
        league: '',
        team: '',
        size: '',
        patches: '',
        playerName: '',
        playerNumber: '',
        price: 29.99
    };
    
    // Limpiar selecciones visuales
    document.querySelectorAll('.option-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Limpiar inputs
    document.getElementById('playerName').value = '';
    document.getElementById('playerNumber').value = '';
    document.getElementById('teamSelect').value = '';
}

// ============================================
// CARRITO - UI
// ============================================
function setupCart() {
    const cartButton = document.getElementById('cartButton');
    const cartSidebar = document.getElementById('cartSidebar');
    const cartOverlay = document.getElementById('cartOverlay');
    const cartClose = document.getElementById('cartClose');
    
    if (cartButton) {
        cartButton.addEventListener('click', openCart);
    }
    
    if (cartClose) {
        cartClose.addEventListener('click', closeCart);
    }
    
    if (cartOverlay) {
        cartOverlay.addEventListener('click', closeCart);
    }
}

function openCart() {
    const cartSidebar = document.getElementById('cartSidebar');
    const cartOverlay = document.getElementById('cartOverlay');
    
    if (cartSidebar) cartSidebar.classList.add('open');
    if (cartOverlay) cartOverlay.classList.add('active');
}

function closeCart() {
    const cartSidebar = document.getElementById('cartSidebar');
    const cartOverlay = document.getElementById('cartOverlay');
    
    if (cartSidebar) cartSidebar.classList.remove('open');
    if (cartOverlay) cartOverlay.classList.remove('active');
}

function updateCartDisplay() {
    const cartBody = document.getElementById('cartBody');
    const cartFooter = document.getElementById('cartFooter');
    const cartCount = document.getElementById('cartCount');
    const cartBadge = document.getElementById('cartBadge');
    const itemCount = document.getElementById('itemCount');
    
    // Actualizar contador
    if (cartCount) cartCount.textContent = `(${cart.length})`;
    if (itemCount) itemCount.textContent = cart.length;
    
    // Badge del botón flotante
    if (cartBadge) {
        if (cart.length > 0) {
            cartBadge.style.display = 'flex';
            cartBadge.textContent = cart.length;
        } else {
            cartBadge.style.display = 'none';
        }
    }
    
    // Si no hay items
    if (cart.length === 0) {
        if (cartBody) {
            cartBody.innerHTML = `
                <div class="cart-empty">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="21" r="1"/>
                        <circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                    </svg>
                    <p>Tu carrito está vacío</p>
                    <p style="font-size: 0.875rem; margin-top: 0.5rem;">Añade tu primera camiseta</p>
                </div>
            `;
        }
        if (cartFooter) cartFooter.style.display = 'none';
        return;
    }
    
    // Mostrar items del carrito
    if (cartBody) {
        cartBody.innerHTML = cart.map((item, index) => `
            <div class="cart-item">
                <img src="img/camisetas/${item.league}_${formatTeamName(item.team)}.png" 
                     alt="${item.team}" 
                     class="cart-item-image"
                     onerror="this.src='img/hero-jersey.png'">
                <div class="cart-item-details">
                    <div class="cart-item-title">${item.team}</div>
                    <div class="cart-item-info">
                        ${getLeagueName(item.league)} • Talla ${item.size}
                    </div>
                    ${item.playerName || item.playerNumber ? `
                        <div class="cart-item-info">
                            ${item.playerName || ''} ${item.playerNumber || ''}
                        </div>
                    ` : ''}
                    <div class="cart-item-price">${item.price.toFixed(2)} €</div>
                </div>
                <button class="cart-item-remove" onclick="removeFromCart(${index})">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
        `).join('');
    }
    
    // Calcular totales
    const subtotal = cart.reduce((sum, item) => sum + item.price, 0);
    let discount = 0;
    
    if (cart.length >= 3) {
        discount = Math.min(...cart.map(item => item.price));
    }
    
    const total = subtotal - discount;
    
    // Actualizar resumen
    if (cartFooter) {
        cartFooter.style.display = 'block';
        
        const cartSubtotal = document.getElementById('cartSubtotal');
        const cartDiscount = document.getElementById('cartDiscount');
        const cartDiscountRow = document.getElementById('cartDiscountRow');
        const cartTotal = document.getElementById('cartTotal');
        
        if (cartSubtotal) cartSubtotal.textContent = `${subtotal.toFixed(2)} €`;
        if (cartTotal) cartTotal.textContent = `${total.toFixed(2)} €`;
        
        if (discount > 0) {
            if (cartDiscountRow) cartDiscountRow.style.display = 'flex';
            if (cartDiscount) cartDiscount.textContent = `-${discount.toFixed(2)} €`;
        } else {
            if (cartDiscountRow) cartDiscountRow.style.display = 'none';
        }
    }
}

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCartDisplay();
    updateCartCounter();
    updateUpsell();
}

// ============================================
// EXPORTAR FUNCIONES GLOBALES
// ============================================
window.nextStep = nextStep;
window.previousStep = previousStep;
window.sendToWhatsApp = sendToWhatsApp;
window.addAnotherItem = addAnotherItem;
window.removeFromCart = removeFromCart;
window.openCart = openCart;
window.closeCart = closeCart;