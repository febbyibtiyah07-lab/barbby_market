
<?php
require __DIR__ . '/koneksi_api.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Allow: GET');
    jsonResponse([
        'success' => false,
        'message' => 'Method tidak diizinkan. Gunakan GET.'
    ], 405);
}

try {
    $result = $koneksi->query(
        'SELECT id_kategori, nama_kategori
         FROM kategori
         ORDER BY id_kategori ASC'
    );

    $kategori = $result->fetch_all(MYSQLI_ASSOC);

    jsonResponse([
        'success' => true,
        'message' => 'Daftar kategori berhasil diambil.',
        'jumlah' => count($kategori),
        'data' => $kategori
    ]);

} catch (Throwable $e) {
    jsonResponse([
        'success' => false,
        'message' => 'Terjadi kesalahan saat mengambil kategori.'
    ], 500);
}
