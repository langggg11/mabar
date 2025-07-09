<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $category = $_GET['category'] ?? null;
    $search = $_GET['search'] ?? '';
    $sort = $_GET['sort'] ?? 'popularity';
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
    
    $filters = [];
    if (isset($_GET['subcategory'])) {
        $filters['subcategory'] = explode(',', $_GET['subcategory']);
    }
    if (isset($_GET['target_fish'])) {
        $filters['target_fish'] = explode(',', $_GET['target_fish']);
    }
    if (isset($_GET['price_range'])) {
        $range = explode('-', $_GET['price_range']);
        if (count($range) == 2) {
            $filters['price_min'] = (int)$range[0];
            $filters['price_max'] = (int)$range[1];
        }
    }
    
    $products = getProducts($category, $search, $sort, $limit, $filters);
    
    foreach ($products as &$product) {
        $rating = getProductRating($product['id']);
        $product['rating'] = $rating['rating'];
        $product['review_count'] = $rating['review_count'];
        $product['is_in_wishlist'] = isInWishlist($product['id']);
    }
    
    echo json_encode([
        'success' => true,
        'products' => $products,
        'count' => count($products)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
