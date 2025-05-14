<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Data Management UI</title>
    <link rel="icon" href="assets/images/crud.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <style>
        .section-title {
            margin-top: 40px;
            margin-bottom: 20px;
            font-weight: bold;
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
        }
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-row > div {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Student Data Management</h2>

        <!-- Personal Information Section -->
        <h4 class="section-title">Personal Information</h4>
        <form id="myform" method="POST" action="insert.php" enctype="multipart/form-data">
            <div class="form-row">
                <div>
                    <label for="studentName" class="form-label">Full Name</label>
                    <input type="text" class="form-control " id="studentName" name="full_name" placeholder="Enter Full Name">
                </div>
                <div>
                    <label for="dob" class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" name="dob" id="dob">
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email">
                </div>
                <div>
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone"placeholder="Enter Phone">
                </div>
            </div>
            <label for="phone" class="form-label">Choose Gender : </label></br>
            <div class="form-row">
                <div class="form-check form-check-inline">
                     <label class="form-check-label" for="inlineRadio1">Female</label>
                     <input class="form-check-input" type="radio" id="gender" name="gender" value="Female">

                <div class="form-check form-check-inline">
                  <label class="form-check-label" for="inlineRadio2">Male</label>
                  <input class="form-check-input" type="radio"  id="gender" name="gender" value="Male">

                <div class="form-check form-check-inline">
                    <label class="form-check-label" for="inlineRadio3">Other</label>
                    <input class="form-check-input" type="radio"  id="gender" name="gender" value="Other">
                </div>
                </div>
                </div>
            </div>

            <h4 class="section-title">Address Information</h4>
            <div class="form-row">
                <div>
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address" placeholder="Enter Address">
                </div>
                <div>
                    <label for="pincode" class="form-label">Pincode</label>
                    <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Enter Pincode">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4">
                    <label for="country" class="form-label">Country</label>
                    <select class="form-select" name="country" id="country">
                        <option value="">Select Country</option>
                        <option value="India">India</option>
                        <option value="USA">USA</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="state" class="form-label">State</label>
                    <select class="form-select" name="state" id="state">
                        <option value="">Select State</option>
                        <option value="Gujarat">Gujarat</option>
                        <option value="Maharashtra">Maharashtra</option>
                        <option value="Rajasthan">Rajasthan</option>
                        <option value="Telangana">Telangana</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="city" class="form-label">City</label>
                    <select class="form-select" name="city" id="city">
                        <option value="">Select City</option>
                        <option value="Ahmedabad">Ahmedabad</option>
                        <option value="Surat">Surat</option>
                        <option value="Rajkot">Rajkot</option>
                        <option value="Delhi">Delhi</option>
                        <option value="Jaipur">Jaipur</option>
                    </select>
                </div>
            </div>
            <h4 class="section-title">Document Upload</h4>
            <div class="mb-3">
                <label for="documents" class="form-label">Upload Documents</label>
                <input class="form-control" type="file" id="documents" name="documents[]" multiple>
            </div>
            <input type="submit" class="btn btn-primary" name="submit"></button>
        </div>
        </form>
        <div class="container">
        <?php
        echo "<h2 style='text-align:center;'>All Students Data</h2>";
        try {
            $servername = "localhost";
            $username = "root";
            $pass = "Ami@2211!";
            $pdo = new PDO("mysql:host=$servername;dbname=student_management", $username, $pass);
            $sql = "SELECT * FROM students";
            $result = $pdo->query($sql);
            $rows = $result->fetchAll();

            echo "<table class='table table-hover table-striped'>";
             echo "<tr>
                <th>FullName</th>
                <th>DOB</th>
                <th>Email</th>
                <th>Phoneno</th>
                <th>Gender</th>
                <th>address</th>
                <th>City</th>
                <th>Documents</th>
                <th>Action</th>

             </tr>";

             foreach ($rows as $row){
                echo "<tr>";
                echo "<td>".$row["full_name"]."</td>";
                echo "<td>".$row["dob"]."</td>";
                echo "<td>".$row["email"]."</td>";
                echo "<td>".$row["phone"]."</td>";
                echo "<td>".$row["gender"]."</td>";
                echo "<td>".$row["address"]."</td>";
                echo "<td>".$row["city"]."</td>";
                $images = explode(',', $row['documents']);
                echo "<td>";
                foreach ($images as $img):
                    $img = trim($img);
                    if ($img):
                ?>
                <img src="media/<?= htmlspecialchars($img)?>" width="40" height="20" style="margin:5px;" />
                <?php endif; endforeach;?>
               <?php echo "</td><td><a href=\"delete_formdata.php?id=$row[c_id]\">Edit</a></td>";
                echo "</tr>";
                // <td><button class='btn btn-secondary m-1' id='$row[c_id]'>Edit</button><button class='btn btn-secondary''>Delete</button></td></tr>" ;
            }
         echo "</table>";

        } catch (PDOException $e){
            echo "Database error: " . $e->getMessage();
        }
            ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
 <script src="form_validation.js"></script>
</body>
</html>
