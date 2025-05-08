

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

