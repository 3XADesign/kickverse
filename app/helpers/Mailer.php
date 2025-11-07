<?php
/**
 * Mailer Helper
 * Librería interna para envío de emails con plantilla profesional reutilizable
 */

class Mailer {
    private $from = 'noreply@kickverse.es';
    private $fromName = 'Kickverse';
    private $logoUrl = 'https://kickverse.es/img/logo.png';

    /**
     * Send email with custom subject and content using base template
     *
     * @param string $to Email destinatario
     * @param string $subject Asunto del email
     * @param string $htmlContent Contenido HTML del email
     * @param array $options Opciones adicionales (header_title, show_button, button_text, button_url, footer_text)
     * @return bool
     */
    public function send($to, $subject, $htmlContent, $options = []) {
        $headerTitle = $options['header_title'] ?? 'Kickverse';
        $showButton = $options['show_button'] ?? false;
        $buttonText = $options['button_text'] ?? '';
        $buttonUrl = $options['button_url'] ?? '';
        $footerText = $options['footer_text'] ?? 'Este es un correo automático, por favor no respondas a este mensaje.';

        $html = $this->getBaseTemplate($headerTitle, $htmlContent, $showButton, $buttonText, $buttonUrl, $footerText);

        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            "From: {$this->fromName} <{$this->from}>",
            "Reply-To: {$this->from}",
            'X-Mailer: PHP/' . phpversion()
        ];

        return mail($to, $subject, $html, implode("\r\n", $headers));
    }

    /**
     * Send email verification link to customer
     */
    public function sendVerificationEmail($email, $fullName, $verificationLink, $lang = 'es') {
        $isSpanish = ($lang === 'es');

        $subject = $isSpanish ?
            'Verifica tu Correo Electrónico - Kickverse' :
            'Verify Your Email Address - Kickverse';

        $greeting = $isSpanish ? 'Hola' : 'Hello';
        $paragraph1 = $isSpanish ?
            'Gracias por registrarte en Kickverse. Para completar tu registro y poder acceder a tu cuenta, necesitamos verificar tu dirección de correo electrónico.' :
            'Thank you for signing up at Kickverse. To complete your registration and access your account, we need to verify your email address.';

        $paragraph2 = $isSpanish ?
            'Haz clic en el siguiente botón para verificar tu cuenta:' :
            'Click the button below to verify your account:';

        $warningTitle = $isSpanish ? '⚠️ Importante:' : '⚠️ Important:';
        $warningText = $isSpanish ?
            'Este enlace expira en 24 horas. Si no solicitaste este registro, puedes ignorar este correo.' :
            'This link expires in 24 hours. If you didn\'t request this registration, you can ignore this email.';

        $linkHelpText = $isSpanish ?
            'Si el botón no funciona, copia y pega este enlace en tu navegador:' :
            'If the button doesn\'t work, copy and paste this link into your browser:';

        $buttonText = $isSpanish ? 'Verificar mi Cuenta' : 'Verify my Account';
        $headerTitle = $isSpanish ? 'Verificación de Correo' : 'Email Verification';
        $footerText = $isSpanish ?
            'Este es un correo automático, por favor no respondas a este mensaje.' :
            'This is an automated email, please do not reply to this message.';

        $content = <<<HTML
<p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 0 0 24px;">
    {$greeting} <strong>{$fullName}</strong>,
</p>

<p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 0 0 24px;">
    {$paragraph1}
</p>

<p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 0 0 24px;">
    {$paragraph2}
</p>

<div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px; border-radius: 8px; margin: 24px 0;">
    <p style="color: #92400e; font-size: 14px; line-height: 1.5; margin: 0;">
        <strong>{$warningTitle}</strong> {$warningText}
    </p>
</div>

<p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin: 24px 0 0;">
    {$linkHelpText}
</p>
<p style="color: #b054e9; font-size: 13px; word-break: break-all; margin: 8px 0 0;">
    {$verificationLink}
</p>
HTML;

        return $this->send($email, $subject, $content, [
            'header_title' => $headerTitle,
            'show_button' => true,
            'button_text' => $buttonText,
            'button_url' => $verificationLink,
            'footer_text' => $footerText
        ]);
    }

    /**
     * Send magic link email to admin
     */
    public function sendAdminMagicLink($email, $fullName, $magicLink) {
        $subject = 'Acceso al Panel de Administración - Kickverse';

        $content = <<<HTML
<p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 0 0 24px;">
    Hola <strong>{$fullName}</strong>,
</p>

<p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 0 0 24px;">
    Has solicitado acceso al panel de administración de Kickverse. Haz clic en el siguiente botón para iniciar sesión de forma segura:
</p>

<div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px; border-radius: 8px; margin: 24px 0;">
    <p style="color: #92400e; font-size: 14px; line-height: 1.5; margin: 0;">
        <strong>⚠️ Importante:</strong> Este enlace expira en 15 minutos y solo puede usarse una vez. Si no solicitaste este acceso, ignora este correo.
    </p>
</div>

<p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin: 24px 0 0;">
    Si el botón no funciona, copia y pega este enlace en tu navegador:
</p>
<p style="color: #b054e9; font-size: 13px; word-break: break-all; margin: 8px 0 0;">
    {$magicLink}
</p>
HTML;

        return $this->send($email, $subject, $content, [
            'header_title' => 'Acceso al Panel de Administración',
            'show_button' => true,
            'button_text' => 'Acceder al Panel de Admin',
            'button_url' => $magicLink
        ]);
    }

    /**
     * Base email template - Plantilla reutilizable con diseño inline CSS
     *
     * @param string $headerTitle Título en el header
     * @param string $content Contenido HTML del email
     * @param bool $showButton Mostrar botón CTA
     * @param string $buttonText Texto del botón
     * @param string $buttonUrl URL del botón
     * @param string $footerText Texto adicional en el footer
     * @return string
     */
    private function getBaseTemplate($headerTitle, $content, $showButton, $buttonText, $buttonUrl, $footerText) {
        $year = date('Y');
        $buttonHtml = '';

        if ($showButton && $buttonText && $buttonUrl) {
            $buttonHtml = <<<HTML
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td align="center" style="padding: 30px 0;">
            <a href="{$buttonUrl}" style="display: inline-block; background: linear-gradient(135deg, #b054e9 0%, #c151d4 100%); color: #ffffff; text-decoration: none; padding: 16px 40px; border-radius: 12px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(176, 84, 233, 0.3);">
                {$buttonText}
            </a>
        </td>
    </tr>
</table>
HTML;
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$headerTitle} - Kickverse</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f3f4f6; padding: 40px 20px;">
        <tr>
            <td align="center">
                <!-- Container -->
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 16px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); overflow: hidden; max-width: 100%;">
                    <!-- Header with gradient and logo -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #b054e9 0%, #c151d4 100%); padding: 40px 40px 30px; text-align: center;">
                            <img src="{$this->logoUrl}" alt="Kickverse" style="height: 50px; margin-bottom: 20px; display: block; margin-left: auto; margin-right: auto;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 700; line-height: 1.3;">
                                {$headerTitle}
                            </h1>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 40px;">
                            {$content}
                            {$buttonHtml}
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px 40px; border-top: 1px solid #e5e7eb;">
                            <p style="color: #6b7280; font-size: 14px; text-align: center; margin: 0 0 12px;">
                                © {$year} Kickverse. Todos los derechos reservados.
                            </p>
                            <p style="color: #9ca3af; font-size: 12px; text-align: center; margin: 0;">
                                {$footerText}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }
}
