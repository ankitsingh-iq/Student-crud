$(document).ready(function () {
  //! Country DropDown
  populateCountryDropdown();

  // !When a country is selected, populate the state dropdown
  $("#country").change(function () {
    var countryId = $(this).val();
    if (countryId) {
      //Make Sure City Diabale
      $("#city").empty();
      $("#city").prop("disabled", true);
      $("#state").prop("disabled", false); // Enable state dropdown
      fetchStates(countryId);
    } else {
      $("#state").prop("disabled", true); // Disable state dropdown if no country is selected
    }
  });

  // !When a state is selected, populate the city dropdown
  $(document).on("change", "#state", function () {
    var stateId = $(this).val();
    if (stateId) {
      $("#city").prop("disabled", false); // Enable city dropdown
      fetchCities(stateId);
    } else {
      $("#city").prop("disabled", true); // Disable city dropdown if no state is selected
    }
  });

  // !form submission with AJAX
  $("#studentForm").on("submit", function (e) {
    e.preventDefault(); // Prevent form submission

    // Clear previous validation states
    $(this).removeClass("was-validated");
    $(this).find(".is-invalid, .is-valid").removeClass("is-invalid is-valid");
    // Perform Bootstrap validation before AJAX request
    if (!this.checkValidity()) {
      // If the form is invalid, don't proceed with the AJAX request
      this.classList.add("was-validated");
      return; // Stop the AJAX request
    }

    let formData = new FormData(this);
    const isEdit = formData.get("id") !== ""; // If `id` is not empty, it's an Edit
    const url = isEdit ? "server/update.php" : "server/insert.php";

    $.ajax({
      type: "post",
      url: url,
      data: formData,
      contentType: false, // Prevent jQuery from overriding content type
      processData: false, // Prevent jQuery from processing the data
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          Swal.fire({
            icon: "success",
            title: "Success!",
            text: response.message,
            confirmButtonText: "OK",
          }).then(function () {
            $("#studentForm").trigger("reset");
            $("#imagePreviewContainer").empty(); // Clear image previews
          });
        }
      },
      error: function (xhr, status, error) {
        console.error(`Error ${xhr.status}: ${xhr.statusText} - ${error}`);
        const response = JSON.parse(xhr.responseText);

        if (response.status === "error" && response.errors) {
          Object.keys(response.errors).forEach(function (field) {
            const errorMessage = response.errors[field];
            const inputElement = $(`[name="${field}"]`);

            // 1. Add 'was-validated' to the form
            //
            inputElement.closest("form").addClass("needs-validated");

            // 2. Add Bootstrap validation class
            inputElement.addClass("is-invalid");

            // Check if the error message element exists; if not, create it
            let errorDiv = inputElement.next(".invalid-feedback");
            if (errorDiv.length === 0) {
              errorDiv = $('<div class="invalid-feedback"></div>');
              inputElement.after(errorDiv);
            }
            errorDiv.text(errorMessage);
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Oops...",
            text: response.message,
          });
        }
      },
    });
  });

  //Custom Delete
  $(document).on("click", ".delete-btn", function () {
    // Get the student ID from the data-id attribute
    var studentId = $(this).data("id");

    // Show a confirmation prompt using SweetAlert2
    Swal.fire({
      title: "Are you sure?",
      text: "You won't be able to revert this!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, delete it!",
      cancelButtonText: "No, cancel!",
    }).then((result) => {
      if (result.isConfirmed) {
        // Make an AJAX request to delete the student record
        $.ajax({
          type: "POST",
          url: "server/delete.php", // PHP script to handle deletion
          data: { id: studentId },
          dataType: "json",
          success: function (response) {
            if (response.status === "success") {
              // Show a success message and remove the row from the table
              Swal.fire(
                "Deleted!",
                "The record has been deleted.",
                "success"
              ).then(function () {
                // Remove the table row of the deleted record
                $("button[data-id='" + studentId + "']")
                  .closest("tr")
                  .remove();
              });
            } else {
              // Handle error in case deletion fails
              Swal.fire(
                "Error!",
                "There was a problem deleting the record.",
                "error"
              );
            }
          },
          error: function (xhr, status, error) {
            // Handle any AJAX error
            console.error(error);
            Swal.fire(
              "Error!",
              "There was an error with the request.",
              "error"
            );
          },
        });
      }
    });
  });

  // Edit Section
  $(document).on("click", ".edit-btn", function () {
    var studentId = $(this).data("id");
    $("#documents").removeAttr("required");
    let existingFiles = [];

    function showExistingDocumentPreviews(documentArray) {
      existingFiles = [...documentArray]; // Track current files
      const previewContainer = $("#imagePreviewContainer");
      previewContainer.empty();
      documentArray.forEach(function (docPath, idx) {
        const fileName = docPath.split("/").pop();
        const uniqueId = "existing_preview_" + idx;
        let previewHtml = `
      <div id="${uniqueId}" class="position-relative d-inline-block m-2">
        <img src="${docPath}" alt="${fileName}" class="img-thumbnail" style="width: 120px; height: 120px;" />
        <button type="button" data-docpath="${docPath}" class="btn btn-danger btn-sm position-absolute remove-existing-doc-btn" style="top: 5px; right: 5px;">X</button>
      </div>
    `;
        previewContainer.append(previewHtml);
      });
      // Update hidden input
      $("#existingDocumentsInput").val(JSON.stringify(existingFiles));
    }

    // Handle delete for existing files
    $(document).on("click", ".remove-existing-doc-btn", function () {
      const docPath = $(this).data("docpath");
      // Remove from array
      existingFiles = existingFiles.filter((path) => path !== docPath);
      // Remove preview
      $(this).closest("div").remove();
      // Update hidden input
      $("#existingDocumentsInput").val(JSON.stringify(existingFiles));
    });
    $.ajax({
      type: "POST",
      url: "server/fetch-student-list.php",
      data: { id: studentId },
      dataType: "json",
      success: function (response) {
        const data = response.data;
        console.log(data);

        const fieldMapping = {
          full_name: "fullname",
        };

        $.each(data, function (key, value) {
          const formField = fieldMapping[key] || key;
          const field = $(`[name="${formField}"]`);

          if (field.is(":radio")) {
            $(`input[name="${key}"][value="${value}"]`).prop("checked", true);
          }
          if (formField === "country") {
            $("#country").val(data.country).trigger("change");

            // Wait for states to load, then set state
            fetchStates(data.country, function () {
              $("#state").val(data.state).trigger("change");

              // Wait for cities to load, then set city
              fetchCities(data.state, function () {
                $("#city").val(data.city).trigger("change");
              });
            });
          }

          if (formField === "documents") {
            // Handle document preview
            if (value) {
              console.log("Document value from DB:", value); // Step 1: Check format

              //step:2 Parse the JSON string to array
              let documentArray = [];
              try {
                documentArray = JSON.parse(value);
                console.log("Parsed document array:", documentArray); // Step 2: Check format
                 showExistingDocumentPreviews(documentArray);
              } catch (error) {
                console.log("Error parsing document array:", error);
              }
            }
          }

          field.val(value);
        });
      },
    });
  });

  //View Section
  $(document).on("click", ".view-btn", function () {
    var studentId = $(this).data("id");
    $.ajax({
      type: "POST",
      url: "server/fetch-student-list.php",
      data: { id: studentId },
      dataType: "json",
      success: function (response) {
        const data = response.data;
        // Fill the modal fields
        $("#viewFullName").text(data.full_name || "N/A");
        $("#viewDOB").text(data.dob || "N/A");
        $("#viewEmail").text(data.email || "N/A");
        $("#viewPhone").text(data.phone || "N/A");
        $("#viewGender").text(data.gender || "N/A");
        $("#viewAddress").text(data.address || "N/A");
        $("#viewCountry").text(data.country || "N/A");
        $("#viewState").text(data.state || "N/A");
        $("#viewCity").text(data.city || "N/A");
        $("#viewPincode").text(data.pincode || "N/A");

        // Show the modal
        $("#studentInfoModal").modal("show");
      },
    });
  });
});

