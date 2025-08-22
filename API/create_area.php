<?php
require_once 'db.php';

// Nhận dữ liệu JSON từ body
$data = json_decode(file_get_contents("php://input"), true);

// Kiểm tra dữ liệu hợp lệ
if (!isset($data['name'], $data['location'], $data['crop'], $data['area_size'])) {
    echo json_encode(["error" => "Thiếu dữ liệu"]);
    exit;
}

// Chuẩn bị truy vấn
$stmt = $pdo->prepare("INSERT INTO planting_areas (name, location, crop, area_size) VALUES (?, ?, ?, ?)");
$success = $stmt->execute([
    $data['name'],
    $data['location'],
    $data['crop'],
    $data['area_size']
]);

// Lấy ID vừa tạo
$lastId = $pdo->lastInsertId();

// Phản hồi
if ($success) {
    echo json_encode([
        "message" => "Thêm vùng trồng thành công",
        "id" => $lastId
    ]);
} else {
    echo json_encode(["error" => "Thêm thất bại"]);
}
?>
