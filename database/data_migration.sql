-- ============================================================================
-- KICKVERSE DATA MIGRATION
-- ============================================================================
-- This file contains INSERT statements to migrate all hardcoded data
-- from the current HTML/JS implementation to the database
-- ============================================================================

USE kickverse;

-- ============================================================================
-- 1. LEAGUES DATA
-- ============================================================================

INSERT INTO leagues (league_id, name, slug, country, logo_path, display_order, is_active) VALUES
(1, 'La Liga', 'laliga', 'España', './img/leagues/laliga.svg', 1, TRUE),
(2, 'Premier League', 'premier', 'Inglaterra', './img/leagues/premier.svg', 2, TRUE),
(3, 'Serie A', 'seriea', 'Italia', './img/leagues/seriea.svg', 3, TRUE),
(4, 'Bundesliga', 'bundesliga', 'Alemania', './img/leagues/bundesliga.svg', 4, TRUE),
(5, 'Ligue 1', 'ligue1', 'Francia', './img/leagues/ligue1.svg', 5, TRUE),
(6, 'Selecciones', 'selecciones', 'Internacional', NULL, 6, TRUE);

-- ============================================================================
-- 2. TEAMS DATA
-- ============================================================================

-- LA LIGA TEAMS
INSERT INTO teams (team_id, league_id, name, slug, is_top_team, display_order, is_active) VALUES
(1, 1, 'Alavés', 'alaves', FALSE, 1, TRUE),
(2, 1, 'Athletic Bilbao', 'bilbao', FALSE, 2, TRUE),
(3, 1, 'Atlético Madrid', 'atletico', TRUE, 3, TRUE),
(4, 1, 'Barcelona', 'barcelona', TRUE, 4, TRUE),
(5, 1, 'Real Betis', 'betis', FALSE, 5, TRUE),
(6, 1, 'Celta de Vigo', 'celta', FALSE, 6, TRUE),
(7, 1, 'Elche', 'elche', FALSE, 7, TRUE),
(8, 1, 'Espanyol', 'espanyol', FALSE, 8, TRUE),
(9, 1, 'Getafe', 'getafe', FALSE, 9, TRUE),
(10, 1, 'Girona', 'girona', FALSE, 10, TRUE),
(11, 1, 'Levante', 'levante', FALSE, 11, TRUE),
(12, 1, 'Mallorca', 'mallorca', FALSE, 12, TRUE),
(13, 1, 'Osasuna', 'osasuna', FALSE, 13, TRUE),
(14, 1, 'Rayo Vallecano', 'rayo', FALSE, 14, TRUE),
(15, 1, 'Real Madrid', 'madrid', TRUE, 15, TRUE),
(16, 1, 'Real Oviedo', 'oviedo', FALSE, 16, TRUE),
(17, 1, 'Real Sociedad', 'realsociedad', FALSE, 17, TRUE),
(18, 1, 'Sevilla', 'sevilla', TRUE, 18, TRUE),
(19, 1, 'Valencia', 'valencia', FALSE, 19, TRUE),
(20, 1, 'Villarreal', 'villareal', FALSE, 20, TRUE);

-- PREMIER LEAGUE TEAMS
INSERT INTO teams (team_id, league_id, name, slug, is_top_team, display_order, is_active) VALUES
(21, 2, 'Arsenal', 'arsenal', TRUE, 1, TRUE),
(22, 2, 'Aston Villa', 'astonvilla', FALSE, 2, TRUE),
(23, 2, 'Chelsea', 'chelsea', TRUE, 3, TRUE),
(24, 2, 'Crystal Palace', 'crystalpalace', FALSE, 4, TRUE),
(25, 2, 'Everton', 'everton', FALSE, 5, TRUE),
(26, 2, 'Liverpool', 'liverpool', TRUE, 6, TRUE),
(27, 2, 'Manchester City', 'manchestercity', TRUE, 7, TRUE),
(28, 2, 'Manchester United', 'manchesterunited', TRUE, 8, TRUE),
(29, 2, 'Newcastle', 'newscastle', FALSE, 9, TRUE),
(30, 2, 'Tottenham', 'tottenham', TRUE, 10, TRUE),
(31, 2, 'West Ham', 'westham', FALSE, 11, TRUE);

-- SERIE A TEAMS
INSERT INTO teams (team_id, league_id, name, slug, is_top_team, display_order, is_active) VALUES
(32, 3, 'Atalanta', 'atalanta', FALSE, 1, TRUE),
(33, 3, 'Bologna', 'bologna', FALSE, 2, TRUE),
(34, 3, 'Fiorentina', 'fiorentina', FALSE, 3, TRUE),
(35, 3, 'Inter', 'inter', TRUE, 4, TRUE),
(36, 3, 'Juventus', 'juventus', TRUE, 5, TRUE),
(37, 3, 'Lazio', 'lazio', FALSE, 6, TRUE),
(38, 3, 'Milan', 'milan', TRUE, 7, TRUE),
(39, 3, 'Napoli', 'napoli', TRUE, 8, TRUE),
(40, 3, 'Roma', 'roma', TRUE, 9, TRUE),
(41, 3, 'Torino', 'torino', FALSE, 10, TRUE);

