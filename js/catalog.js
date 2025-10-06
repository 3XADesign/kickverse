/* ============================================
   KICKVERSE CATALOG - DYNAMIC GENERATION
   Generates product cards from team data
   ============================================ */

// Catálogo completo de camisetas
const catalogData = {
    laliga: [
        // Los nombres deben coincidir exactamente con los archivos: laliga_{nombre}_local.jpg
        { name: 'real-madrid', displayName: 'Real Madrid', type: 'Local', price: 29.99, badge: 'hot' },
        { name: 'real-madrid', displayName: 'Real Madrid', type: 'Visitante', price: 29.99 },
        { name: 'barcelona', displayName: 'Barcelona', type: 'Local', price: 29.99, badge: 'hot' },
        { name: 'barcelona', displayName: 'Barcelona', type: 'Visitante', price: 29.99 },
        { name: 'atletico', displayName: 'Atlético Madrid', type: 'Local', price: 29.99 },
        { name: 'atletico', displayName: 'Atlético Madrid', type: 'Visitante', price: 29.99 },
        { name: 'sevilla', displayName: 'Sevilla', type: 'Local', price: 29.99 },
        { name: 'sevilla', displayName: 'Sevilla', type: 'Visitante', price: 29.99 },
        { name: 'real-sociedad', displayName: 'Real Sociedad', type: 'Local', price: 29.99 },
        { name: 'real-sociedad', displayName: 'Real Sociedad', type: 'Visitante', price: 29.99 },
        { name: 'betis', displayName: 'Betis', type: 'Local', price: 29.99 },
        { name: 'betis', displayName: 'Betis', type: 'Visitante', price: 29.99 },
        { name: 'bilbao', displayName: 'Athletic Bilbao', type: 'Local', price: 29.99 },
        { name: 'bilbao', displayName: 'Athletic Bilbao', type: 'Visitante', price: 29.99 },
        { name: 'villarreal', displayName: 'Villarreal', type: 'Local', price: 29.99 },
        { name: 'villarreal', displayName: 'Villarreal', type: 'Visitante', price: 29.99 },
        { name: 'valencia', displayName: 'Valencia', type: 'Local', price: 29.99 },
        { name: 'valencia', displayName: 'Valencia', type: 'Visitante', price: 29.99 },
        { name: 'rayo', displayName: 'Rayo Vallecano', type: 'Local', price: 29.99 },
        { name: 'rayo', displayName: 'Rayo Vallecano', type: 'Visitante', price: 29.99 },
        { name: 'osasuna', displayName: 'Osasuna', type: 'Local', price: 29.99 },
        { name: 'osasuna', displayName: 'Osasuna', type: 'Visitante', price: 29.99 },
        { name: 'mallorca', displayName: 'Mallorca', type: 'Local', price: 29.99 },
        { name: 'mallorca', displayName: 'Mallorca', type: 'Visitante', price: 29.99 },
        { name: 'girona', displayName: 'Girona', type: 'Local', price: 29.99 },
        { name: 'girona', displayName: 'Girona', type: 'Visitante', price: 29.99 },
        { name: 'getafe', displayName: 'Getafe', type: 'Local', price: 29.99 },
        { name: 'getafe', displayName: 'Getafe', type: 'Visitante', price: 29.99 },
        { name: 'espanyol', displayName: 'Espanyol', type: 'Local', price: 29.99 },
        { name: 'espanyol', displayName: 'Espanyol', type: 'Visitante', price: 29.99 },
        { name: 'celta', displayName: 'Celta', type: 'Local', price: 29.99 },
        { name: 'celta', displayName: 'Celta', type: 'Visitante', price: 29.99 },
        { name: 'alaves', displayName: 'Alavés', type: 'Local', price: 29.99 },
        { name: 'alaves', displayName: 'Alavés', type: 'Visitante', price: 29.99 }
    ],
    premier: [
        { name: 'Manchester City', type: 'Local', price: 29.99, badge: 'hot' },
        { name: 'Manchester City', type: 'Visitante', price: 29.99 },
        { name: 'Arsenal', type: 'Visitante', price: 29.99, badge: 'hot' },
        { name: 'Liverpool', type: 'Local', price: 29.99, badge: 'hot' },
        { name: 'Liverpool', type: 'Visitante', price: 29.99 },
        { name: 'Chelsea', type: 'Local', price: 29.99 },
        { name: 'Chelsea', type: 'Visitante', price: 29.99 },
        { name: 'Tottenham', type: 'Local', price: 29.99 },
        { name: 'Tottenham', type: 'Visitante', price: 29.99 },
        { name: 'Manchester United', type: 'Local', price: 29.99, badge: 'hot' }
    ],
    seriea: [
        { name: 'Inter', type: 'Local', price: 29.99, badge: 'new' },
        { name: 'Milan', type: 'Local', price: 29.99, badge: 'new' },
        { name: 'Juventus', type: 'Local', price: 29.99, badge: 'new' },
        { name: 'Napoli', type: 'Local', price: 29.99, badge: 'new' },
        { name: 'Roma', type: 'Local', price: 29.99 },
        { name: 'Lazio', type: 'Local', price: 29.99 },
        { name: 'Atalanta', type: 'Local', price: 29.99 },
        { name: 'Fiorentina', type: 'Local', price: 29.99 }
    ],
    bundesliga: [
        { name: 'Bayern München', type: 'Local', price: 29.99, badge: 'new' },
        { name: 'Borussia Dortmund', type: 'Local', price: 29.99, badge: 'new' },
        { name: 'RB Leipzig', type: 'Local', price: 29.99, badge: 'new' },
        { name: 'Bayer Leverkusen', type: 'Local', price: 29.99 },
        { name: 'Union Berlin', type: 'Local', price: 29.99 },
        { name: 'Eintracht Frankfurt', type: 'Local', price: 29.99 }
    ],
    ligue1: [
        { name: 'PSG', type: 'Local', price: 29.99, badge: 'new' },
        { name: 'Olympique Marseille', type: 'Local', price: 29.99, badge: 'new' },
        { name: 'Olympique Lyon', type: 'Local', price: 29.99, badge: 'new' },
        { name: 'Monaco', type: 'Local', price: 29.99 },
        { name: 'Lille', type: 'Local', price: 29.99 },
        { name: 'Rennais', type: 'Local', price: 29.99 }
    ],
    selecciones: [
        { name: 'España', type: 'Selección', price: 29.99, badge: 'hot' },
        { name: 'Argentina', type: 'Selección', price: 29.99, badge: 'hot' },
        { name: 'Brasil', type: 'Selección', price: 29.99, badge: 'hot' },
        { name: 'Francia', type: 'Selección', price: 29.99 },
        { name: 'Alemania', type: 'Selección', price: 29.99 },
        { name: 'Italia', type: 'Selección', price: 29.99 },
        { name: 'Inglaterra', type: 'Selección', price: 29.99 },
        { name: 'Portugal', type: 'Selección', price: 29.99 },
        { name: 'México', type: 'Selección', price: 29.99 },
        { name: 'USA', type: 'Selección', price: 29.99 }
    ]
};

