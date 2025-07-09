<?php
require_once 'includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
  header('Location: index.php');
  exit;
}

$page_title = "Admin Dashboard - Mabar";
$current_user = getCurrentUser();

$stats = [];
try {
  $stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
  $stats['users'] = $stmt->fetch()['total_users'];
  
  $stmt = $pdo->query("SELECT COUNT(*) as total_products FROM products");
  $stats['products'] = $stmt->fetch()['total_products'];
  
  $stmt = $pdo->query("SELECT COUNT(*) as total_categories FROM categories");
  $stats['categories'] = $stmt->fetch()['total_categories'];
  
  $stmt = $pdo->query("SELECT COUNT(*) as total_wishlist FROM wishlist");
  $stats['wishlist'] = $stmt->fetch()['total_wishlist'];
  
  $stmt = $pdo->query("SELECT COUNT(*) as total_reviews FROM reviews");
  $stats['reviews'] = $stmt->fetch()['total_reviews'];
} catch (Exception $e) {
  $stats = ['users' => 0, 'products' => 0, 'categories' => 0, 'wishlist' => 0, 'reviews' => 0];
}

// Get recent users
$recent_users = [];
try {
  $stmt = $pdo->query("SELECT id, name, email, created_at FROM users ORDER BY created_at DESC LIMIT 10");
  $recent_users = $stmt->fetchAll();
} catch (Exception $e) {
  $recent_users = [];
}

