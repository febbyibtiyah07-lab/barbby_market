
<?php
require __DIR__ . '/../koneksi.php';

require_admin();

$id = (int)($_GET['id'] ?? 0);

$p = [
    'nama_produk' => '',
    'id_kategori' => '',
    'harga' => '',
    'stok' => '',
    'deskripsi' => '',
    'gambar' => ''
];

if ($id > 0) {
    $st = $koneksi->prepare(
        'SELECT * FROM produk WHERE id_produk=?'
    );

    $st->bind_param('i', $id);
    $st->execute();

    $p = $st->get_result()->fetch_assoc();

    if (!$p) {
        http_response_code(404);
        exit('Produk tidak ditemukan.');
    }
}

// Daftar gambar yang sudah tersedia di folder assets
$gambarProduk = [
    'assets/Aksesoris pearl set.jpeg',
    'assets/blous.jpeg',
    'assets/Cardigan.jpeg',
    'assets/Handbag.jpeg',
    'assets/skirt.jpeg',
    'assets/Sneakers.jpeg'
];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim($_POST['nama_produk'] ?? '');
    $cat = (int)($_POST['id_kategori'] ?? 0);
    $price = (float)($_POST['harga'] ?? 0);
    $stock = (int)($_POST['stok'] ?? -1);
    $desc = trim($_POST['deskripsi'] ?? '');
    $img = trim($_POST['gambar'] ?? '');

    // Izinkan gambar lokal dalam format umum
    $gambarValid = (
        $img === '' ||
        (
            strlen($img) <= 255 &&
            preg_match(
                '~^assets/[a-zA-Z0-9 _-]+\.(jpeg|jpg|png|webp|svg)$~i',
                $img
            ) &&
            is_file(__DIR__ . '/../' . $img)
        )
    );

    if (
        $name === '' ||
        $cat < 1 ||
        !is_numeric($_POST['harga'] ?? null) ||
        $price < 0 ||
        $stock < 0 ||
        !$gambarValid
    ) {
        $error = 'Data tidak valid. Pastikan gambar tersedia di folder assets.';
    } else {
        if ($id > 0) {
            $st = $koneksi->prepare(
                'UPDATE produk
                 SET nama_produk=?,
                     id_kategori=?,
                     harga=?,
                     stok=?,
                     deskripsi=?,
                     gambar=?
                 WHERE id_produk=?'
            );

            $st->bind_param(
                'sidissi',
                $name,
                $cat,
                $price,
                $stock,
                $desc,
                $img,
                $id
            );
        } else {
            $st = $koneksi->prepare(
                'INSERT INTO produk
                 (nama_produk,id_kategori,harga,stok,deskripsi,gambar)
                 VALUES (?,?,?,?,?,?)'
            );

            $st->bind_param(
                'sidiss',
                $name,
                $cat,
                $price,
                $stock,
                $desc,
                $img
            );
        }

        $st->execute();

        redirect('index.php');
    }
}

$cats = $koneksi->query(
    'SELECT * FROM kategori ORDER BY nama_kategori'
);

$base = '../';
$page_title = $id ? 'Edit Produk' : 'Tambah Produk';

require __DIR__ . '/../partials/header.php';

$gambarTerpilih = $_POST['gambar'] ?? $p['gambar'];
?>

<main class="container py-5" style="max-width:750px">

    <h2 class="fw-bold mb-4">
        <?= e($page_title) ?>
    </h2>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <form method="post" class="soft-card p-4">

        <input
            type="hidden"
            name="csrf"
            value="<?= e(csrf()) ?>"
        >

        <label class="form-label">Nama Produk</label>
        <input
            class="form-control mb-3"
            name="nama_produk"
            value="<?= e($_POST['nama_produk'] ?? $p['nama_produk']) ?>"
            required
        >

        <label class="form-label">Kategori</label>
        <select
            class="form-select mb-3"
            name="id_kategori"
            required
        >
            <?php while ($c = $cats->fetch_assoc()): ?>
                <option
                    value="<?= (int)$c['id_kategori'] ?>"
                    <?= (
                        (int)($_POST['id_kategori'] ?? $p['id_kategori'])
                        === (int)$c['id_kategori']
                    ) ? 'selected' : '' ?>
                >
                    <?= e($c['nama_kategori']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label class="form-label">Harga</label>
        <input
            class="form-control mb-3"
            type="number"
            step="0.01"
            min="0"
            name="harga"
            value="<?= e($_POST['harga'] ?? $p['harga']) ?>"
            required
        >

        <label class="form-label">Stok</label>
        <input
            class="form-control mb-3"
            type="number"
            min="0"
            name="stok"
            value="<?= e($_POST['stok'] ?? $p['stok']) ?>"
            required
        >

        <label class="form-label">
            Pilih Gambar Produk
        </label>

        <select
            class="form-select mb-3"
            name="gambar"
            id="gambar"
        >
            <option value="">
                -- Tanpa gambar --
            </option>

            <?php
            // Pertahankan gambar lama jika tidak ada di daftar
            if (
                $gambarTerpilih !== '' &&
                !in_array($gambarTerpilih, $gambarProduk, true)
            ):
            ?>
                <option
                    value="<?= e($gambarTerpilih) ?>"
                    selected
                >
                    <?= e($gambarTerpilih) ?> (gambar sebelumnya)
                </option>
            <?php endif; ?>

            <?php foreach ($gambarProduk as $gambar): ?>
                <option
                    value="<?= e($gambar) ?>"
                    <?= $gambarTerpilih === $gambar ? 'selected' : '' ?>
                >
                    <?= e(basename($gambar)) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label class="form-label">Deskripsi</label>
        <textarea
            class="form-control mb-3"
            name="deskripsi"
            rows="4"
        ><?= e($_POST['deskripsi'] ?? $p['deskripsi']) ?></textarea>

        <button type="submit" class="btn btn-pink">
            Simpan Produk
        </button>

        <a href="index.php" class="btn btn-outline-secondary">
            Batal
        </a>

    </form>

</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>
