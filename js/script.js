$(document).ready(function () {
  // Self-invoking function for form validation
  (() => {
    "use strict";

    // Select all forms with the class "needs-validation"
    const forms = document.querySelectorAll(".needs-validation");

    // Loop over each form element
    Array.from(forms).forEach((form) => {
      // Attach a submit event listener to each form
      form.addEventListener(
        "submit",
        (event) => {
          // If the form is not valid, prevent submission
          if (!form.checkValidity()) {
            event.preventDefault(); // Prevent the form from submitting
            event.stopPropagation(); // Stop further event propagation
          }
          // Add Bootstrap's visual feedback class
          form.classList.add("was-validated");
        },
        false
      );
    });
  })();

  // Custom form submission with AJAX
  $("#studentForm").on("submit", function (e) {
    e.preventDefault(); // Prevent form submission

    // Perform Bootstrap validation before AJAX request
    if (!this.checkValidity()) {
      // If the form is invalid, don't proceed with the AJAX request
      this.classList.add("was-validated");
      return; // Stop the AJAX request
    }
    let formData = new FormData(this);
    // console.log(`form data`,formData);
    $.ajax({
      type: "post",
      url: "server/insert.php",
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
          });
        }
      },
      error: function (xhr, status, error) {
        console.log("Error Status:", status); // Logs "error" if it failed
        console.log("Error Message:", error); // Logs the error message
        console.log("Server Response:", xhr.responseText); // Logs the raw server response
        const response = JSON.parse(xhr.responseText);

        console.log("error respnse ", response);

        // If there are field-specific errors, show them
        if (response.status === "error" && response.errors) {
          Object.keys(response.errors).forEach(function (field) {
            const errorMessage = response.errors[field];
            const inputElement = $(`[name="${field}"]`);
            console.log(inputElement);
            //  Remove any previous "is-valid" class if present
            inputElement.removeClass("is-valid");
            //  Highlight the field and show the message
            inputElement.addClass("is-invalid");
            let errorDiv = inputElement.next(".invalid-feedback");
            if (errorDiv.length > 0) {
              // If the error div exists, just update the message
              errorDiv.text(errorMessage);
            }
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
