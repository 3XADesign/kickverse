<header class="header">
    <div class="container header-container">
        <a href="/" class="header-logo">
            <img src="/img/logo.png" alt="Kickverse Logo">
            <span>Kickverse</span>
        </a>

        <nav class="header-nav">
            <a href="/" class="nav-link"><?= __('nav.home') ?></a>
            <a href="/mystery-box" class="nav-link nav-highlight">
                <i class="fas fa-gift"></i>
                <?= __('nav.mystery_box') ?>
            </a>
            <a href="/productos" class="nav-link"><?= __('nav.jerseys') ?></a>
            <a href="/ligas" class="nav-link"><?= __('nav.leagues') ?></a>
        </nav>

        <div class="header-actions">
            <!-- Language Selector -->
            <div class="lang-selector">
                <button class="lang-button" onclick="toggleLangDropdown()">
                    <span class="lang-flag">
                        <?php if (i18n::getLang() === 'es'): ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="15" viewBox="0 0 900 600">
                                <rect fill="#c60b1e" width="900" height="600"/>
                                <rect fill="#ffc400" y="150" width="900" height="300"/>
                            </svg>
                        <?php else: ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="15" viewBox="0 0 60 30">
                                <clipPath id="s"><path d="M0,0 v30 h60 v-30 z"/></clipPath>
                                <clipPath id="t"><path d="M30,15 h30 v15 z v15 h-30 z h-30 v-15 z v-15 h30 z"/></clipPath>
                                <g clip-path="url(#s)"><path d="M0,0 v30 h60 v-30 z" fill="#012169"/><path d="M0,0 L60,30 M60,0 L0,30" stroke="#fff" stroke-width="6"/><path d="M0,0 L60,30 M60,0 L0,30" clip-path="url(#t)" stroke="#C8102E" stroke-width="4"/><path d="M30,0 v30 M0,15 h60" stroke="#fff" stroke-width="10"/><path d="M30,0 v30 M0,15 h60" stroke="#C8102E" stroke-width="6"/></g>
                            </svg>
                        <?php endif; ?>
                    </span>
                    <span class="lang-code"><?= strtoupper(i18n::getLang()) ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="lang-dropdown" id="lang-dropdown">
                    <button class="lang-option" onclick="changeLang('es')" data-lang="es">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="18" viewBox="0 0 900 600">
                            <rect fill="#c60b1e" width="900" height="600"/>
                            <rect fill="#ffc400" y="150" width="900" height="300"/>
                        </svg>
                        <span>Español</span>
                    </button>
                    <button class="lang-option" onclick="changeLang('en')" data-lang="en">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="18" viewBox="0 0 60 30">
                            <clipPath id="s2"><path d="M0,0 v30 h60 v-30 z"/></clipPath>
                            <clipPath id="t2"><path d="M30,15 h30 v15 z v15 h-30 z h-30 v-15 z v-15 h30 z"/></clipPath>
                            <g clip-path="url(#s2)"><path d="M0,0 v30 h60 v-30 z" fill="#012169"/><path d="M0,0 L60,30 M60,0 L0,30" stroke="#fff" stroke-width="6"/><path d="M0,0 L60,30 M60,0 L0,30" clip-path="url(#t2)" stroke="#C8102E" stroke-width="4"/><path d="M30,0 v30 M0,15 h60" stroke="#fff" stroke-width="10"/><path d="M30,0 v30 M0,15 h60" stroke="#C8102E" stroke-width="6"/></g>
                        </svg>
                        <span>English</span>
                    </button>
                </div>
            </div>

            <?php if (isset($_SESSION['user'])): ?>
                <div class="account-dropdown-wrapper">
                    <button onclick="toggleAccountDropdown(event)" class="btn btn-secondary btn-sm account-dropdown-btn">
                        <i class="fas fa-user"></i>
                        <?= __('nav.account') ?>
                        <i class="fas fa-chevron-down account-chevron"></i>
                    </button>
                    <div class="account-dropdown" id="account-dropdown">
                        <a href="/mi-cuenta/perfil" class="account-dropdown-item">
                            <i class="fas fa-user-circle"></i>
                            <span><?= __('account.profile') ?></span>
                        </a>
                        <a href="/mi-cuenta/pedidos" class="account-dropdown-item">
                            <i class="fas fa-box"></i>
                            <span><?= __('account.orders') ?></span>
                        </a>
                        <a href="/mi-cuenta/suscripciones" class="account-dropdown-item">
                            <i class="fas fa-crown"></i>
                            <span><?= __('account.subscriptions') ?></span>
                        </a>
                        <div class="account-dropdown-divider"></div>
                        <a href="/api/auth/logout" class="account-dropdown-item account-dropdown-logout">
                            <i class="fas fa-sign-out-alt"></i>
                            <span><?= __('account.logout') ?></span>
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <button onclick="openLoginModal()" class="btn btn-secondary btn-sm">
                    <i class="fas fa-sign-in-alt"></i>
                    <?= __('nav.login') ?>
                </button>
            <?php endif; ?>

            <?php if (!isset($_SERVER['REQUEST_URI']) || strpos($_SERVER['REQUEST_URI'], '/admin') !== 0): ?>
            <a href="/carrito" class="cart-link" title="Ver carrito">
                <div class="cart-icon-wrapper">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <span class="cart-count" id="cart-count">0</span>
            </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Floating Menu Button (Mobile) -->
<button class="floating-menu-btn" id="floating-menu-btn" aria-label="Menu">
    <span class="menu-line"></span>
    <span class="menu-line"></span>
    <span class="menu-line"></span>
</button>

