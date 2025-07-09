<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
    
    $products = getFeaturedProducts($limit);
    
    foreach ($products as &$product) {
        $rating = getProductRating($product['id']);
        $product['rating'] = $rating['rating'];
        $product['review_count'] = $rating['review_count'];
        $product['is_in_wishlist'] = isInWishlist($product['id']);
    }
    
    echo json_encode([
        'success' => true,
        'products' => $products
    ]);
    
} catch (Exception $e) {
    error_log("Featured products API error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'products' => []
    ]);
}
?>
