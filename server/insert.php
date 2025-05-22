<?php
require 'db.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

// Include the validation class
require 'StudentFormHandler.php';

// Create an instance of the FormHandler class
$formHandler = new FormHandler($conn, $tableName);

// calling method to handle data and apply validation
$formHandler->handlePost();

$conn->close();

// Return the response as JSON
echo json_encode([
    'status' => empty($formHandler->errors) ? 'success' : 'error',
    'message' => $formHandler->statusMsg,
    'data' => $formHandler->fields,
    'errors' => $formHandler->errors
]);
