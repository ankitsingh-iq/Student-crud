<?php

require_once __DIR__ . '/config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['importFile']) && $_FILES['importFile']['error'] === 0) {

        $fileName = $_FILES['importFile']['name'];
        $fileTmpName = $_FILES['importFile']['tmp_name'];
        $fileSize = $_FILES['importFile']['size'];
        $fileType = $_FILES['importFile']['type'];


        if ($fileType == 'text/csv') { {
                $file = fopen($fileTmpName, 'r');

                // 1. Read the CSV header
                $header = fgetcsv($file);
                $header = array_map('trim', $header);
                // print_r($header);


                //2. Fetch the column names from the database table dynamically
                $result = $conn->query("SHOW COLUMNS FROM students");

                if ($result === false) {
                    die("Error fetching table columns: " . $conn->error);
                }

                $dbcolumns = [];
                while ($row = $result->fetch_assoc()) {
                    $dbcolumns[] = $row['Field'];
                }

                // print_r($dbcolumns);


                //!mapping

                // 3. Check if CSV headers match database columns
                $mappingHeader = [];
                foreach ($header as $col) {
                    if (in_array($col, $dbcolumns)) {
                        $mappingHeader[] = $col;
                    } else {
                        echo ("Column '$col' does not exist in the database.");
                        fclose($file);
                        exit;
                    }
                }

                // 4. Read data rows and reorder them based on the mapped columns

                $studentsData = [];

                while ($row = fgetcsv($file)) {
                    $rowdata = [];
                    foreach ($mappingHeader as $index => $column) {
                        $rowdata[$column] = $row[$index];
                    }
                    $studentsData[] = $rowdata;
                }
                // echo ("<pre>");
                // print_r($studentsData);
                // echo ("</pre>");

                fclose($file);


                // 5. Insert data into the database

                foreach ($studentsData as $student) {

                    $colomns = implode(", ", array_keys($student));
                    $values = implode("', '", array_values($student));

                    $sql = "INSERT INTO students ($colomns) VALUES ('$values')";

                    // echo ($sql);


                    if (!$conn->query($sql)) {
                        echo ("Error: " . $sql . "<br>" . $conn->error);
                    }

                }



            }
        } else {
            echo ("Invalid file type. Please upload a CSV file.");
        }
    } else {
        echo ("No file uploaded or there was an error.");
    }
}
