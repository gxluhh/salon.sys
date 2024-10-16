<?php
session_start();
require("config.php");

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['admin_id']) || $_SESSION['stat'] === "inactive") {
    // Handle the case where client_id is not set in the session
    header("Location: /admin/main.php");
    die("Admin not logged in");
}

$Fname = "";
$Lname = "";
$main_skill = "";
$position = "";
$salary= "";
$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET["staff_id"])) {
        $staff_id = $_GET["staff_id"];

        // Read the staff's row
        $sql = "SELECT * FROM staff WHERE staff_id=$staff_id";
        $result = $connection->query($sql);
        $found = $result->fetch_assoc();

        if (!$found) {
            header("location: /admin/staff.php");
            exit;
        }

        $Fname = $found["fName"];
        $Lname = $found["lName"];
        $main_skill = $found["mainSkill"];
        $position = $found["position"];
        $salary = $found["salary"];
    }
} else {
    // Store input values to php variables
        $Fname = $_POST["Fname"];
        $Lname = $_POST["Lname"];
        $main_skill = $_POST["main_skill"];
        $position = $_POST["position"];
        $salary = $_POST["salary"];
        
      if (empty($Fname) || empty($Lname) || empty($main_skill) || empty($position) || empty($salary)) {
          $errorMessage = "All the fields are required";
      } 
       else {
	          // Update the service
	          $staff_id = $_GET["staff_id"];
	          $sql = "UPDATE staff SET fName=?, lName=?, mainSkill=?, position=?, salary=? WHERE staff_id=?";
	          $stmt = $connection->prepare($sql);
	          $stmt->bind_param("sssssi", $Fname, $Lname, $main_skill, $position, $salary, $staff_id);
	  
	         // Execute the statement
	         if ($stmt->execute()) {
	             echo "<script>alert ('Staff updated successfully!'); window.location.href = '/admin/staff.php'</script>";
	        } else {
	               $errorMessage = "Invalid query: " . $connection->error;
	           }
	            // Close statement
            $stmt->close();
        
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Edit</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"> </script>
    <style>
    	body{background-color:#E8D8C4;}
    	
    	.btn{
		width:100%;
		height: 100%;
		border: 2px;
		border-radius: 20px;
		background-color:#675D50;
		padding: 14px 40px;
		color: white;
		font-weight:bold;
		font-family: Joker;
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
    <h2 align="center">Edit Staff</h2><br> <br>

    <?php
            if (!empty($errorMessage)) {
                echo " <script> alert('$errorMessage') </script> ";
            }
         ?>
        
          <?php
                   if (!empty($errorMessage)) {
                       echo " <script> alert('$errorMessage') </script> ";
                   }
     ?>

    <form method="POST">
    	
         <div class="row mb-3">
             <label class="col-sm-3 col-form-label">First Name: </label>
             <div class="col-sm-6">
                 <input type="text" class="form-control" name="Fname" value="<?php echo $Fname; ?>">
             </div>
         </div>
 
         <div class="row mb-3">
             <label class="col-sm-3 col-form-label">Last Name: </label>
             <div class="col-sm-6">
                 <input type="text" class="form-control" name="Lname" value="<?php echo $Lname; ?>">
             </div>
         </div>
         
         <div class="row mb-3">
 	     <label class="col-sm-3 col-form-label">Main Skill: </label>
 	     <div class="col-sm-6">
                 <input type="text" class="form-control" name="main_skill" value="<?php echo $main_skill; ?>">
 	      </div>
         </div>
 
         <div class="row mb-3">
 	     <label class="col-sm-3 col-form-label">Position: </label>
 	     <div class="col-sm-6">
                 <input type="text" class="form-control" name="position" value="<?php echo $position; ?>">
 	      </div>
         </div>
         
         <div class="row mb-3">
 	     <label class="col-sm-3 col-form-label">Salary: </label>
 	     <div class="col-sm-6">
                 <input type="text" class="form-control" name="salary" value="<?php echo $salary; ?>">
 	      </div>
         </div>
          
 
         <div class="row mb-3">
             <div class="offset-sm-3 col-sm-3 d-grid">
                 <button type="submit" class="btn btn-primary">Update</button>
             </div>
 
             <div class="col-sm-3 d-grid">
                 <a class="btn btn-outline-danger" href="/admin/staff.php" role="button">Cancel</a>
             </div>
        </div>
    </form>
</div>
</body>
</html>
