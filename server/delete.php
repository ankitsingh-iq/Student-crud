<?php

require_once __DIR__ . '/config/config.php';


$id = $_POST['id'];

$result = $conn->query("DELETE from students where id =$id");

if ($result) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete record']);
}
