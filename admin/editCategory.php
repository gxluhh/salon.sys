<?php
session_start();
require("config.php");

if (!isset($_SESSION['admin_id']) || $_SESSION['stat'] === "inactive") {
    // Handle the case where client_id is not set in the session
    header("Location: /admin/main.php");
    die("Admin not logged in");
}

$admin_id = $_SESSION['admin_id'];

$service_category = "";
$staff_category = "";
$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_GET["id"])) {
        $category_no = $_GET["id"];

        // Read the service's row
        $sql = "SELECT * FROM category WHERE category_no =$category_no";
        $result = $connection->query($sql);
        $row = $result->fetch_assoc();

        if (!$row) {
            header("location: /admin/category.php");
            exit;
        }
	
	$category_no = $row["category_no"];
        $service_category = $row["service_category"];
        $staff_category = $row["staff_category"];
    }else{
    	echo "<script>alert('Please choose a category!');  window.location.href = '/admin/category.php'</script>";
    }
} else {
    // store the input to php variables
    $category_no = $_POST["category_no"];
    $service_category = $_POST["service_category"];
    $staff_category = $_POST["staff_category"];
    
    if (empty($service_category) && empty($staff_category)) {
        $errorMessage = "All the fields are required!";
    } else {
        // Check for duplicate service name excluding the current service
	$checkDuplicateQuery = "SELECT * FROM category WHERE service_category=? AND staff_category=? AND category_no <> ?";
	$prepareStmt = $connection->prepare($checkDuplicateQuery);
	$prepareStmt->bind_param("ssi", $service_category, $staff_category, $category_no);
	$prepareStmt->execute();
	$result = $prepareStmt->get_result();

        if ($result->num_rows > 0) {
            $errorMessage = "Category already exists!";
             header("location: /admin/category.php");
	     exit;
        } else {
            // Update the category
             $category_no = $_GET["id"];
             
          
               $sql = "UPDATE category SET service_category=?, staff_category=? WHERE category_no=?";
               $stmt = $connection->prepare($sql);
               $stmt->bind_param("ssi", $service_category, $staff_category, $category_no);

               // Execute the statement
              if ($stmt->execute()) {
                  $successMessage = "Category updated successfully!";
                  // Redirect after successful update
                  header("location: /admin/category.php");
                  exit;
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
    <h2 align="center">EDIT CATEGORY</h2><br><br>

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
	      <label class="col-sm-3 col-form-label">Service Category Name: </label>
	      <div class="col-sm-6">
	      <input type="text" class="form-control" name="service_category" value="<?php echo $service_category; ?>">
	      </div>
        </div>

        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Staff Category Name:</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="staff_category" value="<?php echo $staff_category; ?>">
            </div>
        </div>

        <div class="row mb-3">
            <div class="offset-sm-3 col-sm-3 d-grid">
                <button type="submit" class="btn">Update</button>
            </div>

            <div class="col-sm-3 d-grid">
                <a class="btn" href="/admin/category.php" role="button">Cancel</a>
            </div>
        </div>
    </form>
</div>

</body>

</html>
