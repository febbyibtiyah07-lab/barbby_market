
<?php
require __DIR__ . '/koneksi.php';

$id = (int)($_GET['id'] ?? 0);
$token = $_GET['token'] ?? '';

if (
    !$id ||
    !hash_equals(
        hash_hmac('sha256', (string)$id, session_id()),
        $token
    )
) {
    http_response_code(403);
    exit('Akses tidak valid.');
}

$st = $koneksi->prepare(
    'SELECT id_pesanan,total_harga,status FROM pesanan WHERE id_pesanan=?'
);

$st->bind_param('i', $id);
$st->execute();

$p = $st->get_result()->fetch_assoc();

if (!$p) {
    http_response_code(404);
    exit('Pesanan tidak ditemukan.');
}

$page_title = 'Pesanan Berhasil';

require __DIR__ . '/partials/header.php';
?>

<main class="container py-5 text-center">
    <div class="soft-card p-5">
        <h1>✓ Pesanan Berhasil!</h1>

        <p>
            Nomor pesanan:
            <strong>#<?= (int)$p['id_pesanan'] ?></strong>
        </p>

        <p>
            Total:
            <strong><?= money($p['total_harga']) ?></strong>
        </p>

        <p>
            Status: <?= e($p['status']) ?>
        </p>

        <a class="btn btn-pink" href="index.php">
            Kembali Belanja
        </a>
    </div>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
