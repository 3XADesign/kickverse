// ============================================
// KICKVERSE - FORM LOGIC - FLAT DESIGN
// Con grid visual de equipos con logos
// ============================================

// Mapeo de equipos con sus imágenes
const teamsData = {
    laliga: [
        { name: 'Real Madrid', logo: 'img/clubs/laliga_realmadrid.png', search: 'real madrid' },
        { name: 'FC Barcelona', logo: 'img/clubs/laliga_barcelona.png', search: 'barcelona barça' },
        { name: 'Atlético Madrid', logo: 'img/clubs/laliga_atlmadrid.png', search: 'atletico atleti' },
        { name: 'Sevilla FC', logo: 'img/clubs/laliga_sevilla.png', search: 'sevilla' },
        { name: 'Valencia CF', logo: 'img/clubs/laliga_valencia.png', search: 'valencia' },
        { name: 'Real Betis', logo: 'img/clubs/laliga_betis.png', search: 'betis' },
        { name: 'Athletic Club', logo: 'img/clubs/laliga_athletic.png', search: 'athletic bilbao' },
        { name: 'Real Sociedad', logo: 'img/clubs/laliga_realsociedad.png', search: 'sociedad' },
        { name: 'Villarreal CF', logo: 'img/clubs/laliga_villarreal.png', search: 'villarreal' },
        { name: 'Getafe CF', logo: 'img/clubs/laliga_getafe.png', search: 'getafe' },
        { name: 'RCD Espanyol', logo: 'img/clubs/laliga_espanyol.png', search: 'espanyol' },
        { name: 'Celta de Vigo', logo: 'img/clubs/laliga_celta.png', search: 'celta vigo' },
        { name: 'Rayo Vallecano', logo: 'img/clubs/laliga_rayovallecano.png', search: 'rayo vallecano' },
        { name: 'RCD Mallorca', logo: 'img/clubs/laliga_mallorca.png', search: 'mallorca' },
        { name: 'CA Osasuna', logo: 'img/clubs/laliga_osasuna.png', search: 'osasuna' },
        { name: 'Girona FC', logo: 'img/clubs/laliga_girona.png', search: 'girona' }
    ],
    premier: [
        { name: 'Man United', logo: 'img/clubs/premier_manutd.png', search: 'manchester united' },
        { name: 'Man City', logo: 'img/clubs/premier_mancity.png', search: 'manchester city' },
        { name: 'Liverpool', logo: 'img/clubs/premier_liverpool.png', search: 'liverpool' },
        { name: 'Chelsea', logo: 'img/clubs/premier_chelsea.png', search: 'chelsea' },
        { name: 'Arsenal', logo: 'img/clubs/premier_arsenal.png', search: 'arsenal gunners' },
        { name: 'Tottenham', logo: 'img/clubs/premier_tottenham.png', search: 'tottenham spurs' },
        { name: 'Newcastle', logo: 'img/clubs/premier_newcastle.png', search: 'newcastle' },
        { name: 'Aston Villa', logo: 'img/clubs/premier_villa.png', search: 'aston villa' },
        { name: 'Brighton', logo: 'img/clubs/premier_brighton.png', search: 'brighton' },
        { name: 'West Ham', logo: 'img/clubs/premier_westham.png', search: 'west ham' }
    ],
    seriea: [
        { name: 'Inter Milan', logo: 'img/clubs/seriea_inter.png', search: 'inter milan nerazzurri' },
        { name: 'AC Milan', logo: 'img/clubs/seriea_milan.png', search: 'milan rossoneri' },
        { name: 'Juventus', logo: 'img/clubs/seriea_juventus.png', search: 'juventus juve bianconeri' },
        { name: 'Napoli', logo: 'img/clubs/seriea_napoli.png', search: 'napoli azzurri' },
        { name: 'AS Roma', logo: 'img/clubs/seriea_roma.png', search: 'roma giallorossi' },
        { name: 'Lazio', logo: 'img/clubs/seriea_lazio.png', search: 'lazio aquile' },
        { name: 'Atalanta', logo: 'img/clubs/seriea_atalanta.png', search: 'atalanta bergamo' },
        { name: 'Fiorentina', logo: 'img/clubs/seriea_fiorentina.png', search: 'fiorentina viola' },
        { name: 'Torino', logo: 'img/clubs/seriea_torino.png', search: 'torino granata' },
        { name: 'Bologna', logo: 'img/clubs/seriea_bologna.png', search: 'bologna rossoblu' },
        { name: 'Udinese', logo: 'img/clubs/seriea_udinese.png', search: 'udinese' },
        { name: 'Genoa', logo: 'img/clubs/seriea_genoa.png', search: 'genoa' },
        { name: 'Cagliari', logo: 'img/clubs/seriea_cagliari.png', search: 'cagliari' },
        { name: 'Lecce', logo: 'img/clubs/seriea_lecce.png', search: 'lecce' },
        { name: 'Hellas Verona', logo: 'img/clubs/seriea_hellasverona.png', search: 'verona hellas' }
    ],
    bundesliga: [
        { name: 'Bayern Munich', logo: 'img/clubs/bundesliga_bayern.png', search: 'bayern munich baviera' },
        { name: 'Borussia Dortmund', logo: 'img/clubs/bundesliga_dortmund.png', search: 'dortmund bvb borussia' },
        { name: 'RB Leipzig', logo: 'img/clubs/bundesliga_leipzig.png', search: 'leipzig red bull' },
        { name: 'Bayer Leverkusen', logo: 'img/clubs/bundesliga_leverkusen.png', search: 'leverkusen bayer' },
        { name: 'Union Berlin', logo: 'img/clubs/bundesliga_union.png', search: 'union berlin' },
        { name: 'SC Freiburg', logo: 'img/clubs/bundesliga_freiburg.png', search: 'freiburg' },
        { name: 'Eintracht Frankfurt', logo: 'img/clubs/bundesliga_frankfurt.png', search: 'frankfurt eintracht' },
        { name: 'VfL Wolfsburg', logo: 'img/clubs/bundesliga_wolfsburg.png', search: 'wolfsburg' },
        { name: 'Borussia Mönchengladbach', logo: 'img/clubs/bundesliga_gladbach.png', search: 'gladbach monchengladbach borussia' },
        { name: 'VfB Stuttgart', logo: 'img/clubs/bundesliga_stuttgart.png', search: 'stuttgart' }
    ],
    ligue1: [
        { name: 'PSG', logo: 'img/clubs/ligue1_psg.png', search: 'psg paris saint germain' },
        { name: 'Olympique Marseille', logo: 'img/clubs/ligue1_olimpiquemarsella.png', search: 'marseille marsella om olympique' },
        { name: 'Olympique Lyon', logo: 'img/clubs/ligue1_olympiquelyon.png', search: 'lyon ol olympique' },
        { name: 'AS Monaco', logo: 'img/clubs/ligue1_monaco.png', search: 'monaco' },
        { name: 'Lille OSC', logo: 'img/clubs/ligue1_lille.png', search: 'lille losc' },
        { name: 'Stade Rennais', logo: 'img/clubs/ligue1_rennais.png', search: 'rennes rennais' },
        { name: 'RC Lens', logo: 'img/clubs/ligue1_racinglens.png', search: 'lens racing' },
        { name: 'OGC Nice', logo: 'img/clubs/ligue1_niza.png', search: 'nice niza' },
        { name: 'FC Nantes', logo: 'img/clubs/ligue1_nantes.png', search: 'nantes' },
        { name: 'Toulouse FC', logo: 'img/clubs/ligue1_toulouse.png', search: 'toulouse' },
        { name: 'Strasbourg', logo: 'img/clubs/ligue1_racingestrasburgo.png', search: 'strasbourg estrasburgo' },
        { name: 'Montpellier', logo: 'img/clubs/ligue1_montpellier.png', search: 'montpellier' }
    ],
    selecciones: [
        { name: 'España', logo: 'img/clubs/selecciones_espana.png', search: 'españa spain' },
        { name: 'Argentina', logo: 'img/clubs/selecciones_argentina.png', search: 'argentina' },
        { name: 'Brasil', logo: 'img/clubs/selecciones_brasil.png', search: 'brasil brazil' },
        { name: 'Francia', logo: 'img/clubs/selecciones_francia.png', search: 'francia france' },
        { name: 'Alemania', logo: 'img/clubs/selecciones_alemania.png', search: 'alemania germany' },
        { name: 'Portugal', logo: 'img/clubs/selecciones_portugal.png', search: 'portugal' },
        { name: 'Inglaterra', logo: 'img/clubs/selecciones_inglaterra.png', search: 'inglaterra england' },
        { name: 'Italia', logo: 'img/clubs/selecciones_italia.png', search: 'italia italy' },
        { name: 'Países Bajos', logo: 'img/clubs/selecciones_paisesbajos.png', search: 'holanda netherlands' },
        { name: 'Bélgica', logo: 'img/clubs/selecciones_belgica.png', search: 'belgica belgium' },
        { name: 'Uruguay', logo: 'img/clubs/selecciones_uruguay.png', search: 'uruguay' },
        { name: 'Colombia', logo: 'img/clubs/selecciones_colombia.png', search: 'colombia' },
        { name: 'México', logo: 'img/clubs/selecciones_mexico.png', search: 'mexico' },
        { name: 'Croacia', logo: 'img/clubs/selecciones_croacia.png', search: 'croacia croatia' },
        { name: 'Marruecos', logo: 'img/clubs/selecciones_marruecos.png', search: 'marruecos morocco' },
        { name: 'USA', logo: 'img/clubs/selecciones_usa.png', search: 'usa estados unidos' }
    ]
};

