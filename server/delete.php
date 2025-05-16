<?php

require_once __DIR__ . '/config/config.php';

$id = $_POST['id'];


// unlink all documents
$fileQuery = "SELECT documents FROM students WHERE id = ?";
$fileStmt = $conn->prepare($fileQuery);
$fileStmt->bind_param("i", $id);
$fileStmt->execute();
$fileStmt->bind_result($documentJson);
$fileStmt->fetch();
$fileStmt->close();



if (isset($documentJson)) {
    $documentPaths = json_decode($documentJson, true);
    foreach ($documentPaths as $documentPath) {
        $Path = "../" . $documentPath;
        unlink($Path);
    }
}


$query = "DELETE FROM students WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success']);
        exit();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No record found to delete']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete record']);
}
