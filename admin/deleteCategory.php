<?php
session_start();
require("config.php");

if (!isset($_SESSION['admin_id']) || $_SESSION['stat'] === "inactive") {
      // Handle the case where client_id is not set in the session
      header("Location: /admin/main.php");
      die("Admin not logged in");
}

	$admin_id = $_SESSION['admin_id'];

	if(!empty($_GET["id"]) ){
		$id = $_GET["id"];
		
		$sql = "DELETE FROM services WHERE category_no = $id";
		$connection->query($sql);
		
		$sql = "DELETE FROM category WHERE category_no = $id";
		$connection->query($sql);
		
		echo "<script>alert ('Category Deleted!'); window.location.href = '/admin/category.php'</script>";
	}else{
    	echo "<script>alert('Please choose a category!');  window.location.href = '/admin/category.php'</script>";
    	}
	
	exit;

?>