<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

$current_user = getCurrentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$current_user) {
        echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu']);
        exit;
    }

    $product_id = $_POST['product_id'] ?? '';
    $rating = $_POST['rating'] ?? '';
    $comment = $_POST['comment'] ?? '';

    if (empty($product_id) || empty($rating) || empty($comment)) {
        echo json_encode(['success' => false, 'message' => 'Semua field harus diisi']);
        exit;
    }

    if ($rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Rating harus antara 1-5']);
        exit;
    }

    try {
        
        $stmt = $pdo->prepare("SELECT id FROM reviews WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$current_user['id'], $product_id]);
        
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Anda sudah memberikan review untuk produk ini']);
            exit;
        }

        $stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$current_user['id'], $product_id]);
        
        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Produk harus ada di daftar yang disukai untuk memberikan review']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO reviews (user_id, product_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$current_user['id'], $product_id, $rating, $comment]);

        echo json_encode(['success' => true, 'message' => 'Review berhasil ditambahkan']);
    } catch (Exception $e) {
        error_log("Review error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem']);
    }
} else {
    // Get reviews
    $product_id = $_GET['product_id'] ?? '';
    
    if (empty($product_id)) {
        echo json_encode(['success' => false, 'message' => 'Product ID required']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            SELECT r.*, u.name as user_name 
            FROM reviews r 
            JOIN users u ON r.user_id = u.id 
            WHERE r.product_id = ? 
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$product_id]);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'reviews' => $reviews]);
    } catch (Exception $e) {
        error_log("Get reviews error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem']);
    }
}
?>
