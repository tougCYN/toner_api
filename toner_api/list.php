<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once "db.php";

try {
    $stmt = $pdo->query("SELECT * FROM toners ORDER BY location ASC");
    $toners = $stmt->fetchAll();

    echo json_encode($toners);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Błąd bazy danych: '.$e->getMessage()]);
}
