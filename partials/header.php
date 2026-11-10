
<?php
$base = $base ?? '';
?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width,initial-scale=1">

    <title>
        <?= e($page_title ?? "Barb'by Market") ?>
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="<?= $base ?>assets/style.css"
        rel="stylesheet"
    >
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark py-3">
        <div class="container">

            <a
                class="navbar-brand fw-bold"
                href="<?= $base ?>index.php"
            >
                ✿ Barb'by Market
            </a>

            <div class="ms-auto d-flex gap-2">

                <a
                    class="btn btn-outline-light btn-sm"
                    href="<?= $base ?>produk.php"
                >
                    Produk
                </a>

                <a
                    class="btn btn-light btn-sm"
                    href="<?= $base ?>keranjang.php"
                >
                    Keranjang (<?= cart_count() ?>)
                </a>

                <a
                    class="btn btn-outline-light btn-sm"
                    href="<?= $base ?>admin/login.php"
                >
                    Admin
                </a>

            </div>
        </div>
    </nav>
