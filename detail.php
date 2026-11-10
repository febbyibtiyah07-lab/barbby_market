
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

// Menentukan gambar produk dari folder assets
function gambarDetailProduk($produk)
{
    $nama = strtolower($produk['nama_produk']);

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

    // Gunakan gambar database jika file benar-benar tersedia
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

    // Jika gambar database tidak ditemukan,
    // cocokkan berdasarkan nama produk
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

$page_title = $p['nama_produk'];

require __DIR__ . '/partials/header.php';
?>

<main class="container py-5">

    <a href="index.php" class="text-decoration-none">
        ← Kembali ke koleksi
    </a>

    <div class="row g-5 mt-2">

        <div class="col-md-6">

            <?php if ($gambarProduk !== ''): ?>
                <img
                    class="detail-photo"
                    src="<?= e($gambarProduk) ?>"
                    alt="<?= e($p['nama_produk']) ?>"
                >
            <?php else: ?>
                <div class="soft-card p-5 text-center">
                    Gambar produk belum tersedia
                </div>
            <?php endif; ?>

        </div>

        <div class="col-md-6">

            <span class="badge badge-pink">
                <?= e($p['nama_kategori'] ?? 'Fashion') ?>
            </span>

            <h1 class="mt-3">
                <?= e($p['nama_produk']) ?>
            </h1>

            <h3 class="price">
                <?= money($p['harga']) ?>
            </h3>

            <p>
                <?= nl2br(e($p['deskripsi'])) ?>
            </p>

            <p>
                Stok tersedia:
                <b><?= (int)$p['stok'] ?></b>
            </p>

            <?php if ($p['stok'] > 0): ?>

                <form action="keranjang.php" method="post">

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
                        value="<?= $id ?>"
                    >

                    <label class="form-label">
                        Jumlah
                    </label>

                    <input
                        class="form-control mb-3"
                        style="max-width:120px"
                        type="number"
                        name="jumlah"
                        min="1"
                        max="<?= (int)$p['stok'] ?>"
                        value="1"
                        required
                    >

                    <button type="submit" class="btn btn-pink">
                        Tambah ke Keranjang
                    </button>

                </form>

            <?php else: ?>

                <p class="text-danger">
                    Stok habis
                </p>

            <?php endif; ?>

        </div>
    </div>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
