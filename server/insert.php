<?php
include 'db_connection.php';
echo "<pre>";
print_r($_POST);
print_r($_FILES);
echo "</pre>";

if(isset($_POST['submit'])){
    $fullName = $_POST['full_name'];
    $dob = $_POST['dob'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $pincode = $_POST['pincode'];
    $country = $_POST['country'];
    $state = $_POST['state'];
    $city = $_POST['city'];
    $allfile=[];

    foreach($_FILES['documents']['name'] as $key=> $value){
        $file=$value;
        if(move_uploaded_file($_FILES['documents']['tmp_name'][$key],'media/'.$file)){
            $allfile[] = $file;
        }
    }
    $newimplode = implode(',',$allfile);
    try {
            $stmt = $conn->prepare("INSERT INTO students(full_name, dob, email, phone, gender, address, pincode, country, state, city, documents)
                                    VALUES (:full_name, :dob, :email, :phone, :gender, :address, :pincode, :country, :state, :city, :documents)");
            $stmt-> bindParam(':full_name', $fullName);
            $stmt-> bindParam(':email',$email);
            $stmt-> bindParam(':dob',$dob);
            $stmt-> bindParam(':phone',$phone);
            $stmt-> bindParam(':gender',$gender);
            $stmt-> bindParam(':address',$address);
            $stmt-> bindParam(':pincode',$pincode);
            $stmt-> bindParam(':country',$country);
            $stmt-> bindParam(':state',$state);
            $stmt-> bindParam(':city',$city);
            $stmt-> bindParam(':documents',$newimplode);
            $stmt-> execute();
            echo "<script>
            alert('New record inserted successfully!');
            window.location.href = 'index.php';
            </script>";

        } catch(PDOException $err) {            echo "<script>alert('data Not inserted');</script>";
            echo $err->getMessage();

        }
}



?>