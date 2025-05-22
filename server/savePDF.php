<?php
// This script generates a PDF for a student based on their ID.
// it gets content from the client-side and generates a PDF file using Dompdf.
// It then returns a response message.
require_once '../vendor/autoload.php';
use Dompdf\Dompdf;

// Check for incoming data
if (!isset($_POST['pdfContent']) || !isset($_POST['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Required data missing.']);
    exit;
}

// Get the PDF content and ID from POST request
$pdfContent = $_POST['pdfContent'];
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

// Validate the ID and PDF content
if (!$id || empty($pdfContent)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input provided.']);
    exit;
}

try {
    // Initialize Dompdf
    $dompdf = new Dompdf();
    $dompdf->loadHtml($pdfContent);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Save PDF to exports folder
    $output = $dompdf->output();
    $filename = "student_$id.pdf";
    $filepath = __DIR__ . '/../exports/' . $filename;

    // Ensure exports directory exists
    if (!file_exists(__DIR__ . '/../exports')) {
        mkdir(__DIR__ . '/../exports', 0777, true);
    }

    // Save the PDF file
    file_put_contents($filepath, $output);

   // send the response back to the client
    echo json_encode([
        'status' => 'success',
        'message' => 'PDF saved successfully.',
        'filepath' => 'exports/' . $filename // Optional, to use for download/view later
    ]);
} catch (Exception $e) {
    // Handle any errors that occur during PDF generation
    echo json_encode(['status' => 'error', 'message' => 'PDF generation failed.', 'error' => $e->getMessage()]);
}
?>
