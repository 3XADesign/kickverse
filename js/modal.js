// ============================================
//  KICKVERSE MODAL - Personalization System
//  Handles product customization modal
// ============================================

// Constants
const WHATSAPP_NUMBER = '34614299735';
const BASE_PRICE = 29.99;
const PATCHES_PRICE = 5.00;

// Discount codes - Will be loaded from JSON
let DISCOUNT_CODES = {};

// Modal state
let modalState = {
    productName: '',
    productType: '',
    productLeague: '',
    productImage: '',
    basePrice: BASE_PRICE,
    size: '',
    number: '',
    dorsal: '',
    patches: false,
    discountCode: '',
    discountAmount: 0,
    paymentMethod: ''
};

// DOM Elements
const modal = document.getElementById('personalizationModal');
const closeModalBtn = document.getElementById('closeModal');
const form = document.getElementById('personalizationForm');
const modalProductImage = document.getElementById('modalProductImage');
const modalProductName = document.getElementById('modalProductName');
const modalProductType = document.getElementById('modalProductType');
const modalProductLeague = document.getElementById('modalProductLeague');

// Size buttons
const sizeButtons = document.querySelectorAll('.size-btn');
const selectedSizeInput = document.getElementById('selectedSize');

// Form inputs
const numberInput = document.getElementById('number');
const dorsalInput = document.getElementById('dorsal');
const patchesCheckbox = document.getElementById('patches');
const discountCodeInput = document.getElementById('discountCode');
const applyDiscountBtn = document.getElementById('applyDiscount');
const discountMessage = document.getElementById('discountMessage');

// Payment methods
const paymentMethods = document.querySelectorAll('.payment-method');
const selectedPaymentInput = document.getElementById('selectedPayment');

// Price elements
const basePriceEl = document.getElementById('basePrice');
const patchesRow = document.getElementById('patchesRow');
const discountRow = document.getElementById('discountRow');
const discountAmountEl = document.getElementById('discountAmount');
const totalPriceEl = document.getElementById('totalPrice');

// ============= Load Discount Codes ============= //
async function loadDiscountCodes() {
    try {
        const response = await fetch('data/discount-codes.json');
        if (!response.ok) throw new Error('Failed to load discount codes');
        const data = await response.json();
        DISCOUNT_CODES = data.codes;
    } catch (error) {
        // Silently fail - no codes available
        console.warn('Discount codes not available');
        DISCOUNT_CODES = {};
    }
}

// ============= Initialize ============= //
async function initModal() {
    // Load discount codes first
    await loadDiscountCodes();
    
    // Setup event listeners
    setupSizeSelection();
    setupPatchesToggle();
    setupDiscountCode();
    setupPaymentMethods();
    setupFormSubmit();
    setupModalClose();
    setupPersonalizeButtons();
}

// ============= Setup Personalize Buttons ============= //
function setupPersonalizeButtons() {
    // Add click event to all personalize buttons
    document.addEventListener('click', (e) => {
        if (e.target.closest('.btn-personalize')) {
            e.preventDefault();
            const btn = e.target.closest('.btn-personalize');
            
            // Get product data from button attributes
            const productData = {
                name: btn.dataset.team,
                type: btn.dataset.type,
                image: btn.dataset.image,
                league: btn.dataset.league,
                price: parseFloat(btn.dataset.price) || BASE_PRICE
            };
            
            openModal(productData);
        }
    });
}

// ============= Open Modal ============= //
function openModal(productData) {
    // Reset state
    resetModalState();
    
    // Set product data
    modalState.productName = productData.name;
    modalState.productType = productData.type;
    modalState.productLeague = productData.league;
    modalState.productImage = productData.image;
    modalState.basePrice = productData.price;
    
    // Update modal UI
    modalProductImage.src = productData.image;
    modalProductImage.alt = productData.name;
    modalProductName.textContent = productData.name;
    modalProductType.textContent = productData.type;
    modalProductLeague.textContent = getLeagueName(productData.league);
    basePriceEl.textContent = `${productData.price.toFixed(2)}€`;
    
    // Show modal
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    // Update total price
    updateTotalPrice();
}

