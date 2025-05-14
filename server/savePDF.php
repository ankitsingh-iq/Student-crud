<?php
$pdfData = $_POST['pdf'] ?? '';
$filename = $_POST['filename'] ?? 'document'.time().'.pdf';

if (!$pdfData) {
    echo json_encode(['status' => 'error', 'message' => 'No PDF data received.']);
    exit;
}

$pdfBinary = base64_decode($pdfData);
$path = __DIR__ . "/../exports/" . basename($filename);
if (!file_exists(__DIR__ . '/../exports')) {
    mkdir(__DIR__ . '/../exports', 0777, true);
}

if (file_put_contents($path, $pdfBinary)) {
    echo json_encode(['status' => 'success', 'message' => 'PDF saved successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save PDF.']);
}
