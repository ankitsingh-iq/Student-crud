
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('studentForm');
    resetForm();
    // fetch student data from server on load
    fetchtableData();

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = getFormData();
        document.querySelectorAll('.text-danger').forEach(el => {
            el.innerText = '';
            el.style.display = 'none';
        });

        if (form.attributes['data-update-id'].value === '') {
            fetch('server/insert.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // console.log(data.data);
                        resetForm();
                        fetchtableData();
                        // Show success message
                        showSuccessAlert('Success!', data.message);
                        // window.location.href = window.location.pathname + '?success=' + encodeURIComponent(data.message);
                    } else {
                        console.log(data.message);
                        showErrorAlert('Error!', data.message);
                        // Show error messages
                        console.log(data.errors);
                        for (const field in data.errors) {
                            const errorMessages = data.errors[field];
                            const error = (document.querySelector(`.${field}-error`));
                            if (error) {
                                error.innerText = errorMessages.join(', ');
                                error.style.display = 'block';
                                error.classList.add('text-danger');
                            }
                        }
                    }
                })
                .catch(error => {
                    console.log('Error:', error);
                    showErrorAlert('Error!', 'Unexpected error.');
                });
        }
        else {
            formData.append('id', form.getAttribute('data-update-id'));
            console.log(formData.get('id'));
            fetch('server/edit.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        resetForm();
                        fetchtableData()
                        // Show success message
                        showSuccessAlert('Success!', data.message);
                        // window.location.href = window.location.pathname + '?success=' + encodeURIComponent(data.message);
                    } else {
                        console.log(data.message);
                        showErrorAlert('Error!', data.message);
                        // Show error messages
                        console.log(data.errors);
                        for (const field in data.errors) {
                            const errorMessages = data.errors[field];
                            const error = (document.querySelector(`.${field}-error`));
                            if (error) {
                                error.innerText = errorMessages.join(', ');
                                error.style.display = 'block';
                                error.classList.add('text-danger');
                            }
                        }
                    }
                })
                .catch(error => {
                    console.log('Error:', error);
                    showErrorAlert('Error!', 'Unexpected error.');
                });
        }
    });
    document.getElementById('studentTableBody').addEventListener('click', function (e) {
        const target = e.target;

        if (target.classList.contains('edit-btn')) {
            const studentId = target.getAttribute('data-id');
            handleEdit(studentId);
        }

        if (target.classList.contains('delete-btn')) {
            const studentId = target.getAttribute('data-id');
            handleDelete(studentId);
        }

        if (target.classList.contains('PDF-generate')) {
            const studentId = target.getAttribute('data-id');
            handlePDF(studentId);
        }
    });
});

function fetchtableData() {
    fetch('server/fetch.php')
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('studentTableBody');
            tableBody.innerHTML = data.result;
        })
}

function handleEdit(studentId) {
    const form = document.getElementById('studentForm');
    const formData = new FormData();
    formData.append('id', studentId);

    fetch('server/getStudent.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const student = data.data;
                // populate fields
                document.getElementById('studentName').value = student.full_name;
                document.getElementById('dob').value = student.dob;
                document.getElementById('email').value = student.email;
                document.getElementById('phone').value = student.phone;

                document.querySelectorAll('input[name="gender"]').forEach(radio => {
                    if (radio.value === student.gender) {
                        radio.checked = true;
                    }
                });

                document.getElementById('address').value = student.address;
                document.getElementById('pincode').value = student.pincode;
                document.getElementById('country').value = student.country;

                getStates();
                document.getElementById('state').value = student.state;

                setTimeout(() => {
                    getCities();
                    setTimeout(() => {
                        document.getElementById('city').value = student.city;
                    }, 100);
                }, 100);

                form.setAttribute('data-update-id', studentId);
                const submitButton = document.querySelector('button[type="submit"]');
                submitButton.innerText = 'Update';
                submitButton.classList.remove('btn-primary');
                submitButton.classList.add('btn-warning');
            } else {
                showErrorAlert('Error!', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorAlert('Error!', 'Unexpected error.');
        });
}

function handleDelete(studentId) {
    showConfirmationDialog(
        'Are you sure?',
        'You won\'t be able to revert this!',
        'Yes, delete it!'
    ).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('id', studentId);

            fetch('server/delete.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        fetchtableData();
                        showSuccessAlert('Deleted!', data.message || 'Student has been deleted.');
                        // Remove row from HTML table
                        // if (row) row.remove();
                        // if (document.querySelectorAll('tbody tr').length === 0) {
                        //     window.location.reload();
                        // }
                    } else {
                        showErrorAlert('Error!', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorAlert('Error!', 'Unexpected error.');
                });
        }
    });
}

