const DROP_STORAGE_KEY = 'kickverse_drops_state_v1';
const MAX_DROPS = 100;
const INITIAL_REMAINING = 37;
const ANIMATION_STEPS = 28;
const ANIMATION_INTERVAL = 110;
const LEGENDARY_CONFETTI_COUNT = 80;

const rarityConfig = {
    common: {
        label: 'Común',
        badgeClass: 'rarity-common',
        whatsappTag: 'Común',
        weight: 62
    },
    rare: {
        label: 'Rara',
        badgeClass: 'rarity-rare',
        whatsappTag: 'Rara',
        weight: 30
    },
    legendary: {
        label: 'Legendaria',
        badgeClass: 'rarity-legendary',
        whatsappTag: 'Legendaria',
        weight: 8
    }
};

const dropPool = [
    {
        name: 'Real Sociedad 24/25',
        rarity: 'common',
        image: './img/camisetas/laliga_realsociedad_local.png',
        whatsappName: 'Real Sociedad 24/25'
    },
    {
        name: 'Aston Villa 24/25',
        rarity: 'common',
        image: './img/camisetas/premier_astonvilla_local.png',
        whatsappName: 'Aston Villa 24/25'
    },
    {
        name: 'Olympique Lyon 24/25',
        rarity: 'common',
        image: './img/camisetas/ligue1_lyon_local.png',
        whatsappName: 'Olympique Lyon 24/25'
    },
    {
        name: 'Benfica 24/25',
        rarity: 'common',
        image: './img/camisetas/primeira_benfica_local.png',
        whatsappName: 'SL Benfica 24/25'
    },
    {
        name: 'Atlético de Madrid 24/25',
        rarity: 'rare',
        image: './img/camisetas/laliga_atletico_local.png',
        whatsappName: 'Atlético de Madrid 24/25'
    },
    {
        name: 'Arsenal 24/25',
        rarity: 'rare',
        image: './img/camisetas/premier_arsenal_local.png',
        whatsappName: 'Arsenal 24/25'
    },
    {
        name: 'Juventus Retro 1997',
        rarity: 'rare',
        image: './img/camisetas/seriea_juventus_local.png',
        whatsappName: 'Juventus Retro 1997'
    },
    {
        name: 'Inter Milano Third',
        rarity: 'rare',
        image: './img/camisetas/seriea_inter_local.png',
        whatsappName: 'Inter Milano Third'
    },
    {
        name: 'Real Madrid 24/25',
        rarity: 'legendary',
        image: './img/camisetas/laliga_madrid_local.png',
        whatsappName: 'Real Madrid 24/25'
    },
    {
        name: 'FC Barcelona 24/25',
        rarity: 'legendary',
        image: './img/camisetas/laliga_barcelona_local.png',
        whatsappName: 'FC Barcelona 24/25'
    },
    {
        name: 'Argentina 1986',
        rarity: 'legendary',
        image: './img/camisetas/selecciones_argentina_local.png',
        whatsappName: 'Argentina 1986'
    },
    {
        name: 'Brasil 2002',
        rarity: 'legendary',
        image: './img/camisetas/selecciones_brasil_local.png',
        whatsappName: 'Brasil 2002'
    }
];

const stateDefaults = {
    remaining: INITIAL_REMAINING,
    opened: 0,
    counters: {
        common: 0,
        rare: 0,
        legendary: 0
    }
};

let dropState = { ...stateDefaults };
let isAnimating = false;
let animationIntervalRef = null;

const elements = {
    remainingLabel: document.getElementById('drops-remaining'),
    heroRemaining: document.getElementById('hero-remaining'),
    limitedNote: document.getElementById('limitedNote'),
    statsRemaining: document.getElementById('stats-remaining'),
    statsOpened: document.getElementById('stats-opened'),
    statsLegendary: document.getElementById('stats-legendary'),
    progressCommon: document.getElementById('progress-common'),
    progressRare: document.getElementById('progress-rare'),
    progressLegendary: document.getElementById('progress-legendary'),
    heroLegendary: document.getElementById('hero-legendary'),
    openButton: document.getElementById('openDropButton'),
    previewCards: Array.from(document.querySelectorAll('.preview-card')),
    sizeSelect: document.getElementById('drop-size'),
    modal: document.getElementById('resultModal'),
    resultTitle: document.getElementById('resultTitle'),
    resultRarity: document.getElementById('resultRarity'),
    resultImage: document.getElementById('resultImage'),
    resultName: document.getElementById('resultName'),
    resultWhatsapp: document.getElementById('resultWhatsapp'),
    closeModal: document.getElementById('closeModal'),
    playAgain: document.getElementById('playAgainBtn'),
    timer: document.getElementById('drop-timer'),
    confetti: document.getElementById('confetti'),
    dropCase: document.getElementById('dropCase'),
    cursorDot: null,
    cursorRing: null
};

