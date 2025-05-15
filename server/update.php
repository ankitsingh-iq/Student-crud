<?php

require_once __DIR__ . '/config/config.php';


$fullname = $_POST['fullname'];
$dob = $_POST['dob'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$gender = $_POST['gender'];
$address = $_POST['address'];
$pincode = $_POST['pincode'];
$country = $_POST['country'];
$city = $_POST['city'];
$state = $_POST['state'];
$id = $_POST['id'];


//check for email filed on in the database
$email_check_query = "SELECT * FROM students WHERE email = ? AND id != ?";
$stmt = $conn->prepare($email_check_query);
$stmt->bind_param("si", $email, $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "errors" => ["email" => "Email already exists"]
    ]);
    exit;
}
// check for phone filed on in the database
$phone_check_query = "SELECT * FROM students WHERE phone = ? AND id != ?";
$stmt = $conn->prepare($phone_check_query);
$stmt->bind_param("si", $phone, $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "errors" => ["phone" => "Phone number already exists"]
    ]);
    exit;
}

// Hidden input for existing documents (JSON string)
$existing_documents_json = $_POST['existing_documents'] ?? '[]';

// New files (if any)
$documents = $_FILES['documents'];
// print_r($documents);
// exit;

// Decode the JSON string from the hidden input to get the array of existing documents
$existing_documents = [];
if (!empty($_POST['existing_documents'])) {
    $existing_documents = json_decode($_POST['existing_documents'], true);
    if (!is_array($existing_documents)) {
        $existing_documents = [];
    }
}

$upload_dir = __DIR__ . '/../uploads/';
$new_files = [];

if (isset($_FILES['documents']) && count($_FILES['documents']['name']) > 0) {
    // echo('<pre>');
    // print_r($_FILES['documents']);  
    // echo('</pre>');
    $uploadDir = __DIR__ . '/../uploads/';
    // Loop through all uploaded files
    $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
    for ($i = 0; $i < count($_FILES['documents']['name']); $i++) {
        $fileName = $_FILES['documents']['name'][$i];
        $fileTmpName = $_FILES['documents']['tmp_name'][$i];
        $fileError = $_FILES['documents']['error'][$i];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Skip if no file uploaded in this slot
        if ($fileError === 4) {
            continue;
        }
        if (!in_array($fileExt, $allowedTypes)) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "errors" => ["file" => "Invalid file type: " . $fileName]
            ]);
            exit;
        }

        if ($fileError === 0) {
            $uniqueName = uniqid() . '_' . basename($fileName);
            $fileDestination = $uploadDir . $uniqueName;
            $new_files[] = 'uploads/' . $uniqueName; // Store relative path

            if (!move_uploaded_file($fileTmpName, $fileDestination)) {
                http_response_code(500);
                echo json_encode([
                    "status" => "error",
                    "errors" => ["file" => "Failed to upload file: " . $fileName]
                ]);
                exit;
            }
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "errors" => ["file" => "Error uploading file: " . $fileName]
            ]);
            exit;
        }
    }
} else {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "errors" => ["file" => "No File Uplord"]
    ]);
    exit;
}

// Merge new files with existing documents
$all_documents = array_merge($existing_documents, $new_files);

// Convert all document paths to JSON for storage
$documents_json = json_encode($all_documents);

// SQL Query (replacing named parameters with ? placeholders)
$sql = "UPDATE students SET 
    full_name = ?,
    dob = ?,
    email = ?,
    phone = ?,
    gender = ?,
    address = ?,
    pincode = ?,
    country = ?,
    city = ?,
    state = ?,
    documents = ?
    WHERE id = ?";

// Prepare the statement
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["status" => "error", "errors" => ["db" => "Failed to prepare statement"]]);
    exit;
}

// Bind parameters (types: s = string, i = integer)
$stmt->bind_param(
    "sssssssssssi", // 11 strings (s) and 1 integer (i)
    $fullname,
    $dob,
    $email,
    $phone,
    $gender,
    $address,
    $pincode,
    $country,
    $city,
    $state,
    $documents_json,
    $id
);

// Execute the statement
$result = $stmt->execute();

if ($result) {
    echo json_encode(["status" => "success"]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "errors" => ["db" => "Failed to update student"]]);
}