function handlePDF(studentId) {
    const formData = new FormData();
    formData.append('id', studentId);
    fetch('server/generatePDF.php', {
        method: 'POST',
        body: formData
    }).then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const PDFview = document.getElementById('resultContainer');
                PDFview.innerHTML = data.data;
                // Show the result container
                PDFview.style.display = 'block';
                PDFview.scrollIntoView({ behavior: 'smooth' });
                // Hide the form
                const form = document.getElementById('studentForm');
                form.style.display = 'none';
                document.getElementById("downloadBtn").addEventListener("click", function () {

                    // clearing buttons from pdf view
                    document.getElementById("btn-g").style.display = 'none';
                    var pdfContent = document.getElementById("resultContainer").innerHTML;
                    const formdata = new FormData();
                    formdata.append('pdfContent', pdfContent);
                    formdata.append('id', studentId);
                    // Send the PDF content to the server for saving
                    fetch('server/savePDF.php', {
                        method: 'POST',
                        body: formdata,
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                showSuccessAlert('Success!', data.message);
                            } else {
                                showErrorAlert('Error!', data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showErrorAlert('Error!', 'Unexpected error.');
                        });
                    // Hide the PDF view and show the form again
                    PDFview.style.display = 'none';
                    PDFview.innerHTML = '';
                    form.style.display = 'block';
                    form.reset();
                });
                document.getElementById("closeBtn").addEventListener("click", function () {
                    // Hide the PDF view and show the form again
                    PDFview.style.display = 'none';
                    PDFview.innerHTML = '';
                    form.style.display = 'block';
                    form.reset();
                });
            }
            else {
                showErrorAlert('Error!', data.message);
                // Show error messages
                console.log(data.errors);
            }
        }
        )
        .catch(error => {
            console.error('Error:', error);
            showErrorAlert('Error!', 'Unexpected error.');
        });
}

function showSuccessAlert(title, message) {
    return Swal.fire(title, message, 'success');
}

function showErrorAlert(title, message) {
    return Swal.fire(title, message || 'Something went wrong.', 'error');
}

function showConfirmationDialog(title, text, confirmText = 'Yes, do it!') {
    return Swal.fire({
        title,
        text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: confirmText,
    });
}
// Function to handle country selection and populate states
function getStates() {
    const country = document.getElementById('country').value;
    const stateSelect = document.getElementById('state');
    const citySelect = document.getElementById('city');

    // Reset state dropdown
    stateSelect.innerHTML = '<option value="" selected disabled>Select State</option>';
    stateSelect.disabled = false;

    // Reset city dropdown
    citySelect.innerHTML = '<option value="" selected disabled>Select City</option>';
    citySelect.disabled = true;

    const states = getStatesByCountry(country);

    if (states.length > 0) {
        states.forEach(state => {
            const option = document.createElement('option');
            option.text = state;
            option.value = state;
            stateSelect.add(option);
        });
    } else {
        stateSelect.disabled = true;
    }

    console.log("Selected country:", country);
    console.log("Available states:", states);
}

// Function to return states based on selected country
function getStatesByCountry(country) {
    const states = {
        'India': ['Andhra Pradesh', 'Arunachal Pradesh', 'Assam',],
        'USA': ['Alabama', 'Alaska', 'Arizona']
    };
    return states[country] || [];
}

// Function to handle state selection and populate cities
function getCities() {
    const state = document.getElementById('state').value;
    const citySelect = document.getElementById('city');

    citySelect.innerHTML = '<option value="" selected disabled>Select City</option>';
    const cities = getCitiesByState(state);

    if (cities.length > 0) {
        cities.forEach(city => {
            const option = document.createElement('option');
            option.text = city;
            option.value = city;
            citySelect.add(option);
        });

        citySelect.disabled = false;
        citySelect.style.display = 'block';
    } else {
        citySelect.disabled = true;
        citySelect.style.display = 'none';
    }
}

// Function to return cities based on selected state
function getCitiesByState(state) {
    const cities = {
        'Andhra Pradesh': ['Visakhapatnam', 'Vijayawada', 'Guntur', 'Nellore', 'Tirupati'],
        'Arunachal Pradesh': ['Itanagar', 'Naharlagun', 'Pasighat', 'Tezpur'],
        'Assam': ['Guwahati', 'Silchar', 'Dibrugarh', 'Jorhat'],
        'Alabama': ['Birmingham', 'Montgomery', 'Huntsville', 'Mobile'],
        'Alaska': ['Anchorage', 'Fairbanks', 'Juneau', 'Sitka'],
        'Arizona': ['Phoenix', 'Tucson', 'Mesa', 'Chandler'],
        // Add more states and their cities as needed
    };
    return cities[state] || [];
}

// Utility functions to get selected values
function getCountry() {
    return document.getElementById('country').value;
}
function getState() {
    return document.getElementById('state').value;
}
function getCity() {
    return document.getElementById('city').value;
}

// Utility function to get form data
function getFormData() {
    const form = document.getElementById('studentForm');
    const formData = new FormData(form);
    return formData;
}
// Utility function to reset form
function resetForm() {
    const form = document.getElementById('studentForm');
    form.reset();
    form.setAttribute('data-update-id', '');
    const submitButton = document.querySelector('button[type="submit"]');
    if (submitButton) {
        submitButton.innerText = 'Save';
        submitButton.classList.remove('btn-warning');
        submitButton.classList.add('btn-primary');
    }
}