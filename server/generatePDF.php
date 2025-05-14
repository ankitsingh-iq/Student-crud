<?php
require 'db.php';
require __DIR__ .'/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

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

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'No student found with the provided ID.']);
    exit;
}

$student = $result->fetch_assoc();

$stmt->close();
$conn->close();

// Set up Dompdf options
$options = new Options();
$options->set('defaultFont', 'Courier');
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

// Simple HTML template for the PDF
$html = '
    <h2>Student Details</h2>
    <table border="1" cellpadding="10" cellspacing="0">
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
    <button style="margin-top: 20px; padding: 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer;">
        download
    </button>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            text-align: center;
        }
        table {
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
';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output as base64
$pdfOutput = $dompdf->output();
$base64PDF = base64_encode($pdfOutput);

echo json_encode([
    'status' => 'success',
    'pdf' => $base64PDF
]);