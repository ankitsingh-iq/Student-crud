<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Student Data Management UI</title>
    <link rel="icon" href="assets/images/working.png" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/styles.css" />
</head>

<body>
    <div style="text-align: center;">
        <h2 class="mt-2">Student Data Management</h2>
    </div>
    <div class="container">
        <!-- Form Section -->
        <div class="form-container">
            <!-- Personal Information Section -->
            <div id="resultContainer"></div>
            <form id="studentForm" method="POST" action="">
                <p class="database-error"></p>
                <h4 class="section-title">Personal Information</h4>
                <div class="form-row">
                    <div>
                        <label for="studentName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="studentName" name="studentName" placeholder="Enter Full Name" />
                        <p class="studentName-error"></p>
                    </div>
                    <div>
                        <label for="dob" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" id="dob" name="dob" />
                        <p class="dob-error"></p>
                    </div>
                </div>

                <div class="form-row">
                    <div>
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email" />
                        <p class="email-error"></p>
                    </div>
                    <div>
                        <label for="phone" class="form-label">Phone</label>
                        <input type="number" class="form-control" id="phone" name="phone" placeholder="Enter Phone" />
                        <p class="phone-error"></p>
                    </div>
                </div>

                <div class="form-row">
                    <label class="form-label">Gender</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="inlineRadio1" value="Male" />
                            <label class="form-check-label" for="inlineRadio1">Male</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="inlineRadio2" value="Female" />
                            <label class="form-check-label" for="inlineRadio2">Female</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="inlineRadio3" value="Other" />
                            <label class="form-check-label" for="inlineRadio3">Other</label>
                        </div>
                    </div>
                    <p class="gender-error"></p>
                </div>

                <!-- Address Information Section -->
                <h4 class="section-title">Address Information</h4>
                <div class="form-row">
                    <div>
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" placeholder="Enter Address" />
                        <p class="address-error"></p>
                    </div>
                    <div>
                        <label for="pincode" class="form-label">Pincode</label>
                        <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Enter Pincode" />
                        <p class="pincode-error"></p>
                    </div>
                </div>

                <div class="form-row">
                    <div>
                        <label for="country" class="form-label">Country</label>
                        <select id="country" name="country" class="form-select" onchange="getStates()">
                            <option value="" selected disabled>Select Country</option>
                            <option value="India">India</option>
                            <option value="USA">USA</option>
                        </select>
                        <p class="country-error"></p>
                    </div>
                    <div>
                        <label for="state" class="form-label">State</label>
                        <select id="state" name="state" class="form-select" onchange="getCities()" disabled>
                            <option value="" selcted diabled>Select State</option>
                        </select>
                        <p class="state-error"></p>
                    </div>
                    <div>
                        <label for="city" class="form-label">City</label>
                        <select id="city" name="city" class="form-select" disabled>
                            <option value="" selcted diabled>Select City</option>
                        </select>
                        <p class="city-error"></p>
                    </div>
                </div>

                <!-- Document Upload Section -->
                <h4 class="section-title">Document Upload</h4>
                <div class="imagePreview"></div>
                <div class="mb-3">
                    <label for="documents" class="form-label">Upload Documents</label>
                    <input class="form-control" type="file" id="documents" multiple onchange="updateDocs()" />
                    <p class="documents-error"></p>
                </div>

                <!-- Import/Export Section
                <h4 class="section-title">Data Management</h4>
                <div class="mb-3">
                    <button type="button" class="btn btn-secondary">Import Data</button>
                    <button type="button" class="btn btn-secondary">Export Data</button>
                </div> -->

                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>

        <!-- Table Section -->
        <div class="table-responsive">
            <h4 class="section-title">Sample Student Data List</h4>
            <table class="table table-striped table-hover mt-5" id="studentTable">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>City</th>
                        <th colspan="3">Actions</th>
                    </tr>
                </thead>
                <tbody id="studentTableBody">
                    <!-- data will be dynamically inserted here -->
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script src="js/script.js"></script>
</body>
</html>