<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Data Management UI</title>
    <link rel="icon" href="assets/images/crud.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
    <div class="container">
        <div class="card shadow-lg">
            <h2 class="text-center mb-4">Student Data Management</h2>
            <form action="./insert.php" method="post" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="fullName" placeholder="Enter Name" required>
                    </div>
    
                    <div class="col-md-4">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email" required>
                    </div>

                    <div class="col-md-4">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter Phone" required>
                    </div>
        
                </div><br>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="dob" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" id="dob" name="dob" required>
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
                        <input class="form-control" id="address" name="address" style="width:600px;" placeholder="Enter Address" required>
                    </div>

                    <div class="col">
                        <label for="pincode" class="form-label">Pincode</label>
                        <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Enter Pincode" required>
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
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="city" class="form-label">City</label>
                        <select class="form-select" id="city" name="city">
                            <option>Select City</option>
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
                    <button type="button" class="btn btn-secondary">Import Data</button>
                    <button type="button" class="btn btn-secondary">Export Data</button>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary" id="btnSubmit">Submit</button>
                </div>
            </form>
        </div>

        <!-- Table Section -->
        <div class="mt-5">
            <h3>Sample Student Data List</h3>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Date Of Birth</th>
                        <th>Gender</th>
                        <th>City</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>John Doe</td>
                        <td>john.doe@example.com</td>
                        <td>1234567890</td>
                        <td>21-06-2003</td>
                        <td>Male</td>
                        <td>New York</td>
                        <td>
                            <button class="btn btn-warning btn-sm">Edit</button>
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
