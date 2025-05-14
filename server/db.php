<?php
// Database connection parameters
$servername = "localhost";
$username = "naresh";
$password = "Naresh@2003";
$dbname = "student_management";
$tableName= "students";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    $db_message = "Connection failed: " . $conn->connect_error . ".<br>";
    exit();
}
// Check if the database exists, if not create it
$conn->query("CREATE DATABASE IF NOT EXISTS `$dbname`");
$conn->select_db($dbname);

// Create table if it doesn't exist
$table_sql = "CREATE TABLE IF NOT EXISTS `$tableName` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT   NULL,
    dob DATE NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    address TEXT,
    pincode VARCHAR(20),
    country VARCHAR(100),
    state VARCHAR(100),
    city VARCHAR(100),
    documents TEXT
)";
// Execute the query to create the table
if ($conn->query($table_sql) !== TRUE) {
    die("Error creating table: " . $conn->error);
}
