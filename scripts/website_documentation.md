# Dokumentasi Website dan Database Mabar (Mancing Bareng)

## 1. Penjelasan UML (Unified Modeling Language) Database
Database website ini dirancang untuk mengelola pengguna, produk, kategori, wishlist, dan ulasan. Berikut adalah visualisasi dan penjelasan dari setiap tabel dan relasinya.

### Diagram Relasi Entitas (Entity-Relationship Diagram)
```
[users] 1--* [wishlist] *--1 [products]
  |                            |
  |                            |
  *-- [reviews] --*            *--1 [categories]
```

### Deskripsi Tabel

#### a. Tabel `users`
Menyimpan data semua pengguna yang terdaftar di website, termasuk admin.
- `id`: Primary Key, identifikasi unik untuk setiap pengguna.
- `email`: Email pengguna, digunakan untuk login dan harus unik.
- `password`: Kata sandi pengguna yang sudah di-hash.
- `name`: Nama lengkap pengguna.
- `is_admin`: Flag (0 atau 1) untuk menandakan apakah pengguna adalah admin.
- `created_at`: Waktu kapan pengguna mendaftar.

#### b. Tabel `categories`
Menyimpan kategori utama dari produk yang dijual.
- `id`: Primary Key, identifikasi unik untuk setiap kategori.
- `name`: Nama kategori (e.g., 'Joran', 'Reel').
- `slug`: Versi URL-friendly dari nama kategori.
- `description`: Deskripsi singkat tentang kategori.

#### c. Tabel `products`
Tabel inti yang menyimpan semua detail produk alat pancing.
- `id`: Primary Key.
- `name`: Nama produk.
- `description`: Deskripsi lengkap produk.
- `specifications`: Spesifikasi teknis produk.
- `price`: Harga produk.
- `category_id`: Foreign Key yang terhubung ke `categories.id`.
- `image`: Path atau URL ke gambar produk.
- `shopee_link`: Link afiliasi ke halaman produk di Shopee.
- ... dan atribut spesifik lainnya seperti `gear_ratio`, `rod_length`, dll.

#### d. Tabel `wishlist`
Berfungsi sebagai tabel penghubung (pivot) untuk relasi Many-to-Many antara `users` dan `products`. Menyimpan produk mana yang disukai oleh pengguna.
- `id`: Primary Key.
- `user_id`: Foreign Key ke `users.id`.
- `product_id`: Foreign Key ke `products.id`.

#### e. Tabel `reviews`
Menyimpan ulasan dan rating yang diberikan oleh pengguna untuk suatu produk.
- `id`: Primary Key.
- `user_id`: Foreign Key ke `users.id`.
- `product_id`: Foreign Key ke `products.id`.
- `rating`: Angka rating (misal: 1-5).
- `review_text`: Teks ulasan dari pengguna.

## 2. Penjelasan Cara Kerja Website
Website ini adalah sebuah platform katalog dan afiliasi untuk produk-produk alat pancing. Pengguna dapat melihat, mencari, dan menyimpan produk yang mereka minati, kemudian diarahkan ke marketplace (Shopee) untuk melakukan pembelian.

### Alur Pengguna (User Flow)
1.  **Pendaftaran & Login:** Pengguna baru dapat mendaftar melalui halaman `register.php`. Pengguna yang sudah ada dapat masuk melalui `login.php`. Sesi pengguna dikelola untuk menjaga status login.
2.  **Browsing Produk:** Pengguna dapat menjelajahi produk melalui halaman utama (`index.php`) atau melalui halaman kategori spesifik seperti `joran.php`, `reel.php`, dll.
3.  **Pencarian Produk:** Terdapat fitur pencarian (`search.php`) dan pencarian langsung/live search (`api/live_search.php`) untuk menemukan produk dengan cepat.
4.  **Melihat Detail Produk:** Dengan mengklik produk, pengguna akan diarahkan ke `product-detail.php` untuk melihat informasi lengkap, spesifikasi, dan ulasan dari pengguna lain.
5.  **Wishlist:** Pengguna yang sudah login dapat menambahkan produk ke `wishlist.php` mereka. Fitur ini memungkinkan mereka menyimpan produk yang diminati.
6.  **Memberi Ulasan:** Setelah "membeli" (atau hanya untuk tujuan data), pengguna dapat memberikan rating dan ulasan pada halaman detail produk, yang akan diproses oleh `api/submit_review.php`.
7.  **Pembelian (Afiliasi):** Ketika pengguna memutuskan untuk membeli, mereka akan mengklik tombol yang mengarah ke `shopee_link` dari produk tersebut, menyelesaikan transaksi di luar website ini.

### Alur Admin (Admin Flow)
1.  **Login Admin:** Admin masuk menggunakan akun yang memiliki flag `is_admin = 1`.
2.  **Dashboard Admin:** Setelah login, admin diarahkan ke `admin.php` yang berfungsi sebagai dashboard utama.
3.  **Manajemen Produk:** Admin dapat menambah (`add-product.php`), mengedit (`edit-product.php`), dan menghapus produk melalui halaman `manage-products.php`.
4.  **Manajemen Pengguna & Konten Lain:** (Asumsi) Admin kemungkinan juga memiliki hak untuk mengelola pengguna dan konten lainnya, meskipun tidak secara eksplisit terlihat dari nama file.