function populateCountryDropdown() {
  $.ajax({
    url: "server/fetch-countries.php",
    type: "GET",
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        $("#country").empty();
        $("#country").append('<option value="">Select Country</option>');
        // Populate dropdown with data from the server
        $.each(response.data, function (index, country) {
          $("#country").append(
            `<option value="${country.id}">${country.name}</option>`
          );
        });
      }
    },
    error: function (xhr, status, error) {
      console.error(`Error ${xhr.status}: ${xhr.statusText} - ${error}`);
      // Handle server error (500 status code)
      if (xhr.status === 500) {
        toastr.error("Server Error! Failed to load countries."); // Show server error toast
      } else {
        toastr.warning("Unexpected error occurred while loading countries."); // Show warning toast for other errors
      }
    },
  });
}

function fetchStates(countryId, callback) {
  $.ajax({
    type: "POST",
    url: "server/fetch-state.php",
    data: { countryId: countryId },
    dataType: "json",
    success: function (response) {
      $("#state").empty();
      $("#state").append('<option value="">Select State</option>');
      $.each(response.data, function (index, state) {
        $("#state").append(
          `<option value="${state.id}">${state.name}</option>`
        );
      });
      if (typeof callback === "function") callback();
    },
    error: function (xhr, status, error) {
      console.error(`Error ${xhr.status}: ${xhr.statusText} - ${error}`);
      toastr.error("Failed to load states.");
      if (typeof callback === "function") callback();
    },
  });
}

function fetchCities(stateId, callback) {
  $.ajax({
    type: "POST",
    url: "server/fetch-city.php",
    data: { stateId: stateId },
    dataType: "json",
    success: function (response) {
      $("#city").empty();
      $("#city").append('<option value="">Select City</option>');
      $.each(response.data, function (index, city) {
        $("#city").append(`<option value="${city.id}">${city.name}</option>`);
      });
      if (typeof callback === "function") callback();
    },
    error: function (xhr, status, error) {
      console.error(`Error ${xhr.status}: ${xhr.statusText} - ${error}`);
      toastr.error("Failed to load cities.");
      if (typeof callback === "function") callback();
    },
  });
}
