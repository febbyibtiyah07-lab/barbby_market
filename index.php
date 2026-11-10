
<?php
require __DIR__ . '/koneksi.php';

$page_title = "Barb'by Market | Koleksi Fashion";

$q = $koneksi->query(
    'SELECT p.*, k.nama_kategori
     FROM produk p
     LEFT JOIN kategori k
         ON p.id_kategori = k.id_kategori
     ORDER BY p.id_produk DESC'
);

// Menentukan gambar produk dari folder assets
function gambarBeranda($produk)
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

    // Gunakan gambar database jika filenya tersedia
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

    // Jika gambar lama tidak ditemukan,
    // cocokkan gambar berdasarkan nama produk
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

<section class="hero">
    <div class="container">

        <p class="text-uppercase fw-semibold">
            New season, new you
        </p>

        <h1 class="display-4">
            Welcome to Barb'by Market
        </h1>

        <p class="lead">
            Discover Your Style, Express Yourself
        </p>

        <a class="btn btn-pink btn-lg" href="#produk">
            Belanja Sekarang
        </a>

    </div>
</section>

<main class="container py-5" id="produk">

    <div class="text-center mb-4">

        <h2 class="fw-bold">
            Koleksi Fashion
        </h2>

        <p class="text-muted">
            Pilihan cantik untuk setiap gaya
        </p>

    </div>

    <div class="row g-4">

        <?php while ($p = $q->fetch_assoc()): ?>

            <div class="col-sm-6 col-lg-4">

                <div class="card product-card shadow-sm h-100">

                    <img
                        class="product-photo"
                        src="<?= e(gambarBeranda($p)) ?>"
                        alt="<?= e($p['nama_produk']) ?>"
                        loading="lazy"
                    >

                    <div class="card-body">

                        <span class="badge badge-pink mb-2">
                            <?= e($p['nama_kategori'] ?? 'Fashion') ?>
                        </span>

                        <h5>
                            <?= e($p['nama_produk']) ?>
                        </h5>

                        <p class="price fs-5">
                            <?= money($p['harga']) ?>
                        </p>

                        <p class="small text-muted">
                            Stok: <?= (int)$p['stok'] ?>
                        </p>

                        <a
                            class="btn btn-pink w-100"
                            href="detail.php?id=<?= (int)$p['id_produk'] ?>"
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
