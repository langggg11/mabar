<?php 
$page_title = "Umpan & Kail Pancing - Mabar";
$page_description = "Umpan buatan dan kail berkualitas untuk berbagai jenis ikan dan kondisi perairan, dari air tawar hingga laut dalam.";
include 'includes/header.php'; 
?>

    <body class="umpan-page">
    <!-- Category Hero -->
    <section class="category-hero">
        <div class="container">
            <h1>Umpan & Kail</h1>
            <p>Umpan buatan dan kail berkualitas untuk berbagai jenis ikan dan kondisi perairan</p>
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
                        <label class="filter-checkbox"><input type="checkbox" value="Umpan Buatan"> Umpan Buatan</label>
                        <label class="filter-checkbox"><input type="checkbox" value="Kail"> Kail</label>
                        <label class="filter-checkbox"><input type="checkbox" value="Soft Lure"> Soft Lure</label>
                        <label class="filter-checkbox"><input type="checkbox" value="Metal Lure"> Metal Lure</label>
                    </div>
                    
                    <div class="filter-group">
                        <h4 class="filter-group-title">Rentang Harga</h4>
                        <select class="filter-input" id="priceRange">
                            <option value="">Semua Harga</option>
                            <option value="0-50000">Di bawah Rp 50.000</option>
                            <option value="50000-100000">Rp 50.000 - Rp 100.000</option>
                            <option value="100000-200000">Rp 100.000 - Rp 200.000</option>
                            <option value="200000-999999999">Di atas Rp 200.000</option>
                        </select>
                    </div>
                </div>
                
                <!-- Products Grid -->
                <div class="products-content">
                    <div class="products-header">
                        <div class="products-info">
                            <h2>Semua Produk</h2>
                            <span class="products-count" id="productCount">Menampilkan produk umpan & kail</span>
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
