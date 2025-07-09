<?php
require_once 'functions.php';

if (!isset($page_title)) $page_title = "Mabar - Platform Rekomendasi Alat Pancing Terbaik";
if (!isset($page_description)) $page_description = "Platform rekomendasi alat pancing terpercaya di Indonesia";

$current_user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎣</text></svg>">
    <style>
/* Logo styling */
.logo {
    display: flex;
    align-items: center;
    gap: 12px;
}

.logo-image {
    width: 40px;
    height: 40px;
    object-fit: contain;
    border-radius: 6px;
}


/* Hapus bagian unified-notification yang lama dan ganti dengan ini */
.toast-notification {
    position: fixed;
    top: 100px;
    right: 20px;
    background: white;
    border-radius: 12px;
    padding: 16px 20px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    border-left: 4px solid #10b981;
    z-index: 10000;
    transform: translateX(400px);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    max-width: 350px;
    min-width: 280px;
}

.toast-notification.show {
    transform: translateX(0);
    opacity: 1;
}

.toast-notification.success {
    border-left-color: #10b981;
}

.toast-notification.error {
    border-left-color: #ef4444;
}

.toast-notification.info {
    border-left-color: #3b82f6;
}

.toast-notification.warning {
    border-left-color: #f59e0b;
}

.toast-notification-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.toast-notification-icon {
    font-size: 18px;
    flex-shrink: 0;
}

.toast-notification.success .toast-notification-icon {
    color: #10b981;
}

.toast-notification.error .toast-notification-icon {
    color: #ef4444;
}

.toast-notification.info .toast-notification-icon {
    color: #3b82f6;
}

.toast-notification.warning .toast-notification-icon {
    color: #f59e0b;
}

.toast-notification-message {
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    line-height: 1.4;
}

.toast-notification-close {
    position: absolute;
    top: 8px;
    right: 8px;
    background: none;
    border: none;
    color: #9ca3af;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    transition: all 0.2s;
}

.toast-notification-close:hover {
    color: #6b7280;
    background: #f3f4f6;
}

