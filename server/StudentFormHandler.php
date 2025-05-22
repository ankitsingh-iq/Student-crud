
<?php
class FormHandler
{
    public $fields = [];
    public $errors = [];
    public $statusMsg = "";
    private $conn;
    private $tableName;

    public function __construct($conn, $tableName)
    {
        $this->conn = $conn;
        $this->tableName = $tableName;
        $this->fields = [
            'studentName' => ['label' => 'Name', 'rules' => ['required', 'alpha', 'min:2', 'max:50']],
            'dob'         => ['label' => 'DOB', 'rules' => ['required', 'date']],
            'email'       => ['label' => 'Email', 'rules' => ['required', 'email', 'unique:email']],
            'phone'       => ['label' => 'Phone', 'rules' => ['required', 'num', 'min:10', 'max:10']],
            'gender'      => ['label' => 'Gender', 'rules' => ['required', 'alpha']],
            'address'     => ['label' => 'Address', 'rules' => ['required', 'min:5', 'max:100']],
            'pincode'     => ['label' => 'Pincode', 'rules' => ['required', 'num', 'min:6', 'max:6']],
            'country'     => ['label' => 'Country', 'rules' => ['required', 'alpha']],
            'state'       => ['label' => 'State', 'rules' => ['required', 'alpha']],
            'city'        => ['label' => 'City', 'rules' => ['required', 'alpha']],
            'documents'   => ['label' => 'Documents', 'rules' => []],
        ];
    }

    public function handlePost($id = null)
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;

        $this->errors = [];
        $formData = $_POST;

        foreach ($this->fields as $key => &$field) {
            $field['value'] = trim($formData[$key] ?? '');
        }

        $this->validateFields($id);
        $uploadedFiles = $this->handleUploads();
        $this->removeUploads();

        if ($uploadedFiles !== false) {
            $existing = isset($formData['existingFiles']) ? array_filter(array_map('trim', explode(',', $formData['existingFiles']))) : [];
            $allFiles = array_merge($existing, $uploadedFiles);
            $this->fields['documents']['value'] = implode(", ", $allFiles);

            if (!$id && count($allFiles) === 0) {
                $this->errors['documents'][] = "At least one document is required.";
            }
        }

