-- Verifikasi database dan data yang telah diinsert
USE fishing_gear_db;

-- Cek apakah semua tabel sudah terbuat
SHOW TABLES;

-- Cek data categories
SELECT * FROM categories;

-- Cek jumlah produk per kategori
SELECT c.name as category_name, COUNT(p.id) as product_count 
FROM categories c 
LEFT JOIN products p ON c.id = p.category_id 
GROUP BY c.id, c.name;

-- Cek sample products
SELECT p.name, p.price, c.name as category, p.subcategory 
FROM products p 
JOIN categories c ON p.category_id = c.id 
LIMIT 10;

-- Cek users
SELECT id, name, email, created_at FROM users;

-- Cek struktur tabel products
DESCRIBE products;
