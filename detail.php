
<?php
require __DIR__ . '/koneksi.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $koneksi->prepare(
    'SELECT p.*, k.nama_kategori
     FROM produk p
     LEFT JOIN kategori k
       ON p.id_kategori = k.id_kategori
     WHERE p.id_produk = ?'
);

$stmt->bind_param('i', $id);
$stmt->execute();

$p = $stmt->get_result()->fetch_assoc();

if (!$p) {
    http_response_code(404);
    exit('Produk tidak ditemukan.');
}

// ======================================
// GAMBAR PRODUK
// Mendukung assets dan assets/uploads
// ======================================

function gambarDetailProduk($produk)
{
    $gambar = trim($produk['gambar'] ?? '');
    $nama = strtolower($produk['nama_produk']);

    // Prioritaskan gambar database
    if (
        $gambar !== '' &&
        preg_match(
            '~^assets/(?:uploads/)?[a-zA-Z0-9 _.-]+\.(jpg|jpeg|png|webp|svg)$~i',
            $gambar
        ) &&
        is_file(__DIR__ . '/' . $gambar)
    ) {
        return $gambar;
    }

    // Gambar cadangan produk lama
    $daftarGambar = [
        'aksesoris' => 'assets/Aksesoris pearl set.jpeg',
        'pearl'     => 'assets/Aksesoris pearl set.jpeg',
        'rok'       => 'assets/skirt.jpeg',
        'skirt'     => 'assets/skirt.jpeg',
        'sneakers'  => 'assets/Sneakers.jpeg',
        'sepatu'    => 'assets/Sneakers.jpeg',
        'handbag'   => 'assets/Handbag.jpeg',
        'tas'       => 'assets/Handbag.jpeg',
        'cardigan'  => 'assets/Cardigan.jpeg',
        'blouse'    => 'assets/blous.jpeg'
    ];

    foreach ($daftarGambar as $kata => $file) {
        if (
            strpos($nama, $kata) !== false &&
            is_file(__DIR__ . '/' . $file)
        ) {
            return $file;
        }
    }

    return '';
}

$gambarProduk = gambarDetailProduk($p);
$page_title = $p['nama_produk'] . " | Barb'by Market";

require __DIR__ . '/partials/header.php';
?>

<main class="detail-chic-page">
    <div class="container">

        <!-- NAVIGASI -->
        <nav class="detail-chic-breadcrumb"
             aria-label="Navigasi produk">

            <a href="index.php" class="detail-crumb-link">
                <span>⌂</span> Beranda
            </a>

            <span class="detail-crumb-separator">›</span>

            <a href="produk.php" class="detail-crumb-link">
                Koleksi
            </a>

            <span class="detail-crumb-separator">›</span>

            <span class="detail-crumb-current">
                <?= e($p['nama_produk']) ?>
            </span>

        </nav>

        <div class="row g-5 align-items-center">

            <!-- FOTO PRODUK -->
            <div class="col-lg-6">

                <div class="detail-chic-image-wrap">

                    <?php if ($gambarProduk !== ''): ?>

                        <img
                            src="<?= e($gambarProduk) ?>"
                            alt="<?= e($p['nama_produk']) ?>"
                            class="detail-chic-image"
                        >

                    <?php else: ?>

                        <div class="detail-chic-no-image">
                            Gambar produk belum tersedia
                        </div>

                    <?php endif; ?>

                    <span class="detail-chic-image-label">
                        BARB'BY EDIT
                    </span>

                </div>

            </div>

            <!-- INFORMASI PRODUK -->
            <div class="col-lg-6">

                <div class="detail-chic-info">

                    <span class="detail-chic-overline">
                        BARB'BY MARKET COLLECTION
                    </span>

                    <div class="detail-chic-category">
                        <?= e($p['nama_kategori'] ?? 'Fashion') ?>
                    </div>

                    <h1 class="detail-chic-title">
                        <?= e($p['nama_produk']) ?>
                    </h1>

                    <div class="detail-chic-price">
                        <?= money($p['harga']) ?>
                    </div>

                    <div class="detail-chic-divider"></div>

                    <h3 class="detail-chic-subtitle">
                        Product Details
                    </h3>

                    <p class="detail-chic-description">
                        <?= nl2br(e($p['deskripsi'] ?? '')) ?>
                    </p>

                    <div class="detail-chic-stock">

                        <?php if ((int)$p['stok'] > 0): ?>

                            <span class="detail-chic-stock-dot"></span>

                            Tersedia — <?= (int)$p['stok'] ?> item

                        <?php else: ?>

                            <span class="text-danger">
                                Stok habis
                            </span>

                        <?php endif; ?>

                    </div>

                    <!-- TAMBAH KE KERANJANG -->
                    <?php if ((int)$p['stok'] > 0): ?>

                        <form
                            action="keranjang.php"
                            method="post"
                            class="detail-chic-form"
                        >

                            <input
                                type="hidden"
                                name="csrf"
                                value="<?= e(csrf()) ?>"
                            >

                            <input
                                type="hidden"
                                name="action"
                                value="add"
                            >

                            <input
                                type="hidden"
                                name="id_produk"
                                value="<?= (int)$p['id_produk'] ?>"
                            >

                            <label
                                for="jumlah"
                                class="detail-chic-qty-label"
                            >
                                Jumlah Produk
                            </label>

                            <div class="detail-chic-actions">

                                <input
                                    id="jumlah"
                                    type="number"
                                    name="jumlah"
                                    min="1"
                                    max="<?= (int)$p['stok'] ?>"
                                    value="1"
                                    required
                                    class="detail-chic-qty"
                                >

                                <button
                                    type="submit"
                                    class="detail-chic-cart-btn"
                                >
                                    <span>Tambah ke Keranjang</span>
                                    <span>↗</span>
                                </button>

                            </div>

                        </form>

                    <?php else: ?>

                        <button
                            type="button"
                            class="detail-chic-cart-btn"
                            disabled
                        >
                            Stok Habis
                        </button>

                    <?php endif; ?>

                    <!-- INFORMASI TAMBAHAN -->
                    <div class="detail-chic-benefits">

                        <div>
                            <span>♡</span>
                            Curated Fashion
                        </div>

                        <div>
                            <span>✦</span>
                            Chic Everyday Style
                        </div>

                    </div>

                    <a href="produk.php"
                       class="detail-chic-back">
                        ← Kembali Belanja
                    </a>

                </div>

            </div>

        </div>

    </div>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>

