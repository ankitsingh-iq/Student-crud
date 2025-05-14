$(document).ready();

// // loaddata()
$(document).ready(function ()
    {
      $("#myform").validate({
        rules: {
          full_name:{
              required: true
          },
          dob:{
              required: true
          },
          email: {
              required: true,
              email: true
          },
          phone: {
              required: true,
              minlength: 10,
              maxlength: 10
          },
          gender: {
              required: true
          },
          address: {
              required: true
          },
          pincode: {
              required: true,
              maxlength:6
          },
          country: {
              required: true
          },
          state: {
              required: true
          },
          city: {
              required: true
          },
          documents:
          {
              fileRequired: true
          }
        },
        messages: {
            full_name: {
                required: "Name is required",
            },
            dob: {
                required: "Date of Birth is required"
            },
            email: {
                required: "Email is required",
                email: "Enter a valid email address"
            },
            phone: {
                required: "Phone number is required",
                minlength: "Phone number must be 10 digits",
                maxlength: "Phone number must be 10 digits"
            },
            gender: {
                required: "Please select gender"
            },
            address: {
                required: "Address is required"
            },
            pincode: {
                required: "Pincode is required",
                maxlength:"Pincode have maximum 6 length"
            },
            country: {
                required: "Country is required"
            },
            state: {
                required: "State is required"
            },
            city: {
                required: "City is required"
            },
            documents: {
                required: "Please upload documents"
            }
        },
        errorElement: "div",
      errorClass: "text-danger"

    })
})
$.validator.addMethod("fileRequired", function (value, element)
{
    return element.files.length > 0;
}, "Please upload at least one file.");
