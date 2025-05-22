<?php
// this file is used to delete student data
// it gets id from the client and deletes the data from the database.
// It then returns a response message.
// Include the database connection file
require 'db.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($id === false) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid ID provided.']);
    exit;
}

// Delete files from uploads directory
$uploadDir = __DIR__ . '/../uploads/';

// Prepare the SQL statement
$sql = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $removedFiles = array_filter(array_map('trim', explode(',', $row['documents'])));
} else {
    echo json_encode(['status' => 'error', 'message' => "Student not foumd"]);
    exit;
}
$sql = "DELETE FROM students WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
if ($stmt->execute() == TRUE) {
    $db_message .= "Record deleted successfully";
    $uploadDir = __DIR__ . '/../uploads/';
    foreach ($removedFiles as $file) {
        $filePath = $uploadDir . $file;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    echo json_encode(['status' => 'success', 'message' => $db_message]);
} else {
    $db_message .= "Error: " . $sql . "<br>" . $conn->error;
    echo json_encode(['status' => 'error', 'message' => $db_message]);
    exit;
}
$stmt->close();
$conn->close();
