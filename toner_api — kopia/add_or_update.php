<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


require_once "db.php"; // połączenie z PDO

$data_raw = file_get_contents("php://input");
$data = json_decode($data_raw, true);

if (!$data) {
    echo json_encode(["error" => "Brak danych JSON."]);
    exit;
}

$barcode = $data['barcode'] ?? '';
$model = $data['model'] ?? '';
$location = $data['location'] ?? '';
$quantity = isset($data['quantity']) ? intval($data['quantity']) : 1;

if (!$barcode || !$model || !$location) {
    echo json_encode(["error" => "Brakuje wymaganych pól (barcode/model/location)."]);
    exit;
}

// Normalizacja lokalizacji: R1-P2-C -> R12C
function normalizeLocation($loc) {
    return str_replace(['-','P'], '', $loc);
}

$location = normalizeLocation($location);

try {
    // Sprawdzenie czy rekord istnieje
    $stmt = $pdo->prepare("SELECT * FROM toners WHERE barcode=? AND model=? AND location=?");
    $stmt->execute([$barcode, $model, $location]);
    $row = $stmt->fetch();

    if ($row) {
        // Aktualizacja ilości
        $new_quantity = $row['quantity'] + $quantity;
        $update = $pdo->prepare("UPDATE toners SET quantity=?, updated_at=CURRENT_TIMESTAMP WHERE id=?");
        $update->execute([$new_quantity, $row['id']]);
        echo json_encode(["updated" => true, "new_quantity" => $new_quantity]);
        exit;
    }

    // Dodanie nowego rekordu
    $insert = $pdo->prepare("INSERT INTO toners (barcode, model, location, quantity) VALUES (?,?,?,?)");
    $insert->execute([$barcode, $model, $location, $quantity]);

    echo json_encode(["inserted" => true, "quantity" => $quantity]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Błąd bazy danych: '.$e->getMessage()]);
}
