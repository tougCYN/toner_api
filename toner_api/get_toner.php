<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'db.php';

$barcode = $_GET['barcode'] ?? '';
if (!$barcode) {
    echo json_encode(['error'=>'Brak barcode w query']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM toners WHERE barcode = ?");
    $stmt->execute([$barcode]);
    $rows = $stmt->fetchAll();

    if ($rows) {
        echo json_encode(['found'=>true,'toners'=>$rows]);
    } else {
        echo json_encode(['found'=>false,'toners'=>[]]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Błąd bazy danych: '.$e->getMessage()]);
}
