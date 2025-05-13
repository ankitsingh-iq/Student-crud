<?php
$servername = "localhost";
$username = "root";
$password = "Nirav@2307";

try {
    $conn = new PDO("mysql:host=$servername;dbname=student_management", $username, $password);

    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>