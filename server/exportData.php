<?php

include_once 'db_connection.php';

$filename = "studentsList_" . date('Y-m-d') . ".csv";
$delimiter = ",";

// Create a file pointer
$f = fopen('php://memory', 'w');

// Set column headers
$fields = array('ID', 'Name', 'Email', 'Phone', 'DateOfBirth', 'Gender', 'Address', 'City');
fputcsv($f, $fields, $delimiter);

// Get records from the database
$result = $conn->prepare("SELECT id, full_name, email, phone, dob, gender, address, city FROM students");
$result->execute();
while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($f, [
        $row['id'],
        $row['full_name'],
        $row['email'],
        $row['phone'],
        $row['dob'],
        $row['gender'],
        $row['address'],
        $row['city']
    ], $delimiter);
}

// Move back to beginning of file
fseek($f, 0);

// Set headers to download file rather than displayed
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '";');

// Output all remaining data on a file pointer
fpassthru($f);

// Exit from file
exit();
?>