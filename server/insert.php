<?php 
include 'db_connection.php';

try {
    if(!empty($name) && !empty($email) && !empty($pswd) && !empty($gender)){
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