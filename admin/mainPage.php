<?php
session_start();
require("config.php");

if (!isset($_SESSION['admin_id']) || $_SESSION['stat'] === "inactive") {
	// Handle the case where client_id is not set in the session
	header("Location: /admin/main.php");
	die("Admin not logged in");
	exit;
}


$admin_id = $_SESSION['admin_id'];

?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>Salon Appointment Webpage </title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<frameset cols="200px, *" noresize border="0">
	<frame src="menu.php" name="menu">

		<frameset rows="18%, *" noresize border="0">
			<frame src="header.php" resize scrolling="no">
				<frame src="homepage.php" name="display">
		</frameset>
</frameset>

</html>