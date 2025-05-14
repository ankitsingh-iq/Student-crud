<?php

require 'db.php';
// Check if the ID is provided
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($id === false) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid ID provided.']);
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
