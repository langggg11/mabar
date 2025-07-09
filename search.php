<?php
require_once 'includes/functions.php';

$query = $_GET['q'] ?? '';

// Hanya proses jika ada query pencarian
if (empty(trim($query))) {
    header('Location: index.php');
    exit;
}

$page_title = 'Hasil Pencarian untuk "' . htmlspecialchars($query) . '"';
$products = getProducts(null, $query); // Menggunakan getProducts agar konsisten

include 'includes/header.php';
?>

<section class="wishlist-section">
    <div class="container">

        <div class="wishlist-header">
            <h1>Hasil Pencarian</h1>
            <p>Menampilkan hasil untuk: "<?php echo htmlspecialchars($query); ?>"</p>
        </div>

        <?php if (empty($products)): ?>
            <div class="empty-wishlist">
                <h3>Produk Tidak Ditemukan</h3>
                <p>Coba gunakan kata kunci lain yang lebih umum.</p>
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-home"></i> Kembali ke Beranda
                </a>
            </div>
        <?php else: ?>
            <div class="products-grid" id="searchResultsGrid">
                </div>
        <?php endif; ?>

    </div>
</section>

<script id="page-data" type="application/json">
    <?php echo json_encode(['products' => $products]); ?>
</script>

<?php include 'includes/footer.php'; ?>
