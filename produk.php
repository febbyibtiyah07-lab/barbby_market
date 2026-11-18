
<?php
require __DIR__ . '/koneksi.php';

// =====================================
// AMBIL KATEGORI DARI URL
// =====================================

$kategori = filter_input(
    INPUT_GET,
    'kategori',
    FILTER_VALIDATE_INT
);

$kategori = $kategori ?: 0;
$namaKategori = 'Semua Koleksi';

// =====================================
// AMBIL NAMA KATEGORI
// =====================================

if ($kategori > 0) {

    $stmtKategori = $koneksi->prepare(
        "SELECT nama_kategori
         FROM kategori
         WHERE id_kategori = ?"
    );

    $stmtKategori->bind_param('i', $kategori);
    $stmtKategori->execute();

    $hasilKategori = $stmtKategori
        ->get_result()
        ->fetch_assoc();

    if ($hasilKategori) {
        $namaKategori = $hasilKategori['nama_kategori'];
    } else {
        $namaKategori = 'Kategori Tidak Ditemukan';
    }
}

// =====================================
// AMBIL DATA PRODUK
// =====================================

if ($kategori > 0) {

    $stmt = $koneksi->prepare(
        "SELECT p.*, k.nama_kategori
         FROM produk p
         LEFT JOIN kategori k
           ON p.id_kategori = k.id_kategori
         WHERE p.id_kategori = ?
         ORDER BY p.id_produk DESC"
    );

    $stmt->bind_param('i', $kategori);
    $stmt->execute();

    $q = $stmt->get_result();

} else {

    $q = $koneksi->query(
        "SELECT p.*, k.nama_kategori
         FROM produk p
         LEFT JOIN kategori k
           ON p.id_kategori = k.id_kategori
         ORDER BY p.id_produk DESC"
    );
}

// =====================================
// FUNGSI GAMBAR PRODUK
// =====================================

function gambarProduk($produk)
{
    $nama = strtolower($produk['nama_produk']);

    // Prioritaskan gambar dari database
    $gambar = trim($produk['gambar'] ?? '');

    // Mendukung assets dan assets/uploads
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

    // Gambar default
    return 'assets/blous.jpeg';
}

// =====================================
// JUDUL HALAMAN
// =====================================

$page_title = $namaKategori . " | Barb'by Market";

require __DIR__ . '/partials/header.php';
?>

<!-- =====================================
     HALAMAN KATALOG
===================================== -->

<main class="chic-products">

    <div class="container">

        <!-- KEMBALI KE BERANDA -->
        <div class="mb-4">

            <a
                href="index.php"
                class="chic-outline-btn"
            >
                ← Kembali ke Beranda
            </a>

        </div>

        <!-- JUDUL -->
        <div class="chic-section-heading">

            <div>

                <span class="chic-overline">
                    BARB'BY COLLECTION
                </span>

                <h2>
                    <?= e($namaKategori) ?>
                </h2>

            </div>

            <p>
                Temukan fashion favoritmu
                dan tampil percaya diri.
            </p>

        </div>

        <!-- FILTER KATEGORI -->
        <div class="d-flex flex-wrap gap-2 mb-5">

            <a
                href="produk.php"
                class="chic-outline-btn"
            >
                Semua
            </a>

            <a
                href="produk.php?kategori=1"
                class="chic-outline-btn"
            >
                Pakaian
            </a>

            <a
                href="produk.php?kategori=2"
                class="chic-outline-btn"
            >
                Tas
            </a>

            <a
                href="produk.php?kategori=3"
                class="chic-outline-btn"
            >
                Sepatu
            </a>

            <a
                href="produk.php?kategori=4"
                class="chic-outline-btn"
            >
                Aksesoris
            </a>

        </div>

        <!-- DAFTAR PRODUK -->
        <div class="row g-4">

            <?php if ($q->num_rows > 0): ?>

                <?php while ($p = $q->fetch_assoc()): ?>

                    <div class="col-6 col-lg-4">

                        <article class="chic-product-card">

                            <!-- GAMBAR PRODUK -->
                            <a
                                href="detail.php?id=<?= (int)$p['id_produk'] ?>"
                                class="chic-product-image"
                            >

                                <img
                                    src="<?= e(gambarProduk($p)) ?>"
                                    alt="<?= e($p['nama_produk']) ?>"
                                    loading="lazy"
                                >

                                <span class="chic-product-tag">
                                    STYLE PICK
                                </span>

                            </a>

                            <!-- INFORMASI PRODUK -->
                            <div class="chic-product-info">

                                <span class="chic-product-category">
                                    <?= e($p['nama_kategori'] ?? 'Fashion') ?>
                                </span>

                                <h3>
                                    <?= e($p['nama_produk']) ?>
                                </h3>

                                <div class="chic-product-bottom">

                                    <strong>
                                        <?= money($p['harga']) ?>
                                    </strong>

                                    <span>
                                        Stok: <?= (int)$p['stok'] ?>
                                    </span>

                                </div>

                                <!-- DETAIL -->
                                <a
                                    href="detail.php?id=<?= (int)$p['id_produk'] ?>"
                                    class="chic-product-link"
                                >
                                    LIHAT DETAIL
                                    <span>↗</span>
                                </a>

                            </div>

                        </article>

                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <!-- JIKA PRODUK KOSONG -->
                <div class="col-12 text-center py-5">

                    <h3>
                        Belum ada produk
                    </h3>

                    <p class="text-muted">
                        Produk untuk kategori ini
                        belum tersedia.
                    </p>

                    <a
                        href="produk.php"
                        class="chic-btn"
                    >
                        Lihat Semua Produk
                    </a>

                </div>

            <?php endif; ?>

        </div>

    </div>

</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
