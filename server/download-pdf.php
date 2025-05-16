<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$id = $_POST['id'];
$query = "SELECT * FROM students WHERE id=?";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Error preparing statement']);
    exit();
}
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "No data found for the given ID.";
    exit();
}

//  Load the HTML template and fill data
ob_start();
$templatePath = __DIR__ . '/pdf_template.php';
if (!file_exists($templatePath)) {
    echo "Template file not found: " . $templatePath;
    exit();
}

ob_start();                    // Start buffer
include $templatePath;         // Load HTML template
$html = ob_get_clean();        // Get the content and clean the buffer

//  Initialize DOMPDF and set options
$options = new Options();
$options->set('defaultFont', 'Arial');
$options->set('chroot', realpath(__DIR__ . '/../')); // Allow Dompdf to access ../uploads
$dompdf = new Dompdf($options);

//  Load HTML into DOMPDF
$dompdf->loadHtml($html);

//  Set the paper size and orientation
$dompdf->setPaper('A4', 'portrait');

//  Render the PDF
$dompdf->render();

// Output the PDF (force download)
$dompdf->stream($data['full_name'] . "_Details.pdf", ["Attachment" => true]);