<!-- Mobile Sidebar Menu -->
<div class="mobile-menu-overlay" id="mobile-menu-overlay"></div>
<div class="mobile-menu-sidebar" id="mobile-menu-sidebar">
    <div class="mobile-menu-header">
        <img src="/img/logo.png" alt="Kickverse" style="height: 40px;">
        <button class="mobile-menu-close" id="mobile-menu-close">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <nav class="mobile-menu-nav">
        <a href="/" class="mobile-nav-link">
            <i class="fas fa-home"></i>
            <span><?= __('nav.home') ?></span>
        </a>
        <a href="/mystery-box" class="mobile-nav-link mobile-nav-highlight">
            <i class="fas fa-gift"></i>
            <span><?= __('nav.mystery_box') ?></span>
        </a>
        <a href="/productos" class="mobile-nav-link">
            <i class="fas fa-tshirt"></i>
            <span><?= __('nav.jerseys') ?></span>
        </a>
        <a href="/ligas" class="mobile-nav-link">
            <i class="fas fa-trophy"></i>
            <span><?= __('nav.leagues') ?></span>
        </a>
    </nav>

</div>

<!-- Login Modal -->
<div id="loginModal" class="auth-modal">
    <div class="auth-modal-overlay" onclick="closeLoginModal()"></div>
    <div class="auth-modal-content">
        <button class="auth-modal-close" onclick="closeLoginModal()">
            <i class="fas fa-times"></i>
        </button>
        <div class="auth-modal-header">
            <h2><?= __('auth.login_title') ?></h2>
            <p><?= i18n::getLang() === 'es' ? 'Bienvenido de nuevo a Kickverse' : 'Welcome back to Kickverse' ?></p>
        </div>
        <form id="loginForm" onsubmit="handleLogin(event)" class="auth-form">
            <div id="loginError" class="auth-error" style="display:none;">
                <i class="fas fa-exclamation-circle"></i>
                <span id="loginErrorMessage"></span>
            </div>
            <div class="form-group">
                <label for="login-email">
                    <i class="fas fa-envelope"></i>
                    <?= __('auth.email') ?>
                </label>
                <input
                    type="email"
                    id="login-email"
                    name="email"
                    required
                    placeholder="<?= i18n::getLang() === 'es' ? 'tu@email.com' : 'your@email.com' ?>"
                    autocomplete="email"
                >
            </div>
            <div class="form-group">
                <label for="login-password">
                    <i class="fas fa-lock"></i>
                    <?= __('auth.password') ?>
                </label>
                <input
                    type="password"
                    id="login-password"
                    name="password"
                    required
                    placeholder="<?= i18n::getLang() === 'es' ? 'Tu contraseña' : 'Your password' ?>"
                    autocomplete="current-password"
                >
            </div>
            <button type="submit" class="btn btn-primary btn-block auth-submit-btn">
                <span class="btn-text"><?= __('auth.login_title') ?></span>
                <span class="btn-loading" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i> <?= i18n::getLang() === 'es' ? 'Iniciando...' : 'Logging in...' ?>
                </span>
            </button>
            <div class="auth-divider">
                <span><?= __('auth.no_account') ?></span>
            </div>
            <button type="button" onclick="showRegisterModal()" class="btn btn-outline btn-block">
                <?= __('auth.register_title') ?>
            </button>
        </form>
    </div>
</div>

<!-- Register Modal -->
<div id="registerModal" class="auth-modal">
    <div class="auth-modal-overlay" onclick="closeRegisterModal()"></div>
    <div class="auth-modal-content">
        <button class="auth-modal-close" onclick="closeRegisterModal()">
            <i class="fas fa-times"></i>
        </button>
        <div class="auth-modal-header">
            <h2><?= __('auth.register_title') ?></h2>
            <p><?= i18n::getLang() === 'es' ? 'Únete a la comunidad Kickverse' : 'Join the Kickverse community' ?></p>
        </div>
        <form id="registerForm" onsubmit="handleRegister(event)" class="auth-form">
            <div id="registerError" class="auth-error" style="display: none;">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong id="registerErrorTitle"><?= __('common.error') ?></strong>
                    <p id="registerErrorMessage"></p>
                </div>
            </div>
            <div class="form-group">
                <label for="register-name">
                    <i class="fas fa-user"></i>
                    <?= __('auth.name') ?>
                </label>
                <input
                    type="text"
                    id="register-name"
                    name="name"
                    required
                    minlength="3"
                    placeholder="<?= i18n::getLang() === 'es' ? 'Juan Pérez' : 'John Doe' ?>"
                    autocomplete="name"
                >
            </div>
            <div class="form-group">
                <label for="register-email">
                    <i class="fas fa-envelope"></i>
                    <?= __('auth.email') ?>
                </label>
                <input
                    type="email"
                    id="register-email"
                    name="email"
                    required
                    placeholder="<?= i18n::getLang() === 'es' ? 'tu@email.com' : 'your@email.com' ?>"
                    autocomplete="email"
                >
            </div>
            <div class="form-group">
                <label for="register-password">
                    <i class="fas fa-lock"></i>
                    <?= __('auth.password') ?>
                </label>
                <input
                    type="password"
                    id="register-password"
                    name="password"
                    required
                    minlength="6"
                    placeholder="<?= i18n::getLang() === 'es' ? 'Mínimo 6 caracteres' : 'Minimum 6 characters' ?>"
                    autocomplete="new-password"
                >
            </div>
            <div class="form-group">
                <label for="register-password-confirm">
                    <i class="fas fa-lock"></i>
                    <?= __('auth.password_confirm') ?>
                </label>
                <input
                    type="password"
                    id="register-password-confirm"
                    name="password_confirm"
                    required
                    minlength="6"
                    placeholder="<?= i18n::getLang() === 'es' ? 'Repite tu contraseña' : 'Repeat your password' ?>"
                    autocomplete="new-password"
                >
            </div>
            <button type="submit" class="btn btn-primary btn-block auth-submit-btn">
                <span class="btn-text"><?= __('auth.register_title') ?></span>
                <span class="btn-loading" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i> <?= i18n::getLang() === 'es' ? 'Registrando...' : 'Registering...' ?>
                </span>
            </button>
            <div class="auth-divider">
                <span><?= __('auth.have_account') ?></span>
            </div>
            <button type="button" onclick="showLoginFromRegister()" class="btn btn-outline btn-block">
                <?= __('auth.login_title') ?>
            </button>
        </form>
    </div>