        if (empty($this->errors)) {
            $id ? $this->updateDatabase($id) : $this->saveToDatabase();
        } else {
            $this->statusMsg = "Validation failed!";
        }
    }

    private function validateFields($id = null)
    {
        foreach ($this->fields as $name => $field) {
            $value = $field['value'];
            foreach ($field['rules'] as $rule) {
                if (!empty($this->errors[$name])) break;
                [$ruleName, $param] = array_pad(explode(':', $rule), 2, null);

                switch ($ruleName) {
                    case 'required':
                        if ($value === '') $this->addError($name, "{$field['label']} is required.");
                        break;
                    case 'alpha':
                        if (!preg_match('/^[a-zA-Z ]+$/', $value)) $this->addError($name, "{$field['label']} must contain only letters.");
                        break;
                    case 'num':
                        if (!preg_match('/^\d+$/', $value)) $this->addError($name, "{$field['label']} must contain only numbers.");
                        break;
                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) $this->addError($name, "{$field['label']} must be a valid email.");
                        break;
                    case 'min':
                        if (strlen($value) < (int)$param) $this->addError($name, "{$field['label']} must be at least $param characters.");
                        break;
                    case 'max':
                        if (strlen($value) > (int)$param) $this->addError($name, "{$field['label']} must be less than $param characters.");
                        break;
                    case 'date':
                        if (!strtotime($value)) $this->addError($name, "{$field['label']} must be a valid date.");
                        break;
                    case 'unique':
                        $query = $id ? "SELECT id FROM $this->tableName WHERE $param = ? AND id != ?" : "SELECT id FROM $this->tableName WHERE $param = ?";
                        $stmt = $this->conn->prepare($query);
                        $id ? $stmt->bind_param("si", $value, $id) : $stmt->bind_param("s", $value);
                        $stmt->execute();
                        if ($stmt->get_result()->num_rows > 0) $this->addError($name, "{$field['label']} already exists.");
                        $stmt->close();
                        break;
                }
            }
        }
    }
    private function removeUploads()
    {
        // Handle removedFiles for file update
        $removedFiles = isset($_POST['removedFiles']) ? array_filter(array_map('trim', explode(',', $_POST['removedFiles']))) : [];

        // Delete removed files from uploads directory
        $uploadDir = __DIR__ . '/../uploads/';
        foreach ($removedFiles as $file) {
            $filePath = $uploadDir . $file;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }
    private function handleUploads()
    {
        if (empty($_FILES['files']['name'])) return [];

        $uploadDir = __DIR__ . '/../uploads/';
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $maxSize = 5 * 1024 * 1024;
        $files = [];

        foreach ($_FILES['files']['name'] as $i => $name) {
            $tmp = $_FILES['files']['tmp_name'][$i];
            $size = $_FILES['files']['size'][$i];
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                $this->addError('documents', "Disallowed file type: $name");
                continue;
            }
            if ($size > $maxSize) {
                $this->addError('documents', "File too large: $name");
                continue;
            }

            $unique = uniqid() . ".$ext";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            if (move_uploaded_file($tmp, $uploadDir . $unique)) {
                $files[] = $unique;
            } else {
                $this->addError('documents', "Failed to upload: $name");
            }
        }

        return count($files) ? $files : false;
    }

    private function addError($field, $message)
    {
        $this->errors[$field][] = $message;
    }

    private function saveToDatabase()
    {
        $keys = ['studentName', 'dob', 'email', 'phone', 'gender', 'address', 'pincode', 'country', 'state', 'city', 'documents'];
        $placeholders = implode(', ', array_fill(0, count($keys), '?'));
        $fields = implode(', ', ['full_name', 'dob', 'email', 'phone', 'gender', 'address', 'pincode', 'country', 'state', 'city', 'documents']);
        $sql = "INSERT INTO `$this->tableName` ($fields) VALUES ($placeholders)";
        $stmt = $this->conn->prepare($sql);
        $values = array_map(fn($k) => $this->fields[$k]['value'], $keys);
        $stmt->bind_param(str_repeat('s', count($values)), ...$values);
        $stmt->execute() ? $this->statusMsg = "New record created successfully!" : $this->addError('database', $stmt->error);
        $stmt->close();
    }

    private function updateDatabase($id)
    {
        $keys = ['studentName', 'dob', 'email', 'phone', 'gender', 'address', 'pincode', 'country', 'state', 'city', 'documents'];
        $setClause = implode(', ', array_map(fn($k) => "$k = ?", ['full_name', 'dob', 'email', 'phone', 'gender', 'address', 'pincode', 'country', 'state', 'city', 'documents']));
        $sql = "UPDATE `$this->tableName` SET $setClause WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        $values = array_map(fn($k) => $this->fields[$k]['value'], $keys);
        $values[] = $id;
        $stmt->bind_param(str_repeat('s', count($values) - 1) . 'i', ...$values);
        $stmt->execute() ? $this->statusMsg = "Record updated successfully!" : $this->statusMsg = "Update failed.";
        $stmt->close();
    }
}




// class FormHandler
// {
//     public $fields = [];
//     public $errors = [];
//     public $statusMsg = "";
//     private $conn;
//     private $tableName;

//     public function __construct($conn, $tableName)
//     {
//         $this->conn = $conn;
//         $this->tableName = $tableName;
//         // Define all fields and validation rules
//         $this->fields = [
//             'studentName' => [
//                 'label' => 'Name',
//                 'value' => '',
//                 'rules' => ['required', 'alpha', 'min:2', 'max:50']
//             ],
//             'dob' => [
//                 'label' => 'dob',
//                 'value' => '',
//                 'rules' => ['required', 'date'],
//             ],
//             'email' => [
//                 'label' => 'Email',
//                 'value' => '',
//                 'rules' => ['required', 'email', "unique:email"]
//             ],
//             'phone' => [
//                 'label' => 'Phone',
//                 'value' => '',
//                 'rules' => ['required', 'num', 'min:10', 'max:10']
//             ],
//             'gender' => [
//                 'label' => 'gender',
//                 'value' => '',
//                 'rules' => ['required', 'alpha']
//             ],
//             'address' => [
//                 'label' => 'Address',
//                 'value' => '',
//                 'rules' => ['required', 'min:5', 'max:100']
//             ],
//             'pincode' => [
//                 'label' => 'pincode',
//                 'value' => '',
//                 'rules' => ['required', 'num', 'min:6', 'max:6']
//             ],
//             'country' => [
//                 'label' => 'Country',
//                 'value' => '',
//                 'rules' => ['required', 'alpha']
//             ],
//             'state' => [
//                 'label' => 'State',
//                 'value' => '',
//                 'rules' => ['required', 'alpha']
//             ],
//             'city' => [
//                 'label' => 'City',
//                 'value' => '',
//                 'rules' => ['required', 'alpha']
//             ],
//             'documents' => [
//                 'label' => 'Documents',
//                 'value' => '',
//                 'rules' => []
//             ],
//         ];
//     }
//     public function handlePost($id = null)
//     {
//         if ($_SERVER["REQUEST_METHOD"] === "POST") {
//             $uploadDir = __DIR__ . '/../uploads/';
//             $formData = $_POST;