// ============= Close Modal ============= //
function closeModal() {
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

function setupModalClose() {
    // Close button
    closeModalBtn.addEventListener('click', closeModal);
    
    // Click outside modal
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    // Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeModal();
        }
    });
}

// ============= Reset Modal State ============= //
function resetModalState() {
    modalState = {
        productName: '',
        productType: '',
        productLeague: '',
        productImage: '',
        basePrice: BASE_PRICE,
        size: '',
        number: '',
        dorsal: '',
        patches: false,
        discountCode: '',
        discountAmount: 0,
        paymentMethod: ''
    };
    
    // Reset form
    form.reset();
    
    // Reset size selection
    sizeButtons.forEach(btn => btn.classList.remove('active'));
    selectedSizeInput.value = '';
    
    // Reset patches
    patchesCheckbox.checked = false;
    patchesRow.style.display = 'none';
    
    // Reset discount
    discountCodeInput.value = '';
    discountMessage.textContent = '';
    discountMessage.className = 'discount-message';
    discountRow.style.display = 'none';
    
    // Reset payment
    paymentMethods.forEach(method => method.classList.remove('active'));
    selectedPaymentInput.value = '';
}

// ============= Size Selection ============= //
function setupSizeSelection() {
    sizeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const size = btn.dataset.size;
            
            // Remove active from all buttons
            sizeButtons.forEach(b => b.classList.remove('active'));
            
            // Add active to clicked button
            btn.classList.add('active');
            
            // Update state and hidden input
            modalState.size = size;
            selectedSizeInput.value = size;
        });
    });
}

// ============= Patches Toggle ============= //
function setupPatchesToggle() {
    patchesCheckbox.addEventListener('change', (e) => {
        modalState.patches = e.target.checked;
        patchesRow.style.display = e.target.checked ? 'flex' : 'none';
        
        // Re-validate discount if one is applied
        if (modalState.discountCode) {
            const discount = DISCOUNT_CODES[modalState.discountCode];
            let currentTotal = modalState.basePrice;
            if (modalState.patches) {
                currentTotal += PATCHES_PRICE;
            }
            
            // If total is now below minimum, remove discount
            if (discount && discount.minPurchase && currentTotal < discount.minPurchase) {
                modalState.discountCode = '';
                modalState.discountAmount = 0;
                discountRow.style.display = 'none';
                discountCodeInput.value = '';
                showDiscountMessage(`El total debe ser superior a ${discount.minPurchase}€ para usar este código`, 'error');
            }
        }
        
        updateTotalPrice();
    });
}

// ============= Discount Code ============= //
function setupDiscountCode() {
    applyDiscountBtn.addEventListener('click', applyDiscount);
    
    // Allow Enter key to apply discount
    discountCodeInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            applyDiscount();
        }
    });
}

function applyDiscount() {
    const code = discountCodeInput.value.trim().toUpperCase();
    
    if (!code) {
        showDiscountMessage('Introduce un código de descuento', 'error');
        return;
    }
    
    const discount = DISCOUNT_CODES[code];
    
    if (!discount) {
        modalState.discountCode = '';
        modalState.discountAmount = 0;
        discountRow.style.display = 'none';
        showDiscountMessage('Código no válido', 'error');
        updateTotalPrice();
        return;
    }
    
    // Calculate current total (base + patches)
    let currentTotal = modalState.basePrice;
    if (modalState.patches) {
        currentTotal += PATCHES_PRICE;
    }
    
    // Check minimum purchase requirement
    if (discount.minPurchase && currentTotal < discount.minPurchase) {
        showDiscountMessage(`Este código requiere una compra mínima de ${discount.minPurchase}€`, 'error');
        modalState.discountCode = '';
        modalState.discountAmount = 0;
        discountRow.style.display = 'none';
        updateTotalPrice();
        return;
    }
    
    // Apply discount
    modalState.discountCode = code;
    
    if (discount.type === 'percentage') {
        modalState.discountAmount = (modalState.basePrice * discount.amount) / 100;
        showDiscountMessage(`¡Código aplicado! ${discount.amount}% de descuento`, 'success');
    } else {
        modalState.discountAmount = discount.amount;
        showDiscountMessage(`¡Código aplicado! ${discount.amount}€ de descuento`, 'success');
    }
    
    discountRow.style.display = 'flex';
    discountAmountEl.textContent = `-${modalState.discountAmount.toFixed(2)}€`;
    updateTotalPrice();
}

