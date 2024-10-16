<?php

session_start();
require("config.php");
  
  if (!isset($_SESSION['admin_id']) || $_SESSION['stat'] === "inactive") {
    // Handle the case where client_id is not set in the session
    header("Location: /admin/main.php");
    die("Admin not logged in");
} 
	if(isset($_GET["staff_id"]) ){
		$staff_id = $_GET["staff_id"];
		
          		
		$sql = "DELETE FROM staff WHERE staff_id=$staff_id";
		$connection->query($sql);
		
		echo "<script>alert ('Staff Deleted!'); window.location.href = '/admin/staff.php'</script>";
	}else{
    		echo "<script> window.location.href = '/admin/staff.php'</script>";
    	}
	
?>