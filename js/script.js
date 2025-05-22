let selectedFiles = [];
let existingFiles = [];
let removedFiles = [];

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('studentForm');
    resetForm();
    // fetch student data from server on load
    fetchtableData();

    form?.addEventListener('submit', async function (e) {
        e.preventDefault();

        const isUpdate = form.getAttribute('data-update-id') !== '';
        const formData = await getFormData();

        const endpoint = isUpdate ? 'server/edit.php' : 'server/insert.php';

        if (isUpdate) {
            formData.append('id', form.getAttribute('data-update-id'));
        }
        selectedFiles.forEach(file => formData.append('files[]', file));

        // show form data in console
        formData.forEach((value, key) => key == 'files[]' ?
            console.log(`${key}: ${value.name}`) :
            console.log(`${key}: ${value}`));
        fetch(endpoint, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    console.log(data.message);
                    resetForm();
                    fetchtableData();
                    showSuccessAlert('Success!', data.message);
                } else {
                    showErrorAlert('Error!', data.message);
                    for (const field in data.errors) {
                        const errorMessages = data.errors[field];
                        const error = document.querySelector(`.${field}-error`);
                        if (error) {
                            error.innerText = errorMessages.join(', ');
                            error.style.display = 'block';
                            error.classList.add('text-danger');
                        }
                    }
                }
            })
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
    resetForm();
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
                console.log(student.documents);

                existingFiles = student.documents;
                updateDocs();

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
                    resetForm();
                });
                document.getElementById("closeBtn").addEventListener("click", function () {
                    // Hide the PDF view and show the form again
                    resetForm();
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

// Utility function to reset form
function resetForm() {
    selectedFiles = [];
    existingFiles = [];
    removedFiles = [];

    const form = document.getElementById('studentForm');
    if (form) {
        form.setAttribute('data-update-id', '');
        form.style.display = 'block';
        form.reset();
    }

    const PDFview = document.getElementById('resultContainer');
    if (PDFview) {
        PDFview.style.display = 'none';
        PDFview.innerHTML = '';
    }
    const el = document.querySelector('.imagePreview')
    if (el) el.innerHTML = '';

    const submitButton = document.querySelector('button[type="submit"]');
    if (submitButton) {
        submitButton.innerText = 'Save';
        submitButton.classList.remove('btn-warning');
        submitButton.classList.add('btn-primary');
    }
}

function updateDocs() {
    const fileInput = document.getElementById('documents');
    const previewContainer = document.querySelector('.imagePreview');
    const size = 5 * 1024 * 1024;

    previewContainer.innerHTML = ''; // clear old previews

    // Display previously uploaded files
    if (existingFiles && existingFiles.length > 0) {
        existingFiles.forEach(fileName => {
            const imgDiv = document.createElement('div');
            imgDiv.className = 'img-thumbnail m-2 position-relative';
            imgDiv.style.display = 'inline-block';
            imgDiv.style.width = '100px';
            imgDiv.dataset.existing = 'true';
            imgDiv.dataset.fileName = fileName;

            imgDiv.innerHTML = `
                <img src="/../uploads/${fileName}" style="width: 100%; height: auto;" />
                <button class="btn btn-sm btn-danger position-absolute top-0 end-0" title="Remove">&times;</button>
            `;

            imgDiv.querySelector('button').addEventListener('click', () => {
                removedFiles.push(fileName);
                existingFiles = existingFiles.filter(f => f !== fileName);
                previewContainer.removeChild(imgDiv);
            });
            previewContainer.appendChild(imgDiv);
        });
    }

    Array.from(fileInput.files).forEach((file, index) => {
        if (!file.type.startsWith('image/')) {
            showErrorAlert('Invalid File', 'Only image files are allowed.');
            return;
        }

        if (file.size > size) {
            showErrorAlert('File Too Large', 'Each file must be less than 5MB.');
            return;
        }

        // Enforce max file limit
        if ((selectedFiles.length + existingFiles.length) >= 5) {
            showErrorAlert('Limit Reached', 'You can only upload a maximum of 5 files.');
            return;
        }

        // Check for duplicates by name and size
        const isDuplicate = selectedFiles.some(f => f.name === file.name && f.size === file.size);
        if (isDuplicate) {
            showErrorAlert('Duplicate File', `"${file.name}" has already been added.`);
            return;
        }

        selectedFiles.push(file);

        const reader = new FileReader();
        reader.onload = function (e) {
            const imgDiv = document.createElement('div');
            imgDiv.className = 'img-thumbnail m-2 position-relative';
            imgDiv.style.display = 'inline-block';
            imgDiv.style.width = '100px';

            // Store file in dataset
            imgDiv.dataset.fileName = file.name;
            imgDiv.dataset.fileSize = file.size;

            imgDiv.innerHTML = `
            <img src="${e.target.result}" style="width: 100%; height: auto;" />
            <button class="btn btn-sm btn-danger position-absolute top-0 end-0" title="Remove">&times;</button>`;

            imgDiv.querySelector('button').addEventListener('click', () => {
                // Find index dynamically
                const fileName = imgDiv.dataset.fileName;
                const fileSize = Number(imgDiv.dataset.fileSize);

                const fileIndex = selectedFiles.findIndex(f => f.name === fileName && f.size === fileSize);

                if (fileIndex !== -1) {
                    selectedFiles.splice(fileIndex, 1);
                }

                previewContainer.removeChild(imgDiv);
                if (selectedFiles.length === 0 && existingFiles.length === 0) {
                    previewContainer.innerHTML = '';
                }
                console.log(selectedFiles);
            });
            previewContainer.appendChild(imgDiv);
        };
        reader.readAsDataURL(file);
    });
    // Clear original input so re-selecting same file triggers change
    fileInput.value = '';
}

// Utility function to get form data
async function getFormData() {
    console.log(selectedFiles);

    const form = document.getElementById('studentForm');
    const formData = new FormData(form);

    if (existingFiles.length > 0) {
        formData.append('existingFiles', existingFiles.join(', ')); // previously uploaded & not deleted
    }
    if (removedFiles.length > 0) {
        formData.append('removedFiles', removedFiles.join(', ')); // to delete from server
    }

    return formData;
}