// Nombres amigables de ligas
const leagueNames = {
    laliga: 'LaLiga 🇪🇸',
    premier: 'Premier League 🏴󠁧󠁢󠁥󠁮󠁧󠁿',
    seriea: 'Serie A 🇮🇹',
    bundesliga: 'Bundesliga 🇩🇪',
    ligue1: 'Ligue 1 �🇷',
    selecciones: 'Selecciones 🌍'
};

// Estado del formulario
let formData = {
    league: '',
    team: '',
    teamLogo: '',
    size: '',
    patches: '',
    playerName: '',
    playerNumber: '',
    price: 29.99
};

// Paso actual
let currentStep = 1;
const totalSteps = 6;

// ============================================
// INICIALIZACIÓN
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    updateProgressBar();
    setupTeamSearch();
});

// ============================================
// STEP 1: SELECCIONAR LIGA
// ============================================
function selectLeague(league) {
    formData.league = league;
    
    // Marcar como seleccionado
    document.querySelectorAll('#step1 .league-card').forEach(card => {
        card.classList.remove('selected');
    });
    event.target.closest('.league-card').classList.add('selected');
    
    // Poblar equipos
    populateTeams(league);
    
    // Avanzar al siguiente paso
    setTimeout(() => nextStep(), 400);
}

// ============================================
// STEP 2: GRID DE EQUIPOS CON LOGOS
// ============================================
function populateTeams(league) {
    const teamsGrid = document.getElementById('teamsGrid');
    const leagueName = document.getElementById('leagueName');
    
    if (!teamsGrid) return;
    
    // Actualizar título
    leagueName.textContent = leagueNames[league] || league;
    
    // Limpiar grid
    teamsGrid.innerHTML = '';
    
    // Obtener equipos de la liga
    const teams = teamsData[league] || [];
    
    // Crear cards de equipos
    teams.forEach(team => {
        const card = document.createElement('button');
        card.className = 'team-card';
        card.setAttribute('data-team', team.name);
        card.setAttribute('data-logo', team.logo);
        card.onclick = () => selectTeam(team.name, team.logo);
        
        card.innerHTML = `
            <div class="team-card-logo">
                <img src="${team.logo}" alt="${team.name}" onerror="this.src='img/logo.png'">
            </div>
            <div class="team-card-name">${team.name}</div>
        `;
        
        teamsGrid.appendChild(card);
    });
}