/* Dark theme support */
[data-theme="dark"] .toast-notification {
    background: #1f2937;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

[data-theme="dark"] .toast-notification-message {
    color: #e5e7eb;
}

[data-theme="dark"] .toast-notification-close {
    color: #9ca3af;
}

[data-theme="dark"] .toast-notification-close:hover {
    color: #d1d5db;
    background: #374151;
}
</style>
</head>
<body>
    <!-- Header -->
    <header class="header" id="header">
        <div class="container">
            <div class="header-content">
                <!-- Logo -->
                <div class="logo">
                    <a href="index.php" style="display: flex; align-items: center; gap: 12px; text-decoration: none;">
                        <img src="assets/images/logo.jpg" alt="Mabar Logo" class="logo-image">
                        <span class="logo-text">Mabar</span>
                    </a>
                </div>
                
                <!-- Navigation -->
                <nav class="nav">
                    <a href="index.php" class="nav-link">Beranda</a>
                    <a href="joran.php" class="nav-link">Joran</a>
                    <a href="reel.php" class="nav-link">Reel</a>
                    <a href="umpan.php" class="nav-link">Umpan</a>
                    <a href="aksesoris.php" class="nav-link">Aksesoris</a>
                </nav>
                
                <!-- Search -->
                <div class="nav-search">
                    <div class="search-input-container">
                        <input 
                            type="text" 
                            id="searchInput" 
                            class="search-input" 
                            placeholder="Cari produk alat pancing..." 
                            autocomplete="off"
                            spellcheck="false"
                        >
                        <i class="fas fa-search search-icon"></i>
                    </div>
                    
                    <!-- Search Results Dropdown -->
                    <div id="searchResultsDropdown" class="search-dropdown">
                        <!-- Results will be populated by JavaScript -->
                    </div>
                </div>
                
                <!-- User Actions -->
                <div class="header-actions">
                    <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                        <i class="fas fa-moon"></i>
                    </button>
                    
                    <?php if ($current_user): ?>
                        <div class="user-menu">
                            <button class="user-btn" onclick="toggleUserDropdown(event)">
                                <i class="fas fa-user"></i>
                                <span><?php echo htmlspecialchars($current_user['name']); ?></span>
                                <i class="fas fa-chevron-down dropdown-arrow"></i>
                            </button>
                            <div class="user-dropdown" id="userDropdown">
                                <a href="wishlist.php" class="dropdown-item">
                                    <i class="fas fa-heart"></i>
                                    Produk yang Disukai
                                </a>
                                <a href="profile.php" class="dropdown-item">
                                    <i class="fas fa-user-cog"></i>
                                    Profil
                                </a>
                                <?php if ($current_user['is_admin']): ?>
                                    <a href="admin.php" class="dropdown-item">
                                        <i class="fas fa-crown"></i>
                                        Admin Panel
                                    </a>
                                <?php endif; ?>
                                <div class="dropdown-divider"></div>
                                <button onclick="showLogoutConfirmation()" class="dropdown-item logout-item">
                                    <i class="fas fa-sign-out-alt"></i>
                                    Logout
                                </button>
                            </div>
                        </div>
                    <?php else: ?>
                        <button class="login-btn" onclick="openAuthModal('login')">
                            <i class="fas fa-user"></i>
                            <span>Login</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Enhanced Auth Modal -->
    <?php if (!$current_user): ?>
    <div id="authModal" class="auth-modal">
        <div class="auth-modal-overlay" onclick="closeAuthModal()"></div>
        <div class="auth-modal-content">
            <div class="auth-modal-header">
                <h2 id="authModalTitle">Masuk ke Akun Anda</h2>
                <button class="auth-modal-close" onclick="closeAuthModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="auth-modal-body">
                <div class="auth-tabs">
                    <button class="auth-tab-btn active" onclick="switchAuthTab('login')">
                        <i class="fas fa-sign-in-alt"></i>
                        Masuk
                    </button>
                    <button class="auth-tab-btn" onclick="switchAuthTab('register')">
                        <i class="fas fa-user-plus"></i>
                        Daftar
                    </button>
                </div>
                
                <!-- Login Form -->
                <div id="loginForm" class="auth-form-container active">
                    <form class="auth-form" onsubmit="handleAuthSubmit(event, 'login')">
                        <div class="form-group">
                            <label for="loginEmail">
                                <i class="fas fa-envelope"></i>
                                Email
                            </label>
                            <input type="email" id="loginEmail" name="email" required 
                                   placeholder="nama@email.com">
                        </div>
                        
                        <div class="form-group">
                            <label for="loginPassword">
                                <i class="fas fa-lock"></i>
                                Password
                            </label>
                            <div class="password-input-wrapper">
                                <input type="password" id="loginPassword" name="password" required 
                                       placeholder="Masukkan password" class="password-input">
                                <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('loginPassword')">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                        
                        <button type="submit" class="auth-submit-btn">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Masuk Sekarang</span>
                        </button>
                    </form>
                    
                    <div class="auth-footer">
                        <p>Belum punya akun? 
                            <button type="button" onclick="switchAuthTab('register')" class="auth-switch-btn">
                                Daftar di sini
                            </button>
                        </p>
                    </div>
                </div>
                
                <!-- Register Form -->
                <div id="registerForm" class="auth-form-container">
                    <form class="auth-form" onsubmit="handleAuthSubmit(event, 'register')">
                        <div class="form-group">
                            <label for="registerName">
                                <i class="fas fa-user"></i>
                                Nama Lengkap
                            </label>
                            <input type="text" id="registerName" name="name" required 
                                   placeholder="Masukkan nama lengkap">
                        </div>
                        
                        <div class="form-group">
                            <label for="registerEmail">
                                <i class="fas fa-envelope"></i>
                                Email
                            </label>
                            <input type="email" id="registerEmail" name="email" required 
                                   placeholder="nama@email.com">
                        </div>
                        
                        <div class="form-group">
                            <label for="registerPassword">
                                <i class="fas fa-lock"></i>
                                Password
                            </label>
                            <div class="password-input-wrapper">
                                <input type="password" id="registerPassword" name="password" required 
                                       minlength="6" placeholder="Minimal 6 karakter" class="password-input">
                                <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('registerPassword')">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="registerConfirmPassword">
                                <i class="fas fa-check-circle"></i>
                                Konfirmasi Password
                            </label>
                            <div class="password-input-wrapper">
                                <input type="password" id="registerConfirmPassword" name="confirm_password" required 
                                       minlength="6" placeholder="Ulangi password" class="password-input">
                                <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('registerConfirmPassword')">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                        
                        <button type="submit" class="auth-submit-btn">
                            <i class="fas fa-user-plus"></i>
                            <span>Daftar Sekarang</span>
                        </button>
                    </form>
                    
                    <div class="auth-footer">
                        <p>Sudah punya akun? 
                            <button type="button" onclick="switchAuthTab('login')" class="auth-switch-btn">
                                Masuk di sini
                            </button>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="auth-modal">
        <div class="auth-modal-overlay" onclick="closeLogoutModal()"></div>
        <div class="auth-modal-content logout-modal">
            <div class="logout-icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <h3>Konfirmasi Logout</h3>
            <p>Apakah Anda yakin ingin keluar dari akun?</p>
            <div class="logout-actions">
                <button onclick="closeLogoutModal()" class="btn-cancel">
                    <i class="fas fa-times"></i>
                    Batal
                </button>
                <button onclick="confirmLogout()" class="btn-confirm">
                    <i class="fas fa-sign-out-alt"></i>
                    Ya, Logout
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Notification System -->
    <div id="toastNotification" class="toast-notification">
        <div class="toast-notification-content">
            <i class="fas fa-check-circle toast-notification-icon"></i>
            <span class="toast-notification-message">Operasi berhasil!</span>
        </div>
        <button class="toast-notification-close" onclick="hideToastNotification()">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <script>
        // Enhanced Auth Modal Functions
        function openAuthModal(type = 'login') {
            const modal = document.getElementById('authModal');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            switchAuthTab(type);
            
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);
        }
        
        function closeAuthModal() {
            const modal = document.getElementById('authModal');
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
                resetAuthForms();
            }, 300);
        }
        
        function switchAuthTab(type) {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const title = document.getElementById('authModalTitle');
            const tabs = document.querySelectorAll('.auth-tab-btn');
            
            tabs.forEach(tab => tab.classList.remove('active'));
            loginForm.classList.remove('active');
            registerForm.classList.remove('active');
            
            if (type === 'login') {
                document.querySelector('.auth-tab-btn:first-child').classList.add('active');
                loginForm.classList.add('active');
                title.textContent = 'Masuk ke Akun Anda';
            } else {
                document.querySelector('.auth-tab-btn:last-child').classList.add('active');
                registerForm.classList.add('active');
                title.textContent = 'Buat Akun Baru';
            }
        }
        
        function resetAuthForms() {
            document.querySelectorAll('.auth-form').forEach(form => {
                form.reset();
                const submitBtn = form.querySelector('.auth-submit-btn');
                resetSubmitButton(submitBtn);
            });
        }
        
        // FIXED Password Visibility Toggle - Corrected Logic
        function togglePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            const button = input.parentElement.querySelector('.password-toggle-btn i');
            
            if (input.type === 'password') {
                input.type = 'text';
                button.className = 'fas fa-eye'; // Open eye when showing password
            } else {
                input.type = 'password';
                button.className = 'fas fa-eye-slash'; // Closed eye when hiding password
            }
        }
        
        // Enhanced Form Submission with unified notification
        async function handleAuthSubmit(event, type) {
            event.preventDefault();
            const form = event.target;
            const submitBtn = form.querySelector('.auth-submit-btn');
            const formData = new FormData(form);
            
            if (type === 'register') {
                const password = formData.get('password');
                const confirmPassword = formData.get('confirm_password');
                
                if (password !== confirmPassword) {
                    showToastNotification('Konfirmasi password tidak cocok!', 'error');
                    return;
                }
            }
            
            setSubmitButtonLoading(submitBtn, true);
            
            try {
                const response = await fetch(`auth/${type}.php`, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    closeAuthModal();
                    const message = type === 'login' ? 'Login berhasil! Selamat datang kembali.' : 'Registrasi berhasil! Selamat datang di Mabar.';
                    showToastNotification(message, 'success');
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showToastNotification(result.message, 'error');
                }
            } catch (error) {
                showToastNotification('Terjadi kesalahan koneksi!', 'error');
            } finally {
                setSubmitButtonLoading(submitBtn, false);
            }
        }
        
        function setSubmitButtonLoading(button, loading) {
            const icon = button.querySelector('i');
            const text = button.querySelector('span');
            
            if (loading) {
                button.disabled = true;
                button.classList.add('loading');
                icon.className = 'fas fa-spinner fa-spin';
                text.textContent = 'Memproses...';
            } else {
                button.disabled = false;
                button.classList.remove('loading');
                const isLogin = button.closest('#loginForm');
                if (isLogin) {
                    icon.className = 'fas fa-sign-in-alt';
                    text.textContent = 'Masuk Sekarang';
                } else {
                    icon.className = 'fas fa-user-plus';
                    text.textContent = 'Daftar Sekarang';
                }
            }
        }
        
        function resetSubmitButton(button) {
            if (!button) return;
            button.disabled = false;
            button.classList.remove('loading');
            
            const icon = button.querySelector('i');
            const text = button.querySelector('span');
            const isLogin = button.closest('#loginForm');
            
            if (isLogin) {
                icon.className = 'fas fa-sign-in-alt';
                text.textContent = 'Masuk Sekarang';
            } else {
                icon.className = 'fas fa-user-plus';
                text.textContent = 'Daftar Sekarang';
            }
        }
        
        // Logout Functions
        function showLogoutConfirmation() {
            const modal = document.getElementById('logoutModal');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);
        }
        
        function closeLogoutModal() {
            const modal = document.getElementById('logoutModal');
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }, 300);
        }
        
        async function confirmLogout() {
            try {
                const response = await fetch('auth/logout.php');
                const result = await response.json();
                
                if (result.success) {
                    closeLogoutModal();
                    showToastNotification('Logout berhasil! Terima kasih telah berkunjung.', 'success');
                    
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2000);
                }
            } catch (error) {
                window.location.href = 'auth/logout.php';
            }
        }
        
        // Toast Notification System - Ganti fungsi showUnifiedNotification
        function showToastNotification(message, type = 'success') {
            const notification = document.getElementById('toastNotification');
            const messageEl = notification.querySelector('.toast-notification-message');
            const icon = notification.querySelector('.toast-notification-icon');
            
            messageEl.textContent = message;
            
            // Set icon dan style berdasarkan type
            notification.className = `toast-notification ${type}`;
            
            if (type === 'success') {
                icon.className = 'fas fa-check-circle toast-notification-icon';
            } else if (type === 'error') {
                icon.className = 'fas fa-exclamation-circle toast-notification-icon';
            } else if (type === 'info') {
                icon.className = 'fas fa-info-circle toast-notification-icon';
            } else if (type === 'warning') {
                icon.className = 'fas fa-exclamation-triangle toast-notification-icon';
            }
            
            notification.classList.add('show');
            
            // Auto hide setelah 4 detik
            setTimeout(() => {
                hideToastNotification();
            }, 4000);
        }

        function hideToastNotification() {
            const notification = document.getElementById('toastNotification');
            notification.classList.remove('show');
        }
        
        // Global function for review success
        window.showReviewSuccess = function() {
            showToastNotification('Ulasan berhasil dibuat!', 'success');
        };
        
        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAuthModal();
                closeLogoutModal();
            }
        });
        
        // Existing header functions
        function toggleUserDropdown(event) {
            event.preventDefault();
            event.stopPropagation();
            const dropdown = document.getElementById('userDropdown');
            const arrow = event.currentTarget.querySelector('.dropdown-arrow');
            
            dropdown.classList.toggle('show');
            
            if (dropdown.classList.contains('show')) {
                arrow.style.transform = 'rotate(180deg)';
            } else {
                arrow.style.transform = 'rotate(0deg)';
            }
        }
        
        document.addEventListener('click', function(event) {
            const userMenu = document.querySelector('.user-menu');
            const dropdown = document.getElementById('userDropdown');
            const arrow = document.querySelector('.dropdown-arrow');
            
            if (!userMenu || !userMenu.contains(event.target)) {
                if (dropdown) {
                    dropdown.classList.remove('show');
                    if (arrow) arrow.style.transform = 'rotate(0deg)';
                }
            }
        });
        
        // Theme toggle functionality
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;
        
        const savedTheme = localStorage.getItem('theme') || 'light';
        html.setAttribute('data-theme', savedTheme);
        updateThemeIcon(savedTheme);
        
        themeToggle.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon(newTheme);
        });
        
        function updateThemeIcon(theme) {
            const icon = themeToggle.querySelector('i');
            icon.className = theme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
        }
        
        // Search functionality
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const query = this.value.trim();
                if (query) {
                    window.location.href = `search.php?q=${encodeURIComponent(query)}`;
                }
            }
        });

        // Authentication check functions untuk fitur yang dilindungi
        function requireAuth(callback, feature = 'fitur ini') {
            <?php if ($current_user): ?>
                callback();
            <?php else: ?>
                showToastNotification(`Anda harus login untuk menggunakan ${feature}`, 'info');
                setTimeout(() => {
                    openAuthModal('login');
                }, 1500);
            <?php endif; ?>
        }

        // Global functions
        window.requireAuth = requireAuth;
        window.showToastNotification = showToastNotification;
        window.showReviewSuccess = function() {
            showToastNotification('Ulasan berhasil dibuat!', 'success');
        };
    </script>
</body>
</html>