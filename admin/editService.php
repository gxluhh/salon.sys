<?php
session_start();
require("config.php");

if (!isset($_SESSION['admin_id']) || $_SESSION['stat'] === "inactive") {
    // Handle the case where client_id is not set in the session
    header("Location: /admin/main.php");
    die("Admin not logged in");
}

    $admin_id = $_SESSION['admin_id'];

$category_no = "";
$serviceName = "";
$price = "";
$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET["id"])) {
        $id = $_GET["id"];

        // Read the service's row
        $sql = "SELECT * FROM services WHERE service_id=$id";
        $result = $connection->query($sql);
        $row = $result->fetch_assoc();

        if (!$row) {
            header("location: /admin/category.php");
            exit;
        }
	
        $serviceName = $row["service_name"];
        $price = $row["price"];
    }
} else {
    // store the input to php variables
    $serviceName = $_POST["serviceName"];
    $price = $_POST["price"];
    
    if (empty($serviceName) && empty($price)) {
        $errorMessage = "All the fields are required!";
    } else {
        // Check for duplicate service name excluding the current service
	$checkDuplicateQuery = "SELECT * FROM services WHERE service_name=? AND service_id <> ?";
	$prepareStmt = $connection->prepare($checkDuplicateQuery);
	$prepareStmt->bind_param("si", $serviceName, $id);
	$prepareStmt->execute();
	$result = $prepareStmt->get_result();

        if ($result->num_rows > 0) {
            $errorMessage = "Service already exists!";
        } else {
            // Update the service
            $id = $_GET["id"];
            $sql = "UPDATE services SET service_name=?, price=? WHERE service_id=?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("sdi", $serviceName, $price, $id);

            // Execute the statement
            if ($stmt->execute()) {
                $successMessage = "Service updated successfully!";
                // Redirect after successful update
                echo "<script>alert ('Service updated succesfully!'); window.location.href = '/admin/serviceFunc.php'</script>";
            } else {
                $errorMessage = "Invalid query: " . $connection->error;
            }
            // Close statement
            $stmt->close();
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>salonAppointmentSys</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"> </script>
   <style>
       	body{background-color:#E8D8C4}
       	
       	.btn{
   		width:100%;
   		height: 100%;
   		border: 2px;
   		border-radius: 20px;
   		background-color:#675D50;
   		padding: 14px 40px;
   		color: white;
   		font-family: Joker;
   		font-weight:bold;
   	}
   	
   	.btn:hover{
   		background-color:white;
   		color: black;
   		font-family: Joker;
   		font-weight:bold;
   		box-shadow: 2px 2px 10px #F0EBE3;
   	}
   	
   	h2, label{
   		color: black;
   		font-family: Joker;
   		font-weight:bold;
   	}
   	
    </style>

</head>

<body>
<div class="container my-5">
    <h2 align="center">Edit Service</h2><br><br>

    <?php
            if (!empty($errorMessage)) {
                echo " <script> alert('$errorMessage') </script> ";
            }
         ?>
        
          <?php
                   if (!empty($successMessage)) {
                       echo " <script> alert('$successMessage') </script> ";
                   }
     ?>

    <form method="post">
        
        <div class="row mb-3">
	      <label class="col-sm-3 col-form-label">Service Name</label>
	      <div class="col-sm-6">
	      <input type="text" class="form-control" name="serviceName" value="<?php echo $serviceName; ?>" required>
	      </div>
        </div>

        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Price(Php)</label>
            <div class="col-sm-6">
                <input type="number" step="0.01" class="form-control" name="price" value="<?php echo $price; ?>" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="offset-sm-3 col-sm-3 d-grid">
                <button type="submit" class="btn">Update</button>
            </div>

            <div class="col-sm-3 d-grid">
                <a class="btn" href="/admin/serviceFunc.php" role="button">Cancel</a>
            </div>
        </div>
    </form>
</div>

</body>

</html>