const leagueInfo = {
    laliga: { name: 'LaLiga', logo: 'img/leagues/laliga.svg', flag: '🇪🇸' },
    premier: { name: 'Premier League', logo: 'img/leagues/premier.svg', flag: '🏴󠁧󠁢󠁥󠁮󠁧󠁿' },
    seriea: { name: 'Serie A', logo: 'img/leagues/seriea.svg', flag: '🇮🇹' },
    bundesliga: { name: 'Bundesliga', logo: 'img/leagues/bundesliga.svg', flag: '🇩🇪' },
    ligue1: { name: 'Ligue 1', logo: 'img/leagues/ligue1.svg', flag: '🇫🇷' },
    selecciones: { name: 'Selecciones', logo: null, flag: '🌍' }
};

let currentFilter = 'all';
let searchTerm = '';

document.addEventListener('DOMContentLoaded', () => {
    renderCatalog();
    setupFilters();
    setupSearch();
    checkURLCategory();
});

function checkURLCategory() {
    const urlParams = new URLSearchParams(window.location.search);
    const category = urlParams.get('category');
    
    if (category && (category in catalogData || category === 'all')) {
        currentFilter = category;
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.filter === category) {
                btn.classList.add('active');
            }
        });
        renderCatalog();
    }
}

function renderCatalog() {
    const grid = document.getElementById('catalogGrid');
    const noResults = document.getElementById('noResults');
    const resultsCount = document.getElementById('resultsCount');
    
    grid.innerHTML = '';
    let products = [];
    
    if (currentFilter === 'all') {
        Object.keys(catalogData).forEach(league => {
            products.push(...catalogData[league].map(p => ({ ...p, league })));
        });
    } else {
        products = catalogData[currentFilter].map(p => ({ ...p, league: currentFilter }));
    }
    
    if (searchTerm) {
        products = products.filter(p => {
            const teamName = (p.displayName || p.name).toLowerCase();
            const searchLower = searchTerm.toLowerCase();
            return teamName.includes(searchLower) ||
                   p.type.toLowerCase().includes(searchLower) ||
                   leagueInfo[p.league].name.toLowerCase().includes(searchLower);
        });
    }
    
    resultsCount.textContent = products.length;
    
    if (products.length === 0) {
        noResults.style.display = 'block';
        grid.style.display = 'none';
        return;
    } else {
        noResults.style.display = 'none';
        grid.style.display = 'grid';
    }
    
    products.forEach(product => {
        const card = createProductCard(product);
        grid.appendChild(card);
    });
}

