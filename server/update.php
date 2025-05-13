<?php

require_once __DIR__ . '/config/config.php';



if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['id'])) {
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
        $id = $_POST['id'];


        // $emailCheck = $conn->query("SELECT id FROM students WHERE email='$email'");
        // if ($emailCheck->num_rows > 0) {
        //     http_response_code(500);
        //     echo json_encode([
        //         "status" => "error",
        //         "errors" => ["email" => "This email is already registered."]
        //     ]);
        //     exit;
        // }

        // $phoneCheck = $conn->query("SELECT id FROM students WHERE phone='$phone'");
        // if ($phoneCheck->num_rows > 0) {
        //     http_response_code(500);
        //     echo json_encode([
        //         "status" => "error",
        //         "errors" => ["phone" => "This phone number is already in use."]
        //     ]);
        //     exit;
        // }


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
            $result = $conn->query("UPDATE `students` SET 
                                    `full_name` = '$fullname',
                                    `dob` = '$dob',
                                    `email` = '$email',
                                    `phone` = '$phone',
                                    `gender` = '$gender',
                                    `address` = '$address',
                                    `pincode` = '$pincode',
                                    `country` = '$country',
                                    `state` = '$state',
                                    `city` = '$city'
                                    WHERE `id` = '$id'");

            if ($result) {
                http_response_code(200);
                echo json_encode([
                    "status" => "success",
                    "message" => "Record update successfully!"
                ]);
            } else {
                throw new Exception("Failed to insert record: " . $conn->error);
            }
        } catch (Exception $e) {
            echo json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
    }
} else {

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


    // if (isset($_FILES['documents']) && count($_FILES['documents']['name']) > 0) {
    //     // echo('<pre>');
    //     // print_r($_FILES['documents']);  
    //     // echo('</pre>');
    //     $uploadDir = __DIR__ . '/../uploads/';
    //     // Loop through all uploaded files
    //     for ($i = 0; $i < count($_FILES['documents']['name']); $i++) {
    //         $fileName = $_FILES['documents']['name'][$i];      // Original file name
    //         $fileTmpName = $_FILES['documents']['tmp_name'][$i]; // Temporary path
    //         $fileError = $_FILES['documents']['error'][$i];      // Error code

    //         // Check if the file was successfully uploaded
    //         if ($fileError === 0) {
    //             $fileDestination = $uploadDir . basename($fileName);
    //             $dbdocuments[] = $fileDestination;

    //             if (!move_uploaded_file($fileTmpName, $fileDestination)) {
    //                 http_response_code(500);
    //                 echo json_encode([
    //                     "status" => "error",
    //                     "errors" => ["file" => "Failed to upload file: " . $fileName]
    //                 ]);
    //                 exit;
    //             }
    //         } else {
    //             http_response_code(500);
    //             echo json_encode([
    //                 "status" => "error",
    //                 "errors" => ["file" => "Error uploading file: " . $fileName]
    //             ]);
    //             exit;
    //         }
    //     }
    // } else {
    //     http_response_code(500);
    //     echo json_encode([
    //         "status" => "error",
    //         "errors" => ["file" => "No File Uplord"]
    //     ]);
    //     exit;
    // }



    // Step 2: Convert paths array to JSON
    // $documentPaths = json_encode($dbdocuments) ;
    $dbdocuments= [];

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
