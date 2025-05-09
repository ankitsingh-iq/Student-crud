<?php

require_once __DIR__ . '/config/config.php';

// print_r($_POST);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

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
    $documents = $_FILES['documents'];
    $dbdocuments = [];

    if (isset($_FILES['documents']) && count($_FILES['documents']['name']) > 0) {
        // echo('<pre>');
        // print_r($_FILES['documents']);  
        // echo('</pre>');
        $uploadDir = __DIR__ . '/../uploads/';
        // Loop through all uploaded files
        for ($i = 0; $i < count($_FILES['documents']['name']); $i++) {
            $fileName = $_FILES['documents']['name'][$i];      // Original file name
            $fileTmpName = $_FILES['documents']['tmp_name'][$i]; // Temporary path
            $fileError = $_FILES['documents']['error'][$i];      // Error code

            // Check if the file was successfully uploaded
            if ($fileError === 0) {
                $fileDestination = $uploadDir . basename($fileName);
                $dbdocuments[] = $fileDestination;

                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    echo "File uploaded successfully: " . $fileName . "<br>";
                } else {
                    echo "Failed to upload: " . $fileName . "<br>";
                }
            } else {
                echo "Error uploading file: " . $fileName . "<br>";
            }
        }
    } else {
        echo "No files were uploaded.";
    }


    // print_r($dbdocuments);
    // exit();

    // Step 2: Convert paths array to JSON
    $documentPaths = json_encode($dbdocuments);

    $result = $conn->query("INSERT INTO `students`(`full_name`, `dob`, `email`, `phone`, `gender`, `address`, `pincode`,`country`,`state`,`city`,`documents`) VALUES ('$fullname','$dob','$email','$phone','$gender','$address','$pincode','$country','$state','$city','$documentPaths')");

    if ($result) {
        echo "Record inserted successfully!";
    } else {
        echo "Failed to insert record: " . $conn->error;
    }
}