// Búsqueda de equipos
function setupTeamSearch() {
    const searchInput = document.getElementById('teamSearch');
    if (!searchInput) return;
    
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const teamCards = document.querySelectorAll('.team-card');
        
        teamCards.forEach(card => {
            const teamName = card.getAttribute('data-team').toLowerCase();
            const team = teamsData[formData.league]?.find(t => t.name.toLowerCase() === teamName);
            const searchTerms = team?.search || teamName;
            
            if (searchTerms.includes(query) || query === '') {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    });
}

function selectTeam(teamName, teamLogo) {
    formData.team = teamName;
    formData.teamLogo = teamLogo;
    
    // Marcar como seleccionado
    document.querySelectorAll('.team-card').forEach(card => {
        card.classList.remove('selected');
    });
    event.target.closest('.team-card').classList.add('selected');
    
    // Avanzar al siguiente paso
    setTimeout(() => nextStep(), 400);
}

// ============================================
// STEP 3: SELECCIONAR TALLA
// ============================================
function selectSize(size) {
    formData.size = size;
    
    // Marcar como seleccionado
    document.querySelectorAll('#step3 .size-card').forEach(card => {
        card.classList.remove('selected');
    });
    event.target.closest('.size-card').classList.add('selected');
    
    // Avanzar al siguiente paso
    setTimeout(() => nextStep(), 400);
}

// ============================================
// STEP 4: SELECCIONAR PARCHES
// ============================================
function selectPatches(value) {
    formData.patches = value;
    
    // Calcular precio
    if (value === 'si') {
        formData.price = 34.99;
    } else {
        formData.price = 29.99;
    }
    
    // Marcar como seleccionado
    document.querySelectorAll('#step4 .option-card').forEach(card => {
        card.classList.remove('selected');
    });
    event.target.closest('.option-card').classList.add('selected');
    
    // Avanzar al siguiente paso
    setTimeout(() => nextStep(), 400);
}

// ============================================
// STEP 5: PERSONALIZACIÓN
// ============================================
// (Se captura al hacer clic en "Continuar")

// ============================================
// NAVEGACIÓN
// ============================================
function nextStep() {
    // Validar paso actual
    if (!validateStep(currentStep)) {
        return;
    }
    
    // Capturar personalización en step 5
    if (currentStep === 5) {
        const playerName = document.getElementById('playerName').value.trim();
        const playerNumber = document.getElementById('playerNumber').value;
        
        formData.playerName = playerName;
        formData.playerNumber = playerNumber;
        
        // Añadir costo de personalización si hay datos
        if (playerName || playerNumber) {
            formData.price += 5;
        }
    }
    
    // Ocultar paso actual
    const currentStepEl = document.getElementById(`step${currentStep}`);
    if (currentStepEl) {
        currentStepEl.classList.remove('active');
    }
    
    // Avanzar
    currentStep++;
    if (currentStep > totalSteps) {
        currentStep = totalSteps;
    }
    
    // Mostrar nuevo paso
    const nextStepEl = document.getElementById(`step${currentStep}`);
    if (nextStepEl) {
        nextStepEl.classList.add('active');
    }
    
    // Actualizar progress bar
    updateProgressBar();
    
    // Mostrar resumen en step 6
    if (currentStep === 6) {
        updateSummary();
    }
    
    // Scroll al top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function previousStep() {
    // Ocultar paso actual
    const currentStepEl = document.getElementById(`step${currentStep}`);
    if (currentStepEl) {
        currentStepEl.classList.remove('active');
    }
    
    // Retroceder
    currentStep--;
    if (currentStep < 1) {
        currentStep = 1;
    }
    
    // Mostrar nuevo paso
    const prevStepEl = document.getElementById(`step${currentStep}`);
    if (prevStepEl) {
        prevStepEl.classList.add('active');
    }
    
    // Actualizar progress bar
    updateProgressBar();
    
    // Scroll al top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ============================================
// VALIDACIÓN
// ============================================
function validateStep(step) {
    switch(step) {
        case 1:
            return formData.league !== '';
        case 2:
            return formData.team !== '';
        case 3:
            return formData.size !== '';
        case 4:
            return formData.patches !== '';
        case 5:
            return true; // Personalización es opcional
        default:
            return true;
    }
}

// ============================================
// PROGRESS BAR
// ============================================
function updateProgressBar() {
    // Actualizar círculos
    document.querySelectorAll('.progress-step').forEach((step, index) => {
        const stepNumber = index + 1;
        
        if (stepNumber < currentStep) {
            step.classList.add('completed');
            step.classList.remove('active');
        } else if (stepNumber === currentStep) {
            step.classList.add('active');
            step.classList.remove('completed');
        } else {
            step.classList.remove('active', 'completed');
        }
    });
    
    // Actualizar línea de progreso
    const progressPercentage = ((currentStep - 1) / (totalSteps - 1)) * 100;
    const progressLineFill = document.getElementById('progressLineFill');
    if (progressLineFill) {
        progressLineFill.style.width = `${progressPercentage}%`;
    }
}

// ============================================
// RESUMEN
// ============================================
function updateSummary() {
    // Liga
    document.getElementById('summaryLeague').textContent = leagueNames[formData.league] || formData.league;
    
    // Equipo
    document.getElementById('summaryTeam').textContent = formData.team;
    
    // Talla
    document.getElementById('summarySize').textContent = formData.size;
    
    // Parches
    const patchesText = formData.patches === 'si' ? 'Sí (+5€)' : 'No';
    document.getElementById('summaryPatches').textContent = patchesText;
    
    // Personalización
    if (formData.playerName || formData.playerNumber) {
        const personalization = [];
        if (formData.playerName) personalization.push(formData.playerName);
        if (formData.playerNumber) personalization.push(`#${formData.playerNumber}`);
        
        document.getElementById('summaryPersonalization').textContent = personalization.join(' ');
        document.getElementById('personalizationRow').style.display = 'flex';
        document.getElementById('personalizationPriceRow').style.display = 'flex';
    } else {
        document.getElementById('personalizationRow').style.display = 'none';
        document.getElementById('personalizationPriceRow').style.display = 'none';
    }
    
    // Mostrar/ocultar precio de parches
    if (formData.patches === 'si') {
        document.getElementById('patchesPriceRow').style.display = 'flex';
    } else {
        document.getElementById('patchesPriceRow').style.display = 'none';
    }
    
    // Precio total
    document.getElementById('totalPrice').textContent = `${formData.price.toFixed(2)} €`;
}

// ============================================
// AÑADIR AL CARRITO
// ============================================
function addToCart() {
    // Validar que se haya completado el formulario
    if (!formData.league || !formData.team || !formData.size) {
        alert('Por favor, completa todos los campos obligatorios');
        return;
    }

    // Obtener imagen de la camiseta (puedes personalizarlo)
    const teamImage = getTeamImage();

    // Crear item del carrito
    const cartItem = {
        league: leagueNames[formData.league] || formData.league,
        team: formData.team,
        type: 'Local', // Puedes hacer que el usuario seleccione
        size: formData.size,
        playerName: formData.playerName || '',
        playerNumber: formData.playerNumber || '',
        patches: formData.patches,
        price: formData.price,
        image: teamImage
    };

    // Añadir al carrito usando la clase global
    if (window.shoppingCart) {
        window.shoppingCart.addItem(cartItem);
        window.shoppingCart.openCart();
        
        // Resetear formulario para añadir más camisetas
        setTimeout(() => {
            if (confirm('¿Quieres añadir otra camiseta?')) {
                resetForm();
            }
        }, 1500);
    } else {
        console.error('Shopping cart not initialized');
    }
}

// Función auxiliar para obtener imagen del equipo
function getTeamImage() {
    const league = formData.league;
    const teamName = formData.team;
    
    if (league && teamsData[league]) {
        const team = teamsData[league].find(t => t.name === teamName);
        if (team && team.logo) {
            return team.logo;
        }
    }
    
    return '';
}

// Función para resetear el formulario
function resetForm() {
    currentStep = 1;
    formData = {
        league: '',
        team: '',
        size: '',
        patches: '',
        playerName: '',
        playerNumber: '',
        price: 29.99
    };
    
    // Volver al primer paso
    document.querySelectorAll('.step').forEach(step => step.classList.remove('active'));
    document.getElementById('step1').classList.add('active');
    updateProgressBar();
    updatePrice();
}

// ============================================
// WHATSAPP
// ============================================
function sendToWhatsApp() {
    const phone = '34614299735';
    
    let message = `🎽 *PEDIDO KICKVERSE*\n\n`;
    message += `📁 Liga: ${leagueNames[formData.league]}\n`;
    message += `⚽ Equipo: ${formData.team}\n`;
    message += `📏 Talla: ${formData.size}\n`;
    message += `🏷️ Parches: ${formData.patches === 'si' ? 'Sí' : 'No'}\n`;
    
    if (formData.playerName || formData.playerNumber) {
        message += `✏️ Personalización: `;
        if (formData.playerName) message += formData.playerName;
        if (formData.playerNumber) message += ` #${formData.playerNumber}`;
        message += `\n`;
    }
    
    message += `\n💰 *Total: ${formData.price.toFixed(2)} €*\n\n`;
    message += `¿Podemos proceder con el pedido?`;
    
    const url = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
    window.open(url, '_blank');
}