</div>

<!-- Email Verification Modal -->
<div id="verifyEmailModal" class="auth-modal">
    <div class="auth-modal-overlay" onclick="closeVerifyEmailModal()"></div>
    <div class="auth-modal-content" style="max-width: 500px;">
        <button class="auth-modal-close" onclick="closeVerifyEmailModal()">
            <i class="fas fa-times"></i>
        </button>
        <div class="verify-email-content">
            <div class="verify-email-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <h2>¡Verifica tu Email!</h2>
            <p class="verify-email-message">
                Te hemos enviado un correo electrónico a <strong id="verify-email-address"></strong>
                con un enlace de verificación.
            </p>
            <p class="verify-email-submessage">
                Revisa tu bandeja de entrada (y también la carpeta de spam) y haz clic en el enlace para activar tu cuenta.
            </p>
            <button onclick="closeVerifyEmailModal()" class="btn btn-primary btn-block">
                <i class="fas fa-check"></i>
                Entendido
            </button>
        </div>
    </div>
</div>

<!-- Unverified Account Modal -->
<div id="unverifiedModal" class="auth-modal">
    <div class="auth-modal-overlay" onclick="closeUnverifiedModal()"></div>
    <div class="auth-modal-content" style="max-width: 500px;">
        <button class="auth-modal-close" onclick="closeUnverifiedModal()">
            <i class="fas fa-times"></i>
        </button>
        <div class="verify-email-content">
            <div class="verify-email-icon" style="background: rgba(245, 158, 11, 0.1);">
                <i class="fas fa-exclamation-triangle" style="color: #f59e0b; font-size: 3rem;"></i>
            </div>
            <h2 style="margin-bottom: var(--space-3);"><?= __('auth.account_not_verified') ?></h2>
            <p style="color: var(--gray-600); margin-bottom: var(--space-6); line-height: 1.6;">
                <?= __('auth.account_not_verified_message') ?>
            </p>
            <p style="color: var(--gray-500); font-size: 0.875rem; margin-bottom: var(--space-6);">
                <?= __('auth.check_spam') ?>
            </p>
            <button onclick="resendVerificationEmail()" id="resendVerificationBtn" class="btn btn-primary btn-block">
                <i class="fas fa-envelope"></i>
                <?= __('auth.resend_verification') ?>
            </button>
        </div>
    </div>
</div>

<!-- Resend Verification Result Modal -->
<div id="resendResultModal" class="auth-modal">
    <div class="auth-modal-overlay" onclick="closeResendResultModal()"></div>
    <div class="auth-modal-content" style="max-width: 450px;">
        <button class="auth-modal-close" onclick="closeResendResultModal()">
            <i class="fas fa-times"></i>
        </button>
        <div class="verify-email-content">
            <div class="verify-email-icon" id="resendResultIcon"></div>
            <h2 style="margin-bottom: var(--space-3);" id="resendResultTitle"></h2>
            <p style="color: var(--gray-600); margin-bottom: var(--space-6); line-height: 1.6;" id="resendResultMessage"></p>
            <button onclick="closeResendResultModal()" class="btn btn-primary btn-block">
                <?= __('common.accept') ?>
            </button>
        </div>
    </div>
</div>

<!-- Email Verification Result Modal -->
<div id="verificationResultModal" class="auth-modal">
    <div class="auth-modal-overlay" onclick="closeVerificationResultModal()"></div>
    <div class="auth-modal-content" style="max-width: 500px;">
        <button class="auth-modal-close" onclick="closeVerificationResultModal()">
            <i class="fas fa-times"></i>
        </button>
        <div class="verify-email-content">
            <div class="verify-email-icon" id="verificationResultIcon"></div>
            <h2 style="margin-bottom: var(--space-3);" id="verificationResultTitle"></h2>
            <p style="color: var(--gray-600); margin-bottom: var(--space-6); line-height: 1.6;" id="verificationResultMessage"></p>
            <button onclick="closeVerificationResultModal()" class="btn btn-primary btn-block">
                <?= __('common.accept') ?>
            </button>
        </div>
    </div>
</div>

<style>
/* Email Verification Modal */
.verify-email-content {
    text-align: center;
    padding: var(--space-4) 0;
}

.verify-email-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto var(--space-6);
    background: linear-gradient(135deg, var(--primary), var(--accent));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 24px rgba(176, 84, 233, 0.3);
}

.verify-email-icon i {
    font-size: 3rem;
    color: white;
}

.verify-email-content h2 {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: var(--space-4);
}

.verify-email-message {
    font-size: 1.125rem;
    color: var(--gray-900);
    line-height: 1.6;
    margin-bottom: var(--space-4);
}

.verify-email-message strong {
    color: var(--primary);
    font-weight: 600;
}

.verify-email-submessage {
    font-size: 0.95rem;
    color: var(--gray-600);
    line-height: 1.5;
    margin-bottom: var(--space-6);
    padding: var(--space-4);
    background: var(--gray-50);
    border-radius: var(--radius-md);
}

.verify-email-content .btn {
    font-size: 1.125rem;
    padding: var(--space-4) var(--space-6);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-2);
}

@media (max-width: 768px) {
    .verify-email-icon {
        width: 80px;
        height: 80px;
    }

    .verify-email-icon i {
        font-size: 2.5rem;
    }

    .verify-email-content h2 {
        font-size: 1.75rem;
    }

    .verify-email-message {
        font-size: 1rem;
    }
}

<style>
/* Account Dropdown */
.account-dropdown-wrapper {
    position: relative;
    margin-right: var(--space-3);
}

