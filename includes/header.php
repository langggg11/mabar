<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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

/* Toast notification styles */
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

/* Search dropdown styles */
.nav-search {
    position: relative;
    flex: 1;
    max-width: 600px;
}

.search-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: var(--bg-card);
    border: 1px solid var(--gray-300);
    border-top: none;
    border-radius: 0 0 var(--radius-xl) var(--radius-xl);
    box-shadow: var(--shadow-xl);
    z-index: 1000;
    display: none;
    max-height: 400px;
    overflow-y: auto;
    margin-top: -1px;
}

.suggestion-item {
    display: flex;
    align-items: center;
    padding: var(--space-3) var(--space-4);
    text-decoration: none;
    color: var(--text-primary);
    border-bottom: 1px solid var(--gray-100);
    transition: all var(--transition-fast);
    cursor: pointer;
    gap: var(--space-3);
}

.suggestion-item:hover {
    background: var(--gray-50);
    color: var(--primary-600);
}

.suggestion-item img {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: var(--radius-md);
    flex-shrink: 0;
}

.suggestion-title {
    font-weight: 500;
    font-size: 0.95rem;
    line-height: 1.4;
    flex: 1;
}

.suggestion-title strong {
    color: var(--primary-600);
    font-weight: 700;
}

.search-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--space-4);
    color: var(--text-muted);
}

.search-loading i {
    margin-right: var(--space-2);
    animation: spin 1s linear infinite;
}

.no-suggestion-results {
    padding: var(--space-4);
    text-align: center;
    color: var(--text-muted);
    font-size: 0.9rem;
    font-style: italic;
}

/* Dark mode support */
[data-theme="dark"] .search-dropdown {
    background: var(--bg-card);
    border-color: var(--gray-600);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
}

[data-theme="dark"] .suggestion-item {
    border-bottom-color: var(--gray-700);
}

[data-theme="dark"] .suggestion-item:hover {
    background: var(--gray-700);
    color: var(--primary-400);
}

[data-theme="dark"] .suggestion-title strong {
    color: var(--primary-400);
}
</style>
</head>
<body>
    <!-- Header -->
    
<header class="header" id="header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <a href="index.php" style="display: flex; align-items: center; gap: 12px; text-decoration: none;">
                        <img src="assets/images/logo-mabar.png" alt="Mabar Logo" class="logo-image">
                        <span class="logo-text">Mabar</span>
                </a>
            </div>
            
            <nav class="nav">
                <a href="index.php" class="nav-link">Beranda</a>
                <a href="joran.php" class="nav-link">Joran</a>
                <a href="reel.php" class="nav-link">Reel</a>
                <a href="umpan.php" class="nav-link">Umpan</a>
                <a href="aksesoris.php" class="nav-link">Aksesoris</a>
            </nav>
            
            <div class="nav-search">
                <input type="text" id="searchInput" class="search-input" placeholder="Cari produk pancing..." autocomplete="off">
                <i class="fas fa-search search-icon"></i>
                <div id="searchResultsDropdown" class="search-dropdown"></div>
            </div>
            
            <div class="header-actions">
                <button id="themeToggle" class="theme-toggle" aria-label="Toggle theme">
                    <i class="fas fa-moon"></i>
                </button>
                
                <?php if ($current_user): ?>
                    <div class="user-menu <?php echo isAdmin() ? 'admin-menu' : ''; ?>">
                        <button class="user-btn" onclick="toggleUserDropdown()">
                            <i class="fas fa-user-circle"></i>
                            <span><?php echo htmlspecialchars($current_user['name']); ?></span>
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </button>
                        <div class="user-dropdown" id="userDropdown">
                            <?php if (isAdmin()): ?>
                                <a href="admin.php" class="dropdown-item">
                                    <i class="fas fa-tachometer-alt"></i>
                                    Admin Panel
                                </a>
                                <a href="add-product.php" class="dropdown-item">
                                    <i class="fas fa-plus"></i>
                                    Tambah Produk
                                </a>
                                <a href="admin_profile.php" class="dropdown-item">
                                    <i class="fas fa-user-shield"></i>
                                    Profil Admin
                                </a>
                                <div class="dropdown-divider"></div>
                            <?php else: ?>
                                <a href="wishlist.php" class="dropdown-item">
                                    <i class="fas fa-heart"></i>
                                    Wishlist
                                </a>
                                <a href="profile.php" class="dropdown-item">
                                    <i class="fas fa-user"></i>
                                    Profil Saya
                                </a>
                                <div class="dropdown-divider"></div>
                            <?php endif; ?>
                            <button class="dropdown-item logout-item" onclick="handleLogout()">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <button class="login-btn" onclick="openAuthModal('login')">
                        <i class="fas fa-user"></i>
                        <span>Masuk</span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<!-- Enhanced Auth Modal -->
