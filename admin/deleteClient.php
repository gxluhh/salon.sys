<?php

session_start();
require("config.php");
  if (!isset($_SESSION['admin_id']) || $_SESSION['stat'] === "inactive") {
      // Handle the case where client_id is not set in the session
      header("Location: /admin/main.php");
      die("Admin not logged in");
}

$admin_id = $_SESSION['admin_id'];

	if(isset($_GET["client_id"]) ){
		$client_id = $_GET["client_id"];
          		
		$sql = "DELETE FROM clients WHERE client_id=$client_id";
		$connection->query($sql);
		
		echo "alert ('Client Deleted!')";
	}
	
	header("location: /admin/client.php");
	exit;
?>