.account-dropdown-btn {
    display: flex;
    align-items: center;
    gap: var(--space-2);
}

.account-chevron {
    font-size: 0.7rem;
    margin-left: var(--space-1);
    transition: transform 0.2s;
}

.account-dropdown-btn.active .account-chevron {
    transform: rotate(180deg);
}

.account-dropdown {
    position: fixed;
    top: 60px;
    background: white;
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-lg);
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    overflow: hidden;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transform: translateY(-10px);
    transition: all 0.2s;
    z-index: 99999;
    min-width: 200px;
    display: flex;
    flex-direction: column;
}

.account-dropdown.active {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
    transform: translateY(0);
}

.account-dropdown-item {
    display: flex !important;
    align-items: center;
    gap: var(--space-3);
    width: 100%;
    padding: var(--space-3) var(--space-4);
    background: white;
    border: none;
    cursor: pointer;
    transition: background 0.2s;
    font-size: 0.875rem;
    color: var(--gray-900);
    text-align: left;
    white-space: nowrap;
    flex-shrink: 0;
    text-decoration: none;
}

.account-dropdown-item:hover {
    background: var(--gray-50);
}

.account-dropdown-item i {
    width: 20px;
    text-align: center;
    color: var(--primary);
}

.account-dropdown-logout {
    color: #dc2626 !important;
}

.account-dropdown-logout i {
    color: #dc2626 !important;
}

.account-dropdown-logout:hover {
    background: rgba(220, 38, 38, 0.05);
}

.account-dropdown-divider {
    height: 1px;
    background: var(--gray-200);
    margin: var(--space-2) 0;
}

.lang-selector {
    position: relative;
    margin-right: var(--space-3);
}

.lang-button {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    background: white;
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-md);
    padding: 0.5rem var(--space-3);
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-900);
    cursor: pointer;
    transition: all 0.2s;
    outline: none;
    height: 38px;
}

.lang-button:hover {
    border-color: var(--primary);
}

.lang-button:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(176, 84, 233, 0.1);
}

.lang-flag {
    display: flex;
    align-items: center;
    width: 20px;
    height: 15px;
}

.lang-flag svg {
    width: 100%;
    height: 100%;
    border-radius: 2px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.lang-code {
    font-size: 0.875rem;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.lang-button i {
    font-size: 0.7rem;
    margin-left: var(--space-1);
    transition: transform 0.2s;
}

.lang-button.active i {
    transform: rotate(180deg);
}

.lang-dropdown {
    position: fixed;
    top: 60px;
    background: white;
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-lg);
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    overflow: hidden;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transform: translateY(-10px);
    transition: all 0.2s;
    z-index: 99999;
    min-width: 180px;
    display: flex;
    flex-direction: column;
}

.lang-dropdown.active {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
    transform: translateY(0);
}

.lang-option {
    display: flex !important;
    align-items: center;
    gap: var(--space-3);
    width: 100%;
    padding: var(--space-3) var(--space-4);
    background: white;
    border: none;
    cursor: pointer;
    transition: background 0.2s;
    font-size: 0.875rem;
    color: var(--gray-900);
    text-align: left;
    white-space: nowrap;
    flex-shrink: 0;
}

.lang-option:hover {
    background: var(--gray-50);
}

.lang-option:first-child {
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
}

.lang-option:last-child {
    border-radius: 0 0 var(--radius-lg) var(--radius-lg);
}

.lang-option svg {
    width: 24px;
    height: 18px;
    border-radius: 2px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    flex-shrink: 0;
}

.lang-option span {
    font-weight: 500;
    flex: 1;
}

/* Floating Menu Button */
.floating-menu-btn {
    display: none;
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    border: none;
    border-radius: 50%;
    box-shadow: 0 4px 20px rgba(176, 84, 233, 0.4);
    cursor: pointer;
    z-index: 999;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 5px;
    transition: all 0.3s ease;
}

.floating-menu-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(176, 84, 233, 0.6);
}

.floating-menu-btn:active {
    transform: scale(0.95);
}

.menu-line {
    width: 24px;
    height: 3px;
    background: white;
    border-radius: 2px;
    transition: all 0.3s ease;
}

.floating-menu-btn.active .menu-line:nth-child(1) {
    transform: translateY(8px) rotate(45deg);
}

.floating-menu-btn.active .menu-line:nth-child(2) {
    opacity: 0;
}

.floating-menu-btn.active .menu-line:nth-child(3) {
    transform: translateY(-8px) rotate(-45deg);
}

/* Mobile Menu Overlay */
.mobile-menu-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
}

.mobile-menu-overlay.active {
    opacity: 1;
    visibility: visible;
}

/* Mobile Sidebar */
.mobile-menu-sidebar {
    position: fixed;
    top: 0;
    left: -320px;
    width: 320px;
    height: 100vh;
    background: white;
    box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
    z-index: 1001;
    transition: left 0.3s ease;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}

.mobile-menu-sidebar.active {
    left: 0;
}

.mobile-menu-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--space-6);
    border-bottom: 2px solid var(--gray-100);
}

.mobile-menu-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--gray-600);
    cursor: pointer;
    padding: var(--space-2);
    transition: color 0.2s;
}

.mobile-menu-close:hover {
    color: var(--primary);
}

.mobile-menu-nav {
    flex: 1;
    padding: var(--space-4) 0;
}

.mobile-nav-link {
    display: flex;
    align-items: center;
    gap: var(--space-4);
    padding: var(--space-4) var(--space-6);
    color: var(--gray-900);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s;
    border-left: 4px solid transparent;
}

.mobile-nav-link:hover {
    background: var(--gray-50);
    border-left-color: var(--primary);
    color: var(--primary);
}

.mobile-nav-link i {
    font-size: 1.25rem;
    width: 24px;
    text-align: center;
}