function showDiscountMessage(message, type) {
    discountMessage.textContent = message;
    discountMessage.className = `discount-message ${type}`;
}

// ============= Payment Methods ============= //
function setupPaymentMethods() {
    paymentMethods.forEach(method => {
        method.addEventListener('click', () => {
            const paymentType = method.dataset.method;
            
            // Remove active from all methods
            paymentMethods.forEach(m => m.classList.remove('active'));
            
            // Add active to clicked method
            method.classList.add('active');
            
            // Update state and hidden input
            modalState.paymentMethod = paymentType;
            selectedPaymentInput.value = paymentType;
        });
    });
}

// ============= Update Total Price ============= //
function updateTotalPrice() {
    let total = modalState.basePrice;
    
    // Add patches price
    if (modalState.patches) {
        total += PATCHES_PRICE;
    }
    
    // Subtract discount
    total -= modalState.discountAmount;
    
    // Ensure total is not negative
    total = Math.max(0, total);
    
    // Update UI
    totalPriceEl.textContent = `${total.toFixed(2)}€`;
}

// ============= Form Submit ============= //
function setupFormSubmit() {
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        
        // Validate required fields
        if (!validateForm()) {
            return;
        }
        
        // Update state from inputs
        modalState.number = numberInput.value.trim();
        modalState.dorsal = dorsalInput.value.trim().toUpperCase();
        
        // Send to WhatsApp
        sendToWhatsApp();
    });
}

function validateForm() {
    // Check size
    if (!modalState.size) {
        alert('Por favor, selecciona una talla');
        return false;
    }
    
    // Check payment method
    if (!modalState.paymentMethod) {
        alert('Por favor, selecciona un método de pago');
        return false;
    }
    
    return true;
}

// ============= Send to WhatsApp ============= //
function sendToWhatsApp() {
    // Calculate total
    let total = modalState.basePrice;
    if (modalState.patches) total += PATCHES_PRICE;
    total -= modalState.discountAmount;
    total = Math.max(0, total);
    
    // Build message
    let message = `¡Hola! Quiero hacer un pedido:\n\n`;
    message += `🏆 *Equipo:* ${modalState.productName}\n`;
    message += `👕 *Tipo:* ${modalState.productType}\n`;
    message += `🏅 *Liga:* ${getLeagueName(modalState.productLeague)}\n`;
    message += `📏 *Talla:* ${modalState.size}\n`;
    
    if (modalState.number) {
        message += `🔢 *Número:* ${modalState.number}\n`;
    }
    
    if (modalState.dorsal) {
        message += `📛 *Dorsal:* ${modalState.dorsal}\n`;
    }
    
    message += `⭐ *Parches:* ${modalState.patches ? 'Sí (+5€)' : 'No'}\n`;
    
    if (modalState.discountCode) {
        message += `💰 *Código descuento:* ${modalState.discountCode}\n`;
    }
    
    message += `💳 *Método de pago:* ${modalState.paymentMethod}\n\n`;
    message += `💵 *TOTAL:* ${total.toFixed(2)}€`;
    
    // Encode message
    const encodedMessage = encodeURIComponent(message);
    
    // Build WhatsApp URL
    const whatsappURL = `https://wa.me/${WHATSAPP_NUMBER}?text=${encodedMessage}`;
    
    // Open WhatsApp
    window.open(whatsappURL, '_blank');
    
    // Close modal after a short delay
    setTimeout(() => {
        closeModal();
    }, 500);
}

// ============= Helper Functions ============= //
function getLeagueName(leagueKey) {
    const leagueNames = {
        'laliga': 'LaLiga',
        'premier': 'Premier League',
        'seriea': 'Serie A',
        'bundesliga': 'Bundesliga',
        'ligue1': 'Ligue 1',
        'selecciones': 'Selecciones'
    };
    return leagueNames[leagueKey] || leagueKey;
}

// ============= Initialize on Load ============= //
document.addEventListener('DOMContentLoaded', initModal);
