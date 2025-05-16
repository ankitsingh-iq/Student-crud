<?php

require_once __DIR__ . '/config/config.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id']) && !isset($_POST['mode'])) {

        $id = $_POST['id'];
        $sql = "SELECT * FROM students WHERE id=?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo json_encode(['status' => 'error', 'message' => 'Error preparing statement']);
            exit();
        }
        $stmt->bind_param("i", $id);
        if ($stmt->execute() === false) {
            echo json_encode(['status' => 'error', 'message' => 'Error executing statement']);
            exit();
        }
        $result = $stmt->get_result();
        if ($result === false) {
            echo json_encode(['status' => 'error', 'message' => 'Error fetching result']);
            exit();
        }
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Send JSON response
            echo json_encode([
                "status" => "success",
                "data" => $row
            ]);
            exit;
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Record not found."
            ]);
            exit;
        }
    } elseif (isset($_POST["mode"])) {
        $id = $_POST['id'];
        $sql = "SELECT * FROM students WHERE id=?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo json_encode(['status' => 'error', 'message' => 'Error preparing statement']);
            exit();
        }
        $stmt->bind_param("i", $id);
        if ($stmt->execute() === false) {
            echo json_encode(['status' => 'error', 'message' => 'Error executing statement']);
            exit();
        }
        $result = $stmt->get_result();
        if ($result === false) {
            echo json_encode(['status' => 'error', 'message' => 'Error fetching result']);
            exit();
        }
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Fetch country_id
            $country_id = null;
            $stmt2 = $conn->prepare("SELECT id FROM tbl_countries WHERE name=? LIMIT 1");
            $stmt2->bind_param("s", $row['country']);
            $stmt2->execute();
            $res2 = $stmt2->get_result();
            if ($res2 && $res2->num_rows > 0) {
                $country_id = $res2->fetch_assoc()['id'];
            }

            // Fetch state_id
            $state_id = null;
            $stmt3 = $conn->prepare("SELECT id FROM states WHERE name=? LIMIT 1");
            $stmt3->bind_param("s", $row['state']);
            $stmt3->execute();
            $res3 = $stmt3->get_result();
            if ($res3 && $res3->num_rows > 0) {
                $state_id = $res3->fetch_assoc()['id'];
            }

            // Fetch city_id
            $city_id = null;
            $stmt4 = $conn->prepare("SELECT id FROM cities WHERE name=? LIMIT 1");
            $stmt4->bind_param("s", $row['city']);
            $stmt4->execute();
            $res4 = $stmt4->get_result();
            if ($res4 && $res4->num_rows > 0) {
                $city_id = $res4->fetch_assoc()['id'];
            }

            // Replace names with IDs in response
            $row['country'] = $country_id;
            $row['state'] = $state_id;
            $row['city'] = $city_id;

            echo json_encode([
                "status" => "success",
                "data" => $row
            ]);
            exit;
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Record not found."
            ]);
            exit;
        }
    } else {
        $sql = "SELECT * FROM students";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo json_encode(['status' => 'error', 'message' => 'Error preparing statement']);
            exit();
        }
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result === false) {
            echo json_encode(['status' => 'error', 'message' => 'Error executing statement']);
            exit();
        }
        if ($result->num_rows > 0) {
            $data = $result->fetch_all(MYSQLI_ASSOC);

            echo json_encode([
                "status" => "success_edit",
                "data" => $data
            ]);
            exit;
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "No data found in the database"
            ]);
            exit;
        }
    }
}