.mobile-nav-highlight {
    background: linear-gradient(90deg, rgba(176, 84, 233, 0.1), transparent);
    color: var(--primary);
    border-left-color: var(--primary);
}

@media (max-width: 1030px) {
    /* Hide desktop navigation */
    .header-nav {
        display: none;
    }

    /* Show floating menu button */
    .floating-menu-btn {
        display: flex;
    }
}

@media (max-width: 768px) {
    .lang-selector {
        margin-right: var(--space-2);
    }

    .lang-button {
        padding: var(--space-1) var(--space-2);
    }

    .lang-code {
        display: none;
    }

    .floating-menu-btn {
        width: 56px;
        height: 56px;
        bottom: 20px;
        right: 20px;
    }

    .mobile-menu-sidebar {
        width: 280px;
        left: -280px;
    }
}

/* Auth Modals */
.auth-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 10000;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.auth-modal.active {
    opacity: 1;
}

.auth-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(4px);
}

.auth-modal-content {
    position: relative;
    width: 90%;
    max-width: 450px;
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    padding: var(--space-8);
    max-height: 90vh;
    overflow-y: auto;
    transform: translateY(20px);
    transition: transform 0.3s ease;
}

.auth-modal.active .auth-modal-content {
    transform: translateY(0);
}

.auth-modal-close {
    position: absolute;
    top: var(--space-4);
    right: var(--space-4);
    background: var(--gray-100);
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray-600);
    font-size: 1.25rem;
    transition: all 0.2s;
}

.auth-modal-close:hover {
    background: var(--gray-200);
    color: var(--gray-900);
    transform: rotate(90deg);
}

.auth-modal-header {
    text-align: center;
    margin-bottom: var(--space-6);
}

.auth-modal-header h2 {
    font-size: 1.75rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: var(--space-2);
}

.auth-modal-header p {
    color: var(--gray-600);
    font-size: 0.95rem;
}

.auth-form {
    display: flex;
    flex-direction: column;
    gap: var(--space-4);
}

.auth-form .form-group {
    display: flex;
    flex-direction: column;
    gap: var(--space-2);
}

.auth-form label {
    font-weight: 600;
    color: var(--gray-900);
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: var(--space-2);
}

.auth-form label i {
    color: var(--primary);
    font-size: 0.875rem;
}

.auth-form input {
    width: 100%;
    padding: var(--space-3) var(--space-4);
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-md);
    font-size: 1rem;
    transition: all 0.2s;
    background: white;
}

.auth-form input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(176, 84, 233, 0.1);
}

.auth-form input::placeholder {
    color: var(--gray-400);
}

.auth-submit-btn {
    margin-top: var(--space-2);
    position: relative;
    overflow: hidden;
}

.auth-submit-btn .btn-loading {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: inherit;
}

.auth-divider {
    display: flex;
    align-items: center;
    text-align: center;
    margin: var(--space-3) 0;
}

.auth-divider::before,
.auth-divider::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid var(--gray-200);
}

.auth-divider span {
    padding: 0 var(--space-3);
    color: var(--gray-500);
    font-size: 0.875rem;
}

.auth-error {
    background: rgba(255, 177, 66, 0.1);
    border: 2px solid #ff9933;
    border-radius: 8px;
    padding: 12px 16px;
    display: flex;
    gap: var(--space-3);
    align-items: start;
    margin-bottom: 1rem;
}

.auth-error i {
    color: #ff9933;
    font-size: 1.25rem;
    flex-shrink: 0;
    margin-top: 2px;
}

.auth-error span {
    color: #ff9933;
    font-size: 0.875rem;
    line-height: 1.5;
}

.auth-error strong {
    display: block;
    color: #ff9933;
    font-weight: 600;
    margin-bottom: var(--space-1);
}

.auth-error p {
    color: #ff9933;
    font-size: 0.875rem;
    margin: 0;
}

.btn {
    min-height: 44px;
    white-space: nowrap;
}

.btn-outline {
    background: white;
    color: var(--primary);
    border: 2px solid var(--primary);
}

.btn-outline:hover {
    background: var(--primary);
    color: white;
}

.btn-block {
    width: 100%;
}

@media (max-width: 768px) {
    .auth-modal-content {
        width: 95%;
        padding: var(--space-6);
    }

    .auth-modal-header h2 {
        font-size: 1.5rem;
    }
}
</style>

<script>
// Account Dropdown Functions
function updateAccountDropdownPosition() {
    const accountButton = document.querySelector('.account-dropdown-btn');
    const accountDropdown = document.getElementById('account-dropdown');

    if (accountButton && accountDropdown) {
        const rect = accountButton.getBoundingClientRect();
        accountDropdown.style.right = (window.innerWidth - rect.right) + 'px';
    }
}

function toggleAccountDropdown(event) {
    event?.stopPropagation();
    const dropdown = document.getElementById('account-dropdown');
    const button = document.querySelector('.account-dropdown-btn');

    dropdown.classList.toggle('active');
    button.classList.toggle('active');

    // Update position when opening
    if (dropdown.classList.contains('active')) {
        updateAccountDropdownPosition();
    }
}

// Update dropdown position dynamically
function updateLangDropdownPosition() {
    const langButton = document.querySelector('.lang-button');
    const langDropdown = document.getElementById('lang-dropdown');

    if (langButton && langDropdown) {
        const rect = langButton.getBoundingClientRect();
        langDropdown.style.right = (window.innerWidth - rect.right) + 'px';
    }
}

// Toggle language dropdown
function toggleLangDropdown(event) {
    event?.stopPropagation();
    const dropdown = document.getElementById('lang-dropdown');
    const button = document.querySelector('.lang-button');

    console.log('Toggling dropdown. Current state:', dropdown.classList.contains('active'));
    console.log('Dropdown element:', dropdown);
    console.log('Options in dropdown:', dropdown.querySelectorAll('.lang-option').length);

    dropdown.classList.toggle('active');
    button.classList.toggle('active');

    // Update position when opening
    if (dropdown.classList.contains('active')) {
        updateLangDropdownPosition();
    }
}