<div id="authModal" class="auth-modal">
    <div class="auth-modal-overlay" onclick="closeAuthModal()"></div>
    <div class="auth-modal-content">
        <div class="auth-modal-header">
            <h2 id="authModalTitle">Masuk ke Akun</h2>
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
            <div id="loginFormContainer" class="auth-form-container active">
                <form id="loginForm" class="auth-form">
                    <div class="form-group">
                        <label for="loginEmail">
                            <i class="fas fa-envelope"></i>
                            Email
                        </label>
                        <input type="email" id="loginEmail" name="email" required placeholder="Masukkan email Anda">
                    </div>
                    <div class="form-group">
                        <label for="loginPassword">
                            <i class="fas fa-lock"></i>
                            Password
                        </label>
                        <div class="password-input-wrapper">
                            <input type="password" id="loginPassword" name="password" required placeholder="Masukkan password" class="password-input">
                            <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('loginPassword')">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="auth-submit-btn">
                        <i class="fas fa-sign-in-alt"></i>
                        Masuk
                    </button>
                </form>
            </div>
            
            <!-- Register Form -->
            <div id="registerFormContainer" class="auth-form-container">
                <form id="registerForm" class="auth-form">
                    <div class="form-group">
                        <label for="registerName">
                            <i class="fas fa-user"></i>
                            Nama Lengkap
                        </label>
                        <input type="text" id="registerName" name="name" required placeholder="Masukkan nama lengkap">
                    </div>
                    <div class="form-group">
                        <label for="registerEmail">
                            <i class="fas fa-envelope"></i>
                            Email
                        </label>
                        <input type="email" id="registerEmail" name="email" required placeholder="Masukkan email Anda">
                    </div>
                    <div class="form-group">
                        <label for="registerPassword">
                            <i class="fas fa-lock"></i>
                            Password
                        </label>
                        <div class="password-input-wrapper">
                            <input type="password" id="registerPassword" name="password" required placeholder="Minimal 6 karakter" class="password-input" minlength="6">
                            <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('registerPassword')">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">
                            <i class="fas fa-check-circle"></i>
                            Konfirmasi Password
                        </label>
                        <input type="password" id="confirmPassword" name="confirm_password" required placeholder="Ulangi password" minlength="6">
                    </div>
                    <button type="submit" class="auth-submit-btn">
                        <i class="fas fa-user-plus"></i>
                        Daftar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

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
// Global auth functions
function openAuthModal(type = 'login') {
    const modal = document.getElementById('authModal');
    const title = document.getElementById('authModalTitle');
    
    if (type === 'register') {
        switchAuthTab('register');
        title.textContent = 'Daftar Akun Baru';
    } else {
        switchAuthTab('login');
        title.textContent = 'Masuk ke Akun';
    }
    
    modal.style.display = 'flex';
    setTimeout(() => modal.classList.add('show'), 10);
    document.body.style.overflow = 'hidden';
}

function closeAuthModal() {
    const modal = document.getElementById('authModal');
    modal.classList.remove('show');
    setTimeout(() => {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }, 300);
}

