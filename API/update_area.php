<?php
require_once 'db.php';

header('Content-Type: application/json');

$id = $_GET['id'] ?? null;
if (!$id) {
    echo json_encode(["error" => "Missing ID"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(["error" => "Missing data"]);
    exit;
}

$name = $data['name'] ?? null;
$location = $data['location'] ?? null;
$crop = $data['crop'] ?? null;
$area_size = $data['area_size'] ?? null;

if (!$name || !$location || !$crop || !$area_size) {
    echo json_encode(["error" => "Missing required fields"]);
    exit;
}

$stmt = $pdo->prepare("UPDATE planting_areas SET name = ?, location = ?, crop = ?, area_size = ? WHERE id = ?");
$success = $stmt->execute([$name, $location, $crop, $area_size, $id]);

if ($success) {
    echo json_encode(["message" => "Area updated"]);
} else {
    echo json_encode(["error" => "Update failed"]);
}
?>