// Update position on window resize
window.addEventListener('resize', function() {
    const langDropdown = document.getElementById('lang-dropdown');
    if (langDropdown && langDropdown.classList.contains('active')) {
        updateLangDropdownPosition();
    }

    const accountDropdown = document.getElementById('account-dropdown');
    if (accountDropdown && accountDropdown.classList.contains('active')) {
        updateAccountDropdownPosition();
    }
});

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    // Close language dropdown
    const langSelector = document.querySelector('.lang-selector');
    const langDropdown = document.getElementById('lang-dropdown');
    const langButton = document.querySelector('.lang-button');

    if (langSelector && !langSelector.contains(event.target)) {
        langDropdown?.classList.remove('active');
        langButton?.classList.remove('active');
    }

    // Close account dropdown
    const accountWrapper = document.querySelector('.account-dropdown-wrapper');
    const accountDropdown = document.getElementById('account-dropdown');
    const accountButton = document.querySelector('.account-dropdown-btn');

    if (accountWrapper && !accountWrapper.contains(event.target)) {
        accountDropdown?.classList.remove('active');
        accountButton?.classList.remove('active');
    }
});

// Change language
function changeLang(lang) {
    console.log('Changing language to:', lang);

    // Close dropdown
    const dropdown = document.getElementById('lang-dropdown');
    const button = document.querySelector('.lang-button');
    dropdown?.classList.remove('active');
    button?.classList.remove('active');

    // Close mobile menu if open
    closeMobileMenu();

    fetch('/api/lang', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ lang: lang })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Language change response:', data);
        if (data.success) {
            // Reload page to apply new language
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error changing language:', error);
    });
}

// Mobile Menu Functions
function toggleMobileMenu() {
    console.log('Toggle mobile menu called');
    const sidebar = document.getElementById('mobile-menu-sidebar');
    const overlay = document.getElementById('mobile-menu-overlay');
    const button = document.getElementById('floating-menu-btn');

    const isActive = sidebar.classList.contains('active');
    console.log('Menu is active:', isActive);

    if (isActive) {
        closeMobileMenu();
    } else {
        console.log('Opening menu');
        sidebar.classList.add('active');
        overlay.classList.add('active');
        button.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeMobileMenu() {
    console.log('Closing mobile menu');
    const sidebar = document.getElementById('mobile-menu-sidebar');
    const overlay = document.getElementById('mobile-menu-overlay');
    const button = document.getElementById('floating-menu-btn');

    console.log('Sidebar:', sidebar);
    console.log('Overlay:', overlay);
    console.log('Button:', button);

    if (sidebar) sidebar.classList.remove('active');
    if (overlay) overlay.classList.remove('active');
    if (button) button.classList.remove('active');
    document.body.style.overflow = 'auto';

    console.log('Mobile menu closed');
}

// Sync cart count between desktop and mobile
function updateCartCount(count) {
    const desktopCount = document.getElementById('cart-count');
    const mobileCount = document.getElementById('mobile-cart-count');

    // Siempre mostrar el contador, incluso cuando sea 0
    if (desktopCount) {
        desktopCount.textContent = count;
        desktopCount.style.display = 'flex';
    }
    if (mobileCount) {
        mobileCount.textContent = count;
        mobileCount.style.display = 'flex';
    }
}

// Load cart count from API
function loadCartCount() {
    fetch('/api/cart')
        .then(res => res.json())
        .then(response => {
            if (response.success && response.data && response.data.items) {
                let totalItems = 0;
                response.data.items.forEach(item => {
                    totalItems += item.quantity;
                });
                updateCartCount(totalItems);
            } else {
                // Cart is empty or error
                updateCartCount(0);
            }
        })
        .catch(err => {
            console.error('Error loading cart count:', err);
            updateCartCount(0);
        });
}

// Initialize mobile menu event listeners when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing mobile menu');

    // Load initial cart count
    loadCartCount();

    // Floating menu button
    const floatingBtn = document.getElementById('floating-menu-btn');
    if (floatingBtn) {
        console.log('Floating button found, adding listener');
        floatingBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Floating button clicked');
            toggleMobileMenu();
        });
    } else {
        console.error('Floating button not found');
    }

    // Close button
    const closeBtn = document.getElementById('mobile-menu-close');
    if (closeBtn) {
        console.log('Close button found, adding listener');
        closeBtn.addEventListener('click', function(e) {
            console.log('Close button clicked');
            e.preventDefault();
            e.stopPropagation();

            const sidebar = document.getElementById('mobile-menu-sidebar');
            const overlay = document.getElementById('mobile-menu-overlay');
            const button = document.getElementById('floating-menu-btn');

            console.log('Removing active classes');
            if (sidebar) {
                sidebar.classList.remove('active');
                console.log('Sidebar active removed');
            }
            if (overlay) {
                overlay.classList.remove('active');
                console.log('Overlay active removed');
            }
            if (button) {
                button.classList.remove('active');
                console.log('Button active removed');
            }
            document.body.style.overflow = 'auto';
            console.log('Body overflow reset');
        });
    } else {
        console.error('Close button not found');
    }

    // Overlay
    const overlay = document.getElementById('mobile-menu-overlay');
    if (overlay) {
        console.log('Overlay found, adding listener');
        overlay.addEventListener('click', function(e) {
            console.log('Overlay clicked');
            e.preventDefault();
            e.stopPropagation();

            const sidebar = document.getElementById('mobile-menu-sidebar');
            const overlayEl = document.getElementById('mobile-menu-overlay');
            const button = document.getElementById('floating-menu-btn');

            console.log('Removing active classes from overlay click');
            if (sidebar) {
                sidebar.classList.remove('active');
                console.log('Sidebar active removed');
            }
            if (overlayEl) {
                overlayEl.classList.remove('active');
                console.log('Overlay active removed');
            }
            if (button) {
                button.classList.remove('active');
                console.log('Button active removed');
            }
            document.body.style.overflow = 'auto';
            console.log('Body overflow reset');
        });
    } else {
        console.error('Overlay not found');
    }

    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            console.log('Escape key pressed');
            closeMobileMenu();
            closeLoginModal();
            closeRegisterModal();
        }
    });
});

