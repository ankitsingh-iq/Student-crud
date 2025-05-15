<?php
// this file is used to get student data
// it gets id from the client and fetches the data from the database.
// It then returns data and a response message.

// Include the database connection file
require_once 'db.php';

header('Content-Type: application/json');

// Check if the ID is provided
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid student ID.']);
    exit;
}

// Prepare the SQL statement
$sql = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
    echo json_encode(['status' => 'success', 'data' => $student]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No student found with the provided ID.']);
}
$stmt->close();
$conn->close();
