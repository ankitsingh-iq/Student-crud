let selectedFiles = [], existingFiles = [], removedFiles = [];

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('studentForm');
    const tableBody = document.getElementById('studentTableBody');
    resetForm();
    fetchtableData();

    form?.addEventListener('submit', async function (e) {
        e.preventDefault();

        const isUpdate = form.dataset.updateId !== '';
        const formData = await getFormData();

        const endpoint = isUpdate ? 'server/edit.php' : 'server/insert.php';

        if (isUpdate) {
            console.log("updating");
            formData.append('id', form.dataset.updateId);
        }
        selectedFiles.forEach((file, index) => formData.append(`files[${index}]`, file));
        formData.forEach((value, key) => {
            if (value instanceof File) {
                console.log(`${key}: ${value.name}`);
            } else {
                console.log(`${key}: ${value}`);
            }
        });

        $.ajax({
            url: endpoint,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (data) {
                if (data.status === 'success') {
                    resetForm();
                    fetchtableData();
                    showSuccessAlert('Success!', data.message);
                } else {
                    showErrorAlert('Error!', data.message);
                    showValidationErrors(data.errors);
                }
            },
            error: function (error) {
                console.error('Error:', error);
                showErrorAlert('Error!', 'Unexpected error.');
            }
        })
    });

    tableBody.addEventListener('click', function (e) {
        const Id = e.target.dataset.id;

        if (e.target.classList.contains('edit-btn')) handleEdit(Id);

        if (e.target.classList.contains('delete-btn')) handleDelete(Id);

        if (e.target.classList.contains('PDF-generate')) handlePDF(Id);

    });
});

function fetchtableData() {
    $.ajax({
        url: 'server/fetch.php',
        dataType: 'json',
        success: function (data) {
            document.getElementById('studentTableBody').innerHTML = data.result;
        }
    })
}

function handleEdit(id) {
    resetForm();
    const formData = new FormData();
    formData.append('id', id);

    $.ajax({
        url: 'server/getStudent.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (data) {
            if (data.status === 'success') {
                // populate fields
                const s = data.data;
                ['studentName', 'dob', 'email', 'phone', 'address', 'pincode', 'country'].forEach(id => {
                    document.getElementById(id).value = s[id] || s.full_name || '';
                });

                document.querySelectorAll('input[name="gender"]').forEach((r => r.checked = r.value === s.gender));

                getStates();
                document.getElementById('state').value = s.state;
                setTimeout(() => {
                    getCities();
                    setTimeout(() => document.getElementById('city').value = s.city, 100);
                }, 100);

                existingFiles = s.documents;
                updateDocs();
                document.getElementById('studentForm').dataset.updateId = id;
                const btn = document.querySelector('button[type="submit"]');
                btn.innerText = 'Update';
                btn.classList.replace('btn-primary', 'btn-warning');
            } else {
                showErrorAlert('Error!', data.message);
            }
        },
        error: function (error) {
            console.error('Error:', error);
            showErrorAlert('Error!', 'Unexpected error.');
        }
    })
}

function handleDelete(id) {
    showConfirmationDialog('Are you sure?', 'This action cannot be undone.', 'Yes, delete it!'
    ).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('id', id);
            $.ajax({
                url: 'server/delete.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (data) {
                    if (data.status === 'success') {
                        showSuccessAlert('Deleted!', data.message || 'Student has been deleted.');
                        fetchtableData();
                    } else {
                        showErrorAlert('Error!', data.message);
                    }
                },
                error: function (error) {
                    console.error('Error:', error);
                    showErrorAlert('Error!', 'Unexpected error.');
                }
            })
        }
    });
}

function handlePDF(id) {
    const formData = new FormData();
    formData.append('id', id);
    $.ajax({
        url: 'server/generatePDF.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (data) {
            if (data.status === 'success') {
                const PDFview = document.getElementById('resultContainer');
                PDFview.innerHTML = data.data;
                PDFview.style.display = 'block';
                document.getElementById('studentForm').style.display = 'none';
                document.getElementById("downloadBtn").addEventListener("click", function () {
                    document.getElementById("btn-g").style.display = 'none';
                    const formdata = new FormData();
                    formdata.append('pdfContent', PDFview.innerHTML);
                    formdata.append('id', id);
                    // Send the PDF content to the server for saving
                    $.ajax({
                        url: 'server/savePDF.php',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function (data) {
                            data.status === 'success' ?
                                showSuccessAlert('Success!', data.message) :
                                showErrorAlert('Error!', data.message);
                        },
                        error: function (error) {
                            console.error('Error:', error);
                            showErrorAlert('Error!', 'Unexpected error.');
                        }
                    })
                    // Hide the PDF view and show the form again
                    resetForm();
                });
                document.getElementById("closeBtn").onclick = resetForm;
            }
            else {
                showErrorAlert('Error!', data.message);
            }
        },
        error: function (error) {
            console.error('Error:', error);
            showErrorAlert('Error!', 'Unexpected error.');
        }
    })
}

function showSuccessAlert(title, message) {
    return Swal.fire(title, message, 'success');
}

function showErrorAlert(title, message) {
    return Swal.fire(title, message || 'Something went wrong.', 'error');
}

function showConfirmationDialog(title, text, confirmText = 'Yes, do it!') {
    return Swal.fire({
        title, text, icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
        confirmButtonText: confirmText
    });
}

