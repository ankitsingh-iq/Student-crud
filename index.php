<?php
include 'server/db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Data Management</title>
    <link rel="icon" href="assets/images/crud.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
    <div class="container">
        <div class="card shadow-lg">
            <h2 class="pt-2 pb-2 mb-4 border-dark border-bottom text-center text-black border-2">Student Data Management</h2>
            <form action="server/insert.php" method="POST" enctype="multipart/form-data">
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
                </div>

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
                </div>

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

        <div class="mt-5">
            <h3 class='pt-2 pb-2 mb-4 border-dark border-2 border-top border-bottom text-center text-black'>
                List Of Students
            </h3>

            <?php
            $stmt = $conn->prepare("SELECT * FROM students");
            $stmt->execute();
            $rows = $stmt->fetchAll();

            if (count($rows) > 0) {
                echo "<table class='table table-lg table-light table-hover rounded shadow-sm'>";
                echo "<tr class='table-dark text-center'>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Date Of Birth</th>
                        <th>Gender</th>
                        <th>City</th>
                        <th></th><th></th>
                    </tr>";

                foreach ($rows as $row) {
                    echo "<tr class='text-center'>
                            <td>".$row["id"]."</td>
                            <td>".$row["full_name"]."</td>
                            <td>".$row["email"]."</td>
                            <td>".$row["phone"]."</td>
                            <td>".$row["dob"]."</td>
                            <td>".$row["gender"]."</td>
                            <td>".$row["city"]."</td>
                            <td><button class='btn btn-sm btn-warning'>EDIT</button>
                            <td><button class='btn btn-sm btn-danger'>DELETE</button></td>
                            </tr>";
                }
                echo "</table>";
            } else {
                echo "No records found.";
            }
            ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
