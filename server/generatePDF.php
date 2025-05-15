<?php
// This script generates a PDF for a student based on their ID.
// It fetches the student data from the database and formats it into HTML.
// The HTML is then returned as a JSON response, which can be used to generate the PDF.
// Include the database connection file
require 'db.php';

// Validate student ID
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid ID provided.']);
    exit;
}

// Fetch student data
$sql = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the student data is empty
if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'No student found with the provided ID.']);
    exit;
}
// Fetch the student data
$student = $result->fetch_assoc();

$stmt->close();
$conn->close();

// Simple HTML template for the PDF
$html = '
    <h2>Student Details</h2>
    <table class="pdfTable" border="1" cellpadding="10" cellspacing="0">
        <tr><th>Full Name</th><td>' . htmlspecialchars($student['full_name']) . '</td></tr>
        <tr><th>DOB</th><td>' . htmlspecialchars($student['dob']) . '</td></tr>
        <tr><th>Email</th><td>' . htmlspecialchars($student['email']) . '</td></tr>
        <tr><th>Phone</th><td>' . htmlspecialchars($student['phone']) . '</td></tr>
        <tr><th>Gender</th><td>' . htmlspecialchars($student['gender']) . '</td></tr>
        <tr><th>Address</th><td>' . htmlspecialchars($student['address']) . '</td></tr>
        <tr><th>Pincode</th><td>' . htmlspecialchars($student['pincode']) . '</td></tr>
        <tr><th>Country</th><td>' . htmlspecialchars($student['country']) . '</td></tr>
        <tr><th>State</th><td>' . htmlspecialchars($student['state']) . '</td></tr>
        <tr><th>City</th><td>' . htmlspecialchars($student['city']) . '</td></tr>
    </table>
    <div id="btn-g">
    <button class="btn btn-info btn-sm PDF-download" id="downloadBtn">Download</button>
    <button class="btn btn-danger btn-sm PDF-close" id="closeBtn">Close</button>
    </div>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            text-align: center;
        }
        .pdfTable {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
        }
        th, td {
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        td {
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color:#f6f6f6;
        }
        tr:hover {
            background-color:#ababab;
        }
        #btn-g {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .PDF-download {
            padding: 10px 20px;
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 5px;
        }
        .PDF-download:hover {
            background-color: #007bff;
        }
        .PDF-close {
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            margin-left: 10px;
        }
        .PDF-close:hover {
            background-color:#a22a36;
        }
';

// Return the HTML as a JSON response
// This will be used to generate the PDF
echo json_encode([
    'status' => 'success',
    'data' => $html,
    'message' => 'Student data fetched successfully.'
]);
