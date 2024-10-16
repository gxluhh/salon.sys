<?php
session_start();
require("config.php");

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['admin_id']) || $_SESSION['stat'] === "inactive") {
	// Handle the case where client_id is not set in the session
	 header("Location: /admin/main.php");
	die("Admin not logged in");
}

$admin_id = $_SESSION['admin_id'];
$Fname = "";
$Lname = "";
$mobile = "";
$email = "";
$password = "";
$conpassword = "";

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	if (isset($_SESSION["admin_id"])) {
		$admin_id = $_SESSION['admin_id'];

		// Read the admin's row
		$sql = "SELECT * FROM admin WHERE admin_id=$admin_id";
		$result = $connection->query($sql);
		$found = $result->fetch_assoc();

		if (!$found) {
			header("location:  /admin/adminProfile.php");
			exit;
		}

		$Fname = $found["fName"];
		$Lname = $found["lName"];
		$mobile = $found["mobile"];
		$email = $found["eMail"];
		
	}
} else {
	// Store input values to php variables
	$Fname = $_POST["Fname"];
	$Lname = $_POST["Lname"];
	$mobile = $_POST["mobile"];
	$email = $_POST["email"];
	$password = $_POST["password"];

	if (empty($password) && empty($conpassword)) {
		$errorMessage = "PASSWORD IS REQUIRED!";
	} else if($password !== $conpassword){
		$errorMessage = "PASSWORD DOESN'T MATCH!";
	}
	else {
		// Update the service
		$staff_id = $_GET["staff_id"];
		$sql = "UPDATE admin SET fName=?, lName=?, mobile=?, email=?, pWord=? WHERE admin_id=?";
		$stmt = $connection->prepare($sql);
		$stmt->bind_param("sssssi", $Fname, $Lname, $mobile, $email, $password, $admin_id);

		// Execute the statement
		if ($stmt->execute()) {
			
			echo "<script>alert ('Updated Successfully!'); window.location.href = '/admin/adminProfile.php'</script>";
			
		} else {
			$errorMessage = "Invalid query: " . $connection->error;
		}
		// Close statement
		$stmt->close();
	}
}
?>


<html>

<head>
	<title>Admin Profile</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"> </script>

	<style type="text/css">
		table {
			color: white;
			font-size: 20px;
			font-family: Joker;
			font-weight: bold;
			background-color: #2F2519;
			display: flex;
			align-items: center;
			justify-content: center;
			min-width: 300px;
			max-width: 500px;
			flex-direction: column;
			text-align: center;
			margin: auto;
		}
		
		h1{
			font-family: Joker;
			font-weight: bold;}

		tr {
			color: white;
			background-color: transparent;
			font-family: Joker;
			height: 60px;

		}

		body {
			background-color: #E8D8C4;
		}

		.btn-field {
			display: flex;
			align-items: center;
			justify-content: center;
			width: 100%;
		}

		.btn {
			padding: 10px 10px;
			margin: 10px;
			min-width: 100px;
			max-width: 200px;
			background-color: #FFFFFF;
			color: #2F2519;
			border: none;
			border-radius: 5px;
			cursor: pointer;
			font-weight: bold;
			font-family: Joker;
		}

		.btn:hover {
			background-color: #574d42;
			color: white;
			opacity: 0.5;
		}

		body {
			background-color: #E8D8C4;
		}

		.outside {
			margin: 2% auto 2% auto;
			padding: 30px 10px 20px 10px;
			display: block;
			align-items: center;
			min-width: 300px;
			max-width: 450px;
			border: none;
			min-height: 400px;
			max-height: 500px;
			border-radius: 25px;
			text-align: center;
			box-shadow: 0px 0px 20px #461111;
			background-color: #FCFFE0;
			color: black;
		}

	</style>
</head>

<body>

	<div class="outside">
		<form action="" method="post">
			<h1>ADMIN PROFILE</h1>
			<table>
				<tr>
					<td>
						<label>First Name: </label>
						<input type="text" class="form-control" name="Fname" value="<?php echo $Fname ?>" required>
					</td>
					<td>
						<label>Last Name: </label>
						<input type="text" class="form-control" name="Lname" value="<?php echo $Lname ?>" required>
					</td>
				</tr>
				<tr>
					<td>
						<label>Mobile: </label>
						<input type="text" class="form-control" name="mobile" value="<?php echo $mobile ?>" required>
					</td>
					<td>
						<label>Email: </label>
						<input type="text" class="form-control" name="email" value="<?php echo $email ?>" required>
					</td>
				</tr>
				<tr>
					<td>
						<label>Password: </label>
						<input type="password" class="form-control" id="password" name="password" value="<?php echo $password ?>" required>
					</td>
					<td>
						<label>Confirm Password: </label>
						<input type="password" class="form-control" id="conpassword" name="conpassword" value="" required>
						
					</td>
				</tr>
				
				<tr>
					<td colspan="2">
						</div>  
						    <input type="checkbox" onClick="showPass()"> &nbsp;
						    <span id="passTxt">Show Password</span>
                           			 </div>
					</td>
				</tr>
				
				<tr>
					<td colspan="2">
						<div class="btn-field">
							<button type="submit" name="submit" class="btn ">Update</button>
						</div>
					</td>
				</tr>
		         </form>
				
			</table>
		
	</div>
	
    <script>
    	function showPass(){
    		var pass=document.getElementById("password");
    		  if(pass.type == "password"){
    		   	pass.type ="text";
    		   }
    		   else{
    		   	pass.type="password";
    		   }
    		   
		var pass=document.getElementById("conpassword");
	    	     if(pass.type == "password"){
	    		   pass.type ="text";
	    		}
	    		else{
	    		  pass.type="password";
	             }
   	}
   	
    
    </script>
</body>

</html>