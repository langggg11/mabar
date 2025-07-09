<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $search = $_GET['q'] ?? '';
    $category = $_GET['category'] ?? null;
    $sort = $_GET['sort'] ?? 'popularity';
    $featured = $_GET['featured'] ?? null;
    $limit = $_GET['limit'] ?? null;
    
    // Build filters array
    $filters = [];
    
    // Subcategory filter
    if (isset($_GET['subcategory'])) {
        $filters['subcategory'] = is_array($_GET['subcategory']) ? $_GET['subcategory'] : explode(',', $_GET['subcategory']);
    }
    
    // Price range filter
    if (isset($_GET['price_range']) && $_GET['price_range']) {
        $priceRange = explode('-', $_GET['price_range']);
        if (count($priceRange) == 2) {
            $filters['price_min'] = intval($priceRange[0]);
            $filters['price_max'] = intval($priceRange[1]);
        }
    }
    
    // Target fish filter
    if (isset($_GET['target_fish'])) {
        $filters['target_fish'] = is_array($_GET['target_fish']) ? $_GET['target_fish'] : explode(',', $_GET['target_fish']);
    }

    // Jika request untuk featured products
    if ($featured) {
        $products = getFeaturedProducts($limit ? intval($limit) : 6);
        echo json_encode($products);
        exit;
    }

    // Query normal untuk produk
    $products = getProducts($category, $search, $sort, $limit ? intval($limit) : null, $filters);
    
    // Add rating information to each product
    foreach ($products as &$product) {
        $ratingInfo = getProductRating($product['id']);
        $product['rating'] = $ratingInfo['rating'];
        $product['review_count'] = $ratingInfo['review_count'];
        
        // Check if in wishlist for logged in users
        if (isLoggedIn()) {
            $product['in_wishlist'] = isInWishlist($product['id']);
        } else {
            $product['in_wishlist'] = false;
        }
    }
    
    echo json_encode($products);
    
} catch (Exception $e) {
    error_log("Search error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error', 'message' => $e->getMessage()]);
}
?>
