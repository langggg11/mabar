<?php
require_once 'includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: index.php');
    exit;
}

$product_id = $_GET['id'] ?? 0;
$product = null;

if ($product_id) {
    try {
        $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
    } catch (Exception $e) {
        $product = null;
    }
}

if (!$product) {
    header('Location: admin.php');
    exit;
}

$message = '';
$message_type = '';

// Handle form submission
if ($_POST) {
    if (isset($_POST['update_product'])) {
        $name = sanitizeInput($_POST['name']);
        $description = sanitizeInput($_POST['description']);
        $specifications = sanitizeInput($_POST['specifications']);
        $price = floatval($_POST['price']);
        $category_id = intval($_POST['category_id']);
        $subcategory = sanitizeInput($_POST['subcategory']);
        $product_link = sanitizeInput($_POST['product_link']);
        
        $image_path = $product['image']; // Keep existing image by default
        
        // Handle file upload if new image is provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'assets/images/';
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($file_extension, $allowed_extensions)) {
                $filename = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    // Delete old image if it exists
                    if ($product['image'] && file_exists($product['image'])) {
                        unlink($product['image']);
                    }
                    $image_path = $upload_path;
                } else {
                    $message = "Gagal mengupload gambar.";
                    $message_type = "error";
                }
            } else {
                $message = "Format gambar tidak didukung. Gunakan JPG, PNG, atau GIF.";
                $message_type = "error";
            }
        }
        
        if (empty($message)) {
            // Generate new slug if name changed
            $slug = $product['name'] !== $name ? generateSlug($name) : $product['slug'];
            
            try {
                $stmt = $pdo->prepare("UPDATE products SET name = ?, slug = ?, description = ?, specifications = ?, price = ?, category_id = ?, subcategory = ?, image = ?, shopee_link = ? WHERE id = ?");
                if ($stmt->execute([$name, $slug, $description, $specifications, $price, $category_id, $subcategory, $image_path, $product_link, $product_id])) {
                    $message = "Produk berhasil diperbarui!";
                    $message_type = "success";
                    
                    // Refresh product data
                    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
                    $stmt->execute([$product_id]);
                    $product = $stmt->fetch();
                }
            } catch (Exception $e) {
                $message = "Gagal memperbarui produk: " . $e->getMessage();
                $message_type = "error";
            }
        }
    }
    
    if (isset($_POST['delete_product'])) {
        try {
            // Delete related wishlist entries first
            $stmt = $pdo->prepare("DELETE FROM wishlist WHERE product_id = ?");
            $stmt->execute([$product_id]);
            
            // Delete related reviews
            $stmt = $pdo->prepare("DELETE FROM reviews WHERE product_id = ?");
            $stmt->execute([$product_id]);
            
            // Delete product
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            if ($stmt->execute([$product_id])) {
                // Delete image file if exists
                if ($product['image'] && file_exists($product['image'])) {
                    unlink($product['image']);
                }
                header('Location: admin.php?deleted=1');
                exit;
            }
        } catch (Exception $e) {
            $message = "Gagal menghapus produk: " . $e->getMessage();
            $message_type = "error";
        }
    }
}

// Get categories for dropdown
$categories = [];
try {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll();
} catch (Exception $e) {
    $categories = [];
}

$page_title = "Edit Produk: " . $product['name'] . " - Mabar";
?>

<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    
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
    
    <section class="edit-product-section">
        <div class="container">
            <div class="edit-header">
                <div class="breadcrumb">
                    <a href="admin.php"><i class="fas fa-tachometer-alt"></i> Admin Panel</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>Edit Produk</span>
                </div>
                <h1><i class="fas fa-edit"></i> Edit Produk</h1>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <div class="edit-content">
                <!-- Product Preview -->
                <div class="product-preview">
                    <div class="preview-header">
                        <h3><i class="fas fa-eye"></i> Preview Produk</h3>
                    </div>
                    <div class="preview-card">
                        <div class="preview-image">
                            <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" id="previewImg">
                        </div>
                        <div class="preview-info">
                            <h4 id="previewName"><?php echo htmlspecialchars($product['name']); ?></h4>
                            <p class="preview-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                            <p class="preview-price" id="previewPrice"><?php echo formatPrice($product['price']); ?></p>
                            <div class="preview-actions">
                                <a href="product-detail.php?slug=<?php echo $product['slug']; ?>" target="_blank" class="btn btn-primary">
                                    <i class="fas fa-external-link-alt"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Edit Form -->
                <div class="edit-form-container">
                    <div class="form-header">
                        <h3><i class="fas fa-edit"></i> Edit Informasi Produk</h3>
                    </div>
                    
                    <form method="POST" enctype="multipart/form-data" class="edit-form">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="name">Nama Produk</label>
                                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="category_id">Kategori</label>
                                <select id="category_id" name="category_id" required>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="subcategory">Subkategori</label>
                                <input type="text" id="subcategory" name="subcategory" value="<?php echo htmlspecialchars($product['subcategory']); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="price">Harga</label>
                                <input type="number" id="price" name="price" value="<?php echo $product['price']; ?>" step="1000" min="0" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Deskripsi</label>
                            <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="specifications">Spesifikasi</label>
                            <textarea id="specifications" name="specifications" rows="6"><?php echo htmlspecialchars($product['specifications']); ?></textarea>
                            <small class="form-help">Masukkan spesifikasi produk (satu per baris). Contoh: Panjang: 2.1m</small>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="image">Gambar Produk</label>
                                <input type="file" id="image" name="image" accept="image/*">
                                <small class="form-help">Kosongkan jika tidak ingin mengubah gambar. Format: JPG, PNG, GIF.</small>
                                <div class="current-image">
                                    <span>Gambar saat ini:</span>
                                    <img src="<?php echo $product['image']; ?>" alt="Current image" style="max-width: 100px; height: auto; border-radius: 8px;">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="product_link">Link Produk</label>
                                <input type="url" id="product_link" name="product_link" value="<?php echo htmlspecialchars($product['shopee_link']); ?>">
                            </div>
                        </div>
                        
                        <div class="form-actions">
    <button type="submit" name="update_product" class="btn btn-primary">
        <i class="fas fa-save"></i> Simpan Perubahan
    </button>
    <a href="product-detail.php?slug=<?php echo $product['slug']; ?>" class="btn btn-secondary" target="_blank">
        <i class="fas fa-eye"></i> Lihat Halaman Produk
    </a>
    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
        <i class="fas fa-trash"></i> Hapus Produk
    </button>
