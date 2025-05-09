<?php

require_once __DIR__ . '/config/config.php';

$result = $conn->query("SELECT * FROM students");

if($result)
{
    if ($result->num_rows > 0) {
        $data = $result->fetch_all(MYSQLI_ASSOC);
    
        echo json_encode($data);
    }
    else {
        echo json_encode(array("message"=> "No Reccord Found"));
    }
}
else
{
    echo json_encode(array("error"=>"SQL Error: " . $conn->error));
}