//             // Clear previous errors
//             $this->errors = [];

//             // Clear previous values
//             foreach ($this->fields as $key => &$field) {
//                 $field['value'] = '';
//             }
//             // Populate fields with POST data
//             foreach ($this->fields as $key => &$field) {
//                 $field['value'] = trim($formData[$key] ?? '');
//             }

//             // Validate each field
//             $this->validate($id);

//             if (!empty($_FILES['files']['name'])) {
//                 foreach ($_FILES['files']['name'] as $key => $name) {
//                     $tmpName = $_FILES['files']['tmp_name'][$key];
//                     $name = basename($name); // sanitize name
//                     $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
//                     $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
//                     $maxSize = 5 * 1024 * 1024; // 5MB

//                     if (!in_array($ext, $allowedTypes)) {
//                         $this->errors["documents"][] = "Disallowed file type: $name";
//                         continue; // skip to next iteration
//                     }

//                     if ($_FILES['files']['size'][$key] > $maxSize) {
//                         $this->errors["documents"][] = "File too large: $name";
//                         continue; // skip to next iteration
//                     }

//                     $uniqueName = uniqid() . '.' . $ext;
//                     $destination = $uploadDir . $uniqueName;

//                     if (!is_dir($uploadDir)) {
//                         mkdir($uploadDir, 0755, true);
//                     }

//                     if (move_uploaded_file($tmpName, $destination)) {
//                         $response['files'][] = $uniqueName;
//                     } else {
//                         $this->errors["documents"][] = "Failed to move file: $name to $destination";
//                     }
//                 }

//                 if (count($response['files']) > 0) {
//                     $filesName = implode(", ", $response['files']);
//                     if ($this->fields['documents']['value'] !== "") $this->fields['documents']['value'] .= ", " . $filesName;
//                     else $this->fields['documents']['value'] = $filesName;
//                 } else {
//                     $this->errors["documents"][] = 'No valid files uploaded.';
//                 }
//             } else {
//                 if (!isset($formData['existingFiles'])) $this->errors["documents"][] = 'No files received.';
//             }

//             // Custom validation for documents presence on update and insert
//             $existingFiles = isset($formData['existingFiles']) ? array_filter(array_map('trim', explode(',', $formData['existingFiles']))) : [];
//             $newDocuments = $this->fields['documents']['value'] !== '' ? array_filter(array_map('trim', explode(',', $this->fields['documents']['value']))) : [];
//             $allDocuments = array_merge($existingFiles, $newDocuments);
//             if ($id === null && count($allDocuments) === 0) {
//                 $this->errors['documents'][] = $this->fields['documents']['value'];
//             }

//             // Check if there are any errors
//             if (empty($this->errors)) {
//                 // If no errors, save to database
//                 if ($id) {
//                     $this->updateDatabase($id);
//                 } else {
//                     $this->saveToDatabase();
//                 }
//             } else {
//                 $this->statusMsg = "Validation failed!";
//             }
//         }
//     }


//     public function validate($id = null)
//     {
//         foreach ($this->fields as $name => $field) {
//             if (empty($field['rules'])) {
//                 continue;
//             }
//             $value = $field['value'];
//             $label = $field['label'];

//             foreach ($field['rules'] as $rule) {
//                 if (!empty($this->errors[$name])) {
//                     break;
//                 }
//                 $ruleName = $rule;
//                 $param = null;

//                 if (strpos($rule, ':') !== false) {
//                     [$ruleName, $param] = explode(':', $rule, 2);
//                     [$ruleName, $param] = array_map('trim', [$ruleName, $param]);
//                 }

