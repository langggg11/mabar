<?php 
require_once 'includes/functions.php';

$slug = $_GET['slug'] ?? '';
$product = getProductBySlug($slug);

if (!$product) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

$page_title = $product['name'] . " - Mabar";
$page_description = substr(strip_tags($product['description']), 0, 160);
$current_user = getCurrentUser();
$reviews = getReviewsByProductId($product['id']);
$rating_info = getProductRating($product['id']);
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
<body data-category="<?php echo $product['category_slug']; ?>">
    <?php include 'includes/header.php'; ?>

    <!-- Product Detail -->
    <section class="product-detail">
        <div class="container">
            <div class="product-detail-content">
                <!-- Product Images - Only Main Image -->
                <div class="product-images">
                    <div class="main-image">
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" id="mainImage">
                        <div class="zoom-overlay"></div>
                    </div>
                </div>
                
                <!-- Product Info -->
                <div class="product-info">
                    <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                    
                    <div class="product-price">
                        <span class="current-price"><?php echo formatPrice($product['price']); ?></span>
                    </div>

                    <div class="product-rating-summary">
                        <div class="stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="<?php echo $i <= $rating_info['rating'] ? 'fas' : 'far'; ?> fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <a href="#reviewsTab" class="review-count">(<?php echo $rating_info['review_count']; ?> ulasan)</a>
                    </div>
                    
                    <!-- Product Specs Quick View -->
                    <div class="product-specs-quick">
                        <?php if ($product['rod_length']): ?>
                            <div class="spec-item">
                                <span class="spec-label">Panjang:</span>
                                <span class="spec-value"><?php echo $product['rod_length']; ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($product['gear_ratio']): ?>
                            <div class="spec-item">
                                <span class="spec-label">Gear Ratio:</span>
                                <span class="spec-value"><?php echo $product['gear_ratio']; ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($product['bearings']): ?>
                            <div class="spec-item">
                                <span class="spec-label">Bearings:</span>
                                <span class="spec-value"><?php echo $product['bearings']; ?></span>
                            </div>
                        <?php endif; ?>
                            
                        <?php if ($product['weight']): ?>
                            <div class="spec-item">
                                <span class="spec-label">Berat:</span>
                                <span class="spec-value"><?php echo $product['weight']; ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($product['target_fish']): ?>
                            <div class="spec-item">
                                <span class="spec-label">Target Ikan:</span>
                                <span class="spec-value"><?php echo $product['target_fish']; ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="product-actions">
                        <a href="javascript:void(0)" onclick="requireAuth(() => window.open('<?php echo $product['shopee_link']; ?>', '_blank'), 'melihat produk di toko online')" class="btn btn-primary btn-large">
                            <i class="fas fa-external-link-alt"></i>
                            Lihat di Toko Online
                        </a>
                        
                        <button class="btn btn-secondary btn-large wishlist-btn-detail" 
                                data-product-id="<?php echo $product['id']; ?>"
                                onclick="requireAuth(() => toggleWishlistDetail(<?php echo $product['id']; ?>), 'menambahkan produk ke daftar yang disukai')">
                            <i class="far fa-heart"></i>
                            Tambahkan ke Produk yang Disukai
                        </button>
                    </div>

                    <!-- Review Action - Always Show Interface -->
                    <div class="product-review-action">
                        <button class="btn btn-secondary" id="writeReviewBtn">
                            <i class="fas fa-pencil-alt"></i> Tulis Ulasan
                        </button>
                        <div class="review-form-container" id="reviewFormContainer" style="display: none;">
                            <h3>Tulis Ulasan Anda</h3>
                            <form id="reviewForm">
                                <div class="form-group">
                                    <label for="rating">Peringkat Anda</label>
                                    <div class="star-rating" onclick="<?php echo !$current_user ? "requireAuth(() => {}, 'memberikan rating')" : ''; ?>">
                                        <input type="radio" id="star5" name="rating" value="5" <?php echo !$current_user ? 'disabled' : ''; ?> /><label for="star5" title="5 stars">&nbsp;</label>
                                        <input type="radio" id="star4" name="rating" value="4" <?php echo !$current_user ? 'disabled' : ''; ?> /><label for="star4" title="4 stars">&nbsp;</label>
                                        <input type="radio" id="star3" name="rating" value="3" <?php echo !$current_user ? 'disabled' : ''; ?> /><label for="star3" title="3 stars">&nbsp;</label>
                                        <input type="radio" id="star2" name="rating" value="2" <?php echo !$current_user ? 'disabled' : ''; ?> /><label for="star2" title="2 stars">&nbsp;</label>
                                        <input type="radio" id="star1" name="rating" value="1" <?php echo !$current_user ? 'disabled' : ''; ?> /><label for="star1" title="1 star">&nbsp;</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="review_text">Ulasan Anda</label>
                                    <textarea id="review_text" name="review_text" rows="4" 
                                              placeholder="Bagikan pemikiran Anda tentang produk ini..."
                                              <?php echo !$current_user ? 'readonly onclick="requireAuth(() => {}, \'menulis ulasan\')"' : ''; ?>></textarea>
                                </div>
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product Details Tabs -->
            <div class="product-tabs">
                <div class="tab-buttons">
                    <button class="tab-btn active" onclick="showTab('description')">Deskripsi</button>
                    <button class="tab-btn" onclick="showTab('specifications')">Spesifikasi</button>
                    <button class="tab-btn" onclick="showTab('reviews')">Ulasan (<?php echo $rating_info['review_count']; ?>)</button>
                </div>
                
                <div class="tab-contents">
                    <div id="descriptionTab" class="tab-content active">
                        <div class="description-content">
                            <?php if ($product['description']): ?>
                                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                            <?php else: ?>
                                <p>Deskripsi produk akan segera tersedia.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div id="specificationsTab" class="tab-content">
                        <div class="specifications-content">
                            <?php if ($product['specifications']): ?>
                                <div class="specs-list">
                                    <?php 
                                    $specs = explode("\n", $product['specifications']);
                                    foreach ($specs as $spec): 
                                        if (trim($spec)):
                                    ?>
                                        <div class="spec-row">
                                            <?php 
                                            $spec_parts = explode(':', $spec, 2);
                                            if (count($spec_parts) == 2):
                                            ?>
                                                <span class="spec-label"><?php echo trim($spec_parts[0]); ?>:</span>
                                                <span class="spec-value"><?php echo trim($spec_parts[1]); ?></span>
                                            <?php else: ?>
                                                <span class="spec-full"><?php echo trim($spec); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </div>
                            <?php else: ?>
                                <p>Spesifikasi produk akan segera tersedia.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div id="reviewsTab" class="tab-content">
                        <div class="reviews-content">
                            <h2>Ulasan Pelanggan</h2>
                            
                            <!-- Existing Reviews -->
                            <div class="reviews-list">
                                <?php if (empty($reviews)): ?>
                                    <div class="no-reviews-placeholder"> <i class="fas fa-comment-slash"></i>
                                     <h4>Belum ada ulasan</h4>
                                     <p>Jadilah yang pertama memberikan ulasan untuk produk ini.</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($reviews as $review): ?>
                                        <div class="review-item">
                                            <div class="review-header">
                                                <span class="review-author"><?php echo htmlspecialchars($review['user_name']); ?></span>
                                                <span class="review-date"><?php echo date('d M Y', strtotime($review['created_at'])); ?></span>
                                            </div>
                                            <div class="review-rating">
                                                <div class="stars">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="<?php echo $i <= $review['rating'] ? 'fas' : 'far'; ?> fa-star"></i>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                            <div class="review-body">
                                                <p><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/script.js"></script>
    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all tab buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName + 'Tab').classList.add('active');
            event.target.classList.add('active');
        }

        // Custom notification function untuk review
        function showCustomNotification(message, type = 'success') {
            // Remove existing notifications
            const existingNotifications = document.querySelectorAll('.custom-notification');
            existingNotifications.forEach(notification => notification.remove());

            // Create notification element
            const notification = document.createElement('div');
            notification.className = `custom-notification ${type}`;
            notification.innerHTML = `
                <div class="notification-content">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                    <span>${message}</span>
                </div>
            `;

            // Add styles
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#10b981' : '#ef4444'};
                color: white;
                padding: 16px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 10000;
                display: flex;
                align-items: center;
                gap: 10px;
                font-weight: 500;
                max-width: 400px;
                animation: slideInRight 0.3s ease-out;
            `;

            // Add animation keyframes if not exists
            if (!document.querySelector('#notification-styles')) {
                const style = document.createElement('style');
                style.id = 'notification-styles';
                style.textContent = `
                    @keyframes slideInRight {
                        from { transform: translateX(100%); opacity: 0; }
                        to { transform: translateX(0); opacity: 1; }
                    }
                    @keyframes slideOutRight {
                        from { transform: translateX(0); opacity: 1; }
                        to { transform: translateX(100%); opacity: 0; }
                    }
                `;
                document.head.appendChild(style);
            }

            document.body.appendChild(notification);

            // Auto remove after 4 seconds
            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 300);
            }, 4000);
        }

        document.getElementById('writeReviewBtn')?.addEventListener('click', function() {
            const reviewFormContainer = document.getElementById('reviewFormContainer');
            if (reviewFormContainer.style.display === 'none') {
                reviewFormContainer.style.display = 'block';
                this.innerHTML = '<i class="fas fa-times"></i> Batal';
            } else {
                reviewFormContainer.style.display = 'none';
                this.innerHTML = '<i class="fas fa-pencil-alt"></i> Tulis Ulasan';
            }
        });

        document.getElementById('reviewForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            <?php if (!$current_user): ?>
                requireAuth(() => {}, 'mengirim ulasan');
                return;
            <?php endif; ?>
            
            const formData = new FormData(this);
            const rating = formData.get('rating');
            const reviewText = formData.get('review_text');

            // Validasi rating wajib
            if (!rating) {
                showCustomNotification('Silakan pilih peringkat bintang sebelum mengirim ulasan.', 'error');
                return;
            }

            // Tampilkan loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
            submitBtn.disabled = true;
            
            fetch('api/submit_review.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showCustomNotification('Ulasan berhasil dibuat!', 'success');
                    // Reset form
                    this.reset();
                    // Hide form
                    document.getElementById('reviewFormContainer').style.display = 'none';
                    document.getElementById('writeReviewBtn').innerHTML = '<i class="fas fa-pencil-alt"></i> Tulis Ulasan';
                    // Reload page after 2 seconds to show new review
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showCustomNotification(data.message || 'Gagal mengirim ulasan', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showCustomNotification('Terjadi kesalahan saat mengirim ulasan.', 'error');
            })
            .finally(() => {
                // Restore button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
        
        function toggleWishlistDetail(productId) {
            toggleWishlist(productId);
        }
    </script>
</body>
</html>
