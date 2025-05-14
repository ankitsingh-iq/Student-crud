document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('studentForm');
    const params = new URLSearchParams(window.location.search);
    const messageSuccess = params.get('success');
    console.log(messageSuccess);

    if (messageSuccess) {
        // Show success message
        showSuccessAlert('Success!', messageSuccess);
        // Clear the URL parameters
        const cleanURL = window.location.origin + window.location.pathname;
        window.history.replaceState({}, document.title, cleanURL);
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(form);
        document.querySelectorAll('.text-danger').forEach(el => {
            el.innerText = '';
            el.style.display = 'none';
        });
        // console.log(form.attributes['action'].value=== '');

        if (form.attributes['action'].value === '') {
            fetch('server/insert.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // console.log(data.data);
                        window.location.href = window.location.pathname + '?success=' + encodeURIComponent(data.message);
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
            formData.append('id', form.getAttribute('action'));
            console.log(formData.get('id'));
            fetch('server/edit.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        form.setAttribute('action', '');
                        window.location.href = window.location.pathname + '?success=' + encodeURIComponent(data.message);
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
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const studentId = this.getAttribute('data-id');
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
                        console.log(student);
                        // // Populate the form with student data
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
                        document.getElementById('state').value = student.state;
                        document.getElementById('city').value = student.city;
                        // Show the form
                        document.getElementById('studentForm').scrollIntoView({ behavior: 'smooth' });
                        // Change the form action to update
                        form.setAttribute('action', studentId);
                        // Change the button text to "Update"
                        const submitButton = document.querySelector('button[type="submit"]');
                        if (submitButton) {
                            submitButton.innerText = 'Update';
                            submitButton.classList.remove('btn-primary');
                            submitButton.classList.add('btn-warning');
                        }
                    } else {
                        showErrorAlert('Error!', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorAlert('Error!', 'Unexpected error.');
                });
        });
    });


    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const studentId = this.getAttribute('data-id');
            const row = this.closest('tr');

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
                                showSuccessAlert('Deleted!', data.message || 'Student has been deleted.');
                                // Remove row from HTML table
                                if (row) row.remove();
                                if (document.querySelectorAll('tbody tr').length === 0) {
                                    window.location.reload();
                                }
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
        });
    });
    document.querySelectorAll('.PDF-generate').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const studentId = this.getAttribute('data-id');
            const formData = new FormData();
            formData.append('id', studentId);
            fetch('server/generatePDF.php', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const byteCharacters = atob(data.pdf);
                        const byteNumbers = new Array(byteCharacters.length).fill().map((_, i) => byteCharacters.charCodeAt(i));
                        const byteArray = new Uint8Array(byteNumbers);
                        const blob = new Blob([byteArray], { type: 'application/pdf' });
                        const blobUrl = URL.createObjectURL(blob);
                        window.open(blobUrl, '_blank');

                        // Optional: trigger save on user action
                        document.getElementById('downloadBtn').onclick = function () {
                            const formData = new FormData();
                            formData.append('pdf', data.pdf);
                            formData.append('filename', 'student_' + studentId + '.pdf');

                            fetch('server/savePDF.php', {
                                method: 'POST',
                                body: formData
                            }).then(res => res.json()).then(resp => {
                                Swal.fire('Saved!', resp.message, 'success');
                            });
                        };
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorAlert('Error!', 'Unexpected error.');
                });
        });
    });

});

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
