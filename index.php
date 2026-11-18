
<?php
require __DIR__ . '/koneksi.php';

$page_title = "Barb'by Market | Your Style, Your Rules";

$q = $koneksi->query(
    'SELECT p.*, k.nama_kategori
     FROM produk p
     LEFT JOIN kategori k
       ON p.id_kategori = k.id_kategori
     ORDER BY p.id_produk DESC'
);

function gambarBeranda($produk)
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

    foreach ($daftarGambar as $kata => $file) {
        if (
            strpos($nama, $kata) !== false &&
            is_file(__DIR__ . '/' . $file)
        ) {
            return $file;
        }
    }

    return 'assets/blous.jpeg';
}

require __DIR__ . '/partials/header.php';
?>

<!-- HERO SECTION -->
<section class="chic-hero">
    <div class="container">
        <div class="row align-items-center g-4">

            <div class="col-lg-6">
                <span class="chic-eyebrow">
                    ✦ YOUR DAILY FASHION EDIT
                </span>

                <h1 class="chic-heading">
                    Your Style,<br>
                    <em>Your Rules.</em>
                </h1>

                <p class="chic-description">
                    Fashion is all about expressing who you are.
                    Temukan outfit dan aksesori favoritmu
                    untuk setiap mood dan momen.
                </p>

                <div class="d-flex flex-wrap gap-3">
                    <a href="#produk" class="chic-btn">
                        SHOP THE COLLECTION ↗
                    </a>

                    <a href="#kategori" class="chic-outline-btn">
                        Explore Categories
                    </a>
                </div>

                <div class="chic-hero-note">
                    <span>✦</span>
                    Cute finds. Confident vibes. Everyday style.
                </div>
            </div>

            <div class="col-lg-6">
                <div class="chic-visual">
                    <div class="chic-visual-back"></div>

                    <img
                        src="assets/Cardigan.jpeg"
                        alt="Fashion collection Barb'by Market"
                        class="chic-hero-image"
                    >

                    <div class="chic-sticker">
                        NEW<br>VIBES!
                    </div>

                    <div class="chic-image-label">
                        THE STYLE EDIT / 2026
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- RUNNING STYLE STRIP -->
<div class="chic-strip">
    <div class="container">
        ✦ BE BOLD &nbsp;&nbsp; ✦ BE YOU
        &nbsp;&nbsp; ✦ STAY CHIC
        &nbsp;&nbsp; ✦ BARB'BY MARKET
    </div>
</div>

<!-- CATEGORY SECTION -->
<section class="chic-categories" id="kategori">
    <div class="container">

        <div class="chic-section-heading">
            <div>
                <span class="chic-overline">
                    FIND YOUR VIBE
                </span>

                <h2>
                    Shop by <em>Category</em>
                </h2>
            </div>

            <p>
                Semua yang kamu butuhkan
                untuk melengkapi gayamu.
            </p>
        </div>

        <div class="row g-3">

            <div class="col-6 col-lg-3">
                <a href="#produk" class="chic-category-card">
                    <img
                        src="assets/blous.jpeg"
                        alt="Kategori pakaian"
                    >
                    <div class="chic-category-label">
                        <span>Pakaian</span>
                        <span>↗</span>
                    </div>
                </a>
            </div>

            <div class="col-6 col-lg-3">
                <a href="#produk" class="chic-category-card">
                    <img
                        src="assets/Handbag.jpeg"
                        alt="Kategori tas"
                    >
                    <div class="chic-category-label">
                        <span>Tas</span>
                        <span>↗</span>
                    </div>
                </a>
            </div>

            <div class="col-6 col-lg-3">
                <a href="#produk" class="chic-category-card">
                    <img
                        src="assets/Sneakers.jpeg"
                        alt="Kategori sepatu"
                    >
                    <div class="chic-category-label">
                        <span>Sepatu</span>
                        <span>↗</span>
                    </div>
                </a>
            </div>

            <div class="col-6 col-lg-3">
                <a href="#produk" class="chic-category-card">
                    <img
                        src="assets/Aksesoris pearl set.jpeg"
                        alt="Kategori aksesoris"
                    >
                    <div class="chic-category-label">
                        <span>Aksesoris</span>
                        <span>↗</span>
                    </div>
                </a>
            </div>

        </div>
    </div>
</section>

<!-- PRODUCT SECTION -->
<section class="chic-products" id="produk">
    <div class="container">

        <div class="chic-section-heading">
            <div>
                <span class="chic-overline">
                    CURATED JUST FOR YOU
                </span>

                <h2>
                    The <em>Style Edit</em>
                </h2>
            </div>

            <p>
                Pilihan fashion favorit
                untuk tampil percaya diri.
            </p>
        </div>

        <div class="row g-4">

            <?php while ($p = $q->fetch_assoc()): ?>

                <div class="col-6 col-lg-4">
                    <article class="chic-product-card">

                        <a
                            class="chic-product-image"
                            href="detail.php?id=<?= (int)$p['id_produk'] ?>"
                        >
                            <img
                                src="<?= e(gambarBeranda($p)) ?>"
                                alt="<?= e($p['nama_produk']) ?>"
                                loading="lazy"
                            >

                            <span class="chic-product-tag">
                                STYLE PICK
                            </span>
                        </a>

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

                            <a
                                class="chic-product-link"
                                href="detail.php?id=<?= (int)$p['id_produk'] ?>"
                            >
                                LIHAT DETAIL
                                <span>↗</span>
                            </a>
                        </div>

                    </article>
                </div>

            <?php endwhile; ?>

        </div>
    </div>
</section>

<!-- FASHION STATEMENT -->
<section class="chic-statement">
    <div class="container text-center">

        <span class="chic-overline">
            A LITTLE REMINDER
        </span>

        <h2>
            Life's too short<br>
            to wear <em>boring outfits.</em>
        </h2>

        <p>
            Wear what makes you feel amazing.
            Your confidence is your best accessory.
        </p>

        <a href="#produk" class="chic-btn chic-btn-light">
            FIND YOUR NEXT LOOK ↗
        </a>

    </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
