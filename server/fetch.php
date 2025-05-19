<?php
// Include the database connection file
require 'db.php';
// Fetch data from the database
$sql = "SELECT * FROM students";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    // Output data of each row
    $data = "";
    while ($row = $result->fetch_assoc()) {
        $data .= "<tr>
            <td>" . $row['id'] . "</td>
            <td>" . $row['full_name'] . "</td>
            <td>" . $row['email'] . "</td>
            <td>" . $row['phone'] . "</td>
            <td>" . $row['city'] . "</td>
            <td>
                <button data-id=" . $row['id'] . " class='btn btn-warning btn-sm edit-btn'>Edit</button></td>
            <td>
                <button data-id=" . $row['id'] . " class='btn btn-danger btn-sm delete-btn'>Delete</button></td>
            <td>
                <button data-id=" . $row['id'] . " class='btn btn-info btn-sm PDF-generate'>view</button>
            </td>
        </tr>";
    }echo json_encode([
            'status' => 'success',
            'result' => $data,
            'message' => 'Student data fetched successfully.'
        ]);
} else {
    $data = "<tr><td colspan='6'>No records found</td></tr>";
    echo json_encode([
        'status' => 'error',
        'result' => $data,
        'message' => 'No student found in the database.'
    ]);
}

$conn->close();