// Login/Register Modal Functions (Global)
function openLoginModal() {
    const modal = document.getElementById('loginModal');
    if (modal) {
        // Hide any previous errors
        hideLoginError();

        modal.style.display = 'flex';
        modal.offsetHeight; // Force reflow
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    } else {
        // If modal doesn't exist, redirect to login page
        window.location.href = '/auth/login';
    }
}

function closeLoginModal() {
    const modal = document.getElementById('loginModal');
    if (modal) {
        modal.classList.remove('active');
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }, 300);
    }
}

function showRegisterModal() {
    const loginModal = document.getElementById('loginModal');
    const registerModal = document.getElementById('registerModal');

    if (loginModal) {
        loginModal.classList.remove('active');
        setTimeout(() => {
            loginModal.style.display = 'none';
        }, 300);
    }

    if (registerModal) {
        registerModal.style.display = 'flex';
        registerModal.offsetHeight; // Force reflow
        registerModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    } else {
        window.location.href = '/auth/register';
    }
}

function showLoginFromRegister() {
    const registerModal = document.getElementById('registerModal');
    const loginModal = document.getElementById('loginModal');

    if (registerModal) {
        registerModal.classList.remove('active');
        setTimeout(() => {
            registerModal.style.display = 'none';
        }, 300);
    }

    if (loginModal) {
        loginModal.style.display = 'flex';
        loginModal.offsetHeight; // Force reflow
        loginModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeRegisterModal() {
    const modal = document.getElementById('registerModal');
    if (modal) {
        modal.classList.remove('active');
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }, 300);
    }
}

// Form validation for register
function validateRegisterForm() {
    const password = document.getElementById('register-password').value;
    const passwordConfirm = document.getElementById('register-password-confirm').value;
    const email = document.getElementById('register-email').value;
    const name = document.getElementById('register-name').value;

    // Validate name
    if (name.trim().length < 3) {
        showRegisterError('El nombre debe tener al menos 3 caracteres');
        return false;
    }

    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showRegisterError('Por favor, introduce un email válido');
        return false;
    }

    // Validate password length
    if (password.length < 6) {
        showRegisterError('La contraseña debe tener al menos 6 caracteres');
        return false;
    }

    // Validate passwords match
    if (password !== passwordConfirm) {
        showRegisterError('Las contraseñas no coinciden');
        return false;
    }

    return true;
}

// Handle registration
function handleRegister(event) {
    event.preventDefault();

    // Hide any previous errors
    hideRegisterError();

    if (!validateRegisterForm()) {
        return false;
    }

    const form = event.target;
    const submitBtn = form.querySelector('.auth-submit-btn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');

    // Show loading state
    btnText.style.display = 'none';
    btnLoading.style.display = 'flex';
    submitBtn.disabled = true;

    const formData = new FormData(form);

    fetch('/api/auth/register', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            full_name: formData.get('name'),
            email: formData.get('email'),
            password: formData.get('password')
        })
    })
    .then(res => res.json())
    .then(data => {
        // Reset button state
        btnText.style.display = 'block';
        btnLoading.style.display = 'none';
        submitBtn.disabled = false;

        if (data.success) {
            // Check if email verification is required
            if (data.requires_verification) {
                // Close register modal
                closeRegisterModal();

                // Show verification modal
                showVerifyEmailModal(data.email);
            } else {
                // Old flow: Registration successful - user is now logged in
                // Reload page to show logged in state
                window.location.reload();
            }
        } else {
            // Show error message in modal
            showRegisterError(data.message || 'Error al registrar la cuenta');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        // Reset button state
        btnText.style.display = 'block';
        btnLoading.style.display = 'none';
        submitBtn.disabled = false;
        showRegisterError('Error inesperado. Por favor, intenta de nuevo.');
    });

    return false;
}

// Show register error
function showRegisterError(message) {
    const errorDiv = document.getElementById('registerError');
    const errorTitle = document.getElementById('registerErrorTitle');
    const errorMessage = document.getElementById('registerErrorMessage');

    if (errorDiv && errorTitle && errorMessage) {
        // Check if it's an email in use error
        if (message.includes('ya está') || message.includes('already') || message.includes('en uso') || message.includes('in use')) {
            errorTitle.textContent = 'Email en uso';
            errorMessage.textContent = 'Este correo electrónico ya está registrado. Por favor, inicia sesión o usa otro email.';
        } else {
            errorTitle.textContent = 'Error en el registro';
            errorMessage.textContent = message;
        }

        errorDiv.style.display = 'flex';

        // Scroll to top of modal to show error
        const modalContent = document.querySelector('#registerModal .auth-modal-content');
        if (modalContent) {
            modalContent.scrollTop = 0;
        }
    }
}

// Hide register error
function hideRegisterError() {
    const errorDiv = document.getElementById('registerError');
    if (errorDiv) {
        errorDiv.style.display = 'none';
    }
}

