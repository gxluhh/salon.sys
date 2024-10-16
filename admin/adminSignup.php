<?php

session_start();
require("config.php");

$Fname = "";
$Lname = "";
$email = "";
$mobile = "";
$userPassword = "";
$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Fname = $_POST["Fname"];
    $Lname = $_POST["Lname"];
    $email = $_POST["email"];
    $mobile = $_POST["mobile"];
    $userPassword = $_POST["userPassword"];

    if (empty($Fname) || empty($Lname) || empty($email)|| empty($userPassword) ) {
        $errorMessage = "All fields are required!";
        
        //clear all fields
        $Fname = "";
	$Lname = "";
	$email = "";
	$mobile = "";
	$userPassword = "";
    } else {
        // Check for duplicate email or mobile number
        $checkDuplicateQuery = "SELECT * FROM admin WHERE eMail=? OR mobile=?";
        $prepareStmt = $connection->prepare($checkDuplicateQuery);
        $prepareStmt->bind_param("ss", $email, $mobile);
        $prepareStmt->execute();
        $result = $prepareStmt->get_result();

        if ($result->num_rows > 0) {
            $errorMessage = "Email or mobile is already taken!";
            
            //Clear fields
            $email = "";
	    $mobile = "";
        } else {
            // Prepare and bind the SQL statement
            $sql = "INSERT INTO admin(fName, lName, eMail, mobile, pWord) VALUES (?, ?, ?, ?, md5(?))";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("sssss", $Fname, $Lname, $email, $mobile, $userPassword);

            // Execute the statement
            if ($stmt->execute()) {
                $successMessage = "Account created successfully!";
                
                // Redirect after successful insertion
                header("Location: main.php");
                exit;
            } else {
                $errorMessage = "Error: " . $connection->error;
                //clear all fields
		$Fname = "";
		$Lname = "";
		$email = "";
		$mobile = "";
		$userPassword = "";
            }

            // Close statement
            $stmt->close();
        }

        // Close connection
        $connection->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Signup</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"> </script>
    
    
    <style>
    	#error{
			text-align: center;
			margin-top: 18px;
			color: yellow;
			font-size:15;
			font-family: Joker;
	}
	
	#error{animation:shake .50s;}
	   	@keyframes shake{
	   		0%{transform: translateX(0)};
	   		20%{transform: translateX(-10px)};
	   		40%{transform: translateX(10px)};
	   		60%{transform: translateX(-10px)};
	   		80%{transform: translateX(10px)};
	   		100%{transform: translateX(0)};
   	}
   	
   	.card {
			background: transparent;
			width: 600px;
			height:730px;
			background-size: cover;
			background-position: center;
			border-radius: 10px;
		  	box-shadow: 0px 0px 20px rgba(0,0,0,0.5);
		  	overflow: hidden;
		  	justify-content:center;
	}
	
	#passTxt{
			font-size:13;
			color: white;
			font-family: Joker;
	}
	
	form{
			box_sizing: border-box;
			width: 100%;
			height: 100%;
			padding: 40px;
			flex-direction: column;
			display: flex;
			gap: 20px;
			backdrop-filter: blur(5px);
			font-family: helvetica;
	}
	
	.card-header{
			background-color: #E5BB61;
			width: 600px;
			color: white;
			font-family: Joker;
			font-weight: bold;
			
	}
	
	.btn{
			background-color: #E5BB61;
			font-family: Joker;
			color: white;
			font-weight: bold;
		}
		
		.btn:hover{
			background-color: #E4D3CF;
			color: black;
	}
	
	.mt-3{
			color: white;
			font-family: Joker;
	}

	.signIn a{
			text-decoration: none;
			color: yellow;
			backdrop-filter: grey(2px);
			font-weight: bold;
	}
	
	a:hover{
		text-decoration: underline;
		color:white;
		filter: drop-shadow(10px 5px 10px pink);
	}
	
	body{
			background-image: URL('yellow.jpg');
			background-repeat: no-repeat;
			background-size: cover;
			background-position: center;
	    		position: relative;
	}
    
    </style>
    
</head>

<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Admin Sign Up</h3>
                    </div>
                    <div class="card-body">
    			
                        <form name="AdminSignUp" method="POST">
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="FIRST NAME" value="<?php echo $Fname; ?>" name="Fname">
                            </div>
                            
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="LAST NAME" value="<?php echo $Lname; ?>" name="Lname">
                            </div>
                            
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="MOBILE NUMBER" value="<?php echo $mobile; ?>" name="mobile">
                            </div>
                            
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="EMAIL" value="<?php echo $email; ?>" name="email">
                            </div>
                            
                            <div class="mb-3">
                                <input type="password" class="form-control" placeholder="PASSWORD" id="userPass" value="<?php  echo $userPassword; ?>" name="userPassword">
                              
         		  	<div id="error"> 
                            	    <?php
					if (!empty($errorMessage)) {
					   echo "  $errorMessage ";
					}
    				     ?>  
    			        </div>
    			        
    			    	<input type="checkbox" onClick="showPass()"> &nbsp;
    			    	<span id="passTxt">Show Password</span>
    			    </div>
                            
                            
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn" onClick="">Sign Up</button>
                            </div>
                        
                        
                        <div class="mt-3 text-center">
                            Already have an account? <br>
                            <span class="signIn"><a href="adminLogin.php" target="main">Sign In Here</a></span>
                        </div>
                        
                      </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function showPass(){
        	var pass=document.getElementById("userPass");
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