<?php
require_once "db_connection.php";

$name = $email = $phone = $dob = $gender = $address = $pincode = $country = $state = $city = $documents = "";
$nameErr = $emailErr = $phoneErr = $dobErr = $genderErr = $addressErr = $pincodeErr = $countryErr = $stateErr = $cityErr = $documentsErr = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {

    //Name Validation
    $inputName = test_input($_POST["fullName"]);
    if(empty($inputName)) {
        $nameErr = "Name is required.";
    } else {
        $name = $inputName;
        if(!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
            $nameErr = "Only letters and white space allowed.";
        }
    }

    //Email Validation
    $inputEmail =  test_input($_POST["email"]);
    if(empty($inputEmail)) {
        $emailErr = "Email is required.";
    } else {
        $email = $inputEmail;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format.";
        }
    }

    //Phone Validation
    $inputPhone = test_input($_POST["phone"]);
    if(empty($inputPhone)) {
        $phoneErr = "PhoneNo is required.";
    } else {
        $phone = $inputPhone;
        if(!preg_match("/^[0-9]{11}$/", $phone)) {
            $phoneErr = "Phone number contain only digits.";
        }
    }

    //DOB Validation
    $inputDob = test_input($_POST["dob"]);
    if(empty($inputDob)) {
        $dobErr = "Date Of Birth is required.";
    } else {
        $dob = $inputDob;
    }

    //Gender Validation
    $inputGender = test_input($_POST["gender"]);
    if(empty($inputGender)) {
        $genderErr = "Gender is required.";
    } else {
        $gender = $inputGender;
    }

    //Address Validation
    $inputAddress = test_input($_POST["address"]);
    if(empty($inputAddress)) {
        $addressErr = "Address is required.";
    } else {
        $address = $inputAddress;
    }

    //Pincode Validation
    $inputPin = test_input($_POST["pincode"]);
    if(empty($inputPin)) {
        $pincodeErr = "Pincode is required.";
    } else {
        $pincode = $inputPin;
        if(!preg_match("/^[1-9][0-9]{5}$/", $pincode)) {
            $pincodeErr = "Pincode must be in 6 digit and cannot start with 0.";
        }
    }

    //Country Validation
    $inputCountry = test_input($_POST["country"]);
    if(empty($inputCountry)) {
        $countryErr = "Country is required.";
    } else {
        $country = $inputCountry;
    }

    //State Validation
    $inputState = test_input($_POST["state"]);
    if(empty($inputState)) {
        $stateErr = "State is required.";
    } else {
        $state = $inputState;
    }

    //City Validation
    $inputCity = test_input($_POST["city"]);
    if(empty($inputCity)) {
        $cityErr = "City is required.";
    } else {
        $city = $inputCity;
    }

    if(empty($nameErr) && empty($emailErr) && empty($phoneErr) && empty($dobErr) && empty($genderErr) &&
        empty($addressErr) && empty($pincodeErr) && empty($countryErr) && empty($stateErr) &&
        empty($cityErr)) {

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
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);     // removes backslash
    $data = htmlspecialchars($data);    // converts special characters to HTML entities
    return $data;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Record</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 1000px;
            margin-top: 50px;
        }

        .card {
            padding: 40px;
        }

        #gender {
            margin-left: 70px;
        }

        #gFemale {
            margin-left: 70px;
        }

        #btnSubmit {
            padding: 10px 30px;
        }

        .error {
            color: red;
        }
    </style>
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
                    <span class="error"> <?php echo $nameErr; ?> </span><br>
                </div>

                <div class="col-md-4">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email">
                    <span class="error"> <?php echo $emailErr; ?> </span><br>
                </div>

                <div class="col-md-4">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter Phone">
                    <span class="error"> <?php echo $phoneErr; ?> </span><br>
                </div>
            </div><br>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="dob" class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" id="dob" name="dob">
                    <span class="error"> <?php echo $dobErr; ?> </span><br>
                </div>

                <div class="col-md-6">
                    <label for="gender" class="form-label" id="gender">Gender</label><br>

                    <div class="form-check form-check-inline" id="gFemale">
                        <input class="form-check-input" type="radio" name="gender" id="female"
                        <?php if (isset($gender) && $gender=="female") echo "checked";?> value="female">
                        <label class="form-check-label" for="female">Female</label>
                    </div>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="male"
                        <?php if (isset($gender) && $gender=="male") echo "checked";?> value="male">
                        <label class="form-check-label" for="male">Male</label>
                    </div>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="other"
                        <?php if (isset($gender) && $gender=="other") echo "checked";?> value="other">
                        <label class="form-check-label" for="other">Other</label>
                    </div>
                    <span class="error"> <?php echo $genderErr; ?> </span>
                </div>
            </div><br>

            <div class="row g-3">
                <div class="col">
                    <label for="address" class="form-label">Address</label>
                    <input class="form-control" id="address" name="address" style="width:600px;" placeholder="Enter Address">
                    <span class="error"> <?php echo $addressErr; ?> </span>
                </div>

                <div class="col">
                    <label for="pincode" class="form-label">Pincode</label>
                    <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Enter Pincode">
                    <span class="error"> <?php echo $pincodeErr; ?> </span>
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
                    <span class="error"> <?php echo $countryErr; ?> </span>
                </div>

                <div class="col-md-4">
                    <label for="state" class="form-label">State</label>
                    <select class="form-select" id="state" name="state">
                        <option value="">Select State</option>
                        <option value="Gujarat"
                            <?php if (isset($state) && $state=="Gujarat") echo "selected"; ?>>Gujarat</option>
                        <option value="Maharashtra"
                            <?php if (isset($state) && $state=="Maharashtra") echo "selected"; ?>>Maharashtra</option>
                        <option value="Kerala"
                            <?php if (isset($state) && $state=="Kerala") echo "selected"; ?>>Kerala</option>
                    </select>
                    <span class="error"> <?php echo $stateErr; ?> </span>
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
                    <span class="error"> <?php echo $cityErr; ?> </span>
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