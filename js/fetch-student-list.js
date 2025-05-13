$(document).ready(function () {
  console.log("fetch student data");
  $.ajax({
    type: "post",
    url: "server/fetch-student-list.php",
    dataType: "json",
    success: function (response) {
      console.log(response);
      response.forEach((element) => {
        $("table tbody").append(`<tr>
            <td>${element.id}</td>
            <td>${element.full_name}</td>
            <td>${element.email}</td>
            <td>${element.phone}</td>
            <td>${element.city}</td>
            <td>
               <button class="btn btn-success btn-sm text-white view-btn" data-id="${element.id}">View</button>
               <button class="btn btn-warning btn-sm text-white edit-btn" data-id="${element.id}">Edit</button>
               <button class="btn btn-danger btn-sm text-white delete-btn" data-id="${element.id}">Delete</button>
               <button class="btn btn-info btn-sm text-white pdf-btn" data-id="${element.id}">PDF</button>
            </td>
          </tr>`);
      });
    },
  });
});
