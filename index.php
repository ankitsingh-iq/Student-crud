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

  <!-- Toastr CSS (for styling the toast notifications) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

  <!-- custom css -->
  <link rel="stylesheet" href="css/styles.css">

</head>

<body style="background-color: #f8f9fa;">
  <div class="container py-4">
    <h2 class="mb-4 text-center fw-bold text-primary">Student Data Management</h2>
    <div class="row g-4">
      <!-- Form Section -->
      <div class="col-md-5">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Add / Edit Student</h4>
          </div>
          <div class="card-body">
            <form class="needs-validation" action="server/insert.php" method="post" id="studentForm" enctype="multipart/form-data" novalidate>
              <!-- hidden value for Edit -->
              <input type="hidden" name="id" value="">
              <input type="hidden" name="existing_documents" id="existingDocumentsInput">
              <!-- Full Name and Date of Birth -->
              <div class="row mb-3">
                <div class="col">
                  <label for="studentName" class="form-label">Full Name</label>
                  <input type="text" name="fullname" class="form-control" id="studentName" placeholder="Enter Full Name" required />
                  <div class="invalid-feedback">Please enter your full name.</div>
                </div>
                <div class="col">
                  <label for="dob" class="form-label">Date of Birth</label>
                  <input type="date" name="dob" class="form-control" id="dob" required />
                  <div class="invalid-feedback">Please select your date of birth.</div>
                </div>
              </div>
              <!-- Email and Phone -->
              <div class="row mb-3">
                <div class="col">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" name="email" class="form-control" id="email" placeholder="Enter Email" required />
                  <div class="invalid-feedback">Please enter a valid email address.</div>
                </div>
                <div class="col">
                  <label for="phone" class="form-label">Phone</label>
                  <input type="text" name="phone" class="form-control" id="phone" placeholder="Enter Phone" pattern="\d{10}" required />
                  <div class="invalid-feedback">Please enter a valid 10-digit phone number.</div>
                </div>
              </div>
              <!-- Gender -->
              <div class="mb-3">
                <label class="form-label">Gender</label>
                <div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="inlineRadio1" value="Male" required />
                    <label class="form-check-label" for="inlineRadio1">Male</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="inlineRadio2" value="Female" required />
                    <label class="form-check-label" for="inlineRadio2">Female</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="inlineRadio3" value="Other" required />
                    <label class="form-check-label" for="inlineRadio3">Other</label>
                  </div>
                </div>
              </div>
              <!-- Address Information Section -->
              <h5 class="section-title mt-4">Address Information</h5>
              <div class="row mb-3">
                <div class="col">
                  <label for="address" class="form-label">Address</label>
                  <input type="text" name="address" class="form-control" id="address" placeholder="Enter Address" required />
                  <div class="invalid-feedback">Please enter your address.</div>
                </div>
                <div class="col">
                  <label for="pincode" class="form-label">Pincode</label>
                  <input type="text" name="pincode" class="form-control" id="pincode" placeholder="Enter Pincode" pattern="\d{6}" required />
                  <div class="invalid-feedback">Please enter a valid 6-digit pincode.</div>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col">
                  <label for="country" class="form-label">Country</label>
                  <select name="country" class="form-select" id="country" required></select>
                  <div class="invalid-feedback">Please select a country.</div>
                </div>
                <div class="col">
                  <label for="state" class="form-label">State</label>
                  <select name="state" class="form-select" id="state" disabled></select>
                </div>
                <div class="col">
                  <label for="city" class="form-label">City</label>
                  <select name="city" class="form-select" id="city" disabled></select>
                </div>
              </div>
              <!-- Document Upload Section -->
              <h5 class="section-title mt-4">Document Upload</h5>
              <div class="mb-3">
                <label for="documents" class="form-label">Upload Documents</label>
                <input name="documents[]" class="form-control" type="file" id="documents" multiple required />
                <div class="invalid-feedback">Please upload at least one document.</div>
              </div>
              <div id="imagePreviewContainer" class="d-flex gap-3 mt-3"></div>
              <button type="submit" class="btn btn-primary w-100 mt-3">Save</button>
            </form>
          </div>
        </div>
        <!-- Import/Export Section -->
        <div class="card shadow-sm mt-4">
          <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Data Management</h5>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-6">
                <form action="" id="importForm" method="post" enctype="multipart/form-data">
                  <label class="form-label">Import Data</label>
                  <input type="file" name="importFile" class="form-control mb-2" id="importFile">
                  <button type="submit" class="btn btn-success w-100">
                    <i class="bi bi-cloud-arrow-up-fill"></i> Upload File
                  </button>
                </form>
              </div>
              <div class="col-6 d-flex flex-column justify-content-end">
                
                <button id="exportbtn" class="btn btn-secondary w-100 mt-auto">
                  <i class="bi bi-cloud-arrow-down-fill"></i> Export Data
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Table Section -->
      <div class="col-md-7">
        <div class="card shadow-sm h-100">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Student Data List</h4>
          </div>
          <div class="card-body">
            <table class="table table-striped table-hover align-middle">
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
                <!-- Table rows will be populated dynamically -->
              </tbody>
            </table>
          </div>
        </div>
      </div>
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

            <div class="row mb-3">
              <div class="col-md-12">
                <strong>Documents:</strong>
                <div id="viewDocuments"></div>
              </div>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button id="downloadPdfBtn" data-id="" class="btn btn-info">
            <i class="bi bi-file-earmark-pdf"></i> Download PDF
          </button>
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
  <!-- Toastr JS (for enabling the toast functionality) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

  <!-- Custom JavaScript -->
  <script src="js/script.js"></script>

  <script src="js/multiple-file-priview.js"></script>
</body>

</html>