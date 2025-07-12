<section class="community">
    <div class="container">
        <div class="community-content">
            <h2 class="community-title">Bergabung dengan Komunitas Pemancing Indonesia</h2>
            <p class="community-description">
                Dapatkan tips memancing terbaru, diskusi produk, dan berbagi pengalaman dengan ratusan pemancing lainnya di komunitas WhatsApp kami.
            </p>
            <?php if (isLoggedIn()): ?>
                <a href="https://chat.whatsapp.com/ESRL1L9ImLlGvgBHAk85e6" target="_blank" class="community-btn">
                    <i class="fab fa-whatsapp"></i>
                    Bergabung ke Komunitas WhatsApp
                </a>
            <?php else: ?>
                <button onclick="requireAuth(() => window.open('https://chat.whatsapp.com/ESRL1L9ImLlGvgBHAk85e6', '_blank'), 'bergabung ke komunitas WhatsApp')" class="community-btn">
                    <i class="fab fa-whatsapp"></i>
                    Bergabung ke Komunitas WhatsApp
                </button>
            <?php endif; ?>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-brand-section">
                <div class="footer-brand">
                    <span>Mabar</span>
                </div>
                <p>Platform rekomendasi alat pancing terpercaya di Indonesia. Temukan produk berkualitas dengan review mendalam dari ahli dan komunitas pemancing.</p>
                <div class="social-links">
                    <a href="#" class="social-link" aria-label="Tiktok"><i class="fab fa-tiktok"></i></a>
                    <a href="#" class="social-link" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="social-link" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link" aria-label="Youtube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>Kategori</h3>
                <ul class="footer-links">
                    <li><a href="joran.php"><i class="fas fa-long-arrow-alt-right"></i> Joran Pancing</a></li>
                    <li><a href="reel.php"><i class="fas fa-circle-notch"></i> Reel Pancing</a></li>
                    <li><a href="umpan.php"><i class="fas fa-fish"></i> Umpan & Kail</a></li>
                    <li><a href="aksesoris.php"><i class="fas fa-toolbox"></i> Aksesoris</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Hubungi Kami</h3>
                <ul class="footer-links">
                  <li><a><i class="fas fa-envelope"></i> info@mabar.id</a></li>
                  <li><a><i class="fas fa-phone"></i> +62 819 2837 4650</a></li>
                  <li><a><i class="fab fa-whatsapp"></i> +62 819 2837 4650</a></li>
                  <li><a><i class="fas fa-map-marker-alt"></i> Situbondo, Jawa Timur, Indonesia</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2025 Mabar. Semua Hak Dilindungi.</p>
        </div>
    </div>
</footer>

<style>

.social-links {
    display: flex;
    gap: 15px;
    margin-top: 15px;
}

.social-item {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    color: var(--text-light);
    font-size: 18px;
    transition: color 0.3s ease;
    cursor: pointer;
}

.social-item:hover {
    color: var(--primary-color);
}

.contact-item {
    display: inline-flex;
    align-items: center;
    color: var(--text-light);
    transition: color 0.3s ease;
    cursor: pointer;
}

.contact-item:hover {
    color: var(--primary-400);
}

.contact-item i {
    margin-right: 8px;
    width: 16px;
    text-align: center;
}

.footer-links li:has(.contact-item) {
    list-style: none;
}

.footer-links li:has(.contact-item) a {
    display: none;
}

.footer-section:last-child .footer-links a {
    cursor: default;
}

.footer-section:last-child .footer-links a:hover {
    color: var(--primary-400) !important;
}

.footer-links a.no-click:hover {
    color: inherit !important; 
}

.footer-section:last-child .footer-links a {
    cursor: default;
}

[data-theme="dark"] .social-item {
    background: rgba(255, 255, 255, 0.05);
    color: var(--text-dark);
}

[data-theme="dark"] .social-item:hover {
    color: var(--primary-color);
}

[data-theme="dark"] .contact-item {
    color: var(--text-dark);
}

[data-theme="dark"] .contact-item:hover {
    color: var(--primary-color);
}
</style>

<script src="assets/js/script.js"></script>
</body>
</html>