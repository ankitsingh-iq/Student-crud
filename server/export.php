<?php

require_once __DIR__ . '/config/config.php';


$query = "SELECT * FROM students";
$stmp = $conn->prepare($query);
if ($stmp === false) {
    echo json_encode(['status' => 'error', 'message' => 'Error preparing statement']);
    exit();
}


if ($stmp->execute()) {
    $result = $stmp->get_result();

    // Create a temporary file to hold the CSV data
    $filename = 'students_data.csv';

    // Set the headers to force download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Open the output stream (php://output)
    $output = fopen('php://output', 'w');

    // Fetch the column names from the database table
    $columns = [];
    while ($field = $result->fetch_field()) {
        $columns[] = $field->name;
    }

    // Write the column names to the CSV
    fputcsv($output, $columns);

    // Write the data rows to the CSV
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    // Close the output stream
    fclose($output);
    exit();
} else {
    echo json_encode(['status' => 'error', 'message' => 'No data found in the database']);
    exit();
}