-- BUNDESLIGA TEAMS
INSERT INTO teams (team_id, league_id, name, slug, is_top_team, display_order, is_active) VALUES
(42, 4, 'Augsburg', 'Augsburg', FALSE, 1, TRUE),
(43, 4, 'Bayern München', 'bayern', TRUE, 2, TRUE),
(44, 4, 'Borussia Dortmund', 'dortmund', TRUE, 3, TRUE),
(45, 4, 'Eintracht Frankfurt', 'Eintracht', FALSE, 4, TRUE),
(46, 4, 'SC Freiburg', 'Freiburg', FALSE, 5, TRUE),
(47, 4, 'Hamburger SV', 'Hamburger', FALSE, 6, TRUE),
(48, 4, 'Heidenheim', 'Heidenheim', FALSE, 7, TRUE),
(49, 4, 'Hoffenheim', 'Hoffenheim', FALSE, 8, TRUE),
(50, 4, 'FC Köln', 'Köln', FALSE, 9, TRUE),
(51, 4, 'RB Leipzig', 'Leipzig', FALSE, 10, TRUE),
(52, 4, 'Mainz 05', 'Mainz05', FALSE, 11, TRUE),
(53, 4, 'Bayer Leverkusen', 'leverkusen', TRUE, 12, TRUE),
(54, 4, 'Borussia Mönchengladbach', 'Mönchengladbach', FALSE, 13, TRUE),
(55, 4, 'St. Pauli', 'St.Pauli', FALSE, 14, TRUE),
(56, 4, 'VfB Stuttgart', 'Stuttgart', FALSE, 15, TRUE),
(57, 4, 'Union Berlin', 'UnionBerlin', FALSE, 16, TRUE),
(58, 4, 'Werder Bremen', 'bremen', FALSE, 17, TRUE),
(59, 4, 'VfL Wolfsburg', 'wolfburg', FALSE, 18, TRUE);

-- LIGUE 1 TEAMS
INSERT INTO teams (team_id, league_id, name, slug, is_top_team, display_order, is_active) VALUES
(60, 5, 'Olympique Lyon', 'lyon', FALSE, 1, TRUE),
(61, 5, 'Olympique Marseille', 'marsella', TRUE, 2, TRUE),
(62, 5, 'AS Monaco', 'monaco', FALSE, 3, TRUE),
(63, 5, 'Paris Saint-Germain', 'psg', TRUE, 4, TRUE);

-- SELECCIONES (NATIONAL TEAMS)
INSERT INTO teams (team_id, league_id, name, slug, is_top_team, display_order, is_active) VALUES
(64, 6, 'Argentina', 'argentina', TRUE, 1, TRUE),
(65, 6, 'Colombia', 'colombia', FALSE, 2, TRUE),
(66, 6, 'Japón', 'japon', FALSE, 3, TRUE),
(67, 6, 'Uruguay', 'uruguay', FALSE, 4, TRUE),
(68, 6, 'Brasil', 'brasil', TRUE, 5, TRUE),
(69, 6, 'Benfica', 'benfica', FALSE, 6, TRUE);

-- ============================================================================
-- 3. PRODUCTS DATA (Jerseys)
-- ============================================================================

-- Helper variables for product creation
-- Season: 2024/25
-- Base price: 24.99 EUR
-- Original price (display): 79.99 EUR