</div>
                    </form>
                    
                    <!-- Hidden delete form -->
                    <form id="deleteForm" method="POST" style="display: none;">
                        <input type="hidden" name="delete_product" value="1">
                    </form>
                </div>
            </div>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/script.js"></script>
    <script>
        // Live preview updates
        document.getElementById('name').addEventListener('input', function() {
            document.getElementById('previewName').textContent = this.value;
        });
        
        document.getElementById('price').addEventListener('input', function() {
            const price = parseFloat(this.value) || 0;
            document.getElementById('previewPrice').textContent = 'Rp ' + price.toLocaleString('id-ID');
        });
        
        document.getElementById('image').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
        
        function confirmDelete() {
            if (confirm('Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.')) {
                document.getElementById('deleteForm').submit();
            }
        }
        
        // Auto-hide alerts
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
    
    <style>
        .edit-product-section {
            padding: var(--space-16) 0;
            background: var(--bg-primary);
            min-height: 80vh;
        }
        
        .edit-header {
            margin-bottom: var(--space-8);
        }
        
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: var(--space-2);
            margin-bottom: var(--space-4);
            color: var(--text-secondary);
        }
        
        .breadcrumb a {
            color: var(--primary-600);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: var(--space-2);
        }
        
        .breadcrumb a:hover {
            color: var(--primary-700);
        }
        
        .edit-header h1 {
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: var(--space-3);
        }
        
        .edit-content {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: var(--space-8);
        }
        
        .product-preview, .edit-form-container {
            background: var(--bg-card);
            border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }
        
        .preview-header, .form-header {
            padding: var(--space-6);
            background: var(--gray-100);
            border-bottom: 1px solid var(--gray-200);
        }
        
        .preview-header h3, .form-header h3 {
            margin: 0;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: var(--space-3);
        }
        
        .preview-card {
            padding: var(--space-6);
        }
        
        .preview-image {
            margin-bottom: var(--space-4);
        }
        
        .preview-image img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: var(--radius-lg);
        }
        
        .preview-info h4 {
            margin-bottom: var(--space-2);
            color: var(--text-primary);
        }
        
        .preview-category {
            color: var(--primary-600);
            font-weight: 600;
            margin-bottom: var(--space-2);
        }
        
        .preview-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--accent-green);
            margin-bottom: var(--space-4);
        }
        
        .edit-form {
            padding: var(--space-6);
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--space-4);
            margin-bottom: var(--space-6);
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: var(--space-2);
        }
        
        .form-group label {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: var(--space-3);
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-lg);
            background: var(--bg-primary);
            color: var(--text-primary);
            transition: all var(--transition-fast);
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-500);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
        }
        
        .form-help {
            color: var(--text-muted);
            font-size: 0.85rem;
        }
        
        .current-image {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            margin-top: var(--space-2);
        }
        
        .current-image span {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        
        .form-actions {
            display: flex;
            gap: var(--space-4);
            margin-top: var(--space-8);
            padding-top: var(--space-6);
            border-top: 1px solid var(--gray-200);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            margin-left: auto;
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .alert {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            padding: var(--space-4) var(--space-6);
            border-radius: var(--radius-xl);
            margin-bottom: var(--space-6);
            font-weight: 500;
            border-left: 4px solid;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #065f46;
            border-left-color: #10b981;
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            color: #991b1b;
            border-left-color: #ef4444;
        }
        
        @media (max-width: 768px) {
            .edit-content {
                grid-template-columns: 1fr;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn-danger {
                margin-left: 0;
            }
        }
    </style>
</body>
</html>