function switchAuthTab(type) {
    // Update tab buttons
    document.querySelectorAll('.auth-tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector(`[onclick="switchAuthTab('${type}')"]`).classList.add('active');
    
    // Update form containers
    document.querySelectorAll('.auth-form-container').forEach(container => container.classList.remove('active'));
    document.getElementById(type + 'FormContainer').classList.add('active');
    
    // Update modal title
    const title = document.getElementById('authModalTitle');
    title.textContent = type === 'login' ? 'Masuk ke Akun' : 'Daftar Akun Baru';
}

function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    const button = input.parentElement.querySelector('.password-toggle-btn i');
    
    if (input.type === 'password') {
        input.type = 'text';
        button.className = 'fas fa-eye';
    } else {
        input.type = 'password';
        button.className = 'fas fa-eye-slash';
    }
}

function toggleUserDropdown() {
    const dropdown = document.getElementById('userDropdown');
    const arrow = document.querySelector('.dropdown-arrow');
    
    dropdown.classList.toggle('show');
    arrow.style.transform = dropdown.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
}

function handleLogout() {
    const modal = document.getElementById('logoutModal');
    modal.style.display = 'flex';
    setTimeout(() => modal.classList.add('show'), 10);
    document.body.style.overflow = 'hidden';
}

function closeLogoutModal() {
    const modal = document.getElementById('logoutModal');
    modal.classList.remove('show');
    setTimeout(() => {
        modal.style.display = 'none';
        document.body.style.overflow = '';
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

// Enhanced Live Search Implementation
let searchTimeout;
let currentSearchQuery = '';

function setupLiveSearch() {
    const searchInput = document.getElementById('searchInput');
    const dropdown = document.getElementById('searchResultsDropdown');
    
    if (!searchInput || !dropdown) return;
    
    // Input event for live search
    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        currentSearchQuery = query;
        
        clearTimeout(searchTimeout);
        
        if (query.length >= 2) {
            searchTimeout = setTimeout(() => {
                performLiveSearch(query);
            }, 300);
        } else {
            hideSearchDropdown();
        }
    });
    
    // Enter key to go to search results
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (currentSearchQuery.trim()) {
                window.location.href = `search.php?q=${encodeURIComponent(currentSearchQuery.trim())}`;
            }
        }
    });
    
    // Focus event
    searchInput.addEventListener('focus', function(e) {
        if (e.target.value.trim().length >= 2) {
            performLiveSearch(e.target.value.trim());
        }
    });
    
    // Escape key to close dropdown
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideSearchDropdown();
        }
    });
    
    // Click outside to close
    document.addEventListener('click', function(e) {
        const searchContainer = document.querySelector('.nav-search');
        if (searchContainer && !searchContainer.contains(e.target)) {
            hideSearchDropdown();
        }
    });
}

async function performLiveSearch(query) {
    const dropdown = document.getElementById('searchResultsDropdown');
    if (!dropdown) return;
    
    try {
        // Show loading
        dropdown.innerHTML = `
            <div class="search-loading">
                <i class="fas fa-spinner fa-spin"></i>
                Mencari...
            </div>
        `;
        dropdown.style.display = 'block';
        
        // Fetch search results
        const response = await fetch(`api/live_search.php?q=${encodeURIComponent(query)}&limit=8`);
        const results = await response.json();
        
        // Only show results if this is still the current query
        if (query === currentSearchQuery) {
            displaySearchResults(results, query);
        }
    } catch (error) {
        console.error('Search error:', error);
        dropdown.innerHTML = `
            <div class="no-suggestion-results">
                Terjadi kesalahan saat mencari
            </div>
        `;
    }
}