function createProductCard(product) {
    const { name, displayName, type, price, badge, league } = product;
    const teamName = displayName || name;
    const leagueMeta = leagueInfo[league];
    const imagePath = getImagePath(league, name, type);
    
    const card = document.createElement('div');
    card.className = 'product-card';
    card.dataset.category = league;
    
    // Badge HTML
    const badgeHTML = badge ? `<div class="product-badge ${badge}">${badge === 'hot' ? '🔥 Popular' : '💚 Nuevo'}</div>` : '';
    
    // League logo HTML
    const leagueLogoHTML = leagueMeta.logo ? `<img src="${leagueMeta.logo}" alt="${leagueMeta.name}">` : '';
    
    card.innerHTML = `
        ${badgeHTML}
        
        <div class="product-image">
            <img src="${imagePath}" alt="${teamName} ${type}" loading="lazy" 
                 onerror="this.src='img/hero-jersey.png'">
        </div>
        
        <div class="product-info">
            <div class="product-league">
                ${leagueLogoHTML}
                <span>${leagueMeta.name} ${leagueMeta.flag}</span>
            </div>
            
            <h3 class="product-name">${teamName}</h3>
            <p class="product-type">${type}</p>
            
            <div class="product-pricing">
                <div class="product-price">
                    <span class="price-current">${price.toFixed(2)}€</span>
                    <span class="price-original">79,99€</span>
                </div>
            </div>
            
            <div class="product-actions">
                <button class="btn-product btn-personalize" 
                        data-team="${teamName}"
                        data-type="${type}"
                        data-image="${imagePath}"
                        data-league="${league}"
                        data-price="${price}">
                    ✍️ Personalizar ahora
                </button>
                <a href="https://wa.me/34614299735?text=Hola,%20quiero%20info%20sobre%20${encodeURIComponent(teamName + ' ' + type)}" 
                   class="btn-product btn-product-secondary" target="_blank">
                    💬 Consultar
                </a>
            </div>
        </div>
    `;
    
    return card;
}

function getImagePath(league, name, type) {
    // Para LaLiga, el nombre ya viene limpio (ej: 'real-madrid', 'barcelona')
    // Para otras ligas, limpiamos el nombre
    const cleanName = name.toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/ü/g, 'u')
        .replace(/ö/g, 'o')
        .replace(/ä/g, 'a')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');
    
    const typeSlug = type.toLowerCase().includes('local') ? 'local' : 
                     type.toLowerCase().includes('visitante') ? 'visitante' : 'local';
    
    if (league === 'selecciones') {
        return `img/camisetas/selecciones_${cleanName}.png`;
    }
    
    // Formato: laliga_real-madrid_local.png (cambiado a PNG con transparencia)
    return `img/camisetas/${league}_${cleanName}_${typeSlug}.png`;
}

function setupFilters() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentFilter = btn.dataset.filter;
            renderCatalog();
        });
    });
}

function setupSearch() {
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', (e) => {
        searchTerm = e.target.value.trim();
        renderCatalog();
    });
}
