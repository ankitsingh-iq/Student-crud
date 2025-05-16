<?php
echo '<pre>';
print_r($allfile);
echo 'Final documents string: ' . $newimplode;
echo '</pre>';
$servername = "localhost";
$username = "root";
$pass = "Ami@2211!";
$pdo = new PDO("mysql:host=$servername;dbname=student_management",$username,$pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$id = $_POST['id'];

$query = "SELECT * FROM students WHERE id=:id";
$statement = $pdo->prepare($query);
$data = [':id' => $id];
$statement->execute($data);
$result = $statement->fetch(PDO::FETCH_OBJ);
$existingFiles = !empty($result->documents) ? explode(',', $result->documents) : [];
// print_r($_POST);

if(isset($_POST["submit"])){
    $id=$_POST["id"];
    $name = $_POST["full_name"];
    $dob =  $_POST["dob"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $gender = $_POST["gender"];
    $address = $_POST["address"];
    $pincode = $_POST["pincode"];
    $country = $_POST["country"];
    $state = $_POST["state"];
    $city = $_POST["city"];

    if(!empty($_POST['deletefile'])){
        foreach($_POST['deletefile'] as $delfile){
            $folder = 'media/'.$delfile;
            if (file_exists($folder)) unlink($folder);
            $existingFiles = array_diff($existingFiles, [$delfile]);
        }
        }

    $allfile = $existingFiles;

    if (!empty($_FILES['documents']['name'][0])) {
    foreach($_FILES['documents']['name'] as $key=> $value){
        $file=$value;
        if(move_uploaded_file($_FILES['documents']['tmp_name'][$key],'media/'.$file)){
            $allfile[] = $file;
        }

      }
    }
$newimplode=implode(',',$allfile);

$sql = "UPDATE students set full_name=:full_name,dob=:dob,email=:email,phone=:phone,gender=:gender,address=:address,pincode=:pincode,country=:country,   state=:state,city=:city,documents=:documents WHERE id=:id LIMIT 1";
$stmt = $pdo->prepare($sql);
$data= [
    ':full_name' =>$name,
    ':dob' =>$dob,
    ':email'=>$email,
    ':phone' =>$phone,
    ':gender'=>$gender,
    ':address'=>$address,
    ':pincode'=>$pincode,
    ':country'=>$country,
    ':state'=>$state,
    ':city'=>$city,
    ':documents'=>$newimplode,
    ':id'=>$id
];

    $query_execute = $stmt->execute($data);
    if($query_execute)
    {
        echo "Updated Successfully";
        header('Location:index.php');
        exit(0);
    }
    else{
        echo "Record not updated ";
        header('Location:delete_formdata.php');
        exit();
    }
}

?>
