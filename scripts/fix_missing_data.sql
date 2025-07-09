-- Script untuk memastikan semua data lengkap
USE fishing_gear_db;

-- Jika categories belum ada, insert lagi
INSERT IGNORE INTO categories (name, slug, description, icon) VALUES
('Joran Pancing', 'joran', 'Koleksi lengkap joran pancing berkualitas tinggi untuk berbagai teknik memancing', 'joran-icon'),
('Reel Pancing', 'reel', 'Reel berkualitas tinggi untuk performa maksimal', 'reel-icon'),
('Umpan & Kail', 'umpan', 'Koleksi umpan buatan dan kail terbaik untuk berbagai jenis ikan dan kondisi perairan', 'umpan-icon'),
('Aksesoris', 'aksesoris', 'Perlengkapan dan aksesoris memancing lengkap', 'aksesoris-icon');

-- Insert sample user jika belum ada
INSERT IGNORE INTO users (email, password, name) VALUES 
('admin@fishinggear.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin FishingGear'),
('user@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'User Test');

-- Pastikan ada produk untuk setiap kategori
-- Joran Products (jika belum ada)
INSERT IGNORE INTO products (id, name, slug, description, specifications, price, category_id, subcategory, popularity_score, is_new, is_promo, rod_length, rod_action, weight, target_fish, image, shopee_link) VALUES
(1, 'Joran Pancing Carbon Fiber Pro 2.1m', 'joran-carbon-fiber-pro-21m', 'Joran carbon fiber premium dengan aksi responsif dan daya tahan tinggi. Cocok untuk berbagai teknik memancing dari casting hingga jigging.', 'Material: Carbon Fiber 99%\nPanjang: 2.1m\nBerat: 180g\nAction: Medium Heavy\nLure Weight: 10-30g\nLine Weight: 8-16lb', 450000, 1, 'Carbon Fiber', 88, FALSE, TRUE, '2.1m', 'Medium Heavy', '180g', 'Ikan Predator', 'assets/images/contoh-reel.jpg', 'https://id.shp.ee/KxEYGW4'),

(2, 'Joran Casting Heavy Action 2.4m', 'joran-casting-heavy-action-24m', 'Joran casting dengan aksi heavy untuk memancing ikan besar. Dilengkapi dengan guide berkualitas tinggi dan handle ergonomis.', 'Material: Carbon Composite\nPanjang: 2.4m\nBerat: 220g\nAction: Heavy\nLure Weight: 15-40g\nLine Weight: 12-20lb', 920000, 1, 'Casting', 85, FALSE, TRUE, '2.4m', 'Heavy', '220g', 'Ikan Besar', 'assets/images/contoh-reel.jpg', 'https://id.shp.ee/KxEYGW4'),

(3, 'Joran Spinning Ultralight 1.8m', 'joran-spinning-ultralight-18m', 'Joran spinning ultralight untuk memancing ikan kecil hingga sedang dengan sensitivitas tinggi dan kontrol maksimal.', 'Material: Carbon Fiber\nPanjang: 1.8m\nBerat: 120g\nAction: Ultra Light\nLure Weight: 1-8g\nLine Weight: 2-6lb', 750000, 1, 'Spinning', 78, TRUE, TRUE, '1.8m', 'Ultra Light', '120g', 'Ikan Kecil-Sedang', 'assets/images/contoh-reel.jpg', 'https://id.shp.ee/KxEYGW4'),

(4, 'Joran Surf Casting Long 3.6m', 'joran-surf-casting-long-36m', 'Joran surf casting panjang untuk memancing dari pantai dengan jangkauan maksimal dan power luar biasa.', 'Material: Carbon Composite\nPanjang: 3.6m\nBerat: 350g\nAction: Medium Heavy\nLure Weight: 20-80g\nLine Weight: 15-25lb', 750000, 1, 'Surf Casting', 85, FALSE, FALSE, '3.6m', 'Medium Heavy', '350g', 'Ikan Laut', 'assets/images/contoh-reel.jpg', 'https://id.shp.ee/KxEYGW4'),

(5, 'Joran Fly Fishing Premium 2.7m', 'joran-fly-fishing-premium-27m', 'Joran fly fishing premium dengan aksi medium untuk teknik fly fishing yang presisi dan elegan.', 'Material: Carbon Fiber Premium\nPanjang: 2.7m\nBerat: 150g\nAction: Medium\nLine Weight: 5-7wt\nPieces: 4', 920000, 1, 'Fly Fishing', 78, FALSE, FALSE, '2.7m', 'Medium', '150g', 'Trout, Salmon', 'assets/images/contoh-reel.jpg', 'https://id.shp.ee/KxEYGW4'),

(6, 'Joran Telescopic Travel 2.7m', 'joran-telescopic-travel-27m', 'Joran telescopic portabel untuk traveling dengan kualitas premium dan kemudahan penyimpanan.', 'Material: Carbon Fiber\nPanjang: 2.7m (Collapsed: 60cm)\nBerat: 200g\nAction: Medium\nLure Weight: 5-25g\nSections: 7', 280000, 1, 'Telescopic', 75, TRUE, FALSE, '2.7m', 'Medium', '200g', 'Serbaguna', 'assets/images/contoh-reel.jpg', 'https://id.shp.ee/KxEYGW4');

-- Reel Products
INSERT IGNORE INTO products (id, name, slug, description, specifications, price, category_id, subcategory, popularity_score, is_new, is_promo, gear_ratio, bearings, weight, target_fish, image, shopee_link) VALUES
(7, 'Reel Baitcasting Abu Garcia Cardinal SX 40', 'reel-baitcasting-abu-garcia-cardinal-sx-40', 'Reel baitcasting premium dengan sistem pengereman magnetik dan konstruksi tahan lama untuk performa maksimal.', 'Gear Ratio: 6.2:1\nBearings: 10+1\nMax Drag: 8kg\nLine Capacity: 0.30mm/150m\nWeight: 220g\nBraking System: Magnetic', 450000, 2, 'Baitcasting', 88, FALSE, FALSE, '6.2:1', '10+1', '220g', 'Bass, Pike', 'assets/images/contoh-reel.jpg', 'https://id.shp.ee/KxEYGW4'),

(8, 'Reel Surf Casting Penn Battle III 8000', 'reel-surf-casting-penn-battle-iii-8000', 'Reel surf casting heavy duty dengan sealed bearing dan drag system yang powerful untuk memancing ikan besar.', 'Gear Ratio: 4.3:1\nBearings: 5+1\nMax Drag: 15kg\nLine Capacity: 0.40mm/300m\nWeight: 680g\nSealed Design: Yes', 920000, 2, 'Surf Casting', 85, FALSE, FALSE, '4.3:1', '5+1', '680g', 'Ikan Laut Besar', 'assets/images/contoh-reel.jpg', 'https://id.shp.ee/KxEYGW4'),

(9, 'Reel Fly Fishing Redington Behemoth', 'reel-fly-fishing-redington-behemoth', 'Reel fly fishing dengan large arbor design dan smooth drag system untuk fly fishing yang presisi.', 'Gear Ratio: 1:1\nBearings: 2+1\nWeight: 150g\nLine Weight: 7/8wt\nDrag: Sealed Carbon Fiber\nArbor: Large', 750000, 2, 'Fly Fishing', 78, TRUE, FALSE, '1:1', '2+1', '150g', 'Trout, Salmon', 'assets/images/contoh-reel.jpg', 'https://id.shp.ee/KxEYGW4'),

(10, 'Reel Spinning Daiwa Legalis LT 2500', 'reel-spinning-daiwa-legalis-lt-2500', 'Reel spinning dengan teknologi Light & Tough untuk performa optimal dengan bobot ringan.', 'Gear Ratio: 5.3:1\nBearings: 5+1\nMax Drag: 10kg\nLine Capacity: 0.25mm/200m\nWeight: 230g\nBody: Aluminum', 380000, 2, 'Spinning', 82, FALSE, TRUE, '5.3:1', '5+1', '230g', 'Serbaguna', 'assets/images/contoh-reel.jpg', 'https://id.shp.ee/KxEYGW4'),

(11, 'Reel Jigging Shimano Stella SW 6000', 'reel-jigging-shimano-stella-sw-6000', 'Reel jigging premium dengan teknologi terdepan untuk jigging laut dalam dan memancing ikan monster.', 'Gear Ratio: 4.9:1\nBearings: 12+1\nMax Drag: 25kg\nLine Capacity: 0.50mm/400m\nWeight: 460g\nWaterproof: IPX8', 2850000, 2, 'Jigging', 92, FALSE, FALSE, '4.9:1', '12+1', '460g', 'Ikan Laut Dalam', 'assets/images/contoh-reel.jpg', 'https://id.shp.ee/KxEYGW4');

-- Umpan & Kail Products
INSERT IGNORE INTO products (id, name, slug, description, specifications, price, category_id, subcategory, popularity_score, is_new, is_promo, weight, target_fish, image, shopee_link) VALUES
(12, 'Kail Pancing Assorted Hook Set 50pcs', 'kail-pancing-assorted-hook-set-50pcs', 'Set kail pancing dengan berbagai ukuran dan jenis untuk semua kebutuhan memancing. Terbuat dari baja karbon berkualitas tinggi.', 'Material: High Carbon Steel\nJumlah: 50 pieces\nUkuran: #2-#12\nJenis: Aberdeen, Circle, Octopus\nFinish: Black Nickel', 85000, 3, 'Kail', 90, FALSE, FALSE, '0.5kg', 'Semua Jenis Ikan', 'assets/images/contoh-reel.jpg', 'https://id.shp.ee/KxEYGW4'),

(13, 'Umpan Buatan Minnow Set 10pcs', 'umpan-buatan-minnow-set-10pcs', 'Set umpan buatan minnow berkualitas tinggi yang terdiri dari 10 pieces dengan berbagai warna dan ukuran.', 'Material: ABS Plastic berkualitas tinggi\nJumlah: 10 pieces\nPanjang: 7-12cm\nBerat: 8-15g\nHook: Treble hook tajam dan anti karat\nWarna: Variasi natural dan bright colors', 125000, 3, 'Umpan Buatan', 85, TRUE, TRUE, '0.3kg', 'Ikan Predator', 'assets/images/contoh-reel.jpg', 'https://id.shp.ee/KxEYGW4'),

(14, 'Umpan Buatan Popper Surface Lure', 'umpan-buatan-popper-surface-lure', 'Umpan popper untuk surface fishing dengan aksi popping yang menarik ikan predator ke permukaan air.', 'Material: ABS Plastic\nPanjang: 9cm\nBerat: 12g\nHook: Triple Treble Hook\nAction: Popping\nTarget Depth: Surface', 45000, 3, 'Umpan Buatan', 78, FALSE, FALSE, '0.1kg', 'Bass, Toman', 'assets/images/contoh-reel.jpg', 'https://id.shp.ee/KxEYGW4'),

(15, 'Soft Lure Worm Set Berkualitas', 'soft-lure-worm-set-berkualitas', 'Set soft lure worm dengan tekstur realistis dan aroma yang menarik ikan. Cocok untuk berbagai teknik memancing.', 'Material: Soft Plastic\nJumlah: 20 pieces\nPanjang: 4-6 inch\nWarna: Natural colors\nScent: Fish attractant\nFlexibility: High', 65000, 3, 'Soft Lure', 82, FALSE, TRUE, '0.2kg', 'Bass, Pike', 'assets/images/contoh-reel.jpg', 'https://id.shp.ee/KxEYGW4'),

(16, 'Spoon Lure Metal Spinner Set', 'spoon-lure-metal-spinner-set', 'Set sendok logam dengan spinner untuk memancing ikan predator dengan aksi berputar yang menarik.', 'Material: Stainless Steel\nJumlah: 15 pieces\nBerat: 3-18g\nFinish: Chrome, Gold, Silver\nHook: Single barbless\nAction: Spinning', 95000, 3, 'Metal Lure', 75, FALSE, FALSE, '0.4kg', 'Trout, Salmon', 'assets/images/contoh-reel.jpg', 'https://id.shp.ee/KxEYGW4');

-- Aksesoris Products
INSERT IGNORE INTO products (id, name, slug, description, specifications, price, category_id, subcategory, popularity_score, is_new, is_promo, weight, target_fish, image, shopee_link) VALUES
(17, 'Tas Pancing Waterproof Premium', 'tas-pancing-waterproof-premium', 'Tas pancing tahan air dengan banyak kompartemen untuk menyimpan semua perlengkapan memancing dengan aman dan terorganisir.', 'Material: 600D Oxford Fabric\nUkuran: 40x25x20cm\nKapasitas: 20L\nKompartemen: 8 pockets\nWaterproof: IPX6\nTali: Adjustable shoulder strap', 185000, 4, 'Tas', 80, TRUE, FALSE, '1.2kg', 'Aksesoris', 'assets/images/contoh-reel.jpg', 'https://id.shp.ee/KxEYGW4'),

(18, 'Pelampung Ikan Assorted Float Set', 'pelampung-ikan-assorted-float-set', 'Set pelampung dengan berbagai ukuran dan bentuk untuk berbagai kondisi memancing dan jenis ikan target.', 'Material: Balsa Wood, Plastic\nJumlah: 25 pieces\nUkuran: 1g-10g\nBentuk: Stick, Waggler, Antenna\nWarna: Hi-vis colors\nSensitivity: High', 75000, 4, 'Pelampung', 75, FALSE, TRUE, '0.3kg', 'Ikan Air Tawar', 'assets/images/contoh-reel.jpg', 'https://id.shp.ee/KxEYGW4'),

(19, 'Kotak Pancing Multi Compartment', 'kotak-pancing-multi-compartment', 'Kotak pancing dengan banyak sekat untuk menyimpan umpan, kail, dan aksesoris kecil lainnya dengan rapi.', 'Material: PP Plastic\nUkuran: 27x18x5cm\nKompartemen: 18 adjustable\nTransparent: Yes\nLatch: Secure double lock\nWeight: 450g', 55000, 4, 'Kotak', 70, FALSE, FALSE, '0.5kg', 'Aksesoris', 'assets/images/contoh-reel.jpg', 'https://id.shp.ee/KxEYGW4'),

(20, 'Jaring Ikan Landing Net Telescopic', 'jaring-ikan-landing-net-telescopic', 'Jaring ikan telescopic dengan handle yang dapat diperpanjang untuk memudahkan pengangkatan ikan.', 'Material: Aluminum handle, Nylon net\nPanjang: 1.5-3m (telescopic)\nDiameter net: 50cm\nMesh: Fine rubber coating\nWeight: 680g\nFolded length: 60cm', 145000, 4, 'Jaring', 72, TRUE, FALSE, '0.7kg', 'Aksesoris', 'assets/images/contoh-reel.jpg', 'https://id.shp.ee/KxEYGW4'),

(21, 'Headlamp LED Waterproof Memancing', 'headlamp-led-waterproof-memancing', 'Headlamp LED tahan air khusus untuk memancing malam dengan cahaya terang dan tahan lama.', 'LED: CREE XM-L2\nLumen: 1200lm\nBattery: 18650 rechargeable\nRuntime: 8 hours\nWaterproof: IPX8\nBeam distance: 200m', 125000, 4, 'Lampu', 68, FALSE, TRUE, '0.2kg', 'Aksesoris', 'assets/images/contoh-reel.jpg', 'https://id.shp.ee/KxEYGW4');

-- Insert sample reviews
INSERT IGNORE INTO reviews (user_id, product_id, rating, review_text) VALUES
(1, 1, 4.8, 'Joran yang sangat bagus, responsif dan tahan lama'),
(2, 1, 4.9, 'Kualitas premium dengan harga yang reasonable'),
(1, 7, 4.7, 'Reel yang smooth dan powerful untuk baitcasting'),
(2, 12, 4.9, 'Set kail yang lengkap dan tajam'),
(1, 17, 4.6, 'Tas yang praktis dan tahan air'),
(2, 7, 4.8, 'Reel baitcasting terbaik di kelasnya'),
(1, 13, 4.7, 'Umpan minnow yang realistis dan efektif'),
(2, 18, 4.5, 'Pelampung yang sensitif dan mudah terlihat'),
(1, 10, 4.6, 'Reel spinning yang ringan dan smooth'),
(2, 3, 4.8, 'Joran ultralight yang sangat responsif');
