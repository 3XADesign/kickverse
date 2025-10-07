// ============================================
// KICKVERSE - SISTEMA DE CARRITO COMPARTIDO
// ============================================

class ShoppingCart {
    constructor() {
        this.items = this.loadCart();
        this.init();
    }

    // Inicializar el carrito
    init() {
        this.updateDisplay();
        this.setupEventListeners();
    }

    // Cargar carrito desde localStorage
    loadCart() {
        const saved = localStorage.getItem('kickverse_cart');
        return saved ? JSON.parse(saved) : [];
    }

    // Guardar carrito en localStorage
    saveCart() {
        localStorage.setItem('kickverse_cart', JSON.stringify(this.items));
        this.updateDisplay();
    }

    // Añadir item al carrito
    addItem(item) {
        const newItem = {
            id: Date.now(),
            league: item.league || '',
            team: item.team || '',
            type: item.type || 'Local',
            size: item.size || 'M',
            playerName: item.playerName || '',
            playerNumber: item.playerNumber || '',
            patches: item.patches || 'No',
            price: parseFloat(item.price) || 29.99,
            image: item.image || '',
            timestamp: new Date().toISOString()
        };

        this.items.push(newItem);
        this.saveCart();
        this.showNotification('✅ Camiseta añadida al carrito');
        return newItem;
    }

    // Eliminar item del carrito
    removeItem(itemId) {
        this.items = this.items.filter(item => item.id !== itemId);
        this.saveCart();
        this.showNotification('🗑️ Camiseta eliminada del carrito');
    }

    // Limpiar carrito
    clearCart() {
        this.items = [];
        this.saveCart();
    }

    // Obtener total de items
    getItemCount() {
        return this.items.length;
    }

    // Calcular subtotal
    getSubtotal() {
        return this.items.reduce((sum, item) => sum + item.price, 0);
    }

    // Calcular descuento 3x2
    getDiscount() {
        const count = this.getItemCount();
        if (count >= 3) {
            // Por cada 3 items, el más barato es gratis
            const groups = Math.floor(count / 3);
            
            // Ordenar items por precio ascendente
            const sortedPrices = [...this.items]
                .map(item => item.price)
                .sort((a, b) => a - b);
            
            // Sumar el precio de los items más baratos que serán gratis
            let discount = 0;
            for (let i = 0; i < groups; i++) {
                discount += sortedPrices[i];
            }
            
            return discount;
        }
        return 0;
    }

    // Calcular total final
    getTotal() {
        return this.getSubtotal() - this.getDiscount();
    }

    // Actualizar visualización del carrito
    updateDisplay() {
        const count = this.getItemCount();
        const subtotal = this.getSubtotal();
        const discount = this.getDiscount();
        const total = this.getTotal();

        // Actualizar contador en el header
        const cartCountElements = document.querySelectorAll('[data-cart-count]');
        cartCountElements.forEach(el => {
            el.textContent = count;
            el.style.display = count > 0 ? 'inline-block' : 'none';
        });

        // Actualizar contador en el sidebar
        const cartCountSidebar = document.getElementById('cartCount');
        if (cartCountSidebar) {
            cartCountSidebar.textContent = `(${count})`;
        }

        // Actualizar body del carrito
        const cartBody = document.getElementById('cartBody');
        if (cartBody) {
            if (count === 0) {
                cartBody.innerHTML = `
                    <div class="cart-empty">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="9" cy="21" r="1"/>
                            <circle cx="20" cy="21" r="1"/>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                        </svg>
                        <p>Tu carrito está vacío</p>
                    </div>
                `;
            } else {
                cartBody.innerHTML = this.items.map(item => this.renderCartItem(item)).join('');
                this.attachItemEventListeners();
            }
        }

        // Actualizar footer del carrito
        const cartFooter = document.getElementById('cartFooter');
        if (cartFooter) {
            cartFooter.style.display = count > 0 ? 'block' : 'none';
        }

        // Actualizar precios
        const cartSubtotal = document.getElementById('cartSubtotal');
        if (cartSubtotal) {
            cartSubtotal.textContent = `${subtotal.toFixed(2).replace('.', ',')} €`;
        }

        const cartDiscount = document.getElementById('cartDiscount');
        const cartDiscountRow = document.getElementById('cartDiscountRow');
        if (cartDiscount && cartDiscountRow) {
            if (discount > 0) {
                cartDiscountRow.style.display = 'flex';
                cartDiscount.textContent = `-${discount.toFixed(2).replace('.', ',')} €`;
            } else {
                cartDiscountRow.style.display = 'none';
            }
        }

        const cartTotal = document.getElementById('cartTotal');
        if (cartTotal) {
            cartTotal.textContent = `${total.toFixed(2).replace('.', ',')} €`;
        }

        // Actualizar badge de descuento
        this.updateDiscountBadge();
    }

