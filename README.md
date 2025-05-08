

## 📂 Project Structure

```
project-root/
│
├── index.html                 # Main HTML file
│
├── assets/                    # All static assets like images, fonts, etc.
│   ├── images/                # Folder for images
│   └── fonts/                 # Folder for fonts
│
├── css/                       # Stylesheets
│   └── styles.css             # Custom styles if any
│
├── js/                        # JavaScript files
│   └── main.js                # Main JS logic for your form handling
│
├── vendor/                    # External libraries (Bootstrap, jQuery, etc.)
│   ├── bootstrap/             # Bootstrap files
│   └── jquery/                # jQuery files
│
├── uploads/                   # For storing uploaded documents
│
└── server/                    # Backend PHP scripts
    ├── db.php                 # Database connection
    ├── insert.php             # Insert student data
    ├── update.php             # Update student data
    ├── delete.php             # Delete student data
    └── fetch.php              # Fetch student data
```

---

## 📝 Description

* **index.html**: Main HTML page containing the form and student data list.
* **assets/**: Holds images and fonts used in the project.
* **css/**: Contains custom CSS for styling.
* **js/**: Holds JavaScript logic for form validation, AJAX, and dynamic UI updates.
* **vendor/**: Contains external libraries like Bootstrap and jQuery.
* **uploads/**: Stores all uploaded documents safely.
* **server/**: Contains PHP scripts for database operations (CRUD).

  * `db.php`: Handles the database connection.
  * `insert.php`: Adds new student records.
  * `update.php`: Updates existing student records.
  * `delete.php`: Deletes a student record.
  * `fetch.php`: Retrieves student data to display in the table.

---

# Student Data Management System Roadmap

This roadmap outlines the structured steps to create a fully functional Student Data Management System using PHP, MySQL, and Bootstrap for UI.

## **Phase 1: Database Design**

1. Create a MySQL database named **student\_management**.
2. Design a table called **students** with the following fields:

   * `id` (Primary Key, Auto Increment)
   * `full_name` (VARCHAR)
   * `dob` (DATE)
   * `email` (VARCHAR)
   * `phone` (VARCHAR)
   * `gender` (ENUM: 'Male', 'Female', 'Other')
   * `address` (TEXT)
   * `pincode` (VARCHAR)
   * `country` (VARCHAR)
   * `state` (VARCHAR)
   * `city` (VARCHAR)
   * `documents` (TEXT, for storing file paths if required)

## **Phase 2: Database Connection**

1. Create a file named **db\_connect.php**.
2. Establish a connection to the MySQL database using **MySQLi** or **PDO**.
3. Include error handling for failed connections.

## **Phase 3: CRUD Operations**

### Create (Insert Logic)

* Capture form data upon submission.
* Validate and sanitize the inputs.
* Execute an `INSERT` SQL query to store the data.

### Read (Fetch Logic)

* Fetch all student records from the database.
* Use a `SELECT` query and render the data inside the HTML table.

### Update (Edit Logic)

* When 'Edit' is clicked, fetch the student's data and populate the form fields.
* Allow modifications and execute an `UPDATE` SQL query to save changes.

### Delete (Delete Logic)

* Triggered when 'Delete' is clicked.
* Remove the student record from the database using a `DELETE` query.
* Implement AJAX for smooth deletion without page refresh.

## **Phase 4: Additional Functionalities**

### Document Upload Handling

* Manage file uploads and store paths in the database.
* Implement validation (file type, size).

### Import/Export Logic

* **Import:** Parse CSV files and insert multiple records.
* **Export:** Generate CSV or Excel files of student records.

### PDF Generation (Optional)

* Use **TCPDF** or **FPDF** to generate student information PDFs.


