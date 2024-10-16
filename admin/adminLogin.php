<?php
session_start();
require("config.php");
$_SESSION['stat'] = "inactive";

if($_SESSION['stat'] === "inactive" || empty($_SESSION['stat']) ){
	$_SESSION['stat'] = "inactive";
}

//declare another variables for data retrieval
$email = "";
$userPassword = "";
$admin_id = "";

$errorMessage = "";
$successMessage = "";
         
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST["email"];
    $userPassword = $_POST["userPassword"];

    if (empty($email) || empty($userPassword)) {
        $errorMessage = "All fields are required!";
    } 
    else {
    
        // Check for if email and password exist
        $checkExistQuery = "SELECT admin_id, eMail, pWord FROM admin WHERE eMail=? AND pWord=md5(?)";
        $prepareStmt = $connection->prepare($checkExistQuery);
        $prepareStmt->bind_param("ss", $email, $userPassword);
        $prepareStmt->execute();
        $result = $prepareStmt->get_result();

        if ($result->num_rows > 0) {
        	$user_data = $result->fetch_assoc();
        	
        	$_SESSION['admin_id'] = $user_data['admin_id'];
        	$_SESSION['stat'] = 'active';
		$admin_id = $user_data['admin_id'];
        	
        	header("Location: /admin/mainPage.php?$admin_id");
        	
        	$result->close();
        	exit;
        } else {
        
        	$errorMessage ="Email and password doesn't match any existing accounts!";
        }

        // Close connection
        $connection->close();
    }
    
    //clear fields
    $email = "";
    $userPassword = "";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"> </script>
    <link rel="stylesheet" href="login.css">
  
</head>
<body><br> 
  
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Admin <br> Log In to Continue</h3>
                    </div>
                    <div class="card-body">
    			
                        <form name="adminLogin" method="POST">
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="EMAIL" value="<?php echo $email; ?>" name="email">
                            </div>
                            
                            <div class="mb-3">
                                <input type="password" class="form-control" placeholder="PASSWORD" id="userPass" value="<?php echo $userPassword; ?>" name="userPassword">
                          <div id="error"> 
                            	<?php
					if (!empty($errorMessage)) {
					       echo " $errorMessage ";
					}
    				?>  
    				
    			    </div>  
    			    	<input type="checkbox" onClick="showPass()"> &nbsp;
    			    	<span id="passTxt">Show Password</span>
                            </div>
                            
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn">LOG IN</button>
                            </div>
                            
                            <div class="mt-3 text-center">
			           Don't have an account? <br>
			           <span class="signUp">
			           	<a href="adminSignup.php" target="main">Click here to Sign up</a>
			           </span>
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


