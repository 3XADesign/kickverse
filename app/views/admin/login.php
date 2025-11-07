<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Admin Login - Kickverse' ?></title>
    <link rel="stylesheet" href="/css/modern.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);">
    <div class="admin-login-container">
        <div class="admin-login-box">
            <!-- Logo -->
            <div class="admin-login-logo">
                <img src="/img/logo.png" alt="Kickverse" style="height: 60px;">
            </div>

            <!-- Title -->
            <h1 class="admin-login-title">Panel de Administración</h1>
            <p class="admin-login-subtitle">Introduce tu email para recibir un enlace de acceso</p>

            <!-- Error/Success Messages -->
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <?php if ($_GET['error'] === 'invalid_token'): ?>
                        <i class="fas fa-exclamation-circle"></i>
                        El enlace de acceso no es válido.
                    <?php elseif ($_GET['error'] === 'expired_or_invalid'): ?>
                        <i class="fas fa-clock"></i>
                        El enlace de acceso ha expirado o ya fue utilizado.
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['message']) && $_GET['message'] === 'logged_out'): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    Has cerrado sesión correctamente.
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form id="adminLoginForm" class="admin-login-form">
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Email
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           class="form-control"
                           placeholder="admin@kickverse.com"
                           required
                           autocomplete="email">

                    <!-- Message container debajo del campo -->
                    <div id="messageContainer" style="display: none; margin-top: 12px;"></div>
                </div>

                <button type="submit" class="btn btn-primary btn-block" id="submitBtn">
                    <i class="fas fa-paper-plane" id="btnIcon"></i>
                    <span id="btnText">Enviar Enlace de Acceso</span>
                </button>
            </form>

            <div class="admin-login-footer">
                <a href="/" class="admin-back-link">
                    <i class="fas fa-arrow-left"></i>
                    Volver a la tienda
                </a>
            </div>
        </div>
    </div>

    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--space-4);
        }

        .admin-login-container {
            width: 100%;
            max-width: 450px;
        }

        .admin-login-box {
            background: white;
            border-radius: var(--radius-2xl);
            padding: var(--space-10);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .admin-login-logo {
            text-align: center;
            margin-bottom: var(--space-6);
        }

        .admin-login-title {
            font-size: 1.75rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: var(--space-2);
            color: var(--gray-900);
        }

        .admin-login-subtitle {
            text-align: center;
            color: var(--gray-600);
            margin-bottom: var(--space-8);
            font-size: 0.95rem;
        }

        .alert {
            padding: var(--space-4);
            border-radius: var(--radius-lg);
            margin-bottom: var(--space-6);
            display: flex;
            align-items: center;
            gap: var(--space-3);
            font-size: 0.9rem;
        }

        .alert-error {
            background: #fee;
            color: #c00;
            border: 1px solid #fcc;
        }

        .alert-success {
            background: #efe;
            color: #060;
            border: 1px solid #cfc;
        }

        .admin-login-form {
            margin-bottom: var(--space-6);
        }

        .form-group {
            margin-bottom: var(--space-6);
        }

        .form-group label {
            display: flex;
            align-items: center;
            gap: var(--space-2);
            font-weight: 600;
            margin-bottom: var(--space-2);
            color: var(--gray-700);
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: var(--space-4);
            border: 2px solid var(--gray-200);
            border-radius: var(--radius-lg);
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(176, 84, 233, 0.1);
        }

        .btn-block {
            width: 100%;
            padding: var(--space-4);
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-2);
        }

        .admin-login-footer {
            text-align: center;
            padding-top: var(--space-6);
            border-top: 1px solid var(--gray-200);
        }

        .admin-back-link {
            color: var(--gray-600);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: var(--space-2);
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .admin-back-link:hover {
            color: var(--primary);
        }

        #submitBtn.loading {
            opacity: 0.8;
            pointer-events: none;
        }

        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Estilos para mensajes debajo del campo */
        .field-message {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease;
        }

        .field-message i {
            font-size: 1.1rem;
        }

        .field-message.success {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            border: 1.5px solid rgba(16, 185, 129, 0.3);
        }

        .field-message.error {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border: 1.5px solid rgba(239, 68, 68, 0.3);
        }

        .field-message.info {
            background: rgba(59, 130, 246, 0.1);
            color: #2563eb;
            border: 1.5px solid rgba(59, 130, 246, 0.3);
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <script>
        const form = document.getElementById('adminLoginForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnIcon = document.getElementById('btnIcon');
        const btnText = document.getElementById('btnText');
        const messageContainer = document.getElementById('messageContainer');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const email = document.getElementById('email').value.trim();

            if (!email) {
                showMessage('error', 'Por favor, introduce tu email.');
                return;
            }

            // Clear previous message
            hideMessage();

            // Show loading state with spinner
            submitBtn.classList.add('loading');
            btnIcon.outerHTML = '<span class="spinner"></span>';
            btnText.textContent = 'Enviando...';

            try {
                const response = await fetch('/admin/send-magic-link', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ email })
                });

                const data = await response.json();

                if (data.success) {
                    showMessage('success', data.message || 'Revisa tu email para acceder al panel.');
                    form.reset();
                } else {
                    showMessage('error', data.message || 'Hubo un problema al enviar el enlace.');
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('error', 'No se pudo conectar con el servidor. Inténtalo de nuevo.');
            } finally {
                // Restore button state
                submitBtn.classList.remove('loading');
                const spinner = document.querySelector('.spinner');
                if (spinner) {
                    spinner.outerHTML = '<i class="fas fa-paper-plane" id="btnIcon"></i>';
                }
                btnText.textContent = 'Enviar Enlace de Acceso';
            }
        });

        /**
         * Mostrar mensaje debajo del campo de email
         */
        function showMessage(type, message) {
            let icon = '';

            if (type === 'success') {
                icon = '<i class="fas fa-check-circle"></i>';
            } else if (type === 'error') {
                icon = '<i class="fas fa-exclamation-circle"></i>';
            } else if (type === 'info') {
                icon = '<i class="fas fa-info-circle"></i>';
            }

            messageContainer.innerHTML = `
                <div class="field-message ${type}">
                    ${icon}
                    <span>${message}</span>
                </div>
            `;
            messageContainer.style.display = 'block';
        }

        /**
         * Ocultar mensaje
         */
        function hideMessage() {
            messageContainer.style.display = 'none';
            messageContainer.innerHTML = '';
        }

        // Auto-hide message after 5 seconds for success messages
        setInterval(() => {
            const successMsg = messageContainer.querySelector('.field-message.success');
            if (successMsg) {
                setTimeout(() => hideMessage(), 5000);
            }
        }, 100);
    </script>
</body>
</html>
