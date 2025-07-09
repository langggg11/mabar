<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
        exit;
    }
    
    $productId = $_POST['product_id'] ?? 0;
    if (!$productId) {
        echo json_encode(['success' => false, 'message' => 'Product ID tidak valid']);
        exit;
    }
    
    try {
        $result = toggleWishlist($productId);
        $message = $result === 'added' ? 'Ditambahkan ke wishlist' : 'Dihapus dari wishlist';
        
        echo json_encode(['success' => true, 'action' => $result, 'message' => $message]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
    exit;
}

// GET request - return user's wishlist
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isLoggedIn()) {
    try {
        $wishlist = getUserWishlist($_SESSION['user_id']);
        echo json_encode(['success' => true, 'wishlist' => $wishlist]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Method not allowed']);
?>