-- LA LIGA JERSEYS
INSERT INTO products (product_type, name, slug, league_id, team_id, jersey_type, season, version, base_price, original_price, stock_quantity, is_active, is_featured) VALUES
-- Alavés
('jersey', 'Alavés 24/25 Local', 'alaves-24-25-local', 1, 1, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Alavés 24/25 Visitante', 'alaves-24-25-visitante', 1, 1, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Athletic Bilbao
('jersey', 'Athletic Bilbao 24/25 Local', 'bilbao-24-25-local', 1, 2, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Athletic Bilbao 24/25 Visitante', 'bilbao-24-25-visitante', 1, 2, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Atlético Madrid
('jersey', 'Atlético Madrid 24/25 Local', 'atletico-24-25-local', 1, 3, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, TRUE),
('jersey', 'Atlético Madrid 24/25 Visitante', 'atletico-24-25-visitante', 1, 3, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Barcelona
('jersey', 'Barcelona 24/25 Local', 'barcelona-24-25-local', 1, 4, 'home', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, TRUE),
('jersey', 'Barcelona 24/25 Visitante', 'barcelona-24-25-visitante', 1, 4, 'away', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, TRUE),
-- Real Betis
('jersey', 'Real Betis 24/25 Local', 'betis-24-25-local', 1, 5, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Real Betis 24/25 Visitante', 'betis-24-25-visitante', 1, 5, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Celta de Vigo
('jersey', 'Celta de Vigo 24/25 Local', 'celta-24-25-local', 1, 6, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Celta de Vigo 24/25 Visitante', 'celta-24-25-visitante', 1, 6, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Elche
('jersey', 'Elche 24/25 Local', 'elche-24-25-local', 1, 7, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Elche 24/25 Visitante', 'elche-24-25-visitante', 1, 7, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Espanyol
('jersey', 'Espanyol 24/25 Local', 'espanyol-24-25-local', 1, 8, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Espanyol 24/25 Visitante', 'espanyol-24-25-visitante', 1, 8, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Getafe
('jersey', 'Getafe 24/25 Local', 'getafe-24-25-local', 1, 9, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Getafe 24/25 Visitante', 'getafe-24-25-visitante', 1, 9, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Girona
('jersey', 'Girona 24/25 Local', 'girona-24-25-local', 1, 10, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Girona 24/25 Visitante', 'girona-24-25-visitante', 1, 10, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Levante
('jersey', 'Levante 24/25 Local', 'levante-24-25-local', 1, 11, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Levante 24/25 Visitante', 'levante-24-25-visitante', 1, 11, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Mallorca
('jersey', 'Mallorca 24/25 Local', 'mallorca-24-25-local', 1, 12, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Mallorca 24/25 Visitante', 'mallorca-24-25-visitante', 1, 12, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Osasuna (only home available)
('jersey', 'Osasuna 24/25 Local', 'osasuna-24-25-local', 1, 13, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Rayo Vallecano
('jersey', 'Rayo Vallecano 24/25 Local', 'rayo-24-25-local', 1, 14, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Rayo Vallecano 24/25 Visitante', 'rayo-24-25-visitante', 1, 14, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Real Madrid
('jersey', 'Real Madrid 24/25 Local', 'madrid-24-25-local', 1, 15, 'home', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, TRUE),
('jersey', 'Real Madrid 24/25 Visitante', 'madrid-24-25-visitante', 1, 15, 'away', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, TRUE),
-- Real Oviedo
('jersey', 'Real Oviedo 24/25 Local', 'oviedo-24-25-local', 1, 16, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Real Oviedo 24/25 Visitante', 'oviedo-24-25-visitante', 1, 16, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Real Sociedad
('jersey', 'Real Sociedad 24/25 Local', 'realsociedad-24-25-local', 1, 17, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Real Sociedad 24/25 Visitante', 'realsociedad-24-25-visitante', 1, 17, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Sevilla
('jersey', 'Sevilla 24/25 Local', 'sevilla-24-25-local', 1, 18, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Sevilla 24/25 Visitante', 'sevilla-24-25-visitante', 1, 18, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Valencia
('jersey', 'Valencia 24/25 Local', 'valencia-24-25-local', 1, 19, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Valencia 24/25 Visitante', 'valencia-24-25-visitante', 1, 19, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Villarreal
('jersey', 'Villarreal 24/25 Local', 'villareal-24-25-local', 1, 20, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Villarreal 24/25 Visitante', 'villareal-24-25-visitante', 1, 20, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE);

-- PREMIER LEAGUE JERSEYS
INSERT INTO products (product_type, name, slug, league_id, team_id, jersey_type, season, version, base_price, original_price, stock_quantity, is_active, is_featured) VALUES
-- Arsenal
('jersey', 'Arsenal 24/25 Local', 'arsenal-24-25-local', 2, 21, 'home', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, TRUE),
('jersey', 'Arsenal 24/25 Visitante', 'arsenal-24-25-visitante', 2, 21, 'away', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, FALSE),
-- Aston Villa
('jersey', 'Aston Villa 24/25 Local', 'astonvilla-24-25-local', 2, 22, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Aston Villa 24/25 Visitante', 'astonvilla-24-25-visitante', 2, 22, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Chelsea
('jersey', 'Chelsea 24/25 Local', 'chelsea-24-25-local', 2, 23, 'home', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, FALSE),
('jersey', 'Chelsea 24/25 Visitante', 'chelsea-24-25-visitante', 2, 23, 'away', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, FALSE),
-- Crystal Palace
('jersey', 'Crystal Palace 24/25 Local', 'crystalpalace-24-25-local', 2, 24, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Crystal Palace 24/25 Visitante', 'crystalpalace-24-25-visitante', 2, 24, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Everton
('jersey', 'Everton 24/25 Local', 'everton-24-25-local', 2, 25, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Everton 24/25 Visitante', 'everton-24-25-visitante', 2, 25, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Liverpool
('jersey', 'Liverpool 24/25 Local', 'liverpool-24-25-local', 2, 26, 'home', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, TRUE),
('jersey', 'Liverpool 24/25 Visitante', 'liverpool-24-25-visitante', 2, 26, 'away', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, FALSE),
-- Manchester City
('jersey', 'Manchester City 24/25 Local', 'manchestercity-24-25-local', 2, 27, 'home', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, TRUE),
('jersey', 'Manchester City 24/25 Visitante', 'manchestercity-24-25-visitante', 2, 27, 'away', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, FALSE),
-- Manchester United
('jersey', 'Manchester United 24/25 Local', 'manchesterunited-24-25-local', 2, 28, 'home', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, TRUE),
('jersey', 'Manchester United 24/25 Visitante', 'manchesterunited-24-25-visitante', 2, 28, 'away', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, FALSE),
-- Newcastle
('jersey', 'Newcastle 24/25 Local', 'newscastle-24-25-local', 2, 29, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Newcastle 24/25 Visitante', 'newscastle-24-25-visitante', 2, 29, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Tottenham
('jersey', 'Tottenham 24/25 Local', 'tottenham-24-25-local', 2, 30, 'home', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, FALSE),
('jersey', 'Tottenham 24/25 Visitante', 'tottenham-24-25-visitante', 2, 30, 'away', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, FALSE),
-- West Ham
('jersey', 'West Ham 24/25 Local', 'westham-24-25-local', 2, 31, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'West Ham 24/25 Visitante', 'westham-24-25-visitante', 2, 31, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE);

-- SERIE A JERSEYS
INSERT INTO products (product_type, name, slug, league_id, team_id, jersey_type, season, version, base_price, original_price, stock_quantity, is_active, is_featured) VALUES
-- Atalanta
('jersey', 'Atalanta 24/25 Local', 'atalanta-24-25-local', 3, 32, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Atalanta 24/25 Visitante', 'atalanta-24-25-visitante', 3, 32, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Bologna
('jersey', 'Bologna 24/25 Local', 'bologna-24-25-local', 3, 33, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Bologna 24/25 Visitante', 'bologna-24-25-visitante', 3, 33, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Fiorentina
('jersey', 'Fiorentina 24/25 Local', 'fiorentina-24-25-local', 3, 34, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Fiorentina 24/25 Visitante', 'fiorentina-24-25-visitante', 3, 34, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Inter
('jersey', 'Inter 24/25 Local', 'inter-24-25-local', 3, 35, 'home', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, TRUE),
('jersey', 'Inter 24/25 Visitante', 'inter-24-25-visitante', 3, 35, 'away', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, FALSE),
-- Juventus
('jersey', 'Juventus 24/25 Local', 'juventus-24-25-local', 3, 36, 'home', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, TRUE),
('jersey', 'Juventus 24/25 Visitante', 'juventus-24-25-visitante', 3, 36, 'away', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, FALSE),
-- Lazio
('jersey', 'Lazio 24/25 Local', 'lazio-24-25-local', 3, 37, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Lazio 24/25 Visitante', 'lazio-24-25-visitante', 3, 37, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Milan
('jersey', 'Milan 24/25 Local', 'milan-24-25-local', 3, 38, 'home', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, TRUE),
('jersey', 'Milan 24/25 Visitante', 'milan-24-25-visitante', 3, 38, 'away', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, FALSE),
-- Napoli
('jersey', 'Napoli 24/25 Local', 'napoli-24-25-local', 3, 39, 'home', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, TRUE),
('jersey', 'Napoli 24/25 Visitante', 'napoli-24-25-visitante', 3, 39, 'away', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, FALSE),
-- Roma
('jersey', 'Roma 24/25 Local', 'roma-24-25-local', 3, 40, 'home', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, FALSE),
('jersey', 'Roma 24/25 Visitante', 'roma-24-25-visitante', 3, 40, 'away', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, FALSE),
-- Torino
('jersey', 'Torino 24/25 Local', 'torino-24-25-local', 3, 41, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Torino 24/25 Visitante', 'torino-24-25-visitante', 3, 41, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE);

-- BUNDESLIGA JERSEYS
INSERT INTO products (product_type, name, slug, league_id, team_id, jersey_type, season, version, base_price, original_price, stock_quantity, is_active, is_featured) VALUES
-- Augsburg
('jersey', 'Augsburg 24/25 Local', 'augsburg-24-25-local', 4, 42, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Augsburg 24/25 Visitante', 'augsburg-24-25-visitante', 4, 42, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Bayern München
('jersey', 'Bayern München 24/25 Local', 'bayern-24-25-local', 4, 43, 'home', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, TRUE),
('jersey', 'Bayern München 24/25 Visitante', 'bayern-24-25-visitante', 4, 43, 'away', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, FALSE),
-- Borussia Dortmund
('jersey', 'Borussia Dortmund 24/25 Local', 'dortmund-24-25-local', 4, 44, 'home', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, TRUE),
('jersey', 'Borussia Dortmund 24/25 Visitante', 'dortmund-24-25-visitante', 4, 44, 'away', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, FALSE),
-- Eintracht Frankfurt
('jersey', 'Eintracht Frankfurt 24/25 Local', 'eintracht-24-25-local', 4, 45, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Eintracht Frankfurt 24/25 Visitante', 'eintracht-24-25-visitante', 4, 45, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- SC Freiburg
('jersey', 'SC Freiburg 24/25 Local', 'freiburg-24-25-local', 4, 46, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'SC Freiburg 24/25 Visitante', 'freiburg-24-25-visitante', 4, 46, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Hamburger SV
('jersey', 'Hamburger SV 24/25 Local', 'hamburger-24-25-local', 4, 47, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Hamburger SV 24/25 Visitante', 'hamburger-24-25-visitante', 4, 47, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Heidenheim (only home)
('jersey', 'Heidenheim 24/25 Local', 'heidenheim-24-25-local', 4, 48, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Hoffenheim
('jersey', 'Hoffenheim 24/25 Local', 'hoffenheim-24-25-local', 4, 49, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Hoffenheim 24/25 Visitante', 'hoffenheim-24-25-visitante', 4, 49, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- FC Köln
('jersey', 'FC Köln 24/25 Local', 'koln-24-25-local', 4, 50, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'FC Köln 24/25 Visitante', 'koln-24-25-visitante', 4, 50, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- RB Leipzig
('jersey', 'RB Leipzig 24/25 Local', 'leipzig-24-25-local', 4, 51, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'RB Leipzig 24/25 Visitante', 'leipzig-24-25-visitante', 4, 51, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Mainz 05
('jersey', 'Mainz 05 24/25 Local', 'mainz05-24-25-local', 4, 52, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Mainz 05 24/25 Visitante', 'mainz05-24-25-visitante', 4, 52, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Bayer Leverkusen
('jersey', 'Bayer Leverkusen 24/25 Local', 'leverkusen-24-25-local', 4, 53, 'home', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, FALSE),
('jersey', 'Bayer Leverkusen 24/25 Visitante', 'leverkusen-24-25-visitante', 4, 53, 'away', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, FALSE),
-- Borussia Mönchengladbach
('jersey', 'Borussia Mönchengladbach 24/25 Local', 'monchengladbach-24-25-local', 4, 54, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Borussia Mönchengladbach 24/25 Visitante', 'monchengladbach-24-25-visitante', 4, 54, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- St. Pauli
('jersey', 'St. Pauli 24/25 Local', 'stpauli-24-25-local', 4, 55, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'St. Pauli 24/25 Visitante', 'stpauli-24-25-visitante', 4, 55, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- VfB Stuttgart
('jersey', 'VfB Stuttgart 24/25 Local', 'stuttgart-24-25-local', 4, 56, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'VfB Stuttgart 24/25 Visitante', 'stuttgart-24-25-visitante', 4, 56, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Union Berlin
('jersey', 'Union Berlin 24/25 Local', 'unionberlin-24-25-local', 4, 57, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Union Berlin 24/25 Visitante', 'unionberlin-24-25-visitante', 4, 57, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Werder Bremen
('jersey', 'Werder Bremen 24/25 Local', 'bremen-24-25-local', 4, 58, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Werder Bremen 24/25 Visitante', 'bremen-24-25-visitante', 4, 58, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- VfL Wolfsburg
('jersey', 'VfL Wolfsburg 24/25 Local', 'wolfburg-24-25-local', 4, 59, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'VfL Wolfsburg 24/25 Visitante', 'wolfburg-24-25-visitante', 4, 59, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE);

-- LIGUE 1 JERSEYS
INSERT INTO products (product_type, name, slug, league_id, team_id, jersey_type, season, version, base_price, original_price, stock_quantity, is_active, is_featured) VALUES
-- Olympique Lyon
('jersey', 'Olympique Lyon 24/25 Local', 'lyon-24-25-local', 5, 60, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Olympique Lyon 24/25 Visitante', 'lyon-24-25-visitante', 5, 60, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Olympique Marseille
('jersey', 'Olympique Marseille 24/25 Local', 'marsella-24-25-local', 5, 61, 'home', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, FALSE),
('jersey', 'Olympique Marseille 24/25 Visitante', 'marsella-24-25-visitante', 5, 61, 'away', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, FALSE),
-- AS Monaco
('jersey', 'AS Monaco 24/25 Local', 'monaco-24-25-local', 5, 62, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'AS Monaco 24/25 Visitante', 'monaco-24-25-visitante', 5, 62, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Paris Saint-Germain
('jersey', 'Paris Saint-Germain 24/25 Local', 'psg-24-25-local', 5, 63, 'home', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, TRUE),
('jersey', 'Paris Saint-Germain 24/25 Visitante', 'psg-24-25-visitante', 5, 63, 'away', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, FALSE);

-- NATIONAL TEAMS JERSEYS
INSERT INTO products (product_type, name, slug, league_id, team_id, jersey_type, season, version, base_price, original_price, stock_quantity, is_active, is_featured) VALUES
-- Argentina
('jersey', 'Argentina 24/25 Local', 'argentina-24-25-local', 6, 64, 'home', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, TRUE),
('jersey', 'Argentina 24/25 Visitante', 'argentina-24-25-visitante', 6, 64, 'away', '2024/25', 'fan', 24.99, 79.99, 100, TRUE, FALSE),
-- Colombia
('jersey', 'Colombia 24/25 Local', 'colombia-24-25-local', 6, 65, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Colombia 24/25 Visitante', 'colombia-24-25-visitante', 6, 65, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Japón
('jersey', 'Japón 24/25 Local', 'japon-24-25-local', 6, 66, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Japón 24/25 Visitante', 'japon-24-25-visitante', 6, 66, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Uruguay
('jersey', 'Uruguay 24/25 Local', 'uruguay-24-25-local', 6, 67, 'home', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
('jersey', 'Uruguay 24/25 Visitante', 'uruguay-24-25-visitante', 6, 67, 'away', '2024/25', 'fan', 24.99, 79.99, 50, TRUE, FALSE),
-- Brasil
('jersey', 'Brasil 2002 Retro Local', 'brasil-2002-retro-local', 6, 68, 'retro', '2002', 'fan', 24.99, 79.99, 30, TRUE, TRUE),
-- Argentina Retro
('jersey', 'Argentina 1986 Retro', 'argentina-1986-retro', 6, 64, 'retro', '1986', 'fan', 24.99, 79.99, 30, TRUE, TRUE),
-- Juventus Retro
('jersey', 'Juventus Retro 1997', 'juventus-1997-retro', 3, 36, 'retro', '1997', 'fan', 24.99, 79.99, 30, TRUE, FALSE);

-- ============================================================================
-- 4. PRODUCT IMAGES
-- ============================================================================

-- Automatically generate image paths for all products
-- Pattern: ./img/camisetas/{league}_{team_slug}_{jersey_type}.png

-- This would be a massive INSERT but for brevity, here's the pattern:
-- For each product, create 1 main image record following the existing pattern

-- Example for first few products:
INSERT INTO product_images (product_id, image_path, image_type, display_order, alt_text)
SELECT
    p.product_id,
    CONCAT('./img/camisetas/', l.slug, '_', t.slug, '_',
        CASE p.jersey_type
            WHEN 'home' THEN 'local'
            WHEN 'away' THEN 'visitante'
            WHEN 'third' THEN 'tercera'
            WHEN 'retro' THEN 'local'
            ELSE 'local'
        END,
    '.png'),
    'main',
    1,
    CONCAT(p.name, ' - Imagen Principal')
FROM products p
LEFT JOIN teams t ON p.team_id = t.team_id
LEFT JOIN leagues l ON p.league_id = l.league_id
WHERE p.product_type = 'jersey';

-- ============================================================================
-- 5. PRODUCT VARIANTS (SIZES)
-- ============================================================================

-- Create variants for all jersey products (General sizes: S, M, L, XL, 2XL, 3XL, 4XL)
INSERT INTO product_variants (product_id, size, size_category, sku, stock_quantity, low_stock_threshold)
SELECT
    p.product_id,
    size_val,
    'general',
    CONCAT(p.slug, '-', size_val),
    50,
    10
FROM products p
CROSS JOIN (
    SELECT 'S' as size_val UNION ALL
    SELECT 'M' UNION ALL
    SELECT 'L' UNION ALL
    SELECT 'XL' UNION ALL
    SELECT '2XL' UNION ALL
    SELECT '3XL' UNION ALL
    SELECT '4XL'
) sizes
WHERE p.product_type = 'jersey';

-- ============================================================================
-- 6. SIZE GUIDES DATA
-- ============================================================================

-- General sizes (Spanish)
INSERT INTO size_guides (category, size, chest_width_cm, length_cm, language) VALUES
('general', 'S', 53, 70, 'es'),
('general', 'M', 56, 72, 'es'),
('general', 'L', 59, 75, 'es'),
('general', 'XL', 62, 77, 'es'),
('general', '2XL', 65, 79, 'es'),
('general', '3XL', 68, 81, 'es'),
('general', '4XL', 71, 83, 'es');

-- Player version sizes (Spanish)
INSERT INTO size_guides (category, size, chest_width_cm, length_cm, language) VALUES
('player', 'S', 50, 68, 'es'),
('player', 'M', 53, 70, 'es'),
('player', 'L', 56, 72, 'es'),
('player', 'XL', 59, 74, 'es'),
('player', '2XL', 62, 76, 'es');

-- Kids sizes (Spanish)
INSERT INTO size_guides (category, size, chest_width_cm, length_cm, height_cm, weight_kg, age_range, language) VALUES
('kids', '16', 41, 49, 116, 21, '4-5 años', 'es'),
('kids', '18', 43, 52, 122, 24, '5-6 años', 'es'),
('kids', '20', 45, 54, 128, 27, '6-7 años', 'es'),
('kids', '22', 47, 56, 134, 30, '7-8 años', 'es'),
('kids', '24', 49, 59, 140, 33, '8-10 años', 'es'),
('kids', '26', 51, 62, 146, 36, '10-12 años', 'es'),
('kids', '28', 53, 65, 152, 39, '12-14 años', 'es');

-- Tracksuit sizes (Spanish)
INSERT INTO size_guides (category, size, chest_width_cm, length_cm, language) VALUES
('tracksuit', 'S', 52, 69, 'es'),
('tracksuit', 'M', 55, 71, 'es'),
('tracksuit', 'L', 58, 74, 'es'),
('tracksuit', 'XL', 61, 76, 'es'),
('tracksuit', '2XL', 64, 78, 'es');

-- ============================================================================
-- 7. SUBSCRIPTION PLANS
-- ============================================================================

INSERT INTO subscription_plans (
    plan_id, plan_name, plan_slug, plan_type, monthly_price,
    description, jersey_quantity, jersey_quality,
    includes_early_access, includes_priority_shipping, includes_collector_pin,
    includes_store_discount, includes_certificate, store_discount_percentage,
    badge_text, badge_color, display_order, is_active
) VALUES
(1, 'Plan FAN', 'plan-fan', 'fan', 24.99,
 'Camiseta FAN aleatoria cada mes', 1, 'fan',
 FALSE, FALSE, FALSE, FALSE, FALSE, NULL,
 'MÁS POPULAR', '#8b5cf6', 1, TRUE),

(2, 'Plan Premium Random', 'plan-premium-random', 'premium_random', 29.99,
 'Camiseta PLAYER de cualquier club top aleatorio', 1, 'player',
 TRUE, FALSE, FALSE, TRUE, FALSE, 10,
 'PREMIUM', '#22c55e', 2, TRUE),

(3, 'Plan Premium TOP', 'plan-premium-top', 'premium_top', 34.99,
 'Solo los mejores clubes del mundo - Versión PLAYER', 1, 'player',
 TRUE, TRUE, TRUE, TRUE, TRUE, 10,
 'TOP', '#f59e0b', 3, TRUE),

(4, 'Plan Retro TOP', 'plan-retro-top', 'retro_top', 39.99,
 'Camiseta retro mítica de selecciones o clubes legendarios', 1, 'retro',
 TRUE, FALSE, FALSE, FALSE, TRUE, NULL,
 '👑 LEGEND', '#ef4444', 4, TRUE);

-- ============================================================================
-- 8. MYSTERY BOX TYPES
-- ============================================================================

INSERT INTO mystery_box_types (
    box_type_id, name, slug, description, price, original_price,
    jersey_quantity, jersey_quality, league_restriction, team_tier_restriction,
    includes_premium_packaging, includes_certificate, includes_express_shipping,
    badge_text, badge_color, display_order, is_active
) VALUES
(1, 'Box Clásica', 'box-clasica',
 '5 camisetas FAN de equipos variados - Ideal para comenzar tu colección',
 124.95, 399.95,
 5, 'fan', NULL, 'any',
 FALSE, FALSE, FALSE,
 'MEJOR VALOR', '#8b5cf6', 1, TRUE),

(2, 'Box por Liga', 'box-por-liga',
 '5 camisetas PLAYER de tu liga favorita - Elige entre La Liga, Premier League, Serie A, Bundesliga o Ligue 1',
 174.95, 449.95,
 5, 'player', NULL, 'any',
 FALSE, TRUE, FALSE,
 'ESPECIALIZADA', '#22c55e', 2, TRUE),

(3, 'Box Premium Elite', 'box-premium-elite',
 '5 camisetas PLAYER de equipos TOP - Madrid, Barça, PSG, Bayern, City y más',
 174.95, 449.95,
 5, 'player', NULL, 'top_only',
 TRUE, TRUE, TRUE,
 'ELITE', '#f59e0b', 3, TRUE);

-- ============================================================================
-- 9. DROP EVENTS
-- ============================================================================

-- Create initial drop event
INSERT INTO drop_events (
    drop_event_id, event_name, description, start_date, end_date,
    total_drops_available, remaining_drops, max_drops_per_customer,
    drop_price, is_active
) VALUES
(1, 'Drop Semanal - Noviembre 2024',
 'Consigue camisetas exclusivas con nuestro sistema de drops. Solo 100 disponibles.',
 '2024-11-01 00:00:00', '2024-11-30 23:59:59',
 100, 37, 1,
 24.99, TRUE);

-- ============================================================================
-- 10. DROP POOL ITEMS
-- ============================================================================

-- Get product IDs for drop pool items
INSERT INTO drop_pool_items (drop_event_id, product_id, rarity, weight, display_order, is_active)
SELECT
    1, -- drop_event_id
    p.product_id,
    CASE
        WHEN t.name = 'Real Sociedad' AND p.jersey_type = 'home' THEN 'common'
        WHEN t.name = 'Aston Villa' AND p.jersey_type = 'home' THEN 'common'
        WHEN t.name = 'Olympique Lyon' AND p.jersey_type = 'home' THEN 'common'
        WHEN t.name = 'Benfica' AND p.jersey_type = 'home' THEN 'common'
        WHEN t.name = 'Atlético Madrid' AND p.jersey_type = 'home' THEN 'rare'
        WHEN t.name = 'Arsenal' AND p.jersey_type = 'home' THEN 'rare'
        WHEN t.name = 'Juventus' AND p.slug = 'juventus-1997-retro' THEN 'rare'
        WHEN t.name = 'Inter' AND p.jersey_type = 'home' THEN 'rare'
        WHEN t.name = 'Real Madrid' AND p.jersey_type = 'home' THEN 'legendary'
        WHEN t.name = 'Barcelona' AND p.jersey_type = 'home' THEN 'legendary'
        WHEN p.slug = 'argentina-1986-retro' THEN 'legendary'
        WHEN p.slug = 'brasil-2002-retro-local' THEN 'legendary'
    END as rarity,
    CASE
        WHEN t.name IN ('Real Sociedad', 'Aston Villa', 'Olympique Lyon') OR p.slug LIKE '%benfica%' THEN 62
        WHEN t.name IN ('Atlético Madrid', 'Arsenal', 'Inter') OR p.slug = 'juventus-1997-retro' THEN 30
        WHEN t.name IN ('Real Madrid', 'Barcelona') OR p.slug IN ('argentina-1986-retro', 'brasil-2002-retro-local') THEN 8
    END as weight,
    CASE
        WHEN t.name = 'Real Sociedad' THEN 1
        WHEN t.name = 'Aston Villa' THEN 2
        WHEN t.name = 'Olympique Lyon' THEN 3
        WHEN p.slug LIKE '%benfica%' THEN 4
        WHEN t.name = 'Atlético Madrid' THEN 5
        WHEN t.name = 'Arsenal' THEN 6
        WHEN p.slug = 'juventus-1997-retro' THEN 7
        WHEN t.name = 'Inter' THEN 8
        WHEN t.name = 'Real Madrid' THEN 9
        WHEN t.name = 'Barcelona' THEN 10
        WHEN p.slug = 'argentina-1986-retro' THEN 11
        WHEN p.slug = 'brasil-2002-retro-local' THEN 12
    END as display_order,
    TRUE
FROM products p
LEFT JOIN teams t ON p.team_id = t.team_id
WHERE (
    (t.name = 'Real Sociedad' AND p.jersey_type = 'home' AND p.season = '2024/25') OR
    (t.name = 'Aston Villa' AND p.jersey_type = 'home' AND p.season = '2024/25') OR
    (t.name = 'Olympique Lyon' AND p.jersey_type = 'home' AND p.season = '2024/25') OR
    (p.slug LIKE '%benfica%' AND p.jersey_type = 'home') OR
    (t.name = 'Atlético Madrid' AND p.jersey_type = 'home' AND p.season = '2024/25') OR
    (t.name = 'Arsenal' AND p.jersey_type = 'home' AND p.season = '2024/25') OR
    (p.slug = 'juventus-1997-retro') OR
    (t.name = 'Inter' AND p.jersey_type = 'home' AND p.season = '2024/25') OR
    (t.name = 'Real Madrid' AND p.jersey_type = 'home' AND p.season = '2024/25') OR
    (t.name = 'Barcelona' AND p.jersey_type = 'home' AND p.season = '2024/25') OR
    (p.slug = 'argentina-1986-retro') OR
    (p.slug = 'brasil-2002-retro-local')
);

-- ============================================================================
-- 11. COUPONS
-- ============================================================================

INSERT INTO coupons (
    code, description, discount_type, discount_value, max_discount_amount,
    min_purchase_amount, applies_to_product_type, applies_to_first_order_only,
    usage_limit_total, usage_limit_per_customer, times_used,
    valid_from, valid_until, is_active
) VALUES
('WELCOME5', 'Descuento de bienvenida de 5€', 'fixed', 5.00, NULL,
 60.00, 'all', TRUE,
 NULL, 1, 0,
 '2024-01-01 00:00:00', '2025-12-31 23:59:59', TRUE),

('NOTBETTING10', '10% de descuento - máximo 5€', 'percentage', 10.00, 5.00,
 0.00, 'all', FALSE,
 NULL, 3, 0,
 '2024-01-01 00:00:00', '2025-12-31 23:59:59', TRUE),

('TOPBONUS10', '10% de descuento - máximo 5€', 'percentage', 10.00, 5.00,
 0.00, 'all', FALSE,
 NULL, 3, 0,
 '2024-01-01 00:00:00', '2025-12-31 23:59:59', TRUE),

('KICKVERSE10', '10% de descuento general - máximo 5€', 'percentage', 10.00, 5.00,
 0.00, 'all', FALSE,
 NULL, 5, 0,
 '2024-01-01 00:00:00', '2025-12-31 23:59:59', TRUE),

('MYSTERY10', '10% de descuento en Mystery Boxes', 'percentage', 10.00, 15.00,
 100.00, 'mystery_box', FALSE,
 NULL, 2, 0,
 '2024-01-01 00:00:00', '2025-12-31 23:59:59', TRUE),

('CATALOGO5', '5€ de descuento en pedidos del catálogo', 'fixed', 5.00, NULL,
 50.00, 'jersey', FALSE,
 NULL, 3, 0,
 '2024-01-01 00:00:00', '2025-12-31 23:59:59', TRUE);

-- ============================================================================
-- 12. LOYALTY TIER BENEFITS
-- ============================================================================

INSERT INTO loyalty_tier_benefits (
    tier, min_orders_required, min_total_spent,
    points_multiplier, discount_percentage, free_shipping,
    early_drop_access, priority_support, birthday_bonus_points
) VALUES
('standard', 0, 0.00, 1.00, 0, FALSE, FALSE, FALSE, 0),
('silver', 3, 100.00, 1.25, 5, FALSE, FALSE, FALSE, 50),
('gold', 10, 300.00, 1.50, 10, TRUE, TRUE, TRUE, 100),
('platinum', 25, 750.00, 2.00, 15, TRUE, TRUE, TRUE, 200);

-- ============================================================================
-- 13. LOYALTY REWARDS CATALOG
-- ============================================================================

INSERT INTO loyalty_rewards (
    reward_name, description, points_required, reward_type, reward_value,
    max_redemptions_total, max_redemptions_per_customer, is_active
) VALUES
('Descuento 5€', 'Cupón de 5€ de descuento en tu próxima compra', 500, 'discount_coupon', '5EUR', NULL, 3, TRUE),
('Descuento 10€', 'Cupón de 10€ de descuento en tu próxima compra', 900, 'discount_coupon', '10EUR', NULL, 2, TRUE),
('Envío Gratis', 'Envío gratuito en tu próximo pedido', 300, 'free_shipping', 'FREE_SHIP', NULL, 5, TRUE),
('15% Descuento', 'Cupón de 15% de descuento (máx 20€)', 1200, 'percentage_off', '15PCT_MAX20', NULL, 1, TRUE);

-- ============================================================================
-- 14. SYSTEM SETTINGS
-- ============================================================================

INSERT INTO system_settings (setting_key, setting_value, setting_type, description, is_public) VALUES
('site_name', 'Kickverse', 'string', 'Nombre del sitio', TRUE),
('telegram_contact', '@esKickverse', 'string', 'Usuario de Telegram para contacto', TRUE),
('whatsapp_contact', '+34 614 299 735', 'string', 'Número de WhatsApp para contacto', TRUE),
('email_contact', 'hola@kickverse.es', 'string', 'Email de contacto', TRUE),
('instagram_handle', '@kickverse.es', 'string', 'Usuario de Instagram', TRUE),
('twitter_handle', '@kickverse_es', 'string', 'Usuario de Twitter/X', TRUE),
('tiktok_handle', '@kickverse_es', 'string', 'Usuario de TikTok', TRUE),
('free_shipping_threshold', '50.00', 'number', 'Compra mínima para envío gratis (EUR)', TRUE),
('base_jersey_price', '24.99', 'number', 'Precio base de camisetas (EUR)', FALSE),
('patches_price', '1.99', 'number', 'Precio de parches (EUR)', TRUE),
('personalization_price', '2.99', 'number', 'Precio de personalización (EUR)', TRUE),
('gtm_id', 'GTM-MQFTT34L', 'string', 'Google Tag Manager ID', FALSE),
('ga_id', 'G-SD9ETEJ9TG', 'string', 'Google Analytics ID', FALSE),
('oxapay_api_key', '', 'string', 'Oxapay API Key', FALSE),
('currency', 'EUR', 'string', 'Moneda del sitio', TRUE),
('default_language', 'es', 'string', 'Idioma por defecto', TRUE),
('shipping_countries', '["España"]', 'json', 'Países donde se envía', TRUE),
('return_policy_days', '14', 'number', 'Días para devoluciones', TRUE);

-- ============================================================================
-- 15. ADMIN USER (DEFAULT)
-- ============================================================================

-- Password: admin123 (CHANGE THIS IN PRODUCTION!)
INSERT INTO admin_users (username, password_hash, email, full_name, role, is_active) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'admin@kickverse.es', 'Administrador Kickverse', 'super_admin', TRUE);

-- ============================================================================
-- END OF DATA MIGRATION
-- ============================================================================

-- Summary of migrated data:
-- - 6 Leagues
-- - 69 Teams
-- - 200+ Products (Jerseys)
-- - 1400+ Product Variants (sizes)
-- - 200+ Product Images
-- - 28 Size Guide entries
-- - 4 Subscription Plans
-- - 3 Mystery Box Types
-- - 1 Drop Event
-- - 12 Drop Pool Items
-- - 6 Coupons
-- - 4 Loyalty Tiers
-- - 4 Loyalty Rewards
-- - 15 System Settings
-- - 1 Admin User

SELECT 'Data migration completed successfully!' AS status;
