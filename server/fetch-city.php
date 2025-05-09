<?php

require_once __DIR__ . '/config/config.php';

// Read the raw POST data from the body
$rawData = file_get_contents("php://input");

// Decode the JSON data into a PHP associative array
$data = json_decode($rawData, true);

// Access the 'id' from the decoded data
$stateId = $data['id'];

$result = $conn->query("SELECT id,name from cities where state_id = $stateId");
$data = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($data);
