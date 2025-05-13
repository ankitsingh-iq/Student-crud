<?php

require_once __DIR__ . '/config/config.php';


if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $result = $conn->query("SELECT * FROM students where id='$id'");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Send JSON response
        echo json_encode([
            "status" => "success",
            "data" => $row
        ]);
        exit;
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Record not found."
        ]);
        exit;
    }
}




$result = $conn->query("SELECT * FROM students");

if ($result) {
    if ($result->num_rows > 0) {
        $data = $result->fetch_all(MYSQLI_ASSOC);

        echo json_encode($data);
    } else {
        echo json_encode(array("message" => "No Reccord Found"));
    }
} else {
    echo json_encode(array("error" => "SQL Error: " . $conn->error));
}