    // Renderizar item del carrito
    renderCartItem(item) {
        const customization = item.playerName || item.playerNumber ? 
            `<div class="cart-item-custom">
                ${item.playerName ? `<span>👤 ${item.playerName}</span>` : ''}
                ${item.playerNumber ? `<span>#️⃣ ${item.playerNumber}</span>` : ''}
            </div>` : '';

        return `
            <div class="cart-item" data-item-id="${item.id}">
                <div class="cart-item-image">
                    ${item.image ? 
                        `<img src="${item.image}" alt="${item.team}">` : 
                        `<div class="cart-item-placeholder">👕</div>`
                    }
                </div>
                <div class="cart-item-details">
                    <h4 class="cart-item-title">${item.team || 'Camiseta'}</h4>
                    <p class="cart-item-info">${item.league || ''} · ${item.type || 'Local'} · Talla ${item.size || 'M'}</p>
                    ${customization}
                    <p class="cart-item-price">${item.price.toFixed(2).replace('.', ',')} €</p>
                </div>
                <button class="cart-item-remove" data-item-id="${item.id}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    </svg>
                </button>
            </div>
        `;
    }

    // Event listeners para los items del carrito
    attachItemEventListeners() {
        const removeButtons = document.querySelectorAll('.cart-item-remove');
        removeButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const itemId = parseInt(e.currentTarget.getAttribute('data-item-id'));
                this.removeItem(itemId);
            });
        });
    }

    // Actualizar badge de descuento 3x2
    updateDiscountBadge() {
        const count = this.getItemCount();
        const badges = document.querySelectorAll('.discount-badge');
        
        badges.forEach(badge => {
            if (count === 0) {
                badge.textContent = '🎁 Oferta 3x2 activa';
                badge.classList.remove('active');
            } else if (count === 1) {
                badge.textContent = '🎁 Añade 2 más y el más barato ¡GRATIS!';
                badge.classList.add('active');
            } else if (count === 2) {
                badge.textContent = '🎁 ¡Añade 1 más y el más barato GRATIS!';
                badge.classList.add('active');
            } else if (count >= 3) {
                const freeItems = Math.floor(count / 3);
                badge.textContent = `🎉 ¡${freeItems} camiseta${freeItems > 1 ? 's' : ''} GRATIS aplicada!`;
                badge.classList.add('active', 'applied');
            }
        });
    }

    // Setup event listeners generales
    setupEventListeners() {
        // Botón abrir carrito
        const cartButtons = document.querySelectorAll('[data-cart-open]');
        cartButtons.forEach(btn => {
            btn.addEventListener('click', () => this.openCart());
        });

        // Botón cerrar carrito
        const closeButtons = document.querySelectorAll('[data-cart-close], #cartClose');
        closeButtons.forEach(btn => {
            btn.addEventListener('click', () => this.closeCart());
        });

        // Overlay
        const overlay = document.getElementById('cartOverlay');
        if (overlay) {
            overlay.addEventListener('click', () => this.closeCart());
        }

        // Botón finalizar compra
        const checkoutBtn = document.getElementById('checkoutBtn');
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', () => this.checkout());
        }
    }

    // Abrir carrito
    openCart() {
        const sidebar = document.getElementById('cartSidebar');
        const overlay = document.getElementById('cartOverlay');
        
        if (sidebar) sidebar.classList.add('active');
        if (overlay) overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // Cerrar carrito
    closeCart() {
        const sidebar = document.getElementById('cartSidebar');
        const overlay = document.getElementById('cartOverlay');
        
        if (sidebar) sidebar.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    // Finalizar compra
    checkout() {
        if (this.getItemCount() === 0) {
            this.showNotification('⚠️ El carrito está vacío');
            return;
        }

        // Aquí puedes redirigir a una página de checkout
        alert('🎉 Funcionalidad de pago en desarrollo\n\n' + 
              `Total de items: ${this.getItemCount()}\n` +
              `Total a pagar: ${this.getTotal().toFixed(2)} €`);
    }

    // Mostrar notificación
    showNotification(message) {
        // Crear notificación
        const notification = document.createElement('div');
        notification.className = 'cart-notification';
        notification.textContent = message;
        document.body.appendChild(notification);

        // Mostrar
        setTimeout(() => notification.classList.add('show'), 10);

        // Ocultar y eliminar
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
}

// Instancia global del carrito
let shoppingCart;

// Inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        shoppingCart = new ShoppingCart();
    });
} else {
    shoppingCart = new ShoppingCart();
}
