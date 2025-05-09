$(document).ready(function(){
    console.log("fetch student data");
    $.ajax({
        type: "post",
        url: "server/fetch-student-list.php",
        dataType: "json",
        success: function (response) {
            console.log(response);
            response.forEach(element => {
                $('table tbody').append(`<tr>
            <td>${element.id}</td>
            <td>${element.full_name}</td>
            <td>${element.email}</td>
            <td>${element.phone}</td>
            <td>${element.city}</td>
            <td>
               <a href="view.php?id=${element.id}" class="btn btn-success btn-sm text-white">View</a>
               <a href="edit.php?id=${element.id}" class="btn btn-warning btn-sm text-white">Edit</a>
               <a href="server/delete.php?id=${element.id}" class="btn btn-danger btn-sm text-white">Delete</a>
               <a href="pdf.php?id=${element.id}" class="btn btn-info btn-sm text-white">PDF</a>
            </td>
          </tr>`);
            });
            
        }

    });
});