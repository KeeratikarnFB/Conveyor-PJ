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

$last_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

// ดึงข้อมูลล่าสุด
$sql = "SELECT * FROM conveyor_qr_scan ORDER BY cId DESC LIMIT 1";
$result = $link->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $current_id = (int)$row['cId'];

  $response = [
    'id' => $current_id,
    'job' => $row['cJb'],
    'item' => $row['cItem'] ?? '-',
    'qty' => $row['cQty'] ?? '-',
    'CTN' => $row['cCtn'] ?? '-',   // เพิ่ม ?? '-' กัน NULL
    'machine' => $row['cMc'] ?? '-',
    'device' => $row['cDeviceNo'] ?? '-',
    'datetime' => $row['cDatetime'] ?? '-'
];



    // ตรวจสอบว่าข้อมูลซ้ำหรือไม่
    if ($last_id > 0 && $current_id == $last_id) {
        $response['repeat'] = true;
    }

    echo json_encode($response);
} else {
    echo json_encode(['error' => 'no data']);
}

$link->close();
?>
