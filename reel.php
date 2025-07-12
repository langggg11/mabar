<?php 
$page_title = "Reel Pancing - Mabar";
$page_description = "Reel berkualitas tinggi dengan performa maksimal untuk semua teknik memancing.";
include 'includes/header.php'; 
?>

    <body class="reel-page">
    <!-- Category Hero -->
    <section class="category-hero">
        <div class="container">
            <h1>Reel</h1>
            <p>Reel berkualitas tinggi dengan performa maksimal untuk semua teknik memancing</p>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products-section">
        <div class="container">
            <div class="products-layout">
                <!-- Sidebar Filter -->
                <div class="sidebar">
                    <div class="filter-header">
                        <i class="fas fa-filter"></i>
                        <h3 class="filter-title">Filter Produk</h3>
                    </div>
                    
                    <div class="filter-group">
                        <h4 class="filter-group-title">Cari Produk</h4>
                        <input type="text" class="filter-input" id="productSearch" placeholder="Nama produk...">
                    </div>
                    
                    <div class="filter-group">
                        <h4 class="filter-group-title">Kategori</h4>
                        <label class="filter-checkbox"><input type="checkbox" value="Baitcasting"> Baitcasting</label>
                        <label class="filter-checkbox"><input type="checkbox" value="Surf Casting"> Surf Casting</label>
                        <label class="filter-checkbox"><input type="checkbox" value="Fly Fishing"> Fly Fishing</label>
                        <label class="filter-checkbox"><input type="checkbox" value="Spinning"> Spinning</label>
                        <label class="filter-checkbox"><input type="checkbox" value="Jigging"> Jigging</label>
                    </div>
                    
                    <div class="filter-group">
                        <h4 class="filter-group-title">Rentang Harga</h4>
                        <select class="filter-input" id="priceRange">
                            <option value="">Semua Harga</option>
                            <option value="0-500000">Di bawah Rp 500.000</option>
                            <option value="500000-1000000">Rp 500.000 - Rp 1.000.000</option>
                            <option value="1000000-2000000">Rp 1.000.000 - Rp 2.000.000</option>
                            <option value="2000000-999999999">Di atas Rp 2.000.000</option>
                        </select>
                    </div>
                </div>
                
                <!-- Products Grid -->
                <div class="products-content">
                    <div class="products-header">
                        <div class="products-info">
                            <h2>Semua Produk</h2>
                            <span class="products-count" id="productCount">Menampilkan produk reel</span>
                        </div>
                        
                        <div class="products-controls">
                            <select class="sort-select" id="sortSelect">
                                <option value="popularity">Paling Populer</option>
                                <option value="newest">Terbaru</option>
                                <option value="price_low">Harga Terendah</option>
                                <option value="price_high">Harga Tertinggi</option>
                            </select>
                            
                            <div class="view-toggle">
                                <button class="view-btn active" data-view="grid"><i class="fas fa-th"></i></button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="products-grid" id="productsGrid">
                        <!-- Products will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </section>


<?php include 'includes/footer.php'; ?>
