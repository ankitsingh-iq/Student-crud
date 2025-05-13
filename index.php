<?php

require_once __DIR__ . '/server/config/config.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Data Management UI</title>
  <link rel="icon" href="assets/images/working.png" type="image/x-icon" />

  <!-- boostrap CDN -->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" />

  <!-- SweetAlert2 CSS and JS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">


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

    .form-row>div {
      flex: 1;
    }
  </style>
</head>

<body>
  <div class="container mt-5">
    <h2 class="mb-4">Student Data Management</h2>

    <!-- Personal Information Section -->
    <h4 class="section-title">Personal Information</h4>
    <form class="needs-validation" action="server/insert.php" method="post" id="studentForm" enctype="multipart/form-data" novalidate>

      <!-- hidden value for Edit -->
      <input type="hidden" name="id" value="">

      <!-- Full Name and Date of Birth -->
      <div class="form-row">
        <div>
          <label for="studentName" class="form-label">Full Name</label>
          <input
            type="text"
            name="fullname"
            class="form-control"
            id="studentName"
            placeholder="Enter Full Name" required />
          <div class="invalid-feedback">
            Please enter your full name.
          </div>
        </div>
        <div>
          <label for="dob" class="form-label">Date of Birth</label>
          <input type="date" name="dob" class="form-control" id="dob" required />
          <div class="invalid-feedback">
            Please select your date of birth.
          </div>
        </div>
      </div>

      <!-- Email and Phone -->
      <div class="form-row">
        <div>
          <label for="email" class="form-label">Email</label>
          <input
            type="email"
            name="email"
            class="form-control"
            id="email"
            placeholder="Enter Email" required />
          <div class="invalid-feedback">
            Please enter a valid email address.
          </div>
        </div>
        <div>
          <label for="phone" class="form-label">Phone</label>
          <input
            type="text"
            name="phone"
            class="form-control"
            id="phone"
            placeholder="Enter Phone"
            pattern="\d{10}"
            required />
          <div class="invalid-feedback">
            Please enter a valid 10-digit phone number.
          </div>
        </div>
      </div>

      <!-- Gender -->
      <div class="form-row">
        <label class="form-label">Gender</label>
        <div>
          <div class="form-check form-check-inline">
            <input
              class="form-check-input"
              type="radio"
              name="gender"
              id="inlineRadio1"
              value="Male" required />
            <label class="form-check-label" for="inlineRadio1">Male</label>
          </div>
          <div class="form-check form-check-inline">
            <input
              class="form-check-input"
              type="radio"
              name="gender"
              id="inlineRadio2"
              value="Female" required />
            <label class="form-check-label" for="inlineRadio2">Female</label>
          </div>
          <div class="form-check form-check-inline">
            <input
              class="form-check-input"
              type="radio"
              name="gender"
              id="inlineRadio3"
              value="Other" required />
            <label class="form-check-label" for="inlineRadio3">Other</label>
          </div>
          <!-- <div class="invalid-feedback">
            Please select your gender.
          </div> -->
        </div>
      </div>

      <!-- Address Information Section -->
      <h4 class="section-title">Address Information</h4>
      <div class="form-row">
        <div>
          <label for="address" class="form-label">Address</label>
          <input
            type="text"
            name="address"
            class="form-control"
            id="address"
            placeholder="Enter Address" required />
          <div class="invalid-feedback">
            Please enter your address.
          </div>
        </div>
        <div>
          <label for="pincode" class="form-label">Pincode</label>
          <input
            type="text"
            name="pincode"
            class="form-control"
            id="pincode"
            placeholder="Enter Pincode"
            pattern="\d{6}"
            required />
          <div class="invalid-feedback">
            Please enter a valid 6-digit pincode.
          </div>
        </div>
      </div>

      <!-- Country, State, and City -->
      <div class="form-row">
        <div>
          <label for="country" class="form-label">Country</label>
          <select name="country" class="form-select " id="country" required>
            <option value="Select Country">Select Country</option>
            <?php
            $result = $conn->query("SELECT * FROM tbl_countries");

            // Check if the query failed
            if ($result === false) {
              die("Query failed: " . $conn->error); // Display error and stop script
            }

            // Proceed if query is successful
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
              }
            } else {
              echo '<option value="">No countries available</option>';
            }

            ?>
          </select>
          <div class="invalid-feedback">
            Please select a country.
          </div>
        </div>
        <div>
          <label for="state" class="form-label">State</label>
          <select name="state" class="form-select" id="state" disabled>
            <option>Select State</option>
          </select>
        </div>
        <div>
          <label for="city" class="form-label">City</label>
          <select name="city" class="form-select" id="city" disabled>
            <option>Select City</option>
          </select>
        </div>
      </div>

      <!-- Document Upload Section -->
      <h4 class="section-title">Document Upload</h4>
      <div class="mb-3">
        <label for="documents" class="form-label">Upload Documents</label>
        <input name="documents[]" class="form-control" type="file" id="documents" multiple required />
        <div class="invalid-feedback">
          Please upload at least one document.
        </div>
      </div>

      <!-- Preview Section -->
      <div id="imagePreviewContainer" class="d-flex gap-3 mt-3"></div>
      <button type="submit" class="btn btn-primary">Save</button>
    </form>





    <!-- Import/Export Section -->
    <h4 class="section-title">Data Management</h4>
    <div class="container">
      <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0">Data Management</h4>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <!-- Import Section -->
            <div class="col-md-6">
              <h5><i class="bi bi-upload"></i> Import Data</h5>
              <form action="server/import-file.php" id="importForm" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                  <input type="file" name="importFile" class="form-control" id="importFile">
                </div>
                <button type="submit" class="btn btn-success">
                  <i class="bi bi-cloud-arrow-up-fill"></i> Upload File
                </button>
              </form>
            </div>

            <!-- Export Section -->
            <div class="col-md-6">
              <h5><i class="bi bi-download"></i> Export Data</h5>
              <form id="exportForm" action="server/export.php" method="post">
                <button type="submit" class="btn btn-secondary">
                  <i class="bi bi-cloud-arrow-down-fill"></i> Export Data
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Table Section -->
    <div class="mt-5">
      <h3>Sample Student Data List</h3>
      <table class="table table-striped table-hover">
        <thead class="table-dark">
          <tr>
            <th>No</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>City</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>

          <!-- <tr>
            <td>1</td>
            <td>John Doe</td>
            <td>john.doe@example.com</td>
            <td>1234567890</td>
            <td>New York</td>
            <td>
              <button class="btn btn-success btn-sm"><a href="view.php?id=">View</a></button>
              <button class="btn btn-warning btn-sm">Edit</button>
              <button class="btn btn-danger btn-sm">Delete</button>
              <button class="btn btn-info btn-sm">PDF</button>
            </td>
          </tr> -->
        </tbody>
      </table>
    </div>


  </div>

  <!-- Student Info Modal -->
  <div class="modal fade" id="studentInfoModal" tabindex="-1" aria-labelledby="studentInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="studentInfoModalLabel">Student Information</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="container">
            <div class="row mb-3">
              <div class="col-md-6">
                <strong>Full Name:</strong> <span id="viewFullName"></span>
              </div>
              <div class="col-md-6">
                <strong>Date of Birth:</strong> <span id="viewDOB"></span>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <strong>Email:</strong> <span id="viewEmail"></span>
              </div>
              <div class="col-md-6">
                <strong>Phone:</strong> <span id="viewPhone"></span>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <strong>Gender:</strong> <span id="viewGender"></span>
              </div>
              <div class="col-md-6">
                <strong>Address:</strong> <span id="viewAddress"></span>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <strong>Country:</strong> <span id="viewCountry"></span>
              </div>
              <div class="col-md-6">
                <strong>State:</strong> <span id="viewState"></span>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <strong>City:</strong> <span id="viewCity"></span>
              </div>
              <div class="col-md-6">
                <strong>Pincode:</strong> <span id="viewPincode"></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- boostrap CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
  <!-- jQuery CDN -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Custom JavaScript -->
  <script src="js/script.js"></script>
  <script src="js/dynamic-dropdowns.js"></script>
  <script src="js/fetch-student-list.js"></script>
  <script src="js/multiple-file-priview.js"></script>
</body>

</html>