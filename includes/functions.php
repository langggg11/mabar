<?php
require_once 'config.php';

// User authentication functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function isAdmin() {
    global $pdo;
    if (!isLoggedIn()) return false;
    
    $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    return $user && $user['is_admin'] == 1;
}

function logout() {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Product functions
function getProducts($category = null, $search = '', $sort = 'popularity', $limit = null, $filters = []) {
    global $pdo;
    
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
    $params = [];
    
    // Category filter
    if ($category) {
        $sql .= " AND c.slug = ?";
        $params[] = $category;
    }
    
    // Search filter
    if ($search) {
        $sql .= " AND (p.name LIKE ?)";
        $params[] = "%$search%";
    }
    
    // Subcategory filter
    if (isset($filters['subcategory']) && !empty($filters['subcategory'])) {
        $placeholders = str_repeat('?,', count($filters['subcategory']) - 1) . '?';
        $sql .= " AND p.subcategory IN ($placeholders)";
        $params = array_merge($params, $filters['subcategory']);
    }
    
    // Price range filter
    if (isset($filters['price_min']) && isset($filters['price_max'])) {
        $sql .= " AND p.price BETWEEN ? AND ?";
        $params[] = $filters['price_min'];
        $params[] = $filters['price_max'];
    }
    
    // Target fish filter
    if (isset($filters['target_fish']) && !empty($filters['target_fish'])) {
        $conditions = [];
        foreach ($filters['target_fish'] as $fish) {
            $conditions[] = "p.target_fish LIKE ?";
            $params[] = "%$fish%";
        }
        $sql .= " AND (" . implode(' OR ', $conditions) . ")";
    }
    
    // Sorting
    switch ($sort) {
        case 'price_low':
            $sql .= " ORDER BY p.price ASC";
            break;
        case 'price_high':
            $sql .= " ORDER BY p.price DESC";
            break;
        case 'newest':
            $sql .= " ORDER BY p.created_at DESC";
            break;
        case 'name':
            $sql .= " ORDER BY p.name ASC";
            break;
        default:
            $sql .= " ORDER BY p.popularity_score DESC";
    }
    
    // Limit
    if ($limit) {
        $sql .= " LIMIT ?";
        $params[] = $limit;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getFeaturedProducts($limit = 6) {
    global $pdo;
    
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            ORDER BY p.popularity_score DESC, p.is_promo DESC 
            LIMIT ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

// GANTI DENGAN VERSI BARU INI
function getProductBySlug($slug) {
    global $pdo;

    // Query ini sudah diubah untuk mengambil 'category_slug'
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name, c.slug AS category_slug 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.slug = ?
    ");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

function getProductRating($productId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT AVG(rating) as rating, COUNT(*) as review_count 
                          FROM reviews WHERE product_id = ?");
    $stmt->execute([$productId]);
    $result = $stmt->fetch();
    
    return [
        'rating' => $result['rating'] ? round($result['rating'], 1) : 0,
        'review_count' => $result['review_count']
    ];
}

function getReviewsByProductId($productId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT r.*, u.name as user_name FROM reviews r 
                          JOIN users u ON r.user_id = u.id 
                          WHERE r.product_id = ? ORDER BY r.created_at DESC");
    $stmt->execute([$productId]);
    return $stmt->fetchAll();
}
// Wishlist functions
function toggleWishlist($productId) {
    if (!isLoggedIn()) {
        throw new Exception('User not logged in');
    }
    
    global $pdo;
    $userId = $_SESSION['user_id'];
    
    // Check if already in wishlist
    $stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);
    
    if ($stmt->fetch()) {
        // Remove from wishlist
        $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        return 'removed';
    } else {
        // Add to wishlist
        $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        $stmt->execute([$userId, $productId]);
        return 'added';
    }
}

function getUserWishlist($userId = null) {
    if (!$userId && !isLoggedIn()) {
        return [];
    }
    
    global $pdo;
    $userId = $userId ?: $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function isInWishlist($productId) {
    if (!isLoggedIn()) {
        return false;
    }
    
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$_SESSION['user_id'], $productId]);
    return $stmt->fetch() !== false;
}

function getWishlistProducts($userId = null) {
    if (!$userId && !isLoggedIn()) {
        return [];
    }
    
    global $pdo;
    $userId = $userId ?: $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, w.created_at as added_at 
                          FROM wishlist w 
                          JOIN products p ON w.product_id = p.id 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE w.user_id = ? 
                          ORDER BY w.created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

// Utility functions
function formatPrice($price) {
    return 'Rp ' . number_format($price, 0, ',', '.');
}

function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Categories functions
function getCategories() {
    global $pdo;
    
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll();
}

// Search functions
function searchProducts($query, $limit = 20) {
    global $pdo;
    
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?
            ORDER BY p.popularity_score DESC 
            LIMIT ?";
    
    $searchTerm = "%$query%";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $limit]);
    return $stmt->fetchAll();
}
?>
