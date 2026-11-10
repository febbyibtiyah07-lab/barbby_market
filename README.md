# Barb'by Market
Marketplace fashion demo menggunakan PHP Native, MySQLi, Bootstrap 5. Fitur: katalog, detail, CRUD produk admin, keranjang berbasis session, checkout dan transaksi pesanan MySQL.

## Instalasi XAMPP
1. Ekstrak folder `barbby_market` ke `C:\xampp\htdocs\barbby_market`.
2. Jalankan Apache dan MySQL, buka `http://localhost/phpmyadmin`.
3. Import `database.sql` lewat tab Import di halaman utama phpMyAdmin. File SQL membuat database BARU `barbby_market_app`, tidak menghapus database lama.
4. Salin `config/database.example.php` menjadi `config/database.php`. Default lokal: host localhost, user root, password kosong, database barbby_market_app.
5. Buka `http://localhost/barbby_market/`.
6. Untuk CRUD: buka `http://localhost/barbby_market/admin/setup_admin.php` dan buat username/password kuat. **HAPUS** `admin/setup_admin.php` setelah akun dibuat. Login di `/admin/login.php`.
7. Uji detail produk -> tambah keranjang -> checkout -> lihat tabel `pesanan` dan `detail_pesanan` di phpMyAdmin.

## InfinityFree
1. Buat hosting dan database MySQL di panel InfinityFree; catat DB host, username, password, dan database name.
2. Export database lokal `barbby_market_app` dari phpMyAdmin, lalu import ke database InfinityFree melalui phpMyAdmin hosting. Jika dump berisi CREATE DATABASE / USE, hapus kedua baris itu sebelum import ke DB yang disediakan hosting.
3. Upload isi folder proyek ke `htdocs` melalui File Manager/FTP. Jangan upload `config/database.php` ke GitHub.
4. Buat `config/database.php` pada hosting dengan kredensial hosting (gunakan File Manager), dan pastikan file ini tidak dibagikan.
5. Pastikan akun admin dibuat dengan setup_admin.php lalu hapus file setup tersebut dari hosting. Hapus juga database.sql dari folder publik setelah import.
6. Uji katalog, keranjang, checkout, admin dan simpan screenshot bukti nyata.

## GitHub
Upload source code (tanpa `config/database.php`, tanpa kredensial). `.gitignore` disertakan, tetapi jika upload manual lewat website GitHub, jangan pilih file konfigurasi rahasia. `config/database.example.php` boleh diupload.

## Catatan
- Produk memakai ilustrasi SVG lokal, bukan foto asli. Dapat diganti kemudian.
- Keranjang disimpan di session PHP, sementara pesanan disimpan di database MySQL.
- Checkout tidak memproses pembayaran online.
- Belum tersedia pendaftaran/login pembeli atau dashboard penjual. Admin mengelola katalog.
- File setup admin hanya untuk instalasi awal, wajib dihapus setelah dipakai.
