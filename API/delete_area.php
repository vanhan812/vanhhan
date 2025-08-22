<?php
require_once 'db.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM planting_areas WHERE id=?");
    $stmt->execute([$id]);
    echo json_encode(["message" => "Area deleted"]);
} else {
    echo json_encode(["error" => "Missing ID"]);
}
?>
