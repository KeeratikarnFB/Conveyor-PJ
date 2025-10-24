<?php
session_start();
ob_start();
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

if (!isset($_SESSION["OfaEmpLogin"]) || $_SESSION["OfaEmpLogin"] == "") {
    echo json_encode(["status" => "error", "message" => "❌ Not logged in"]);
    exit;
}

include "inclu/myconfig.php";
$link->set_charset("utf8mb4");

// 🔹 ดึงข้อมูลล่าสุดของแต่ละ Serial (ไม่ให้ซ้ำ)
$sql = "SELECT cQRcode, cDeviceNo, note, MAX(seen_at) AS latest_seen
        FROM conveyor_qr_scan_dup
        GROUP BY cQRcode
        ORDER BY latest_seen DESC";

$result = $link->query($sql);

$dup_data = [];
$latest_data = null;

if ($result && $result->num_rows > 0) {
    $i = 0;
    while ($row = $result->fetch_assoc()) {
        $rawCode = $row['cQRcode'];
        $parts = explode("^", $rawCode);

        $job = isset($parts[1]) ? trim($parts[1]) : "-";
        $item = isset($parts[2]) ? trim($parts[2]) : "-";
        $ctn  = isset($parts[4]) ? trim($parts[4]) : "-";
        $formatted = "{$job} / {$item} / {$ctn}";

        $data = [
            'QRcode'   => $formatted,
            'device'   => $row['cDeviceNo'],
            'note'     => $row['note'],
            'seen_at'  => $row['latest_seen']
        ];

        if ($i == 0) {
            $latest_data = $data; // ✅ อันแรกคือค่าล่าสุดที่สุด
        } else {
            $dup_data[] = $data; // ✅ ส่วนที่เหลือคือ history แต่ละ serial แค่ 1 ค่า
        }
        $i++;
    }
}

echo json_encode([
    'latest' => $latest_data,
    'history' => $dup_data
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$link->close();
?>
