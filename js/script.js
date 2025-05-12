$(document).ready(function () {
  
  

  $("#studentForm").on("submit", function (e) {
    e.preventDefault();
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
        } else {
          Swal.fire({
            icon: "error",
            title: "Oops...",
            text: response.message,
          });
        }
      },
      error: function (xhr, status, error) {
        // This block will be called if there's an issue with the request itself
        // (e.g., server unreachable, network error, etc.)
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: "Something went wrong!",
          footer: `<p>${xhr.responseText}</p>`, // Show the error message from PHP
        });
      },
    });
  });
});
