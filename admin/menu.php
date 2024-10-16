<?php
session_start();
require("config.php");

if (!isset($_SESSION['admin_id']) || $_SESSION['stat'] === "inactive") {
	// Handle the case where client_id is not set in the session
	header("Location: /admin/main.php");
	die("Admin not logged in");
}

$admin_id = $_SESSION['admin_id'];

?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"> </script>

	<style type="text/css">
		a {
			text-decoration: none;
			font-weight: bold;
		}

		#home:hover,
		#profile:hover {
			border: none;
			box-shadow: 2px 2px 10px #F0EBE3;
			backdrop-filter: blur(25px);
			background-color: white;
			border-radius: 30px;
		}

		.btn a {

			text-decoration: none;
			font-family: Joker;
			font-size: 14px;
			color: #322C2B;
		}

		.btn a:hover {
			color: black;
		}

		.btn1:hover,
		.btn2:hover {
			border: none;
			box-shadow: 2px 2px 10px #F0EBE3;
			backdrop-filter: blur(25px);
			background-color: white;

			flex-wrap: wrap;
			overflow: hidden;
			justify-content: center;
			align-items: center;
		}

		.btn1 {
			width: 100%;
			border: 2px;
			border-radius: 20px;
			background-color: #C7B7A3;
			margin-top: 20px;
			padding: 14px 40px;
		}

		.btn2 {
			width: 100%;
			border-radius: 20px;
			border: 2px;
			background-color: #E8D8C4;
			margin-top: 20px;
			padding: 14px 40px;
		}

		body {
			background-color: #6D2932;
		}

		table {
			width: 100%;
			height: 100%;
			table-layout: fixed;
			padding: 0;
			border: 0;
		}

		.notification .badge {
			position: absolute;
			top: 40px;
			right: 38px;
			padding: 4px 8px;
			border-radius: 50%;
			background: red;
			color: white;
			font-size: 9px;
		}
	</style>
</head>

<body bgcolor="#6D2932">
	<br><br>
	<div align="center">
		<table>
			<tr>
				<a href="homepage.php" target="display"><img id="home" src="home_icon.png" title="Home" width="29" height="29"> </a> &nbsp;
				<a href="adminProfile.php" target="display"><img id="profile" src="p_icon.jpg" title="View profile" width="28" height="28"> </a>&nbsp;
				<a href="adminNotif.php" class="notification" onclick="" target="display"><img id="profile" src="notif-icon.png" title="View notifications" width="28" height="28">
					<span class="badge"> 4 </span>
				</a>
	</div>
	<br><br>
	<div class="btn">

		<div class="col-sm-3 d-grid">
			<a class="btn2" role="button" href="/admin/category.php" target="display">Categories</a>
		</div>

		<div class="offset-sm-3 col-sm-3 d-grid">
			<a class="btn1" href="/admin/serviceFunc.php" role="button" target="display">Services </a>
		</div>

		<div class="col-sm-3 d-grid">
			<a class="btn2" role="button" href="/admin/client.php" target="display">Clients</a>
		</div>

		<div class="offset-sm-3 col-sm-3 d-grid">
			<a class="btn1" role="button" href="/admin/staff.php" target="display"> Employees</a>
		</div>

		<div class="col-sm-3 d-grid">
			<a class="btn2" role="button" href="/admin/appointment.php" target="display"> Appointments</a>
		</div>

		<div class="offset-sm-3 col-sm-3 d-grid">
			<a class="btn1" role="button" href="/admin/transaction.php" target="display">Transactions</a>
		</div>

		<div class="col-sm-3 d-grid">
			<a class="btn2" role="button" href="logoutAdmin.php" target="main">Log out</a>
		</div>

		</tr>
		</table>
	</div>

	<script>

	</script>

</body>

</html>