$(document).ready(function () {
  console.log("Dynamic dropdowns script loaded");
  $("#country").change(function () {
    $('#state').removeAttr('disabled');
    let country = $(this).val();
    console.log("Selected country: " + country);
    let data = { id: country };
    $.ajax({
      type: "post",
      url: "server/fetch-state.php",
      data: data,
      dataType: "json", // <-- This should be "json"
      success: function (response) {
        console.log(response);
        $("#state").empty();
        $.each(response, function (indexInArray, valueOfElement) {
          $("#state").append(
            `<option value="${valueOfElement.id}">${valueOfElement.name}</option>`
          );
        });
      },
    });
  });

  $(document).on("change", "#state", function () {
    let state = $(this).val();
    console.log("Selected state: " + state);
    $("#city").empty();
    $('#city').removeAttr('disabled');
    fetch("server/fetch-city.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ id: state }), // Send country ID as JSON
    })
      .then((response) => response.json())
      .then((data) => {
        console.log(data);
        data.forEach((data) => {
          $("#city").append(`<option value="${data.id}">${data.name}</option>`);
        });
      })
      .catch((error) => {
        console.error("Error:", error); // Handle any errors
      });
  });
});
