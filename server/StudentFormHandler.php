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

        if (empty($this->errors)) {
            $this->removeUploads();
            $uploadedFiles = $this->handleUploads();

            if ($uploadedFiles !== false) {
                $existing = isset($formData['existingFiles']) ? array_filter(array_map('trim', explode(',', $formData['existingFiles']))) : [];
                $allFiles = array_merge($existing, $uploadedFiles);
                $this->fields['documents']['value'] = implode(", ", $allFiles);
            }
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
                        $time = strtotime($value);
                        if (!$time) $this->addError($name, "{$field['label']} must be a valid date.");
                        if ($time > time()) $this->addError($name, "{$field['label']} cannot be greater than current date.");
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
        $hasNewFiles = isset($_FILES['files']) && !empty($_FILES['files']['name'][0]);
        $existingFiles = isset($_POST['existingFiles']) ? array_filter(explode(',', $_POST['existingFiles'])) : [];

        if (!$hasNewFiles && count($existingFiles) === 0) {
            $this->addError('documents', "Please upload at least one document.");
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
        $files = $_FILES['files'];
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

            $unique = $_FILES['files']['name'][$i];
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
        $fields = implode(', ', array_map(fn($f) => "`$f`", ['full_name', 'dob', 'email', 'phone', 'gender', 'address', 'pincode', 'country', 'state', 'city', 'documents']));
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
        if ($stmt->execute()) {
            $stmt->affected_rows === 0 ?
                $this->statusMsg = "No changes made." :
                $this->statusMsg = "Record updated successfully!";
        } else {
            $this->statusMsg = "Update failed.";
        }
        $stmt->close();
    }
}
