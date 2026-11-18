
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/koneksi_api.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Allow: GET');
    jsonResponse([
        'success' => false,
        'message' => 'Method tidak diizinkan. Gunakan GET.'
    ], 405);
}

try {
    if (isset($_GET['id'])) {
        $id = filter_var(
            $_GET['id'],
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if ($id === false) {
            jsonResponse([
                'success' => false,
                'message' => 'ID produk tidak valid.'
            ], 400);
        }

        $stmt = $koneksi->prepare(
            'SELECT p.*, k.nama_kategori
             FROM produk p
             LEFT JOIN kategori k
             ON p.id_kategori = k.id_kategori
             WHERE p.id_produk = ?'
        );

        $stmt->bind_param('i', $id);
        $stmt->execute();

        $produk = $stmt->get_result()->fetch_assoc();

        if (!$produk) {
            jsonResponse([
                'success' => false,
                'message' => 'Produk tidak ditemukan.'
            ], 404);
        }

        jsonResponse([
            'success' => true,
            'message' => 'Detail produk berhasil diambil.',
            'data' => $produk
        ]);
    }

    $result = $koneksi->query(
        'SELECT p.*, k.nama_kategori
         FROM produk p
         LEFT JOIN kategori k
         ON p.id_kategori = k.id_kategori
         ORDER BY p.id_produk DESC'
    );

    $produk = $result->fetch_all(MYSQLI_ASSOC);

    jsonResponse([
        'success' => true,
        'message' => 'Daftar produk berhasil diambil.',
        'jumlah' => count($produk),
        'data' => $produk
    ]);

} catch (Throwable $e) {
    jsonResponse([
        'success' => false,
        'message' => 'Terjadi kesalahan saat mengambil produk.'
    ], 500);
}
