<?php
session_start();
require("config.php");
$client_id = $_SESSION['client_id'];
$error = null;
$success = null;

if (!isset($_SESSION['client_id'])) {
	$error = "Log in first!";
	header("location: login.php");
	exit();
} else {
	if (isset($_POST['submit'])) {
		$fName = $_POST['fName'];
		$lName = $_POST['lName'];
		$mNum = $_POST['mNum'];
		$eMail = $_POST['eMail'];
		$pWord = $_POST['pWord'];
		$conpWord = $_POST['ConpWord'];

		$stmt = $con->prepare("UPDATE customers SET fName = ?, lName = ?, mNum = ?, eMail = ?, pWord = md5(?) WHERE customer_id = ?");
		$stmt->bind_param("sssssi", $fName, $lName, $mNum, $eMail, $pWord, $customer_id);

		if ($pWord == $conpWord) {
			$stmt->execute();
			if ($stmt) {
				$success = "Updated Successfully !";
				header("location: index.php");
			} else {
				$error = 'Error updating record: ' . mysqli_error($con);
			}
		} else {
			$error = 'Passwords do not match !';
		}
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Profile</title>
	<link rel="stylesheet" href="style.css">
	<script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</head>

<body>
	<div class="container">
		<?php if (!empty($error)) : ?>
			<div class='error-container' id="error-container">
				<span class='close' id='error-close'>&times;</span>
				<div id='error' class='error'>
					<p><?php echo $error ?></p>
				</div>
			</div>
		<?php endif; ?>
		<?php if (!empty($success)) : ?>
			<div class='success-container' id='success-container'>
				<span class='close' id='success-close'>&times;</span>
				<div id='success' class='success'>
					<p><?php echo $success ?></p>
				</div>
			</div>
		<?php endif; ?>
		<div class="navbar">
			<ul>
				<li><a href="dashboard.php">Home</a></li>
				<li><a href="index.php">Appointments</a></li>
				<li><a href="profile.php">Profile</a></li>
				<li style="float:right"><a href="logout.php">Log Out</a></li>
			</ul>
		</div>
		<div class="form-box">
			<h1>Profile</h1>
			<form action="" method="post">
				<?php
				$sql = $con->prepare("SELECT * FROM clients WHERE client_id = ?");
				$sql->bind_param("i", $client_id);
				$sql->execute();
				$result = $sql->get_result();
				$row = $result->fetch_assoc();
				?>
				<table class="input-group">
					<tr>
						<td class="label">First Name</td>
						<td class="input-field-profile">
							<input required type="text" placeholder="FIRST NAME" value="<?php echo $row['fName'] ?>" id="fName" name="fName" autocomplete="off" autocapitalize="characters">
						</td>
					</tr>
					<tr>
						<td class="label">Last Name</td>
						<td class="input-field-profile">
							<input required type="text" placeholder="LAST NAME" value="<?php echo $row['lName'] ?>" id="lName" name="lName" autocomplete="off" autocapitalize="characters">
						</td>
					</tr>
					<tr>
						<td class="label">Mobile Number</td>
						<td class="input-field-profile">
							<input required type="text" placeholder="MOBILE NUMBER" value="<?php echo $row['mNum'] ?>" id="mNum" name="mNum" autocomplete="off">
						</td>
					</tr>
					<tr>
						<td class="label">Email</td>
						<td class="input-field-profile">
							<input required type="text" placeholder="EMAIL" value="<?php echo $row['eMail'] ?>" id="eMail" name="eMail" autocomplete="off">
						</td>
					</tr>
					<tr>
						<td class="label">Password</td>
						<td class="input-field-profile">
							<input type="password" placeholder="PASSWORD" value="" id="pWord" name="pWord" autocomplete="off">
						</td>
					</tr>
					<tr>
						<td class="label">Confirm Password</td>
						<td class="input-field-profile"><input type="password" placeholder="CONFIRM PASSWORD" value="" id="ConpWord" name="ConpWord"></td>
					</tr>
				</table>
				<div class="btn-field">
					<button class="btn" name="submit" id="submit">UPDATE</button>
				</div>
			</form>
		</div>
	</div>
</body>
<script>
	window.addEventListener('load', function() {
		if (window.history && window.history.pushState) {
			window.history.pushState('forward', null, ''); // Add a dummy state to history
			window.onpopstate = function() {
				// Redirect to home page if user tries to navigate back after logout
				window.location.href = 'login.php';
			};
		}
	});
</script>
</hmtl>