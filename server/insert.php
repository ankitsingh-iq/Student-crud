<?php
include 'db_connection.php';
$full_name = $_POST['full_name'];
$dob = $_POST['dob'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$gender = $_POST['gender'];
$address = $_POST['address'];
$pincode = $_POST['pincode'];
$country = $_POST['country'];
$state = $_POST['state'];
$city = $_POST['city'];
$documents = $_POST['documents'];
$stmt = $conn->prepare("INSERT INTO students(full_name,dob,email,phone,gender,address,pincode,country,state,city,documents);
VALUES (:full_name,:dob,:email,:phone,:gender,:address,:pincode,:country,:state,:city,:documents)");
 $stmt->bindParam(':full_name', $firstname);
 $stmt->bindParam(':dob',$dob);
 $stmt->bindParam(':email',$email);
 $stmt->bindParam(':phone',$phone);
 $stmt->bindParam(':gender',$gender);
 $stmt->bindParam(':address',$address);
 $stmt->bindParam(':pincode',$pincode);
 $stmt->bindParam(':country',$country);
 $stmt->bindParam(':state',$state);
 $stmt->bindParam(':city',$city);
 $stmt->bindParam(':documents',$documents);
$stmt->execute();



?>