//                 switch ($ruleName) {
//                     case 'required':
//                         if ($value === '') {
//                             $this->errors[$name][] = "$label is required.";
//                         }
//                         break;
//                     case 'alpha':
//                         if (!preg_match('/^[a-zA-Z ]+$/', $value)) {
//                             $this->errors[$name][] = "$label must contain only letters and spaces.";
//                         }
//                         break;
//                     case 'address':
//                         if (!preg_match('/^[a-zA-Z0-9-\/, ]+$/', $value)) {
//                             $this->errors[$name][] = "$label must be a valid address.";
//                         }
//                         break;
//                     case 'email':
//                         if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
//                             $this->errors[$name][] = "$label must be a valid email address.";
//                         }
//                         break;
//                     case 'min':
//                         if (strlen($value) < (int)$param) {
//                             $this->errors[$name][] = "$label must be at least $param characters.";
//                         }
//                         break;
//                     case 'max':
//                         if (strlen($value) > (int)$param) {
//                             $this->errors[$name][] = "$label must be less than $param characters.";
//                         }
//                         break;
//                     case 'date':
//                         if (!strtotime($value)) {
//                             $this->errors[$name][] = "$label must be a valid date.";
//                         }
//                         break;
//                     case 'num':
//                         if (!preg_match('/^[0-9]+$/', $value)) {
//                             $this->errors[$name][] = "$label must contain only numbers.";
//                         }
//                         break;
//                     case 'unique':
//                         if ($id) {
//                             // Exclude the current record from the uniqueness check
//                             $sql = "SELECT * FROM $this->tableName WHERE $param = ? AND id != ?";
//                             $stmt = $this->conn->prepare($sql);
//                             $stmt->bind_param("si", $value, $id);
//                         } else {
//                             // Check for uniqueness
//                             $sql = "SELECT * FROM $this->tableName WHERE $param = ?";
//                             $stmt = $this->conn->prepare($sql);
//                             $stmt->bind_param("s", $value);
//                         }
//                         $stmt->execute();
//                         $result = $stmt->get_result();
//                         if ($result->num_rows > 0) {
//                             $this->errors[$name][] = "$label already exists.";
//                         }
//                         break;
//                 }
//             }
//         }
//     }
//     public function saveToDatabase()
//     {
//         $sql = "INSERT INTO `$this->tableName` (full_name, dob, email, phone, gender, address, pincode, country, state, city, documents)
//             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";

//         $stmt = $this->conn->prepare($sql);

//         $stmt->bind_param(
//             "sssssssssss",
//             $this->fields['studentName']['value'],
//             $this->fields['dob']['value'],
//             $this->fields['email']['value'],
//             $this->fields['phone']['value'],
//             $this->fields['gender']['value'],
//             $this->fields['address']['value'],
//             $this->fields['pincode']['value'],
//             $this->fields['country']['value'],
//             $this->fields['state']['value'],
//             $this->fields['city']['value'],
//             $this->fields['documents']['value'],
//         );

//         if ($stmt->execute()) {
//             $this->statusMsg = "New record created successfully!";
//         } else {
//             $this->errors['database'][] = "Database error: " . $stmt->error;
//             $this->statusMsg = "Failed to create record.";
//         }

//         $stmt->close();
//     }
//     public function updateDatabase($id)
//     {
//         $sql = "UPDATE `$this->tableName` SET full_name=?, dob=?, email=?, phone=?, gender=?, address=?, pincode=?, country=?, state=?, city=?, documents=? WHERE id=?";
//         $stmt = $this->conn->prepare($sql);
//         $stmt->bind_param(
//             "sssssssssssi",
//             $this->fields['studentName']['value'],
//             $this->fields['dob']['value'],
//             $this->fields['email']['value'],
//             $this->fields['phone']['value'],
//             $this->fields['gender']['value'],
//             $this->fields['address']['value'],
//             $this->fields['pincode']['value'],
//             $this->fields['country']['value'],
//             $this->fields['state']['value'],
//             $this->fields['city']['value'],
//             $this->fields['documents']['value'],
//             $id
//         );
//         if ($stmt->execute()) {
//             $this->statusMsg = "Record updated successfully!";
//         } else {
//             $this->statusMsg = "Failed to update record.";
//         }
//         $stmt->close();
//     }
// }
