<?php
require_once 'db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if ($data && isset($data['name'], $data['location'], $data['crop'], $data['area_size'])) {
    $stmt = $pdo->prepare("INSERT INTO planting_areas (name, location, crop, area_size) VALUES (?, ?, ?, ?)");
    $success = $stmt->execute([$data['name'], $data['location'], $data['crop'], $data['area_size']]);
    if ($success) {
        $lastId = $pdo->lastInsertId();
        echo json_encode(["message" => "Area created", "id" => $lastId]);
    } else {
        echo json_encode(["error" => "Failed to create area"]);
    }
} else {
    echo json_encode(["error" => "Invalid or missing fields"]);
}
?>
