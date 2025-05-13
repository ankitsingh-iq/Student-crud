<?php
include 'db_connection.php';

$nameErr = $emailErr = $phoneErr = $dobErr = $genderErr = $addErr = $pinErr = $countryErr =
$stateErr = $cityErr = "";

$name = $_POST["fullName"];
$email = $_POST["email"];
$phone = $_POST["phone"];
$dob = $_POST["dob"];
$gender = $_POST["gender"];
$address = $_POST["address"];
$pincode = $_POST["pincode"];
$country = $_POST["country"];
$state = $_POST["state"];
$city = $_POST["city"];
$documents = $_POST["documents"];

try{
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $stmt = $conn->prepare("INSERT INTO students(full_name, dob, email, phone, gender,
                                address,pincode, country, state, city, documents) VALUES
                                (:fName, :dob, :email, :phone, :gender, :address, :pin, :country, 
                                :state, :city, :doc)");
        $stmt->execute([
            ':fName' => $name,
            ':dob' => $dob,
            ':email' => $email,
            ':phone' => $phone,
            ':gender' => $gender,
            ':address' => $address,
            ':pin' => $pincode,
            ':country' => $country,
            ':state' => $state,
            ':city' => $city,
            ':doc' => $documents
        ]);

        echo '<script>alert("New record inserted successfully!!!")</script>';
    }
} catch(PDOException $e) {
    echo $e->getMessage();
}
?>