<?php
require_once 'includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: index.php');
    exit;
}

$page_title = "Tambah Produk Baru - Mabar";
$current_user = getCurrentUser();
$category_param = $_GET['category'] ?? '';

// Handle form submission
if ($_POST) {
    $name = sanitizeInput($_POST['name']);
    $description = sanitizeInput($_POST['description']);
    $specifications = sanitizeInput($_POST['specifications']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);
    $subcategory = sanitizeInput($_POST['subcategory']);
    $product_link = sanitizeInput($_POST['product_link']);
    
    // Handle file upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'assets/images/';
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $filename = uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_path = $upload_path;
            } else {
                $error_message = "Gagal mengupload gambar.";
            }
        } else {
            $error_message = "Format gambar tidak didukung. Gunakan JPG, PNG, atau GIF.";
        }
    } else {
        $error_message = "Gambar produk wajib diupload.";
    }
    
    if (!isset($error_message)) {
        // Generate slug
        $slug = generateSlug($name);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, slug, description, specifications, price, category_id, subcategory, image, shopee_link, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            if ($stmt->execute([$name, $slug, $description, $specifications, $price, $category_id, $subcategory, $image_path, $product_link])) {
                $success_message = "Produk berhasil ditambahkan!";
                // Reset form
                $_POST = [];
            }
        } catch (Exception $e) {
            $error_message = "Gagal menambahkan produk: " . $e->getMessage();
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

// Get default category ID based on parameter
$default_category_id = '';
if ($category_param) {
    foreach ($categories as $category) {
        if (strtolower($category['slug']) === strtolower($category_param)) {
            $default_category_id = $category['id'];
            break;
        }
    }
}
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
    
    <!-- Add Product Section -->
    <section class="add-product-section">
        <div class="container">
            <!-- Breadcrumb -->
            <nav class="breadcrumb">
                <a href="admin.php"><i class="fas fa-tachometer-alt"></i> Admin Panel</a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Tambah Produk</span>
            </nav>
            
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <div class="add-product-layout">
                <!-- Form Section -->
                <div class="add-form-section">
                    <div class="section-header">
                        <h1><i class="fas fa-plus"></i> Tambah Produk Baru</h1>
                        <p>Lengkapi informasi produk di bawah ini</p>
                    </div>
                    
                    <form method="POST" enctype="multipart/form-data" class="add-product-form">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="name">Nama Produk</label>
                                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required placeholder="Masukkan nama produk">
                            </div>
                            
                            <div class="form-group">
                                <label for="category_id">Kategori</label>
                                <select id="category_id" name="category_id" required>
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" <?php echo ($category['id'] == $default_category_id || $category['id'] == ($_POST['category_id'] ?? '')) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="subcategory">Subkategori</label>
                                <input type="text" id="subcategory" name="subcategory" value="<?php echo htmlspecialchars($_POST['subcategory'] ?? ''); ?>" placeholder="Contoh: Spinning, Casting">
                            </div>
                            
                            <div class="form-group">
                                <label for="price">Harga</label>
                                <input type="number" id="price" name="price" value="<?php echo $_POST['price'] ?? ''; ?>" required min="0" step="1000" placeholder="0">
                            </div>
                            
                            <div class="form-group full-width">
                                <label for="description">Deskripsi</label>
                                <textarea id="description" name="description" rows="4" placeholder="Deskripsi produk"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="form-group full-width">
                                <label for="specifications">Spesifikasi</label>
                                <textarea id="specifications" name="specifications" rows="6" placeholder="Masukkan spesifikasi produk (satu per baris)&#10;Contoh:&#10;Panjang: 2.1m&#10;Berat: 150g&#10;Material: Carbon Fiber"><?php echo htmlspecialchars($_POST['specifications'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="image">Gambar Produk</label>
                                <input type="file" id="image" name="image" accept="image/*" required>
                                <small>Format: JPG, PNG, GIF. Maksimal 5MB.</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="product_link">Link Produk</label>
                                <input type="url" id="product_link" name="product_link" value="<?php echo htmlspecialchars($_POST['product_link'] ?? ''); ?>" placeholder="https://shopee.co.id/...">
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Produk
                            </button>
                            <a href="admin.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
                
                <!-- Preview Section -->
                <div class="preview-section">
                    <div class="section-header">
                        <h2><i class="fas fa-eye"></i> Preview Produk</h2>
                        <p>Pratinjau bagaimana produk akan terlihat</p>
                    </div>
                    
                    <div class="product-preview">
                        <div class="preview-image">
                            <img id="previewImage" src="/placeholder.svg?height=200&width=300" alt="Preview">
                        </div>
                        <div class="preview-info">
                            <div class="preview-category" id="previewCategory">Kategori</div>
                            <h3 class="preview-name" id="previewName">Nama Produk</h3>
                            <div class="preview-price" id="previewPrice">Rp 0</div>
                            <div class="preview-description" id="previewDescription">Deskripsi produk</div>
                            <div class="preview-specs" id="previewSpecs" style="margin-top: 1rem;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/script.js"></script>
    <script>
        // Live preview functionality
        document.getElementById('name').addEventListener('input', function() {
            document.getElementById('previewName').textContent = this.value || 'Nama Produk';
        });
        
        document.getElementById('subcategory').addEventListener('input', function() {
            document.getElementById('previewCategory').textContent = this.value || 'Kategori';
        });
        
        document.getElementById('price').addEventListener('input', function() {
            const price = parseInt(this.value) || 0;
            document.getElementById('previewPrice').textContent = 'Rp ' + price.toLocaleString('id-ID');
        });
        
        document.getElementById('description').addEventListener('input', function() {
            document.getElementById('previewDescription').innerHTML = this.value.replace(/\n/g, '<br>') || 'Deskripsi produk';
        });

        document.getElementById('specifications').addEventListener('input', function() {
            const specsContainer = document.getElementById('previewSpecs');
            const specsText = this.value.trim();
            specsContainer.innerHTML = '';

            if (specsText) {
                const specs = specsText.split('\n');
                const specsList = document.createElement('div');
                specsList.className = 'specs-list-preview';
                
                specs.slice(0, 3).forEach(spec => {
                    if (spec.trim()) {
                        const specParts = spec.split(':');
                        if (specParts.length === 2) {
                            const specRow = document.createElement('div');
                            specRow.className = 'spec-row-preview';
                            specRow.innerHTML = `<span class="spec-label-preview">${specParts[0].trim()}:</span> <span class="spec-value-preview">${specParts[1].trim()}</span>`;
                            specsList.appendChild(specRow);
                        }
                    }
                });
                specsContainer.appendChild(specsList);
            }
        });
        
        document.getElementById('image').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImage').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
    
    <style>
        .add-product-section {
            padding: var(--space-16) 0;
            background: var(--bg-primary);
            min-height: 80vh;
        }
        
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: var(--space-2);
            margin-bottom: var(--space-8);
            font-size: 0.9rem;
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
        
        .breadcrumb-separator {
            color: var(--text-muted);
        }
        
        .breadcrumb-current {
            color: var(--text-secondary);
        }
        
        .add-product-layout {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: var(--space-8);
        }
        
        .add-form-section, .preview-section {
            background: var(--bg-card);
            border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }
        
        .section-header {
            padding: var(--space-8);
            border-bottom: 1px solid var(--gray-200);
            background: var(--gray-50);
        }
        
        .section-header h1, .section-header h2 {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            margin-bottom: var(--space-2);
            color: var(--text-primary);
        }
        
        .section-header p {
            color: var(--text-secondary);
            margin: 0;
        }
        
        .add-product-form {
            padding: var(--space-8);
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--space-6);
            margin-bottom: var(--space-8);
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: var(--space-2);
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-group label {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: var(--space-4);
            border: 2px solid var(--gray-300);
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
        
        .form-group small {
            color: var(--text-muted);
            font-size: 0.85rem;
        }
        
        .form-actions {
            display: flex;
            gap: var(--space-4);
            justify-content: flex-start;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: var(--space-2);
            padding: var(--space-4) var(--space-6);
            border: none;
            border-radius: var(--radius-xl);
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-normal);
            text-decoration: none;
            font-size: 1rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-500), var(--primary-700));
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .btn-secondary {
            background: var(--gray-200);
            color: var(--text-secondary);
        }
        
        .btn-secondary:hover {
            background: var(--gray-300);
            color: var(--text-primary);
        }
        
        .product-preview {
            padding: var(--space-8);
        }
        
        .preview-image {
            width: 100%;
            height: 200px;
            background: var(--gray-100);
            border-radius: var(--radius-xl);
            overflow: hidden;
            margin-bottom: var(--space-6);
        }
        
        .preview-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .preview-category {
            color: var(--primary-600);
            font-size: 0.875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: var(--space-2);
        }
        
        .preview-name {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: var(--space-4);
            color: var(--text-primary);
        }
        
        .preview-price {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--accent-green);
            margin-bottom: var(--space-4);
        }
        
        .preview-description {
            color: var(--text-secondary);
            line-height: 1.6;
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
        
        @media (max-width: 1024px) {
            .add-product-layout {
                grid-template-columns: 1fr;
            }
            
            .preview-section {
                order: -1;
            }
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn {
                justify-content: center;
            }
        }
    </style>
</body>
</html>
