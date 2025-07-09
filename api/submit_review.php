<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Anda harus masuk untuk memberikan ulasan.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode permintaan tidak valid.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'] ?? null;
$rating = $_POST['rating'] ?? null;
$review_text = trim($_POST['review_text'] ?? '');

// Validasi input
if (empty($product_id)) {
    echo json_encode(['success' => false, 'message' => 'ID produk diperlukan.']);
    exit;
}

if (empty($rating) || $rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Peringkat harus antara 1 dan 5.']);
    exit;
}

if (strlen($review_text) > 1000) {
    echo json_encode(['success' => false, 'message' => 'Ulasan terlalu panjang (maksimal 1000 karakter).']);
    exit;
}

try {
    global $pdo;

    $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM reviews WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("UPDATE reviews SET rating = ?, review_text = ?, created_at = NOW() WHERE user_id = ? AND product_id = ?");
        $result = $stmt->execute([$rating, $review_text, $user_id, $product_id]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Ulasan berhasil diperbarui.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui ulasan.']);
        }
    } else {
        $stmt = $pdo->prepare("INSERT INTO reviews (user_id, product_id, rating, review_text, created_at) VALUES (?, ?, ?, ?, NOW())");
        $result = $stmt->execute([$user_id, $product_id, $rating, $review_text]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Ulasan berhasil dikirim.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengirim ulasan.']);
        }
    }

} catch (PDOException $e) {
    error_log("Database error in submit_review.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan database.']);
} catch (Exception $e) {
    error_log("General error in submit_review.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan ulasan.']);
}
?>
