<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$host = "localhost";
$username = "root";
$password = "";
$dbname = "qr_attendance_db";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($method === 'GET') {
    if ($action === 'get_all') {
        $emps = $conn->query("SELECT * FROM employees ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
        $logs = $conn->query("SELECT * FROM attendance_logs ORDER BY id DESC LIMIT 100")->fetch_all(MYSQLI_ASSOC);
        echo json_encode(["employees" => $emps, "attendanceLogs" => $logs]);
    }
} 
elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if ($action === 'add_employee') {
        $stmt = $conn->prepare("INSERT INTO employees (idCode, name, department, position, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $data['idCode'], $data['name'], $data['department'], $data['position'], $data['email']);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "id" => $conn->insert_id]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => $stmt->error]);
        }
        $stmt->close();
    } 
    elseif ($action === 'log_attendance') {
        $stmt = $conn->prepare("INSERT INTO attendance_logs (employeeName, department, action, timeStr, fullTimestamp) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $data['employeeName'], $data['department'], $data['action'], $data['timeStr'], $data['fullTimestamp']);
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => $stmt->error]);
        }
        $stmt->close();
    }
} 
elseif ($method === 'DELETE') {
    if ($action === 'delete_employee') {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            $conn->query("DELETE FROM employees WHERE id = $id");
            echo json_encode(["success" => true]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Invalid ID"]);
        }
    }
}

$conn->close();
?>