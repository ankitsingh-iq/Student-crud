<?php

require_once __DIR__ . '/config/config.php';

if ($conn->connect_error) {
    http_response_code(500); // Internal Server Error
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    die();
}


$id=$_POST['countryId'];

$query = "SELECT id, name FROM states WHERE country_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database query failed: " . $stmt->error]);
    die();
}

$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode(["status" => "success", "data" => $data]);

$stmt->close();
$conn->close();
?>
