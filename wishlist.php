<?php
require_once 'includes/functions.php';

// Pastikan pengguna sudah login
if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$page_title = "Produk yang Disukai - Mabar";
$page_description = "Lihat dan kelola semua produk alat pancing yang Anda sukai.";
$wishlist_products = getWishlistProducts(); 

include 'includes/header.php';
?>

<section class="wishlist-section">
    <div class="container">
        <div class="wishlist-header">
            <h1>Produk yang Disukai</h1>
            <p>Daftar produk favorit yang telah Anda simpan.</p>
        </div>

        <?php if (empty($wishlist_products)): ?>
            <div class="empty-wishlist">
                <h3>Wishlist Anda Kosong</h3>
                <p>Jelajahi produk dan tekan ikon hati untuk menyimpannya di sini.</p>
                <a href="index.php#categories" class="btn btn-primary">
                    <i class="fas fa-search"></i> Jelajahi Produk
                </a>
            </div>
        <?php else: ?>
            <div class="products-grid" id="wishlistGrid">
                </div>
        <?php endif; ?>
    </div>
</section>

<script id="wishlist-data" type="application/json">
    <?php echo json_encode($wishlist_products); ?>
</script>

<?php include 'includes/footer.php'; ?>
