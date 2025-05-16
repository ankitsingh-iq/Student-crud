<?php
session_start();
$servername = "localhost";
$username = "root";
$pass = "Ami@2211!";
$pdo = new PDO("mysql:host=$servername;dbname=student_management",$username,$pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// $id = $_POST['id'];
// echo $id;

if(isset($_GET['id'])){
    $id = $_GET['id'];
    echo $id;

    $query = "DELETE FROM students WHERE id=? LIMIT 1";
    $statement = $pdo->prepare($query);
    $statement->bindParam(1, $id, PDO::PARAM_INT);
    $query_execute = $statement->execute();

    if($query_execute)
    {
        echo "<script>
        alert('Record Deleted successfully!');
        window.location.href = 'index.php';
        </script>";
        exit(0);
    }
    else{
        echo "<script>
        alert('Record Not Deleted successfully!');
        window.location.href = 'index.php';
        </script>";
        exit(0);

    }
}

?>