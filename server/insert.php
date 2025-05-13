<?php
require_once "db_connection.php";

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

if($_SERVER["REQUEST_METHOD"] == "POST"){
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

        echo "<script>
                alert('New record inserted successfully!');
                window.location.href = 'index.php';
            </script>";
        //exit;
    }
    // Validate name
//     $input_name = trim($_POST["name"]);
//     if(empty($input_name)){
//         $name_err = "Please enter a name.";
//     } elseif(!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
//         $name_err = "Please enter a valid name.";
//     } else{
//         $name = $input_name;
//     }

//     // Validate address
//     $input_address = trim($_POST["address"]);
//     if(empty($input_address)){
//         $address_err = "Please enter an address.";
//     } else{
//         $address = $input_address;
//     }

//     // Validate salary
//     $input_salary = trim($_POST["salary"]);
//     if(empty($input_salary)){
//         $salary_err = "Please enter the salary amount.";
//     } elseif(!ctype_digit($input_salary)){
//         $salary_err = "Please enter a positive integer value.";
//     } else{
//         $salary = $input_salary;
//     }

//     // Check input errors before inserting in database
//     if(empty($name_err) && empty($address_err) && empty($salary_err)){
//         // Prepare an insert statement
//         $sql = "INSERT INTO employees (name, address, salary) VALUES (:name, :address, :salary)";

//         if($stmt = $pdo->prepare($sql)){
//             // Bind variables to the prepared statement as parameters
//             $stmt->bindParam(":name", $param_name);
//             $stmt->bindParam(":address", $param_address);
//             $stmt->bindParam(":salary", $param_salary);

//             // Set parameters
//             $param_name = $name;
//             $param_address = $address;
//             $param_salary = $salary;

//             // Attempt to execute the prepared statement
//             if($stmt->execute()){
//                 // Records created successfully. Redirect to landing page
//                 header("location: index.php");
//                 exit();
//             } else{
//                 echo "Oops! Something went wrong. Please try again later.";
//             }
//         }

//         // Close statement
//         unset($stmt);
//     }

//     // Close connection
//     unset($pdo);
//}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Record</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
    <div class="container">
<div class="card shadow-lg">
            <h2 class="pt-2 pb-2 mb-4 border-dark border-bottom text-center text-black border-2">Student Data Management</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="fullName" placeholder="Enter Name">
                        <!-- <span class="error"> <?php echo $nameErr; ?> </span><br> -->
                    </div>

                    <div class="col-md-4">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email">
                        <!-- <span class="error"> <?php echo $emailErr; ?> </span><br> -->
                    </div>

                    <div class="col-md-4">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter Phone">
                        <!-- <span class="error"> <?php echo $phoneErr; ?> </span><br> -->
                    </div>

                </div><br>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="dob" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" id="dob" name="dob">
                    </div>

                    <div class="col-md-6">
                        <label for="gender" class="form-label" id="gender">Gender</label><br>

                        <div class="form-check form-check-inline" id="gFemale">
                            <input class="form-check-input" type="radio" name="gender" id="female" value="female">
                            <label class="form-check-label" for="female">Female</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="male" value="male">
                            <label class="form-check-label" for="male">Male</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="other" value="other">
                            <label class="form-check-label" for="other">Other</label>
                        </div>
                    </div>
                </div><br>

                <div class="row g-3">
                    <div class="col">
                        <label for="address" class="form-label">Address</label>
                        <input class="form-control" id="address" name="address" style="width:600px;" placeholder="Enter Address">
                    </div>

                    <div class="col">
                        <label for="pincode" class="form-label">Pincode</label>
                        <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Enter Pincode">
                    </div>
                </div><br>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="country" class="form-label">Country</label>
                        <select class="form-select" id="country" name="country">
                            <option>Select Country</option>
                            <option>India</option>
                            <option>USA</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="state" class="form-label">State</label>
                        <select class="form-select" id="state" name="state">
                            <option>Select State</option>
                            <option>Gujarat</option>
                            <option>Maharashtra</option>
                            <option>Kerala</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="city" class="form-label">City</label>
                        <select class="form-select" id="city" name="city">
                            <option>Select City</option>
                            <option>Ahmedabad</option>
                            <option>Gandhinagar</option>
                            <option>Vadodara</option>
                            <option>Pune</option>
                        </select>
                    </div>
                </div><br>

                <div class="mb-3">
                    <label for="document" class="form-label">Upload Document</label>
                    <input class="form-control" type="file" id="document" name="documents">
                </div>

                <!-- Import/Export Section -->
                <h4 class="section-title">Data Management</h4>
                <div class="mb-3">
                    <button type="button" class="btn btn-secondary">Import</button>
                    <button type="button" class="btn btn-info">Export</button>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary" id="btnSubmit">Submit</button>
                </div>
            </form>
        </div>
</div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>