// Get recent reviews
$recent_reviews = [];
try {
  $stmt = $pdo->query("
    SELECT r.*, u.name as user_name, p.name as product_name 
    FROM reviews r 
    JOIN users u ON r.user_id = u.id 
    JOIN products p ON r.product_id = p.id 
    ORDER BY r.created_at DESC 
    LIMIT 10
  ");
  $recent_reviews = $stmt->fetchAll();
} catch (Exception $e) {
  $recent_reviews = [];
}

// Get top products by reviews
$top_products = [];
try {
  $stmt = $pdo->query("
    SELECT p.name, p.slug, COUNT(r.id) as review_count, AVG(r.rating) as avg_rating
    FROM products p 
    LEFT JOIN reviews r ON p.id = r.product_id 
    GROUP BY p.id 
    ORDER BY review_count DESC, avg_rating DESC 
    LIMIT 10
  ");
  $top_products = $stmt->fetchAll();
} catch (Exception $e) {
  $top_products = [];
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
  
  <!-- Admin Dashboard -->
  <section class="admin-dashboard">
      <div class="container">
          <div class="admin-header">
              <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard - Mabar</h1>
              <p>Kelola dan pantau aktivitas platform Mabar</p>
          </div>
          
          <!-- Statistics Cards -->
          <div class="stats-grid">
              <div class="stat-card users">
                  <div class="stat-icon">
                      <i class="fas fa-users"></i>
                  </div>
                  <div class="stat-info">
                      <h3><?php echo number_format($stats['users']); ?></h3>
                      <p>Total Pengguna</p>
                  </div>
              </div>
              
              <div class="stat-card products">
                  <div class="stat-icon">
                      <i class="fas fa-box"></i>
                  </div>
                  <div class="stat-info">
                      <h3><?php echo number_format($stats['products']); ?></h3>
                      <p>Total Produk</p>
                  </div>
              </div>
              
              <div class="stat-card reviews">
                  <div class="stat-icon">
                      <i class="fas fa-star"></i>
                  </div>
                  <div class="stat-info">
                      <h3><?php echo number_format($stats['reviews']); ?></h3>
                      <p>Total Ulasan</p>
                  </div>
              </div>
              
              <div class="stat-card wishlist">
                  <div class="stat-icon">
                      <i class="fas fa-heart"></i>
                  </div>
                  <div class="stat-info">
                      <h3><?php echo number_format($stats['wishlist']); ?></h3>
                      <p>Total Wishlist</p>
                  </div>
              </div>
          </div>
          
          <!-- Dashboard Content -->
          <div class="dashboard-content">
              <!-- Recent Users -->
              <div class="admin-section">
                  <div class="section-header">
                      <h2><i class="fas fa-user-plus"></i> Pengguna Terbaru</h2>
                  </div>
                  <div class="users-table">
                      <table>
                          <thead>
                              <tr>
                                  <th>ID</th>
                                  <th>Nama</th>
                                  <th>Email</th>
                                  <th>Bergabung</th>
                                  <th>Status</th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php if (empty($recent_users)): ?>
                                  <tr>
                                      <td colspan="5" class="no-data">Belum ada pengguna terdaftar</td>
                                  </tr>
                              <?php else: ?>
                                  <?php foreach ($recent_users as $user): ?>
                                      <tr>
                                          <td><?php echo $user['id']; ?></td>
                                          <td><?php echo htmlspecialchars($user['name']); ?></td>
                                          <td><?php echo htmlspecialchars($user['email']); ?></td>
                                          <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                                          <td><span class="status-badge active">Aktif</span></td>
                                      </tr>
                                  <?php endforeach; ?>
                              <?php endif; ?>
                          </tbody>
                      </table>
                  </div>
              </div>
              
              <!-- Recent Reviews -->
              <div class="admin-section">
                  <div class="section-header">
                      <h2><i class="fas fa-comments"></i> Ulasan Terbaru</h2>
                  </div>
                  <div class="reviews-list">
                      <?php if (empty($recent_reviews)): ?>
                          <div class="no-data">Belum ada ulasan</div>
                      <?php else: ?>
                          <?php foreach ($recent_reviews as $review): ?>
                              <div class="review-item">
                                  <div class="review-header">
                                      <div class="reviewer-info">
                                          <strong><?php echo htmlspecialchars($review['user_name']); ?></strong> : <?php echo htmlspecialchars($review['product_name']); ?>
                                      </div>
                                      <div class="review-rating">
                                          <?php for ($i = 1; $i <= 5; $i++): ?>
                                              <i class="<?php echo $i <= $review['rating'] ? 'fas' : 'far'; ?> fa-star"></i>
                                          <?php endfor; ?>
                                      </div>
                                  </div>
                                  <?php if ($review['review_text']): ?>
                                      <div class="review-text">
                                          <?php echo htmlspecialchars(substr($review['review_text'], 0, 150)); ?>
                                          <?php echo strlen($review['review_text']) > 150 ? '...' : ''; ?>
                                      </div>
                                  <?php endif; ?>
                                  <div class="review-date">
                                      <?php echo date('d M Y, H:i', strtotime($review['created_at'])); ?>
                                  </div>
                              </div>
                          <?php endforeach; ?>
                      <?php endif; ?>
                  </div>
              </div>
              
              <!-- Top Products -->
              <div class="admin-section">
                  <div class="section-header">
                      <h2><i class="fas fa-trophy"></i> Produk Terpopuler</h2>
                  </div>
                  <div class="products-table">
                      <table>
                          <thead>
                              <tr>
                                  <th>Produk</th>
                                  <th>Jumlah Ulasan</th>
                                  <th>Rating Rata-rata</th>
                                  <th>Aksi</th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php if (empty($top_products)): ?>
                                  <tr>
                                      <td colspan="4" class="no-data">Belum ada data produk</td>
                                  </tr>
                              <?php else: ?>
                                  <?php foreach ($top_products as $product): ?>
                                      <tr>
                                          <td><?php echo htmlspecialchars($product['name']); ?></td>
                                          <td><?php echo $product['review_count']; ?> ulasan</td>
                                          <td>
                                              <?php if ($product['avg_rating']): ?>
                                                  <div class="rating-display">
                                                      <?php 
                                                      $rating = round($product['avg_rating'], 1);
                                                      for ($i = 1; $i <= 5; $i++): 
                                                      ?>
                                                          <i class="<?php echo $i <= $rating ? 'fas' : 'far'; ?> fa-star"></i>
                                                      <?php endfor; ?>
                                                      <span>(<?php echo $rating; ?>)</span>
                                                  </div>
                                              <?php else: ?>
                                                  <span class="no-rating">Belum ada rating</span>
                                              <?php endif; ?>
                                          </td>
                                          <td>
                                              <a href="product-detail.php?slug=<?php echo $product['slug']; ?>" 
                                                 class="btn btn-sm btn-primary" target="_blank">
                                                  <i class="fas fa-eye"></i> Lihat
                                              </a>
                                          </td>
                                      </tr>
                                  <?php endforeach; ?>
                              <?php endif; ?>
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div>
  </section>
  
  <?php include 'includes/footer.php'; ?>
  
  <script src="assets/js/script.js"></script>
  
  <style>
      .admin-dashboard {
          padding: var(--space-16) 0;
          background: var(--bg-primary);
          min-height: 80vh;
      }
      
      .admin-header {
          text-align: center;
          margin-bottom: var(--space-12);
      }
      
      .admin-header h1 {
          color: var(--text-primary);
          margin-bottom: var(--space-4);
          display: flex;
          align-items: center;
          justify-content: center;
          gap: var(--space-3);
      }
      
      .admin-header p {
          color: var(--text-secondary);
          font-size: 1.1rem;
      }
      
      .stats-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
          gap: var(--space-6);
          margin-bottom: var(--space-16);
      }
      
      .stat-card {
          background: var(--bg-card);
          padding: var(--space-8);
          border-radius: var(--radius-2xl);
          box-shadow: var(--shadow-md);
          display: flex;
          align-items: center;
          gap: var(--space-6);
          transition: all var(--transition-fast);
          border: 2px solid transparent;
      }
      
      .stat-card:hover {
          transform: translateY(-4px);
          box-shadow: var(--shadow-lg);
      }
      
      .stat-card.users {
          border-color: #3b82f6;
      }
      
      .stat-card.products {
          border-color: #10b981;
      }
      
      .stat-card.reviews {
          border-color: #f59e0b;
      }
      
      .stat-card.wishlist {
          border-color: #ef4444;
      }
      
      .stat-icon {
          width: 5rem;
          height: 5rem;
          border-radius: var(--radius-xl);
          display: flex;
          align-items: center;
          justify-content: center;
          color: white;
          font-size: 1.75rem;
          flex-shrink: 0;
      }
      
      .stat-card.users .stat-icon {
          background: linear-gradient(135deg, #3b82f6, #1d4ed8);
      }
      
      .stat-card.products .stat-icon {
          background: linear-gradient(135deg, #10b981, #047857);
      }
      
      .stat-card.reviews .stat-icon {
          background: linear-gradient(135deg, #f59e0b, #d97706);
      }
      
      .stat-card.wishlist .stat-icon {
          background: linear-gradient(135deg, #ef4444, #dc2626);
      }
      
      .stat-info h3 {
          font-size: 2.5rem;
          font-weight: 800;
          color: var(--text-primary);
          margin-bottom: var(--space-2);
          line-height: 1;
      }
      
      .stat-info p {
          color: var(--text-secondary);
          font-weight: 600;
          font-size: 1.1rem;
      }
      
      .dashboard-content {
          display: grid;
          gap: var(--space-8);
      }
      
      .admin-section {
          background: var(--bg-card);
          padding: var(--space-8);
          border-radius: var(--radius-2xl);
          box-shadow: var(--shadow-md);
      }
      
      .section-header {
          margin-bottom: var(--space-6);
          padding-bottom: var(--space-4);
          border-bottom: 2px solid var(--gray-200);
      }
      
      .section-header h2 {
          color: var(--text-primary);
          display: flex;
          align-items: center;
          gap: var(--space-3);
          font-size: 1.5rem;
      }
      
      .users-table, .products-table {
          overflow-x: auto;
      }
      
      table {
          width: 100%;
          border-collapse: collapse;
      }
      
      th, td {
          padding: var(--space-4);
          text-align: left;
          border-bottom: 1px solid var(--gray-200);
      }
      
      th {
          background: var(--gray-100);
          font-weight: 600;
          color: var(--text-primary);
          font-size: 0.9rem;
          text-transform: uppercase;
          letter-spacing: 0.5px;
      }
      
      td {
          color: var(--text-secondary);
      }
      
      .no-data {
          text-align: center;
          color: var(--text-secondary);
          font-style: italic;
          padding: var(--space-8);
      }
      
      .status-badge {
          padding: var(--space-1) var(--space-3);
          border-radius: var(--radius-full);
          font-size: 0.8rem;
          font-weight: 600;
          text-transform: uppercase;
      }
      
      .status-badge.active {
          background: #dcfce7;
          color: #166534;
      }
      
      .reviews-list {
          display: flex;
          flex-direction: column;
          gap: var(--space-4);
      }
      
      .review-item {
          padding: var(--space-6);
          background: var(--gray-50);
          border-radius: var(--radius-xl);
          border-left: 4px solid var(--primary-500);
      }
      
      .review-header {
          display: flex;
          justify-content: space-between;
          align-items: flex-start;
          margin-bottom: var(--space-3);
      }
      
      .reviewer-info {
          flex: 1;
      }
      
      .reviewer-info strong {
          color: var(--text-primary);
          font-weight: 600;
      }
      
      .review-rating {
          color: #f59e0b;
          flex-shrink: 0;
      }
      
      .review-text {
          color: var(--text-secondary);
          line-height: 1.6;
          margin-bottom: var(--space-3);
      }
      
      .review-date {
          color: var(--text-tertiary);
          font-size: 0.85rem;
      }
      
      .rating-display {
          display: flex;
          align-items: center;
          gap: var(--space-2);
      }
      
      .rating-display .fa-star {
          color: #f59e0b;
      }
      
      .no-rating {
          color: var(--text-tertiary);
          font-style: italic;
      }
      
      .btn-sm {
          padding: var(--space-2) var(--space-4);
          font-size: 0.875rem;
          text-decoration: none;
          display: inline-flex;
          align-items: center;
          gap: var(--space-2);
      }
      
      @media (max-width: 768px) {
          .stats-grid {
              grid-template-columns: 1fr;
          }
          
          .stat-card {
              padding: var(--space-6);
          }
          
          .stat-icon {
              width: 4rem;
              height: 4rem;
              font-size: 1.5rem;
          }
          
          .stat-info h3 {
              font-size: 2rem;
          }
          
          .review-header {
              flex-direction: column;
              gap: var(--space-2);
          }
      }
  </style>
</body>
</html>