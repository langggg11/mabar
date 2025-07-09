<?php
$page_title = "Profil Saya - Mabar";
$page_description = "Kelola profil dan pengaturan akun Anda";

require_once 'includes/functions.php';

// Redirect if not logged in
$current_user = getCurrentUser();
if (!$current_user) {
    header('Location: index.php');
    exit;
}

// Handle password change
$message = '';
$message_type = '';

if ($_POST && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $message = 'Semua field harus diisi';
        $message_type = 'error';
    } elseif ($new_password !== $confirm_password) {
        $message = 'Password baru dan konfirmasi tidak sama';
        $message_type = 'error';
    } elseif (strlen($new_password) < 6) {
        $message = 'Password baru minimal 6 karakter';
        $message_type = 'error';
    } else {
        // Verify current password
        if (password_verify($current_password, $current_user['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($stmt->execute([$hashed_password, $current_user['id']])) {
                $message = 'Password berhasil diubah';
                $message_type = 'success';
            } else {
                $message = 'Gagal mengubah password';
                $message_type = 'error';
            }
        } else {
            $message = 'Password saat ini salah';
            $message_type = 'error';
        }
    }
}

// Get user statistics
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as wishlist_count FROM wishlist WHERE user_id = ?");
    $stmt->execute([$current_user['id']]);
    $wishlist_count = $stmt->fetchColumn();
} catch (Exception $e) {
    $wishlist_count = 0;
}
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
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="profile-section">
        <div class="container">
            <div class="profile-header">
                <h1>
                    <i class="fas fa-user-circle"></i>
                    Profil Saya
                </h1>
                <p>Kelola informasi akun dan pengaturan Anda dengan mudah</p>
            </div>
            
            <div class="profile-content">
                <!-- Profile Information -->
                <div class="profile-card">
                    <div class="card-header">
                        <h2>
                            <i class="fas fa-id-card"></i>
                            Informasi Akun
                        </h2>
                    </div>
                    <div class="card-body">
                        <div class="profile-info">
                            <div class="info-item">
                                <label><i class="fas fa-user"></i> Nama Lengkap:</label>
                                <span class="info-value"><?php echo htmlspecialchars($current_user['name']); ?></span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-envelope"></i> Email:</label>
                                <span class="info-value"><?php echo htmlspecialchars($current_user['email']); ?></span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-lock"></i> Password:</label>
                                <span class="info-value">••••••••••</span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-shield-alt"></i> Status:</label>
                                <span class="info-value">
                                    <?php if ($current_user['is_admin']): ?>
                                        <span class="admin-badge">
                                            <i class="fas fa-crown"></i>
                                            Administrator
                                        </span>
                                    <?php else: ?>
                                        <span style="color: var(--primary-600); font-weight: 600;">
                                            <i class="fas fa-user"></i> Member
                                        </span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-calendar-alt"></i> Bergabung:</label>
                                <span class="info-value"><?php echo date('d F Y', strtotime($current_user['created_at'])); ?></span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-heart"></i> Wishlist:</label>
                                <span class="info-value">
                                    <span style="color: var(--accent-pink); font-weight: 700;">
                                        <?php echo $wishlist_count; ?> produk
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Change Password -->
                <div class="profile-card">
                    <div class="card-header">
                        <h2>
                            <i class="fas fa-key"></i>
                            Ubah Password
                        </h2>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $message_type; ?>">
                                <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="password-form">
                            <div class="form-group">
                                <label for="current_password">
                                    <i class="fas fa-lock"></i>
                                    Password Saat Ini
                                </label>
                                <div class="profile-password-wrapper">
                                    <input type="password" id="current_password" name="current_password" required 
                                           placeholder="Masukkan password saat ini" class="profile-password-input">
                                    <button type="button" class="profile-password-toggle" onclick="toggleProfilePasswordVisibility('current_password')">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="new_password">
                                    <i class="fas fa-key"></i>
                                    Password Baru
                                </label>
                                <div class="profile-password-wrapper">
                                    <input type="password" id="new_password" name="new_password" minlength="6" required 
                                           placeholder="Masukkan password baru (min. 6 karakter)" class="profile-password-input">
                                    <button type="button" class="profile-password-toggle" onclick="toggleProfilePasswordVisibility('new_password')">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm_password">
                                    <i class="fas fa-check-circle"></i>
                                    Konfirmasi Password Baru
                                </label>
                                <!-- Confirm password stays hidden always -->
                                <input type="password" id="confirm_password" name="confirm_password" minlength="6" required 
                                       placeholder="Ulangi password baru">
                            </div>
                            
                            <button type="submit" name="change_password" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Ubah Password
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="profile-card">
                    <div class="card-header">
                        <h2>
                            <i class="fas fa-bolt"></i>
                            Aksi Cepat
                        </h2>
                    </div>
                    <div class="card-body">
                        <div class="quick-actions">
                            <a href="wishlist.php" class="action-btn">
                                <div>
                                    <i class="fas fa-heart"></i>
                                    <span>Lihat Wishlist (<?php echo $wishlist_count; ?>)
                                    </span>
                                </div>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                            
                            <a href="index.php" class="action-btn">
                                <div>
                                    <i class="fas fa-home"></i>
                                    <span>Kembali ke Beranda</span>
                                </div>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                            
                            <?php if ($current_user['is_admin']): ?>
                                <a href="admin.php" class="action-btn admin-action">
                                    <div>
                                        <i class="fas fa-crown"></i>
                                        <span>Panel Admin</span>
                                    </div>
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <script>
        function toggleProfilePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            const button = input.parentElement.querySelector('.profile-password-toggle i');
            
            if (input.type === 'password') {
                input.type = 'text';
                button.className = 'fas fa-eye';
            } else {
                input.type = 'password';
                button.className = 'fas fa-eye-slash';
            }
        }
        
        // Auto-hide alert after 5 seconds
        const alert = document.querySelector('.alert');
        if (alert) {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }, 5000);
        }
    </script>
</body>
</html>