function displaySearchResults(results, query) {
    const dropdown = document.getElementById('searchResultsDropdown');
    if (!dropdown) return;
    
    if (!results || results.length === 0) {
        dropdown.innerHTML = `
            <div class="no-suggestion-results">
                Tidak ada hasil untuk "${query}"
            </div>
        `;
        return;
    }
    
    const html = results.map(product => `
        <a href="product-detail.php?slug=${product.slug}" class="suggestion-item">
            <img src="${product.image}" alt="${product.name}" onerror="this.src='/placeholder.svg?height=40&width=40'">
            <span class="suggestion-title">${highlightSearchTerm(product.name, query)}</span>
        </a>
    `).join('');
    
    // Add "View all results" link
    const viewAllHtml = `
        <a href="search.php?q=${encodeURIComponent(query)}" class="suggestion-item" style="border-top: 1px solid var(--gray-200); font-weight: 600;">
            <i class="fas fa-search" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: var(--gray-100); border-radius: var(--radius-md); color: var(--primary-600);"></i>
            <span class="suggestion-title">Lihat semua hasil untuk "${query}"</span>
        </a>
    `;
    
    dropdown.innerHTML = html + viewAllHtml;
    dropdown.style.display = 'block';
}

function highlightSearchTerm(text, term) {
    if (!term) return text;
    const regex = new RegExp(`(${term})`, 'gi');
    return text.replace(regex, '<strong>$1</strong>');
}

function hideSearchDropdown() {
    const dropdown = document.getElementById('searchResultsDropdown');
    if (dropdown) {
        dropdown.style.display = 'none';
    }
}

// Toast notification function
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

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    setupLiveSearch();
    
    // Setup form submissions
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', handleLoginSubmit);
    }
    
    if (registerForm) {
        registerForm.addEventListener('submit', handleRegisterSubmit);
    }
    
    // Setup theme toggle
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
});

async function handleLoginSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('.auth-submit-btn');
    
    try {
        setButtonLoading(submitBtn, true);
        const response = await fetch('auth/login.php', { method: 'POST', body: formData });
        const result = await response.json();
        
        if (result.success) {
            closeAuthModal();
            showToastNotification('Login berhasil! Selamat datang kembali.', 'success');
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showToastNotification(result.message, 'error');
        }
    } catch (error) {
        showToastNotification('Terjadi kesalahan koneksi.', 'error');
    } finally {
        setButtonLoading(submitBtn, false);
    }
}

async function handleRegisterSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('.auth-submit-btn');
    
    if (formData.get('password') !== formData.get('confirm_password')) {
        showToastNotification('Konfirmasi password tidak cocok.', 'error');
        return;
    }
    
    try {
        setButtonLoading(submitBtn, true);
        const response = await fetch('auth/register.php', { method: 'POST', body: formData });
        const result = await response.json();
        
        if (result.success) {
            closeAuthModal();
            showToastNotification('Registrasi berhasil! Selamat datang di Mabar.', 'success');
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showToastNotification(result.message, 'error');
        }
    } catch (error) {
        showToastNotification('Terjadi kesalahan koneksi.', 'error');
    } finally {
        setButtonLoading(submitBtn, false);
    }
}

function setButtonLoading(button, loading) {
    if (loading) {
        button.disabled = true;
        button.dataset.originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    } else {
        button.disabled = false;
        button.innerHTML = button.dataset.originalText || 'Submit';
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const userMenu = document.querySelector('.user-menu');
    const dropdown = document.getElementById('userDropdown');
    
    if (userMenu && !userMenu.contains(e.target)) {
        dropdown.classList.remove('show');
        document.querySelector('.dropdown-arrow').style.transform = 'rotate(0deg)';
    }
});

// Escape key to close modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const authModal = document.getElementById('authModal');
        const logoutModal = document.getElementById('logoutModal');
        if (authModal && authModal.classList.contains('show')) {
            closeAuthModal();
        }
        if (logoutModal && logoutModal.classList.contains('show')) {
            closeLogoutModal();
        }
    }
});

// Global functions for backward compatibility
window.requireAuth = function(callback, feature = 'fitur ini') {
    <?php if ($current_user): ?>
        callback();
    <?php else: ?>
        showToastNotification(`Anda harus login untuk menggunakan ${feature}`, 'info');
        setTimeout(() => {
            openAuthModal('login');
        }, 1500);
    <?php endif; ?>
};

window.showToastNotification = showToastNotification;
window.openAuthModal = openAuthModal;
</script>
</body>
</html>
