<?php
header("Content-Type: application/json; charset=UTF-8");

// Kết nối cơ sở dữ liệu
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

// Lấy dữ liệu JSON từ body
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !is_array($data)) {
    echo json_encode(["status" => "error", "message" => "Dữ liệu không hợp lệ."]);
    exit();
}

$added = [];
$errors = [];

foreach ($data as $index => $item) {
    $MaC = isset($item['MaC']) ? trim($item['MaC']) : '';
    $TenC = isset($item['TenC']) ? trim($item['TenC']) : '';
    $CSD = isset($item['CSD']) ? trim($item['CSD']) : '';

    if (empty($MaC) || empty($TenC)) {
        $errors[] = "Thiếu mã cây hoặc tên cây tại mục index $index.";
        continue;
    }

    // Kiểm tra trùng mã cây
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM cay_trong WHERE ma_cay = :MaC");
    $checkStmt->bindParam(':MaC', $MaC);
    $checkStmt->execute();

    if ($checkStmt->fetchColumn() > 0) {
        $errors[] = "Mã cây $MaC đã tồn tại.";
        continue;
    }

    // Thêm cây trồng
    try {
        $stmt = $conn->prepare("INSERT INTO cay_trong (ma_cay, ten_cay, cach_su_dung) VALUES (:MaC, :TenC, :CSD)");
        $stmt->bindParam(':MaC', $MaC);
        $stmt->bindParam(':TenC', $TenC);
        $stmt->bindParam(':CSD', $CSD);
        $stmt->execute();

        $added[] = ["MaC" => $MaC, "TenC" => $TenC, "CSD" => $CSD];
    } catch (PDOException $e) {
        $errors[] = "Lỗi khi thêm $MaC: " . $e->getMessage();
    }
}

echo json_encode([
    "status" => "completed",
    "added" => $added,
    "errors" => $errors
]);
?>
