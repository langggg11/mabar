<?php 
$page_title = "Aksesoris Pancing - Mabar";
$page_description = "Perlengkapan dan aksesoris memancing lengkap untuk mendukung aktivitas memancing Anda dengan optimal.";
include 'includes/header.php'; 
?>

    <body class="aksesoris-page">
    <!-- Category Hero -->
    <section class="category-hero">
        <div class="container">
            <h1>Aksesoris</h1>
            <p>Perlengkapan dan aksesoris memancing lengkap untuk mendukung aktivitas memancing Anda</p>
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
                        <label class="filter-checkbox"><input type="checkbox" value="Tas"> Tas</label>
                        <label class="filter-checkbox"><input type="checkbox" value="Kotak"> Kotak</label>
                        <label class="filter-checkbox"><input type="checkbox" value="Jaring"> Jaring</label>
                        <label class="filter-checkbox"><input type="checkbox" value="Lampu"> Lampu</label>
                        <label class="filter-checkbox"><input type="checkbox" value="Tools"> Tools</label>
                    </div>
                    
                    <div class="filter-group">
                        <h4 class="filter-group-title">Rentang Harga</h4>
                        <select class="filter-input" id="priceRange">
                            <option value="">Semua Harga</option>
                            <option value="0-100000">Di bawah Rp 100.000</option>
                            <option value="100000-300000">Rp 100.000 - Rp 300.000</option>
                            <option value="300000-500000">Rp 300.000 - Rp 500.000</option>
                            <option value="500000-999999999">Di atas Rp 500.000</option>
                        </select>
                    </div>
                </div>
                
                <!-- Products Grid -->
                <div class="products-content">
                    <div class="products-header">
                        <div class="products-info">
                            <h2>Semua Produk</h2>
                            <span class="products-count" id="productCount">Menampilkan produk aksesoris</span>
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

    <script>
        // Load aksesoris products with enhanced search
        document.addEventListener('DOMContentLoaded', function() {
            loadProducts('aksesoris');
            
            // Enhanced search functionality
            const productSearch = document.getElementById('productSearch');
            if (productSearch) {
                productSearch.addEventListener('input', function(e) {
                    const query = e.target.value.trim();
                    handleProductSearch(query, 'aksesoris');
                });
            }
            
            // Sort functionality
            const sortSelect = document.getElementById('sortSelect');
            if (sortSelect) {
                sortSelect.addEventListener('change', function(e) {
                    loadProducts('aksesoris', e.target.value);
                });
            }
            
            // Filter functionality
            const filterCheckboxes = document.querySelectorAll('.filter-checkbox input');
            filterCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    applyFilters('aksesoris');
                });
            });
            
            // Price range filter
            const priceRange = document.getElementById('priceRange');
            if (priceRange) {
                priceRange.addEventListener('change', function() {
                    applyFilters('aksesoris');
                });
            }
        });
        
        function handleProductSearch(query, category) {
            const productsGrid = document.getElementById('productsGrid');
            if (!productsGrid) return;
            
            // Show loading
            productsGrid.innerHTML = `
                <div class="loading">
                    <div class="spinner"></div>
                    <p>Mencari produk...</p>
                </div>
            `;
            
            // Build search parameters
            const params = new URLSearchParams();
            if (category) params.append('category', category);
            if (query) params.append('search', query);
            
            // Get selected filters
            const selectedCategories = Array.from(document.querySelectorAll('.filter-checkbox input:checked'))
                .map(cb => cb.value);
            if (selectedCategories.length > 0) {
                params.append('subcategory', selectedCategories.join(','));
            }
            
            const priceRange = document.getElementById('priceRange').value;
            if (priceRange) {
                params.append('price_range', priceRange);
            }
            
            // Perform search
            fetch(`api/products.php?${params.toString()}`)
                .then(response => response.json())
                .then(result => {
                    if (result.success && result.products) {
                        renderProducts(result.products, productsGrid);
                        updateProductCount(result.products.length);
                    } else {
                        showNoResults(productsGrid);
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    showSearchError(productsGrid);
                });
        }
        
        function applyFilters(category) {
            const query = document.getElementById('productSearch').value.trim();
            handleProductSearch(query, category);
        }
        
        function renderProducts(products, container) {
            if (products.length === 0) {
                showNoResults(container);
                return;
            }
            
            container.innerHTML = products.map(product => createProductCard(product)).join('');
        }
        
        function createProductCard(product) {
            const specs = [];
            if (product.material) specs.push(`<div class="spec-item"><span class="spec-label">Material:</span> <span class="spec-value">${product.material}</span></div>`);
            if (product.capacity) specs.push(`<div class="spec-item"><span class="spec-label">Kapasitas:</span> <span class="spec-value">${product.capacity}</span></div>`);
            if (product.dimensions) specs.push(`<div class="spec-item"><span class="spec-label">Dimensi:</span> <span class="spec-value">${product.dimensions}</span></div>`);
            
            return `
                <div class="product-card" data-slug="${product.slug}">
                    <div class="product-image">
                        <img src="${product.image}" alt="${product.name}" loading="lazy">
                        <button class="wishlist-btn" data-product-id="${product.id}" onclick="toggleWishlist(${product.id})">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                    <div class="product-info">
                        <div class="product-category">${product.subcategory || product.category_name}</div>
                        <h3 class="product-name">${product.name}</h3>
                        <div class="product-specs">
                            ${specs.slice(0, 3).join('')}
                        </div>
                        <div class="product-price">
                            ${formatPrice(product.price)}
                        </div>
                        <div class="product-actions">
                            <a href="product-detail.php?slug=${product.slug}" class="btn btn-primary">
                                <i class="fas fa-eye"></i>
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            `;
        }
        
        function showNoResults(container) {
            container.innerHTML = `
                <div class="no-products">
                    <i class="fas fa-search"></i>
                    <h3>Tidak ada produk ditemukan</h3>
                    <p>Coba gunakan kata kunci yang berbeda atau ubah filter pencarian</p>
                </div>
            `;
        }
        
        function showSearchError(container) {
            container.innerHTML = `
                <div class="error-state">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>Terjadi Kesalahan</h3>
                    <p>Gagal memuat hasil pencarian. Silakan coba lagi.</p>
                    <button class="btn btn-primary" onclick="location.reload()">
                        <i class="fas fa-refresh"></i>
                        Coba Lagi
                    </button>
                </div>
            `;
        }
        
        function updateProductCount(count) {
            const countElement = document.getElementById('productCount');
            if (countElement) {
                countElement.textContent = `Menampilkan ${count} produk aksesoris`;
            }
        }
        
        function formatPrice(price) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(price);
        }
        
        // Enhanced wishlist with success notification
        async function toggleWishlist(productId) {
            try {
                const formData = new FormData();
                formData.append('product_id', productId);
                
                const response = await fetch('api/wishlist.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    if (result.action === 'added') {
                        showSuccessNotification('Produk berhasil ditambahkan ke daftar yang disukai!');
                    } else {
                        showSuccessNotification('Produk dihapus dari daftar yang disukai');
                    }
                    
                    // Update button state
                    const button = document.querySelector(`[data-product-id="${productId}"]`);
                    const icon = button.querySelector('i');
                    if (result.action === 'added') {
                        button.classList.add('active');
                        icon.className = 'fas fa-heart';
                    } else {
                        button.classList.remove('active');
                        icon.className = 'far fa-heart';
                    }
                }
            } catch (error) {
                console.error('Wishlist error:', error);
            }
        }
    </script>

<?php include 'includes/footer.php'; ?>
