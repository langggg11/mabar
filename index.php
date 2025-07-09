<?php 
$page_title = "Mabar - Platform Rekomendasi Alat Pancing Terbaik";
$page_description = "Platform rekomendasi alat pancing terpercaya di Indonesia dengan review mendalam dari ahli dan komunitas pemancing.";
require_once 'includes/functions.php';

// Get category statistics from database - only product count and rating
$categories_stats = [];

// Joran stats
$stmt = $pdo->prepare("
    SELECT 
        COUNT(DISTINCT p.id) as product_count,
        AVG(r.rating) as avg_rating
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    LEFT JOIN reviews r ON p.id = r.product_id
    WHERE c.slug = 'joran'
");
$stmt->execute();
$joran_data = $stmt->fetch();
$joran_count = $joran_data['product_count'];
$joran_rating = round($joran_data['avg_rating'] ?? 4.8, 1); // Default 4.8 jika belum ada review

// Reel stats
$stmt = $pdo->prepare("
    SELECT 
        COUNT(DISTINCT p.id) as product_count,
        AVG(r.rating) as avg_rating
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    LEFT JOIN reviews r ON p.id = r.product_id
    WHERE c.slug = 'reel'
");
$stmt->execute();
$reel_data = $stmt->fetch();
$reel_count = $reel_data['product_count'];
$reel_rating = round($reel_data['avg_rating'] ?? 4.9, 1); // Default 4.9 jika belum ada review

// Umpan stats
$stmt = $pdo->prepare("
    SELECT 
        COUNT(DISTINCT p.id) as product_count,
        AVG(r.rating) as avg_rating
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    LEFT JOIN reviews r ON p.id = r.product_id
    WHERE c.slug = 'umpan'
");
$stmt->execute();
$umpan_data = $stmt->fetch();
$umpan_count = $umpan_data['product_count'];
$umpan_rating = round($umpan_data['avg_rating'] ?? 4.7, 1); // Default 4.7 jika belum ada review

// Aksesoris stats
$stmt = $pdo->prepare("
    SELECT 
        COUNT(DISTINCT p.id) as product_count,
        AVG(r.rating) as avg_rating
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    LEFT JOIN reviews r ON p.id = r.product_id
    WHERE c.slug = 'aksesoris'
");
$stmt->execute();
$aksesoris_data = $stmt->fetch();
$aksesoris_count = $aksesoris_data['product_count'];
$aksesoris_rating = round($aksesoris_data['avg_rating'] ?? 4.6, 1); // Default 4.6 jika belum ada review

// Get total stats for hero
$stmt = $pdo->query("SELECT COUNT(*) as total_products FROM products");
$total_products = $stmt->fetch()['total_products'];

$stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
$total_users = $stmt->fetch()['total_users'];

// Calculate overall average rating from all reviews
$stmt = $pdo->query("SELECT AVG(rating) as overall_rating FROM reviews");
$overall_rating_data = $stmt->fetch();
$avg_rating = round($overall_rating_data['overall_rating'] ?? 4.9, 1);

// Create categories array for easy access
$categories_stats = [
    'joran' => [
        'count' => $joran_count,
        'rating' => $joran_rating
    ],
    'reel' => [
        'count' => $reel_count,
        'rating' => $reel_rating
    ],
    'umpan' => [
        'count' => $umpan_count,
        'rating' => $umpan_rating
    ],
    'aksesoris' => [
        'count' => $aksesoris_count,
        'rating' => $aksesoris_rating
    ]
];
?>

<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero">
  <div class="container">
      <div class="hero-content">
          <div class="hero-text">
              <h1 class="hero-title">
                  Temukan Alat Pancing Terbaik untuk Petualangan Memancing Anda
              </h1>
              <p class="hero-description">
                  Platform rekomendasi alat pancing terpercaya dengan review dari ahli dan komunitas pemancing aktif Indonesia. Dapatkan rekomendasi lengkap untuk memilih joran, reel, umpan, dan aksesoris yang tepat sesuai kebutuhan Anda.
              </p>
              <div class="hero-cta-group">
                  <a href="#recommendations" class="hero-cta" onclick="scrollToRecommendations()">
                      <i class="fas fa-compass"></i>
                      Jelajahi Rekomendasi
                  </a>
                  <a href="#categories" class="hero-cta secondary" onclick="scrollToCategories()">
                      <i class="fas fa-th-large"></i>
                      Lihat Kategori
                  </a>
              </div>
              <div class="hero-stats">
                  <div class="stat-item">
                      <span class="stat-number"><?php echo $total_products; ?>+</span>
                      <span class="stat-label">Produk Terpilih</span>
                  </div>
                  <div class="stat-item">
                      <span class="stat-number"><?php echo number_format($total_users); ?>+</span>
                      <span class="stat-label">Pemancing</span>
                  </div>
                  <div class="stat-item">
                      <span class="stat-number"><?php echo $avg_rating; ?></span>
                      <span class="stat-label">Rating</span>
                  </div>
              </div>
          </div>
          
          <div class="hero-visual">
              <div class="hero-image-container">
                  <img src="assets/images/hero-3D.jpg" alt="Fishing Equipment" class="hero-image">
              </div>
              <div class="hero-floating-cards">
                  <div class="floating-card">
                      <div class="card-icon">
                          <i class="fas fa-shield-alt"></i>
                      </div>
                      <div class="card-title">Kualitas Terjamin</div>
                      <div class="card-subtitle">Review dari ahli</div>
                  </div>
                  <div class="floating-card">
                      <div class="card-icon">
                          <i class="fas fa-shipping-fast"></i>
                      </div>
                      <div class="card-title">Toko Terpercaya</div>
                      <div class="card-subtitle">Produk Dijamin Ori</div>
                  </div>
                  <div class="floating-card">
                      <div class="card-icon">
                          <i class="fas fa-users"></i>
                      </div>
                      <div class="card-title">Komunitas Aktif</div>
                      <div class="card-subtitle"><?php echo number_format($total_users); ?>+ pemancing</div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</section>

<!-- Categories Section -->
<section class="categories" id="categories">
  <div class="container">
      <div class="section-header">
          <div class="section-badge">
              <i class="fas fa-th-large"></i>
              Kategori Produk
          </div>
          <h2 class="section-title">Jelajahi Kategori Alat Pancing</h2>
          <p class="section-subtitle">
              Temukan berbagai kategori alat pancing berkualitas tinggi yang telah dipilih khusus untuk memenuhi kebutuhan memancing Anda.
          </p>
      </div>
      
      <div class="categories-grid">
          <div class="category-card" onclick="location.href='joran.php'">
              <div class="category-logo">
                  <i class="fas fa-long-arrow-alt-right"></i>
              </div>
              <h3 class="category-title">Joran Pancing</h3>
              <p class="category-description">
                  Koleksi lengkap joran berkualitas tinggi untuk berbagai teknik memancing dari air tawar hingga laut dalam.
              </p>
              <div class="category-stats">
                  <div class="stat">
                      <span class="stat-number"><?php echo $joran_count; ?></span>
                      <span class="stat-label">Produk</span>
                  </div>
                  <div class="stat">
                      <span class="stat-number"><?php echo $joran_rating; ?></span>
                      <span class="stat-label">Rating</span>
                  </div>
              </div>
          </div>
          
          <div class="category-card" onclick="location.href='reel.php'">
              <div class="category-logo">
                  <i class="fas fa-circle-notch"></i>
              </div>
              <h3 class="category-title">Reel Pancing</h3>
              <p class="category-description">
                  Reel berkualitas tinggi dengan teknologi dan tampilan modern untuk mendukung teknik memancing dalam segala kondisi.
              </p>
              <div class="category-stats">
                  <div class="stat">
                      <span class="stat-number"><?php echo $reel_count; ?></span>
                      <span class="stat-label">Produk</span>
                  </div>
                  <div class="stat">
                      <span class="stat-number"><?php echo $reel_rating; ?></span>
                      <span class="stat-label">Rating</span>
                  </div>
              </div>
          </div>
          
          <div class="category-card" onclick="location.href='umpan.php'">
              <div class="category-logo">
                  <i class="fas fa-fish"></i>
              </div>
              <h3 class="category-title">Umpan & Kail</h3>
              <p class="category-description">
                  Koleksi umpan buatan dan kail yang dirancang efektif untuk menarik perhatian berbagai jenis ikan dalam segala kondisi dan spot memancing. 
              </p>
              <div class="category-stats">
                  <div class="stat">
                      <span class="stat-number"><?php echo $umpan_count; ?></span>
                      <span class="stat-label">Produk</span>
                  </div>
                  <div class="stat">
                      <span class="stat-number"><?php echo $umpan_rating; ?></span>
                      <span class="stat-label">Rating</span>
                  </div>
              </div>
          </div>
          
          <div class="category-card" onclick="location.href='aksesoris.php'">
              <div class="category-logo">
                  <i class="fas fa-briefcase"></i>
              </div>
              <h3 class="category-title">Aksesoris</h3>
              <p class="category-description">
                  Perlengkapan dan aksesori pendukung untuk meningkatkan kenyamanan serta produktivitas kegiatan memancing Anda.
              </p>
              <div class="category-stats">
                  <div class="stat">
                      <span class="stat-number"><?php echo $aksesoris_count; ?></span>
                      <span class="stat-label">Produk</span>
                  </div>
                  <div class="stat">
                      <span class="stat-number"><?php echo $aksesoris_rating; ?></span>
                      <span class="stat-label">Rating</span>
                  </div>
              </div>
          </div>
      </div>
  </div>
</section>

<!-- Featured Products Section -->
<section class="featured-products" id="recommendations">
  <div class="container">
      <div class="section-header">
          <div class="section-badge">
              <i class="fas fa-star"></i>
              Produk Unggulan
          </div>
          <h2 class="section-title">Rekomendasi Produk Terbaik</h2>
          <p class="section-subtitle">
              Produk pilihan dengan rating tertinggi dan paling direkomendasikan oleh komunitas pemancing Indonesia.
          </p>
      </div>
      
      <div class="products-grid" id="featuredProducts">
          
      </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

<script src="assets/js/script.js"></script>
