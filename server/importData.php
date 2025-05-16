<?php
session_start();

include_once 'db_connection.php';

if(isset($_POST['importSubmit'])) {

    // Allowed mime types to verify the uploaded file is a valid CSV format
    $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');

    // Validate whether selected file is a CSV file
    if(!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)) {   //in_array() - check file type is match csv file type

        // If the file is uploaded
        if(is_uploaded_file($_FILES['file']['tmp_name'])) {     //file was really uploaded via browser & HTTP POST

            // Open uploaded CSV file with read-only mode
            $csvFile = fopen($_FILES['file']['tmp_name'], 'r');

            // Skip the first line
            fgetcsv($csvFile);

            // Parse data from CSV file line by line
            while(($line = fgetcsv($csvFile)) !== FALSE){
                // Get row data
                $name    = $line[0];
                $email   = $line[1];
                $phone   = $line[2];
                $dob     = $line[3];
                $gender  = $line[4];
                $addr    = $line[5];
                $pin     = $line[6];
                $country = $line[7];
                $state   = $line[8];
                $city    = $line[9];
                $doc     = $line[10];

                //Check whether member already exists in the database with the same name
                $stmt = $conn->prepare("SELECT id FROM students WHERE email = :email");
                $stmt->execute(['email' => $email]);

                if($stmt->rowCount() > 0) {
                    $update = $conn->prepare("UPDATE students SET full_name = :full_name, phone = :phone,
                                            dob = :dob, gender = :gender, address = :address,
                                            pincode = :pin, country = :country, state = :state,
                                            city = :city, documents = :doc WHERE email = :email");
                    $update->execute([
                                    'full_name' => $name,
                                    'phone' => $phone,
                                    'dob' => $dob,
                                    'gender' => $gender,
                                    'address' => $addr,
                                    'pin' => $pin,
                                    'country' => $country,
                                    'state' => $state,
                                    'city' => $city,
                                    'doc' => $doc,
                                    'email' => $email
                    ]);

                    $_SESSION['status_type'] = 'warning';
                    $_SESSION['status_msg'] = "This Email is already Exist!!! Please Update the data!!!";
                    header("Location: ../index.php");
                    exit();
                }else {
                    $insert = $conn->prepare("INSERT INTO students(full_name, dob, email, phone, gender,
                                address,pincode, country, state, city, documents) VALUES
                                (:fName, :dob, :email, :phone, :gender, :address, :pin, :country,
                                :state, :city, :doc)");

                    $insert->execute([
                            ':fName' => $name,
                            ':dob' => $dob,
                            ':email' => $email,
                            ':phone' => $phone,
                            ':gender' => $gender,
                            ':address' => $addr,
                            ':pin' => $pin,
                            ':country' => $country,
                            ':state' => $state,
                            ':city' => $city,
                            ':doc' => $doc
                    ]);
                }
            }

            // Close opened CSV file
            fclose($csvFile);

            $_SESSION['status_type'] = 'success';
            $_SESSION['status_msg'] = "Students data has been imported successfully!!!";
            header("Location: ../index.php");
            exit();

        }else {

            $_SESSION['status_type'] = 'danger';
            $_SESSION['status_msg'] = "Error uploading the file.";
            header("Location: ../index.php");
            exit();
        }

    }else {

        $_SESSION['status_type'] = 'warning';
        $_SESSION['status_msg'] = "Invalid file type. Please upload a CSV file.";
        header("Location: ../index.php");
        exit();
    }
}
?>