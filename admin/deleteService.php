<?php

session_start();
require("config.php");
  
  if (!isset($_SESSION['admin_id']) || $_SESSION['stat'] === "inactive") {
      // Handle the case where client_id is not set in the session
      header("Location: /admin/main.php");
      die("Admin not logged in");
}
$admin_id = $_SESSION['admin_id'];

	if(isset($_GET["id"]) ){
		$id = $_GET["id"];
		
		$sql = "DELETE FROM services WHERE service_id=$id";
		$connection->query($sql);
		
		echo "<script>alert ('Service Deleted!'); window.location.href = '/admin/serviceFunc.php'</script>";
	}
	
	header("location: /admin/serviceFunc.php");
	exit;
?>