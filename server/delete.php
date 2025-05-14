<?php
require 'db.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($id === false) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid ID provided.']);
    exit;
}
$sql = "DELETE FROM students WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
if ($stmt->execute() == TRUE) {
    $db_message .= "Record deleted successfully";
    echo json_encode(['status' => 'success', 'message' => $db_message]);
} else {
    $db_message .= "Error: " . $sql . "<br>" . $conn->error;
    echo json_encode(['status' => 'error', 'message' => $db_message]);
    exit;
}
$stmt->close();
$conn->close();
