<?php
session_start();
if(isset($_SESSION['status_msg'])) {
    echo "<div class='col-xs-12 text-black'><div class='alert'" . $_SESSION['status_msg'] . "</div></div>";
    unset($_SESSION['status_msg']);
}
?>
<!-- Display status message -->
<!-- <?php if(!empty($_SESSION['status_msg'])){ ?>
<div class="col-xs-12">
    <div class="alert <?php echo $_SESSION['status_msg']; ?>"></div>
</div>
<?php } ?> -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        #btnImport {
            margin-top: 10px;
            margin-left: 680px;
        }

        #btnExport {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="mt-5 mb-3 clearfix">
                    <h2 class='pull-left'>Students Details</h2>
                    <a href="server/insert.php" class="btn btn-success pull-right">
                        <i class="fa fa-plus"></i> Add New Student</a>

                    <div class="impExp">
                        <a href="javascript:void(0);" class="btn btn-success" id="btnImport"
                            onclick="formToggle('importFrm');"><i class="fa fa-download" aria-hidden="true"></i> Import</a>
                        <a href="server/exportData.php" class="btn btn-primary" id="btnExport">
                            <i class="fa fa-upload" aria-hidden="true"></i> Export</a>
                    </div>

                    <!-- CSV file upload form -->
                    <div class="col-md-12" id="importFrm" style="display: none;">
                        <form action="server/importData.php" method="post" enctype="multipart/form-data">
                            <input type="file" name="file" />
                            <input type="submit" class="btn btn-primary" name="importSubmit" value="IMPORT">
                        </form>
                    </div>
                </div>

                <?php
                require_once "server/db_connection.php";
                $sql = "SELECT * FROM students";

                if($result = $conn->query($sql)) {

                    if($result->rowCount() > 0) {
                        echo '<table class="table table-lg table-light table-hover rounded shadow-lg text-center">';
                            echo "<thead class='table-dark'>";
                                echo "<tr>";
                                    echo "<th>ID</th>";
                                    echo "<th>Name</th>";
                                    echo "<th>Email</th>";
                                    echo "<th>Phone</th>";
                                    echo "<th>DateOfBirth</th>";
                                    echo "<th>Gender</th>";
                                    echo "<th>Address</th>";
                                    echo "<th>City</th>";
                                    echo "<th>Action</th>";
                                echo "</tr>";
                            echo "</thead>";

                            echo "<tbody>";
                            while($row = $result->fetch()) {
                                echo "<tr>";
                                    echo "<td>" . $row['id'] . "</td>";
                                    echo "<td>" . $row['full_name'] . "</td>";
                                    echo "<td>" . $row['email'] . "</td>";
                                    echo "<td>" . $row['phone'] . "</td>";
                                    echo "<td>" . $row['dob'] . "</td>";
                                    echo "<td>" . $row['gender'] . "</td>";
                                    echo "<td>" . $row['address'] . "</td>";
                                    echo "<td>" . $row['city'] . "</td>";
                                    echo "<td>";
                                        echo '<a href="server/update.php?id='. $row['id'] .'" class="btn btn-sm btn-warning text-black text-center" title="Update Record" data-toggle="tooltip">EDIT</a>';
                                        echo '<a href="server/delete.php?id='. $row['id'] .'" class="btn btn-sm btn-danger text-center" title="Delete Record" data-toggle="tooltip">DELETE</a>';
                                    echo "</td>";
                                echo "</tr>";
                            }
                            echo "</tbody>";
                        echo "</table>";

                        // Free result set
                        unset($result);
                    } else {
                        echo '<div class="alert alert-danger">No records were found.</div>';
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }

                unset($conn);
                ?>
            </div>
        </div>
    </div>

<!-- Show/hide CSV upload form -->
<script>
    function formToggle(ID) {
        var element = document.getElementById(ID);
        if(element.style.display === "none") {
            element.style.display = "block";
        }else {
            element.style.display = "none";
        }
    }
</script>
    <script src="../js/script.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>