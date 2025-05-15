<?php
// this file is used to edit student data
// it gets content from the form submission and updates the data in the database.
// It then returns a response message.
// Include the database connection file
require 'db.php';

// Check if the ID is provided
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($id === false) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid ID provided.']);
    exit;
}
// Include the validation class
require 'StudentFormHandler.php';

// Create an instance of the FormHandler class
$formHandler = new FormHandler($conn, $tableName);

// calling method to handle data and apply validation
$formHandler->handlePost($id);

$conn->close();

// Return the response as JSON
echo json_encode([
    'status' => empty($formHandler->errors) ? 'success' : 'error',
    'message' => $formHandler->statusMsg,
    'data' => $formHandler->fields,
    'errors' => $formHandler->errors
]);