const showValidationErrors = (errors = {}) => {
    Object.entries(errors).forEach(([field, messages]) => {
        const el = document.querySelector(`.${field}-error`);
        if (el) {
            el.innerText = messages.join(', ');
            el.classList.add('text-danger');
            el.style.display = 'block';
        }
    });
};

// Utility function to reset form
function resetForm() {
    selectedFiles = [];
    existingFiles = [];
    removedFiles = [];

    const form = document.getElementById('studentForm');
    form.reset();
    form.style.display = 'block';
    form.dataset.updateId = '';

    const PDFview = document.getElementById('resultContainer');
    PDFview.innerHTML = '';
    PDFview.style.display = 'none';

    document.querySelector('.imagePreview').innerHTML = '';

    document.querySelectorAll('.text-danger').forEach(el => {
        el.innerText = '';
        el.classList.remove('text-danger');
        el.style.display = 'none';
    });

    const submitButton = document.querySelector('button[type="submit"]');
    if (submitButton) {
        submitButton.innerText = 'Save';
        submitButton.classList.replace('btn-warning', 'btn-primary');
    }
}

// Function to handle country selection and populate states
function getStates() {
    const country = getCountry();
    const stateSelect = document.getElementById('state');
    const citySelect = document.getElementById('city');

    stateSelect.innerHTML = `<option disabled selected>Select State</option>`;
    citySelect.innerHTML = `<option disabled selected>Select City</option>`;
    citySelect.disabled = true;

    const states = getStatesByCountry(country);
    states.forEach(state => stateSelect.add(new Option(state, state)));
    stateSelect.disabled = states.length === 0;
}

// Function to handle state selection and populate cities
function getCities() {
    const state = getState();
    const citySelect = document.getElementById('city');
    const cities = getCitiesByState(state);

    citySelect.innerHTML = `<option disabled selected>Select City</option>`;
    cities.forEach(city => citySelect.add(new Option(city, city)));
    citySelect.disabled = cities.length === 0;
}

// Function to return states based on selected country
function getStatesByCountry(country) {
    return {
        'India': ['Andhra Pradesh', 'Arunachal Pradesh', 'Assam'],
        'USA': ['Alabama', 'Alaska', 'Arizona']
    }[country] || []
}


// Function to return cities based on selected state
function getCitiesByState(state) {
    return {
        'Andhra Pradesh': ['Visakhapatnam', 'Vijayawada'],
        'Arunachal Pradesh': ['Itanagar', 'Naharlagun'],
        'Assam': ['Guwahati', 'Silchar'],
        'Alabama': ['Birmingham', 'Montgomery'],
        'Alaska': ['Anchorage', 'Juneau'],
        'Arizona': ['Phoenix', 'Tucson']
    }[state] || [];
}

// Utility functions to get selected values
function getCountry() { return document.getElementById('country').value; }
function getState() { return document.getElementById('state').value; }
function getCity() { return document.getElementById('city').value; }

function updateDocs() {
    const fileInput = document.getElementById('documents');
    const preview = document.querySelector('.imagePreview');
    const size = 5 * 1024 * 1024;
    preview.innerHTML = "";

    // Display previously uploaded files
    existingFiles.forEach(file => {
        const div = createPreviewDiv(`/../uploads/${file}`, file);
        div.querySelector('button').onclick = () => {
            removedFiles.push(file);
            existingFiles = existingFiles.filter(f => f !== file);
            preview.removeChild(div);
        };
        preview.appendChild(div);
    });

    Array.from(fileInput.files).forEach(file => {
        if (!file.type.startsWith('image/')) return showErrorAlert('Invalid File', 'Only image files allowed.');
        if (file.size > size) return showErrorAlert('File Too Large', 'Each file < 5MB.');
        if ((selectedFiles.length + existingFiles.length) >= 5)
            return showErrorAlert('Limit Reached', 'Max 5 files allowed.');
        if (selectedFiles.some(f => f.name === file.name && f.size === file.size) || existingFiles.some(f => f === file.name))
            return showErrorAlert('Duplicate File', `"${file.name}" is already added.`);

        selectedFiles.push(file);

    });
    fileInput.value = '';

    selectedFiles.forEach(file => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const div = createPreviewDiv(e.target.result, file.name);
            div.querySelector('button').onclick = () => {
                selectedFiles = selectedFiles.filter(f => !(f.name === file.name));
                preview.removeChild(div);
            };
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

const createPreviewDiv = (src, name) => {
    const div = document.createElement('div');
    div.className = 'img-thumbnail m-2 position-relative';
    div.style = 'display:inline-block;width:100px;';
    div.dataset.fileName = name;
    div.innerHTML = `
        <img src="${src}" style="width: 100%; height: auto;" />
        <button class="btn btn-sm btn-danger position-absolute top-0 end-0" title="Remove">&times;</button>
    `;
    return div;
};

// Utility function to get form data
async function getFormData() {
    const form = document.getElementById('studentForm');
    const formData = new FormData(form);

    if (existingFiles.length) formData.append('existingFiles', existingFiles.join(', ')); // previously uploaded & not deleted
    if (removedFiles.length) formData.append('removedFiles', removedFiles.join(', ')); // to delete from server
    return formData;
}
