<?php
require_once 'includes/functions.php';

// Pastikan hanya admin yang bisa mengakses
if (!isLoggedIn() || !isAdmin()) {
    header('Location: index.php');
    exit;
}

$category_slug = $_GET['category'] ?? '';
if (!$category_slug) {
    // Redirect ke admin panel jika tidak ada kategori dipilih
    header('Location: admin.php');
    exit;
}

$category_info = getCategoryBySlug($category_slug);
$page_title = "Kelola Produk: " . ($category_info['name'] ?? 'Kategori');
$products = getProducts($category_slug); // Ambil semua produk dari kategori ini

include 'includes/header.php';
?>

<section class="products-section">
    <div class="container">
        <div class="search-page-header">
            <h1>Kelola Produk - <?php echo htmlspecialchars($category_info['name'] ?? ''); ?></h1>
            <p>Pilih produk di bawah ini untuk diedit atau dihapus.</p>
            <a href="add-product.php?category=<?php echo $category_slug; ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Produk Baru
            </a>
        </div>

        <div class="products-grid" id="manageProductsGrid">
            </div>
    </div>
</section>

<script id="page-data" type="application/json">
    <?php echo json_encode(['products' => $products]); ?>
</script>

<?php include 'includes/footer.php'; ?>