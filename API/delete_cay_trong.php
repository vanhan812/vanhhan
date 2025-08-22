<?php
header("Content-Type: application/json; charset=UTF-8");

// Kết nối CSDL
$host = "localhost";
$dbname = "farm";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Kết nối thất bại: " . $e->getMessage()]);
    exit();
}

// Lấy mã cây cần xóa từ POST (đặt tên giống phía client)
$ma_cay = isset($_POST['MaC']) ? trim($_POST['MaC']) : '';

if (empty($ma_cay)) {
    echo json_encode([
        "status" => "error",
        "message" => "Thiếu mã cây để xóa."
    ]);
    exit();
}

try {
    // Kiểm tra cây tồn tại
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM cay_trong WHERE ma_cay = :ma_cay");
    $checkStmt->bindParam(':ma_cay', $ma_cay);
    $checkStmt->execute();

    if ($checkStmt->fetchColumn() == 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Không tìm thấy cây trồng với mã: $ma_cay"
        ]);
        exit();
    }

    // Thực hiện xóa
    $stmt = $conn->prepare("DELETE FROM cay_trong WHERE ma_cay = :ma_cay");
    $stmt->bindParam(':ma_cay', $ma_cay);
    $stmt->execute();

    echo json_encode([
        "status" => "success",
        "message" => "Đã xóa cây trồng có mã: $ma_cay"
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Lỗi khi xóa: " . $e->getMessage()
    ]);
}
?>
