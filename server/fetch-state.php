<?php

require_once __DIR__ . '/config/config.php';

$id = $_POST['id'];
$result = $conn->query("SELECT id,name from states where country_id = $id");
$data = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($data);
