<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-section">
                <h4><?= __('footer.about_title') ?></h4>
                <p><?= __('footer.about_text') ?></p>
                <div class="footer-social">
                    <a href="https://instagram.com/<?= ltrim($config['contacts']['instagram'] ?? 'kickverse.es', '@') ?>"
                       target="_blank" rel="noopener" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://twitter.com/<?= ltrim($config['contacts']['twitter'] ?? 'kickverse_es', '@') ?>"
                       target="_blank" rel="noopener" aria-label="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://tiktok.com/@<?= ltrim($config['contacts']['tiktok'] ?? 'kickverse_es', '@') ?>"
                       target="_blank" rel="noopener" aria-label="TikTok">
                        <i class="fab fa-tiktok"></i>
                    </a>
                    <a href="https://t.me/<?= ltrim($config['contacts']['telegram'] ?? 'esKickverse', '@') ?>"
                       target="_blank" rel="noopener" aria-label="Telegram">
                        <i class="fab fa-telegram"></i>
                    </a>
                </div>
            </div>

            <div class="footer-section">
                <h4><?= __('footer.links_title') ?></h4>
                <ul class="footer-links">
                    <li><a href="/"><?= __('nav.home') ?></a></li>
                    <li><a href="/productos"><?= __('nav.jerseys') ?></a></li>
                    <li><a href="/ligas"><?= __('nav.leagues') ?></a></li>
                    <li><a href="/sobre-nosotros"><?= __('nav.about') ?></a></li>
                    <li><a href="/contacto"><?= __('nav.contact') ?></a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4><?= __('footer.legal_title') ?></h4>
                <ul class="footer-links">
                    <li><a href="/terminos-y-condiciones"><?= __('footer.terms') ?></a></li>
                    <li><a href="/politica-de-privacidad"><?= __('footer.privacy') ?></a></li>
                    <li><a href="/politica-de-devoluciones"><?= __('footer.returns') ?></a></li>
                    <li><a href="/preguntas-frecuentes"><?= __('footer.faq') ?></a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4><?= __('footer.contact_title') ?></h4>
                <ul class="footer-links">
                    <li>
                        <i class="fab fa-telegram"></i>
                        <a href="https://t.me/<?= ltrim($config['contacts']['telegram'] ?? 'esKickverse', '@') ?>"
                           target="_blank" rel="noopener">
                            <?= $config['contacts']['telegram'] ?? '@esKickverse' ?>
                        </a>
                    </li>
                    <li>
                        <i class="fab fa-whatsapp"></i>
                        <a href="https://wa.me/<?= str_replace(['+', ' '], '', $config['contacts']['whatsapp'] ?? '34614299735') ?>"
                           target="_blank" rel="noopener">
                            <?= $config['contacts']['whatsapp'] ?? '+34 614 299 735' ?>
                        </a>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <a href="mailto:<?= $config['contacts']['email'] ?? 'hola@kickverse.es' ?>">
                            <?= $config['contacts']['email'] ?? 'hola@kickverse.es' ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Kickverse. <?= __('footer.copyright') ?></p>
        </div>
    </div>
</footer>
