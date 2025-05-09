<?php
// config/db_connection.php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$servername = "localhost";
$username = "ankit";
$password = "ankit@123";
$database = "student_management";

try {
    $conn = new mysqli($servername, $username, $password, $database);
} catch (mysqli_sql_exception $e) {
    die("Database connection failed: " . $e->getMessage());
}
