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
        // Define all fields and validation rules
        $this->fields = [
            'studentName' => [
                'label' => 'Name',
                'value' => '',
                'rules' => ['required', 'alpha', 'min:2', 'max:50']
            ],
            'dob' => [
                'label' => 'dob',
                'value' => '',
                'rules' => ['required'],
            ],
            'email' => [
                'label' => 'Email',
                'value' => '',
                'rules' => ['required', 'email', "unique:email"]
            ],
            'phone' => [
                'label' => 'Phone',
                'value' => '',
                'rules' => ['required', 'num', 'min:10', 'max:10']
            ],
            'gender' => [
                'label' => 'gender',
                'value' => '',
                'rules' => ['required', 'alpha']
            ],
            'address' => [
                'label' => 'Address',
                'value' => '',
                'rules' => ['required', 'min:5', 'max:100']
            ],
            'pincode' => [
                'label' => 'pincode',
                'value' => '',
                'rules' => ['required', 'num', 'min:6', 'max:6']
            ],
            'country' => [
                'label' => 'Country',
                'value' => '',
                'rules' => ['required', 'alpha']
            ],
            'state' => [
                'label' => 'State',
                'value' => '',
                'rules' => ['required', 'alpha']
            ],
            'city' => [
                'label' => 'City',
                'value' => '',
                'rules' => ['required', 'alpha']
            ],
        ];
    }
    public function handlePost($id = null)
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $formData = $_POST;

            // Clear previous errors
            $this->errors = [];

            // Clear previous values
            foreach ($this->fields as $key => &$field) {
                $field['value'] = '';
            }
            // Populate fields with POST data
            foreach ($this->fields as $key => &$field) {
                $field['value'] = trim($formData[$key] ?? '');
            }
            // Validate each field
            $this->validate($id);
        }
    }

    public function validate($id = null)
    {
        foreach ($this->fields as $name => $field) {
            if (empty($field['rules'])) {
                continue;
            }
            $value = $field['value'];
            $label = $field['label'];

            foreach ($field['rules'] as $rule) {
                if (!empty($this->errors[$name])) {
                    break;
                }
                $ruleName = $rule;
                $param = null;

                if (strpos($rule, ':') !== false) {
                    [$ruleName, $param] = explode(':', $rule, 2);
                    [$ruleName, $param] = array_map('trim', [$ruleName, $param]);
                }

                switch ($ruleName) {
                    case 'required':
                        if ($value === '') {
                            $this->errors[$name][] = "$label is required.";
                        }
                        break;
                    case 'alpha':
                        if (!preg_match('/^[a-zA-Z ]+$/', $value)) {
                            $this->errors[$name][] = "$label must contain only letters and spaces.";
                        }
                        break;
                    case 'address':
                        if (!preg_match('/^[a-zA-Z0-9-\/, ]+$/', $value)) {
                            $this->errors[$name][] = "$label must be a valid address.";
                        }
                        break;
                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $this->errors[$name][] = "$label must be a valid email address.";
                        }
                        break;
                    case 'min':
                        if (strlen($value) < (int)$param) {
                            $this->errors[$name][] = "$label must be at least $param characters.";
                        }
                        break;
                    case 'max':
                        if (strlen($value) > (int)$param) {
                            $this->errors[$name][] = "$label must be less then $param characters.";
                        }
                        break;
                    case 'date':
                        if (!strtotime($value)) {
                            $this->errors[$name][] = "$label must be a valid date.";
                        }
                        break;
                    case 'num':
                        if (!preg_match('/^[0-9]+$/', $value)) {
                            $this->errors[$name][] = "$label must contain only numbers.";
                        }
                        break;
                    case 'unique':
                        if ($id) {
                            // Exclude the current record from the uniqueness check
                            $sql = "SELECT * FROM $this->tableName WHERE $param = ? AND id != ?";
                            $stmt = $this->conn->prepare($sql);
                            $stmt->bind_param("si", $value, $id);
                        } else {
                            // Check for uniqueness
                            $sql = "SELECT * FROM $this->tableName WHERE $param = ?";
                            $stmt = $this->conn->prepare($sql);
                            $stmt->bind_param("s", $value);
                        }
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0) {
                            $this->errors[$name][] = "$label already exists.";
                        }
                        break;
                }
            }
        }
        // Check if there are any errors
        if (empty($this->errors))
        {
            // If no errors, save to database
            if ($id) {
                $this->updateDatabase($id);
            } else {
                $this->saveToDatabase();
            }
        } else {
            $this->statusMsg = "Validation failed!";
        }
    }
    public function saveToDatabase()
    {
        $sql = "INSERT INTO `$this->tableName` (full_name, dob, email, phone, gender, address, pincode, country, state, city)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "ssssssssss",
            $this->fields['studentName']['value'],
            $this->fields['dob']['value'],
            $this->fields['email']['value'],
            $this->fields['phone']['value'],
            $this->fields['gender']['value'],
            $this->fields['address']['value'],
            $this->fields['pincode']['value'],
            $this->fields['country']['value'],
            $this->fields['state']['value'],
            $this->fields['city']['value']
        );

        if ($stmt->execute()) {
            $this->statusMsg = "New record created successfully!";
        } else {
            $this->errors['database'][] = "Database error: " . $stmt->error;
            $this->statusMsg = "Failed to create record.";
        }

        $stmt->close();
    }
    public function updateDatabase($id)
    {
        $sql = "UPDATE `$this->tableName` SET full_name=?, dob=?, email=?, phone=?, gender=?, address=?, pincode=?, country=?, state=?, city=? WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "ssssssssssi",
            $this->fields['studentName']['value'],
            $this->fields['dob']['value'],
            $this->fields['email']['value'],
            $this->fields['phone']['value'],
            $this->fields['gender']['value'],
            $this->fields['address']['value'],
            $this->fields['pincode']['value'],
            $this->fields['country']['value'],
            $this->fields['state']['value'],
            $this->fields['city']['value'],
            $id
        );
        if ($stmt->execute()) {
            $this->statusMsg = "Record updated successfully!";
        } else {
            $this->statusMsg = "Failed to update record.";
        }
        $stmt->close();
    }
}