function loadState() {
    try {
        const stored = localStorage.getItem(DROP_STORAGE_KEY);
        if (stored) {
            const parsed = JSON.parse(stored);
            dropState = {
                ...stateDefaults,
                ...parsed,
                counters: { ...stateDefaults.counters, ...(parsed.counters || {}) }
            };
        } else {
            dropState = { ...stateDefaults };
        }
    } catch (error) {
        console.warn('No se pudo cargar el estado de drops, se usará el valor por defecto.', error);
        dropState = { ...stateDefaults };
    }
}

function saveState() {
    try {
        localStorage.setItem(DROP_STORAGE_KEY, JSON.stringify(dropState));
    } catch (error) {
        console.warn('No se pudo guardar el estado de drops.', error);
    }
}

function updateUI() {
    const { remaining, opened, counters } = dropState;
    elements.remainingLabel.textContent = remaining;
    elements.heroRemaining.textContent = remaining;
    elements.statsRemaining.textContent = remaining;
    elements.statsOpened.textContent = opened;
    elements.statsLegendary.textContent = counters.legendary;
    elements.heroLegendary.textContent = counters.legendary;
    elements.limitedNote.textContent = `Quedan ${remaining} drops antes del reinicio`;

    const totalWins = Math.max(opened, 1);
    elements.progressCommon.style.width = `${Math.min((counters.common / totalWins) * 100, 100)}%`;
    elements.progressRare.style.width = `${Math.min((counters.rare / totalWins) * 100, 100)}%`;
    elements.progressLegendary.style.width = `${Math.min((counters.legendary / totalWins) * 100, 100)}%`;

    elements.openButton.disabled = remaining <= 0;
}

function weightedRandomItem() {
    const totalWeight = dropPool.reduce((sum, item) => sum + rarityConfig[item.rarity].weight, 0);
    const random = Math.random() * totalWeight;
    let cumulative = 0;

    for (const item of dropPool) {
        cumulative += rarityConfig[item.rarity].weight;
        if (random <= cumulative) {
            return item;
        }
    }
    return dropPool[dropPool.length - 1];
}

function setPreviewCard(card, item, highlight = false) {
    const img = card.querySelector('img');
    const nameEl = card.querySelector('.preview-name');
    const rarityEl = card.querySelector('.preview-rarity');

    img.src = item.image;
    img.alt = item.name;
    img.onerror = () => {
        img.onerror = null;
        img.src = './img/hero-jersey.png';
    };
    nameEl.textContent = item.name;
    rarityEl.textContent = rarityConfig[item.rarity].label;

    card.classList.toggle('active', highlight);
    card.classList.toggle('final', false);
    card.classList.toggle('legendary', item.rarity === 'legendary');
}

function playScrollAnimation(resultItem) {
    let index = Math.floor(Math.random() * dropPool.length);
    let iterations = 0;

    animationIntervalRef = setInterval(() => {
        elements.previewCards.forEach((card, slot) => {
            const poolIndex = (index + slot) % dropPool.length;
            const item = dropPool[poolIndex];
            const isCenter = slot === Math.floor(elements.previewCards.length / 2);
            setPreviewCard(card, item, isCenter);
        });

        index = (index + 1) % dropPool.length;
        iterations += 1;

        if (iterations >= ANIMATION_STEPS) {
            clearInterval(animationIntervalRef);
            revealResult(resultItem);
        }
    }, ANIMATION_INTERVAL);
}

function createWhatsappLink(itemName, size) {
    const base = 'https://wa.me/34614299735?text=';
    const message = `Hola! Acabo de abrir un Drop en Kickverse y gané:%0A- Camiseta: ${itemName}%0A- Talla: ${size}%0A¿Puedo confirmarla ya?`;
    return base + encodeURIComponent(message);
}

function revealResult(item) {
    const centerCard = elements.previewCards[Math.floor(elements.previewCards.length / 2)];
    setPreviewCard(centerCard, item, true);
    centerCard.classList.add('final');
    if (item.rarity === 'legendary') {
        centerCard.classList.add('legendary');
        spawnConfetti();
    }

    triggerModal(item);
    isAnimating = false;
    elements.openButton.disabled = dropState.remaining <= 0;
}

