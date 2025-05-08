<?php
$servername = "localhost";
$username = "root";
$password = "Ami@2211!";
$dbname = "student_management";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//   $stmt = $conn->prepare("INSERT INTO MyGuests (firstname, lastname, email)
//   VALUES (:firstname, :lastname, :email)");
//   $stmt->bindParam(':firstname', $firstname);
}
catch(PODException $err){
    echo $err.getMessage();
}


?>