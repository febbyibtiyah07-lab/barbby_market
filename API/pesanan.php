
<?php
require_once __DIR__ . '/koneksi_api.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');

    jsonResponse([
        'success' => false,
        'message' => 'Gunakan HTTP POST.'
    ], 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!is_array($input)) {
    jsonResponse([
        'success' => false,
        'message' => 'Body harus berupa JSON yang valid.'
    ], 400);
}

$nama = trim($input['nama_pembeli'] ?? '');
$email = trim($input['email'] ?? '');
$telepon = trim($input['telepon'] ?? '');
$alamat = trim($input['alamat'] ?? '');
$items = $input['items'] ?? [];

if (
    $nama === '' ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    $telepon === '' ||
    $alamat === '' ||
    !is_array($items) ||
    count($items) === 0
) {
    jsonResponse([
        'success' => false,
        'message' => 'Data pembeli atau item pesanan tidak valid.'
    ], 400);
}

$transaksiDimulai = false;

try {
    $koneksi->begin_transaction();
    $transaksiDimulai = true;

    $total = 0;
    $detail = [];

    $cek = $koneksi->prepare(
        'SELECT id_produk, nama_produk, harga, stok
         FROM produk
         WHERE id_produk = ?
         FOR UPDATE'
    );

    foreach ($items as $item) {
        $id = filter_var(
            $item['id_produk'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        $jumlah = filter_var(
            $item['jumlah'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if (!$id || !$jumlah) {
            throw new InvalidArgumentException(
                'ID produk atau jumlah tidak valid.'
            );
        }

        $cek->bind_param('i', $id);
        $cek->execute();

        $produk = $cek->get_result()->fetch_assoc();

        if (!$produk) {
            throw new InvalidArgumentException(
                'Produk dengan ID ' . $id . ' tidak ditemukan.'
            );
        }

        if ($jumlah > (int)$produk['stok']) {
            throw new InvalidArgumentException(
                'Stok produk ' . $produk['nama_produk'] . ' tidak cukup.'
            );
        }

        $harga = (float)$produk['harga'];
        $subtotal = $harga * $jumlah;
        $total += $subtotal;

        $detail[] = [
            'id_produk' => $id,
            'nama_produk' => $produk['nama_produk'],
            'harga' => $harga,
            'jumlah' => $jumlah,
            'subtotal' => $subtotal
        ];
    }

    // Tolak ID produk yang muncul lebih dari sekali.
    $ids = array_column($detail, 'id_produk');

    if (count($ids) !== count(array_unique($ids))) {
        throw new InvalidArgumentException(
            'Produk yang sama tidak boleh dikirim dua kali.'
        );
    }

    $status = 'baru';

    $simpanPesanan = $koneksi->prepare(
        'INSERT INTO pesanan
         (nama_pembeli, email, telepon, alamat, total_harga, status)
         VALUES (?, ?, ?, ?, ?, ?)'
    );

    $simpanPesanan->bind_param(
        'ssssds',
        $nama,
        $email,
        $telepon,
        $alamat,
        $total,
        $status
    );

    $simpanPesanan->execute();

    $idPesanan = $koneksi->insert_id;

    $simpanDetail = $koneksi->prepare(
        'INSERT INTO detail_pesanan
         (id_pesanan, id_produk, nama_produk, harga, jumlah, subtotal)
         VALUES (?, ?, ?, ?, ?, ?)'
    );

    $kurangiStok = $koneksi->prepare(
        'UPDATE produk
         SET stok = stok - ?
         WHERE id_produk = ? AND stok >= ?'
    );

    foreach ($detail as $d) {
        $idProduk = $d['id_produk'];
        $namaProduk = $d['nama_produk'];
        $harga = $d['harga'];
        $jumlah = $d['jumlah'];
        $subtotal = $d['subtotal'];

        $simpanDetail->bind_param(
            'iisdid',
            $idPesanan,
            $idProduk,
            $namaProduk,
            $harga,
            $jumlah,
            $subtotal
        );

        $simpanDetail->execute();

        $kurangiStok->bind_param(
            'iii',
            $jumlah,
            $idProduk,
            $jumlah
        );

        $kurangiStok->execute();

        if ($kurangiStok->affected_rows !== 1) {
            throw new InvalidArgumentException(
                'Stok tidak mencukupi.'
            );
        }
    }

    $koneksi->commit();
    $transaksiDimulai = false;

    jsonResponse([
        'success' => true,
        'message' => 'Pesanan berhasil dibuat.',
        'id_pesanan' => $idPesanan,
        'total_harga' => $total,
        'status' => $status,
        'items' => $detail
    ], 201);

} catch (InvalidArgumentException $e) {
    if ($transaksiDimulai) {
        $koneksi->rollback();
    }

    jsonResponse([
        'success' => false,
        'message' => $e->getMessage()
    ], 400);

} catch (Throwable $e) {
    if ($transaksiDimulai) {
        $koneksi->rollback();
    }

    jsonResponse([
        'success' => false,
        'message' => 'Gagal menyimpan pesanan.'
    ], 500);
}
