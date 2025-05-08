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
        <form id="studentForm">
            <div class="form-row">
                <div>
                    <label for="studentName" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="studentName" name="full_name" placeholder="Enter Full Name">
                </div>
                <div>
                    <label for="dob" class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" name="dob"id="dob">
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

            <div class="form-row">
                <label for="phone" class="form-label">Choose Gender : </label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="gender" name="gender" value="Female">
                    <label class="form-check-label" for="inlineRadio1">Female</label>
              
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions"  id="gender" name="male" value="Male">
                    <label class="form-check-label" for="inlineRadio2">Male</label>
               
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions"  id="gender" name="Other" value="Other">
                    <label class="form-check-label" for="inlineRadio3">Other</label>
                  </div>
                  </div>
                  </div>
            </div>

            <!-- Address Information Section -->
            <h4 class="section-title">Address Information</h4>
            <div class="form-row">
                <div>
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name= "address"placeholder="Enter Address">
                </div>
                <div>
                    <label for="pincode" class="form-label">Pincode</label>
                    <input type="text" class="form-control" id="pincode" name="pincode"placeholder="Enter Pincode">
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label for="country" class="form-label">Country</label>
                    <select class="form-select" id="country" name="country">
                        <option>Select Country</option>
                        <option>India</option>
                        <option>USA</option>
                    </select>
                </div>
                <div>
                    <label for="state" class="form-label">State</label>
                    <select class="form-select" id="state"  name="state">
                        <option>Gujarat</option>
                        <option>Maharstra</option>
                        <option>Rajasthan</option> 
                        <option>Telangana</option> 
                   
                       
                       
                    </select>
                </div>
                <div>
                    <label for="city" class="form-label">City</label>
                    <select class="form-select" id="city" name="city">
                        <option>Select City</option>
                        <option>Ahmedabad</option>
                        <option>Surat</option>
                        <option>Rajkot</option>
                        <option>Delhi</option>
                        <option>Jaipur</option>
                    </select>
                </div>
            </div>

            <!-- Document Upload Section -->
            <h4 class="section-title">Document Upload</h4>
            <div class="mb-3">
                <label for="documents" class="form-label">Upload Documents</label>
                <input class="form-control" type="file" id="documents" id="documents" multiple>
            </div>

        
            <button type="submit" class="btn btn-primary" name="submit" value="submit">submit</button>
        </form>

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
                        <td>New York</td>
                        <td>
                            <button class="btn btn-success btn-sm">View</button>
                            <button class="btn btn-warning btn-sm">Edit</button>
                            <button class="btn btn-danger btn-sm">Delete</button>
                            <button class="btn btn-info btn-sm">PDF</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
