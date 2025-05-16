<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .title {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <h2 class="title">Student Information</h2>

    <table>
        <tr><th>Field</th><th>Value</th></tr>
        <tr><td>ID</td><td><?= $data['id'] ?></td></tr>
        <tr><td>Full Name</td><td><?= $data['full_name'] ?></td></tr>
        <tr><td>Date of Birth</td><td><?= $data['dob'] ?></td></tr>
        <tr><td>Email</td><td><?= $data['email'] ?></td></tr>
        <tr><td>Phone</td><td><?= $data['phone'] ?></td></tr>
        <tr><td>Gender</td><td><?= $data['gender'] ?></td></tr>
        <tr><td>Address</td><td><?= $data['address'] ?></td></tr>
        <tr><td>Pincode</td><td><?= $data['pincode'] ?></td></tr>
        <tr><td>Country</td><td><?= $data['country'] ?></td></tr>
        <tr><td>State</td><td><?= $data['state'] ?></td></tr>
        <tr><td>City</td><td><?= $data['city'] ?></td></tr>
    </table>

    <h3>Documents:</h3>
    <?php 
    $documents = json_decode($data['documents'], true);
    if (!empty($documents)) {
        echo "<ul>";
        foreach ($documents as $doc) {
            $imgPath = "../". $doc;
            echo "<img src='$imgPath' style='width:100px;height:100px;'/>";
        }
        echo "</ul>";
    } else {
        echo "<p>No documents uploaded.</p>";
    }
    ?>
</body>
</html>
