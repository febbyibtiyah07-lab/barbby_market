
<?php
require __DIR__ . '/koneksi.php';

$page_title = "Katalog Produk | Barb'by Market";

$q = $koneksi->query("
    SELECT p.*, k.nama_kategori
    FROM produk p
    LEFT JOIN kategori k
        ON p.id_kategori = k.id_kategori
    ORDER BY p.id_produk DESC
");

// Menentukan gambar sesuai nama produk
function gambarProduk($produk)
{
    $nama = strtolower($produk['nama_produk']);

    $daftarGambar = [
        'aksesoris' => 'assets/Aksesoris pearl set.jpeg',
        'pearl'    => 'assets/Aksesoris pearl set.jpeg',
        'rok'      => 'assets/skirt.jpeg',
        'skirt'    => 'assets/skirt.jpeg',
        'sneakers' => 'assets/Sneakers.jpeg',
        'handbag'  => 'assets/Handbag.jpeg',
        'tas'      => 'assets/Handbag.jpeg',
        'cardigan' => 'assets/Cardigan.jpeg',
        'blouse'   => 'assets/blous.jpeg'
    ];

    // Utamakan gambar yang tersimpan di database
    $gambar = trim($produk['gambar'] ?? '');

    if (
        $gambar !== '' &&
        preg_match(
            '~^assets/[a-zA-Z0-9 _-]+\.(jpeg|jpg|png|webp|svg)$~i',
            $gambar
        ) &&
        is_file(__DIR__ . '/' . $gambar)
    ) {
        return $gambar;
    }

    // Gunakan gambar baru sesuai nama produk
    foreach ($daftarGambar as $kata => $file) {
        if (
            strpos($nama, $kata) !== false &&
            is_file(__DIR__ . '/' . $file)
        ) {
            return $file;
        }
    }

    // Gambar cadangan
    return 'assets/blous.jpeg';
}

require __DIR__ . '/partials/header.php';
?>

<main class="container py-5">

    <div class="text-center mb-5">
        <p class="text-uppercase fw-semibold"
           style="color:#a64d79">
            Barb'by Collection
        </p>

        <h1 class="fw-bold">
            Shop Our Collection
        </h1>

        <p class="text-muted">
            Temukan fashion favoritmu di Barb'by Market
        </p>
    </div>

    <div class="row g-4">

        <?php while ($p = $q->fetch_assoc()): ?>

            <div class="col-sm-6 col-lg-4">

                <div class="card product-card shadow-sm h-100">

                    <img
                        src="<?= e(gambarProduk($p)) ?>"
                        class="product-photo"
                        alt="<?= e($p['nama_produk']) ?>"
                        loading="lazy"
                    >

                    <div class="card-body">

                        <span class="badge badge-pink mb-2">
                            <?= e($p['nama_kategori'] ?? 'Fashion') ?>
                        </span>

                        <h5 class="fw-bold">
                            <?= e($p['nama_produk']) ?>
                        </h5>

                        <p class="price fs-5">
                            <?= money($p['harga']) ?>
                        </p>

                        <p class="small text-muted">
                            Stok: <?= (int)$p['stok'] ?>
                        </p>

                        <a
                            href="detail.php?id=<?= (int)$p['id_produk'] ?>"
                            class="btn btn-pink w-100"
                        >
                            Lihat Detail
                        </a>

                    </div>
                </div>

            </div>

        <?php endwhile; ?>

    </div>

</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
