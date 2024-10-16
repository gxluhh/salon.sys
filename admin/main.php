<?php
session_start();

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['admin_id']) || $_SESSION['stat'] === "inactive") {
    // Handle the case where client_id is not set in the session
    header("Location: /admin/adminLogin.php");
    die("Admin not logged in");
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Salon Appointment Webpage </title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
	<frameset name="main" cols="100%">

		<frame src="http://localhost/admin/adminLogin.php" name="main">

	</frameset>
</html>


