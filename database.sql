
-- Barb'by Market, new isolated database. Import using phpMyAdmin.

-- ==========================================
-- TABEL USERS
-- ==========================================

CREATE TABLE IF NOT EXISTS users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(120) NOT NULL,
    username VARCHAR(80) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'penjual', 'pembeli') NOT NULL DEFAULT 'pembeli'
) ENGINE=InnoDB;


-- ==========================================
-- TABEL KATEGORI
-- ==========================================

CREATE TABLE IF NOT EXISTS kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;


-- ==========================================
-- TABEL PRODUK
-- ==========================================

CREATE TABLE IF NOT EXISTS produk (
    id_produk INT AUTO_INCREMENT PRIMARY KEY,
    id_kategori INT NOT NULL,
    nama_produk VARCHAR(150) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(12,2) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    gambar VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_kategori)
        REFERENCES kategori(id_kategori)
) ENGINE=InnoDB;


-- ==========================================
-- TABEL PESANAN
-- ==========================================

CREATE TABLE IF NOT EXISTS pesanan (
    id_pesanan INT AUTO_INCREMENT PRIMARY KEY,
    nama_pembeli VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telepon VARCHAR(30) NOT NULL,
    alamat TEXT NOT NULL,
    total_harga DECIMAL(14,2) NOT NULL,
    status ENUM('baru', 'diproses', 'selesai', 'dibatalkan')
        NOT NULL DEFAULT 'baru',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- ==========================================
-- TABEL DETAIL PESANAN
-- ==========================================

CREATE TABLE IF NOT EXISTS detail_pesanan (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_pesanan INT NOT NULL,
    id_produk INT NOT NULL,
    nama_produk VARCHAR(150) NOT NULL,
    harga DECIMAL(12,2) NOT NULL,
    jumlah INT NOT NULL,
    subtotal DECIMAL(14,2) NOT NULL,

    FOREIGN KEY (id_pesanan)
        REFERENCES pesanan(id_pesanan),

    FOREIGN KEY (id_produk)
        REFERENCES produk(id_produk)
) ENGINE=InnoDB;


-- Cart is stored in PHP session, not in a DB table.


-- ==========================================
-- DATA KATEGORI
-- ==========================================

INSERT IGNORE INTO kategori (
    id_kategori,
    nama_kategori
)
VALUES
    (1, 'Pakaian'),
    (2, 'Tas'),
    (3, 'Sepatu'),
    (4, 'Aksesoris');


-- ==========================================
-- DATA PRODUK
-- ==========================================

INSERT INTO produk (
    id_kategori,
    nama_produk,
    deskripsi,
    harga,
    stok,
    gambar
)
VALUES
    (
        1,
        'Blouse Satin Rose',
        'Blouse feminin dengan bahan satin lembut.',
        129000,
        20,
        'assets/blouse.svg'
    ),
    (
        1,
        'Cardigan Lavender',
        'Cardigan rajut nyaman untuk sehari-hari.',
        159000,
        15,
        'assets/cardigan.svg'
    ),
    (
        2,
        'Mini Handbag Cream',
        'Tas mini elegan untuk acara kasual.',
        189000,
        12,
        'assets/tas.svg'
    ),
    (
        3,
        'Sneakers Daily White',
        'Sneakers ringan dan nyaman.',
        249000,
        10,
        'assets/sepatu.svg'
    ),
    (
        1,
        'Rok Midi Pink',
        'Rok midi simpel dan anggun.',
        139000,
        17,
        'assets/rok.svg'
    ),
    (
        4,
        'Aksesoris Pearl Set',
        'Set aksesoris bergaya minimalis.',
        79000,
        25,
        'assets/aksesoris.svg'
    );