// Handle login
function handleLogin(event) {
    event.preventDefault();

    // Hide any previous errors
    hideLoginError();

    const form = event.target;
    const submitBtn = form.querySelector('.auth-submit-btn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');

    // Show loading state
    btnText.style.display = 'none';
    btnLoading.style.display = 'flex';
    submitBtn.disabled = true;

    const formData = new FormData(form);

    fetch('/api/auth/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            email: formData.get('email'),
            password: formData.get('password')
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Login successful - reload page to show logged in state
            window.location.reload();
        } else {
            // Reset button state
            btnText.style.display = 'block';
            btnLoading.style.display = 'none';
            submitBtn.disabled = false;

            // Check if email is not verified
            if (data.email_not_verified && data.email) {
                closeLoginModal();
                showUnverifiedModal(data.email);
            } else {
                // Show error message in modal (not closing it)
                showLoginError(data.message || 'Error al iniciar sesión. Verifica tus credenciales.');
            }
        }
    })
    .catch(err => {
        console.error('Error:', err);
        // Reset button state
        btnText.style.display = 'block';
        btnLoading.style.display = 'none';
        submitBtn.disabled = false;
        showLoginError('Error inesperado. Por favor, intenta de nuevo.');
    });

    return false;
}

// Show login error
function showLoginError(message) {
    const errorDiv = document.getElementById('loginError');
    const errorMessage = document.getElementById('loginErrorMessage');

    if (errorDiv && errorMessage) {
        errorMessage.textContent = message;
        errorDiv.style.display = 'flex';

        // Scroll to top of modal to show error
        const modalContent = document.querySelector('#loginModal .auth-modal-content');
        if (modalContent) {
            modalContent.scrollTop = 0;
        }
    }
}

// Hide login error
function hideLoginError() {
    const errorDiv = document.getElementById('loginError');
    if (errorDiv) {
        errorDiv.style.display = 'none';
    }
}

// Show verification email modal
function showVerifyEmailModal(email) {
    const modal = document.getElementById('verifyEmailModal');
    const emailAddress = document.getElementById('verify-email-address');

    if (modal && emailAddress) {
        emailAddress.textContent = email;
        modal.style.display = 'flex';
        modal.offsetHeight; // Force reflow
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

// Close verification email modal
function closeVerifyEmailModal() {
    const modal = document.getElementById('verifyEmailModal');
    if (modal) {
        modal.classList.remove('active');
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }, 300);
    }
}

// Unverified account modal functions
let pendingVerificationEmail = null;

function showUnverifiedModal(email) {
    pendingVerificationEmail = email;
    const modal = document.getElementById('unverifiedModal');
    if (modal) {
        modal.style.display = 'flex';
        modal.offsetHeight; // Force reflow
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeUnverifiedModal() {
    const modal = document.getElementById('unverifiedModal');
    if (modal) {
        modal.classList.remove('active');
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }, 300);
    }
}

// Resend verification email
function resendVerificationEmail() {
    if (!pendingVerificationEmail) {
        return;
    }

    const btn = document.getElementById('resendVerificationBtn');
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?= i18n::getLang() === "es" ? "Enviando..." : "Sending..." ?>';

    fetch('/api/auth/resend-verification', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            email: pendingVerificationEmail
        })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalHTML;

        closeUnverifiedModal();

        if (data.success) {
            showResendResultModal(true, data.message || '<?= __("auth.verification_email_sent_message") ?>');
        } else {
            showResendResultModal(false, data.message || '<?= __("auth.verification_email_error_message") ?>');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        closeUnverifiedModal();
        showResendResultModal(false, '<?= __("auth.verification_email_error_message") ?>');
    });
}

// Show result modal
function showResendResultModal(success, message) {
    const modal = document.getElementById('resendResultModal');
    const icon = document.getElementById('resendResultIcon');
    const title = document.getElementById('resendResultTitle');
    const messageEl = document.getElementById('resendResultMessage');

    if (success) {
        icon.style.background = 'rgba(34, 197, 94, 0.1)';
        icon.innerHTML = '<i class="fas fa-check-circle" style="color: #22c55e; font-size: 3rem;"></i>';
        title.textContent = '<?= __("auth.verification_email_sent") ?>';
    } else {
        icon.style.background = 'rgba(239, 68, 68, 0.1)';
        icon.innerHTML = '<i class="fas fa-times-circle" style="color: #ef4444; font-size: 3rem;"></i>';
        title.textContent = '<?= __("auth.verification_email_error") ?>';
    }

    messageEl.textContent = message;

    modal.style.display = 'flex';
    modal.offsetHeight; // Force reflow
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeResendResultModal() {
    const modal = document.getElementById('resendResultModal');
    if (modal) {
        modal.classList.remove('active');
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }, 300);
    }
}

// Email Verification Result Modal
function showVerificationResultModal(status, title, message) {
    const modal = document.getElementById('verificationResultModal');
    const icon = document.getElementById('verificationResultIcon');
    const titleElement = document.getElementById('verificationResultTitle');
    const messageElement = document.getElementById('verificationResultMessage');

    if (!modal) return;

    // Set icon based on status
    if (status === 'success') {
        icon.innerHTML = '<i class="fas fa-check-circle" style="color: #10b981; font-size: 3rem;"></i>';
        icon.style.background = 'rgba(16, 185, 129, 0.1)';
    } else {
        icon.innerHTML = '<i class="fas fa-exclamation-circle" style="color: #ef4444; font-size: 3rem;"></i>';
        icon.style.background = 'rgba(239, 68, 68, 0.1)';
    }

    titleElement.textContent = title;
    messageElement.textContent = message;

    modal.style.display = 'flex';
    modal.offsetHeight; // Force reflow
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeVerificationResultModal() {
    const modal = document.getElementById('verificationResultModal');
    if (modal) {
        modal.classList.remove('active');
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }, 300);
    }
}

// Check for verification result on page load
<?php if (isset($_SESSION['verification_result'])): ?>
document.addEventListener('DOMContentLoaded', function() {
    showVerificationResultModal(
        '<?= $_SESSION['verification_result']['status'] ?>',
        '<?= addslashes($_SESSION['verification_result']['title']) ?>',
        '<?= addslashes($_SESSION['verification_result']['message']) ?>'
    );
    <?php unset($_SESSION['verification_result']); ?>
});
<?php endif; ?>
</script>
