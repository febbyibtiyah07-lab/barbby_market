
<?php
header('Content-Type: application/json; charset=utf-8');

function jsonResponse($data, $status = 200)
{
    http_response_code($status);

    echo json_encode(
        $data,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    );

    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $koneksi = new mysqli(
    "localhost",
    "root",
    "",
    "barbby_market_app"
    );

    $koneksi->set_charset("utf8mb4");

} catch (Throwable $e) {
    jsonResponse([
        "success" => false,
        "message" => "Koneksi database gagal.",
        "error" => $e->getMessage()
    ], 500);
}