<?php 
include 'db_connection.php';

$name = $_POST["fullName"];
$dob = $_POST["dob"];
$email = $_POST["email"];
$phone = $_POST["phone"];
$gender = $_POST["gender"]; 
$address = $_POST["address"];
$pincode = $_POST["pincode"];
$country = $_POST["country"];
$state = $_POST["state"];
$city = $_POST["city"];
$documents = $_POST["document"];

try {
    if(!empty($fullName) && !empty($dob) && !empty($email) && !empty($phone) && !empty($gender) && 
    !empty($address) && !empty($pincode) && !empty($country) && !empty($state) && !empty($city) && 
    !empty($documents)){
        $stmt = $conn->prepare("INSERT INTO user(user_name,user_email,user_pswd,gender) 
                        VALUES (:user_name,:user_email,:user_pswd,:gender)");
        $stmt->execute([
            ':user_name' => $name,
            ':user_email' => $email,
            ':user_pswd' => $pswd,
            ':gender' => $gender
        ]);
        echo '<script>alert("New record inserted successfully!!!")</script>';
    }
} catch(PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}
?>