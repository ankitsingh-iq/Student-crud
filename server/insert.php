<?php

require_once __DIR__ . '/config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $fullname = $_POST['fullname'];
    $dob = $_POST['dob'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $pincode = $_POST['pincode'];
    $country_id = $_POST['country'];
    $city_id = $_POST['city'];
    $state_id = $_POST['state'];
    $documents = $_FILES['documents'];
    $dbdocuments = [];

     // Fetch country name
    $countryRes = $conn->query("SELECT name FROM tbl_countries WHERE id='$country_id' LIMIT 1");
    $countryRow = $countryRes->fetch_assoc();
    $country = $countryRow ? $countryRow['name'] : '';

    // Fetch state name
    $stateRes = $conn->query("SELECT name FROM states WHERE id='$state_id' LIMIT 1");
    $stateRow = $stateRes->fetch_assoc();
    $state = $stateRow ? $stateRow['name'] : '';

    // Fetch city name
    $cityRes = $conn->query("SELECT name FROM cities WHERE id='$city_id' LIMIT 1");
    $cityRow = $cityRes->fetch_assoc();
    $city = $cityRow ? $cityRow['name'] : '';

    $emailCheck = $conn->query("SELECT id FROM students WHERE email='$email'");
    if ($emailCheck->num_rows > 0) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "errors" => ["email" => "This email is already registered."]
        ]);
        exit;
    }

    $phoneCheck = $conn->query("SELECT id FROM students WHERE phone='$phone'");
    if ($phoneCheck->num_rows > 0) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "errors" => ["phone" => "This phone number is already in use."]
        ]);
        exit;
    }


    if (isset($_FILES['documents']) && count($_FILES['documents']['name']) > 0) {
        // echo('<pre>');
        // print_r($_FILES['documents']);  
        // echo('</pre>');
        $uploadDir = __DIR__ .'/../uploads/';
        // Loop through all uploaded files
        $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
        for ($i = 0; $i < count($_FILES['documents']['name']); $i++) {
            $fileName = $_FILES['documents']['name'][$i];
            $fileTmpName = $_FILES['documents']['tmp_name'][$i];
            $fileError = $_FILES['documents']['error'][$i];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

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
                $dbdocuments[] = 'uploads/' . $uniqueName; // Store relative path

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



    // Step 2: Convert paths array to JSON
    $documentPaths = json_encode($dbdocuments);

    try {
        $stmt = $conn->prepare("INSERT INTO `students` (`full_name`, `dob`, `email`, `phone`, `gender`, `address`, `pincode`, `country`, `state`, `city`, `documents`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssss", $fullname, $dob, $email, $phone, $gender, $address, $pincode, $country, $state, $city, $documentPaths);

        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Record inserted successfully!"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => $stmt->error
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    }
}
