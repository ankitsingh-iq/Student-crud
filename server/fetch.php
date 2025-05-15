<?php
// Include the database connection file
require 'db.php';
// Fetch data from the database
$sql = "SELECT * FROM students";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>" . $row['id'] . "</td>
            <td>" . $row['full_name'] . "</td>
            <td>" . $row['email'] . "</td>
            <td>" . $row['phone'] . "</td>
            <td>" . $row['city'] . "</td>
            <td>
                <!-- <button class='btn btn-success btn-sm'>View</button> -->
                <button data-id=" . $row['id'] . " class='btn btn-warning btn-sm edit-btn'>Edit</button>
                <button data-id=" . $row['id'] . " class='btn btn-danger btn-sm delete-btn'>Delete</button>
                <button data-id=" . $row['id'] . " class='btn btn-info btn-sm PDF-generate'>view</button>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='6'>No records found</td></tr>";
}

$conn->close();