function triggerModal(item) {
    const rarity = rarityConfig[item.rarity];
    const size = elements.sizeSelect.value;

    elements.resultTitle.textContent = '¡Has desbloqueado una recompensa!';
    elements.resultRarity.textContent = rarity.label;
    elements.resultRarity.className = `result-rarity ${rarity.badgeClass}`;
    elements.resultImage.src = item.image;
    elements.resultName.textContent = `${item.name} · Talla ${size}`;
    elements.resultWhatsapp.href = createWhatsappLink(item.whatsappName, size);

    elements.modal.classList.add('active');
}

function closeModal() {
    elements.modal.classList.remove('active');
}

function spawnConfetti() {
    if (!elements.confetti) return;
    elements.confetti.innerHTML = '';

    for (let i = 0; i < LEGENDARY_CONFETTI_COUNT; i += 1) {
        const piece = document.createElement('span');
        piece.className = 'confetti-piece';
        piece.style.left = `${Math.random() * 100}%`;
        piece.style.background = Math.random() > 0.5 ? 'var(--color-lava)' : 'var(--color-secondary)';
        piece.style.animationDelay = `${Math.random() * 0.5}s`;
        piece.style.transform = `rotate(${Math.random() * 360}deg)`;
        elements.confetti.appendChild(piece);
    }

    setTimeout(() => {
        if (elements.confetti) {
            elements.confetti.innerHTML = '';
        }
    }, 2400);
}

function updateStatsFor(item) {
    dropState.opened += 1;
    dropState.counters[item.rarity] += 1;
    dropState.remaining = Math.max(dropState.remaining - 1, 0);
    saveState();
    updateUI();
}

function startDrop() {
    if (isAnimating || dropState.remaining <= 0) {
        return;
    }

    isAnimating = true;
    elements.openButton.disabled = true;
    elements.dropCase.classList.add('active');

    const result = weightedRandomItem();
    updateStatsFor(result);
    playScrollAnimation(result);

    setTimeout(() => {
        elements.dropCase.classList.remove('active');
    }, ANIMATION_STEPS * ANIMATION_INTERVAL + 400);
}

function handleTimer() {
    const DURATION_MS = (12 * 60 * 60 + 36 * 60 + 12) * 1000;
    const start = Date.now();

    setInterval(() => {
        const elapsed = Date.now() - start;
        const remaining = Math.max(DURATION_MS - elapsed, 0);

        const hours = Math.floor(remaining / (1000 * 60 * 60));
        const minutes = Math.floor((remaining % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((remaining % (1000 * 60)) / 1000);

        elements.timer.textContent = `${hours.toString().padStart(2, '0')}h ${minutes
            .toString()
            .padStart(2, '0')}m ${seconds.toString().padStart(2, '0')}s`;
    }, 1000);
}

function setupCursor() {
    if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
        document.documentElement.classList.add('touch-device');
        return;
    }

    const dot = document.createElement('div');
    dot.className = 'cursor-dot';
    const ring = document.createElement('div');
    ring.className = 'cursor-ring';
    document.body.appendChild(dot);
    document.body.appendChild(ring);

    elements.cursorDot = dot;
    elements.cursorRing = ring;

    window.addEventListener('mousemove', (event) => {
        const { clientX, clientY } = event;
        dot.style.transform = `translate(${clientX}px, ${clientY}px)`;
        ring.style.transform = `translate(${clientX - 16}px, ${clientY - 16}px)`;
    });
}

function attachEvents() {
    elements.openButton.addEventListener('click', startDrop);
    elements.closeModal.addEventListener('click', closeModal);
    elements.playAgain.addEventListener('click', () => {
        closeModal();
        if (dropState.remaining > 0) {
            startDrop();
        }
    });
    elements.modal.addEventListener('click', (event) => {
        if (event.target === elements.modal) {
            closeModal();
        }
    });
}

function initPreview() {
    elements.previewCards.forEach((card, index) => {
        const item = dropPool[index % dropPool.length];
        const highlight = index === Math.floor(elements.previewCards.length / 2);
        setPreviewCard(card, item, highlight);
    });
}

function init() {
    loadState();
    updateUI();
    initPreview();
    attachEvents();
    handleTimer();
    setupCursor();
}

document.addEventListener('DOMContentLoaded', init);
