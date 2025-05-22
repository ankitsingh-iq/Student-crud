
<?php
$uploadDir = __DIR__ . '/../uploads/';
$response = ['success' => false, 'files' => [], 'message' => ''];

if (!empty($_FILES['files']['name'])) {
    foreach ($_FILES['files']['name'] as $key => $name) {
        $tmpName = $_FILES['files']['tmp_name'][$key];
        $name = basename($name); // sanitize name
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($ext, $allowedTypes)) {
            error_log("Disallowed file type: $name");
            continue;
        }

        if ($_FILES['files']['size'][$key] > $maxSize) {
            error_log("File too large: $name");
            continue;
        }

        $uniqueName = uniqid() . '.' . $ext;
        // $destination = $uploadDir . $uniqueName;

        // if (!is_dir($uploadDir)) {
        //     mkdir($uploadDir, 0755, true);
        // }

        // if (move_uploaded_file($tmpName, $destination)) {
        $response['files'][] = $uniqueName;
        // } else {
        //     error_log("Failed to move file: $name to $destination");
        // }
    }

    if (count($response['files']) > 0) {
        $response['success'] = true;
    } else {
        $response['message'] = 'No valid files uploaded.';
    }
} else {
    $response['message'] = 'No files received.';
}

header('Content-Type: application/json');
echo json_encode($response);
?>
