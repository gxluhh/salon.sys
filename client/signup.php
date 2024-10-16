<?php
session_start();
include("config.php");

$error = null;
$success = null;

if (isset($_POST["submit"])) {
	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$fName = $_POST['fName'];
		$lName = $_POST['lName'];
		$mNum = $_POST['mNum'];
		$eMail = $_POST['eMail'];
		$pWord = $_POST['pWord'];
		$conpWord = $_POST['ConpWord'];

		$stmt = $con->prepare("INSERT INTO clients(fName, lName, mNum, eMail, pWord) VALUES(?,?,?,?,?)");
		$stmt->bind_param("sssss", $fName, $lName, $mNum, $eMail, $pWord);

		if ($pWord != $conpWord) {
			$error = "Passwords do not match!";
		} else {
			if (!empty($eMail) || !empty($pWord) || !empty($fName) || !empty($lName) || !empty($mNum)) {
				$verify = mysqli_query($con, "SELECT eMail from clients WHERE eMail = '$eMail' LIMIT 1");

				if (mysqli_num_rows($verify) != 0) {
					$error = "Email already in use! Try another one.";
				} else {
					try {
						$stmt->execute();
						$stmt->close();
						$success = "Account created successfully!";
					} catch (PDOException $e) {
						$error = $e;
					}
				}
			} else {
				$error = "Please fill in all fields!";
			}
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Create Account</title>
	<link rel="stylesheet" href="style.css">
	<script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</head>

<body>
	<div class="container">
		<img style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); opacity: 0.5;" src="JNet Logo (1).png" width="70%">
	</div>
	<div class="form-box">
		<h1 id="title">Sign Up</h1>
		<form action="" method="post">
			<div class="input-group">
				<div class="input-field">
					<input type="text" name="fName" placeholder="FIRST NAME">
				</div>
				<div class="input-field">
					<input type="text" name="lName" placeholder="LAST NAME">
				</div>
				<div class="input-field">
					<input type="text" name="mNum" placeholder="MOBILE NUMBER">
				</div>
				<div class="input-field">
					<input type="text" name="eMail" placeholder="EMAIL">
				</div>
				<div class="input-field">
					<input type="password" id="pWord" name="pWord" placeholder="PASSWORD">
				</div>
				<div class="input-field">
					<input type="password" id="ConpWord" name="ConpWord" placeholder="CONFIRM PASSWORD">
				</div>
				<div class="btn-field">
					<input type="checkbox" id="passTxt" onClick="showPass()">
					<p>Show Password</p>
				</div>
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
				<div class="btn-field">
					<input class="btn" type="submit" name="submit" value="SIGN UP">
				</div>
				<p>Already have an Account? <a href="login.php">Log In Here</p>
		</form>
	</div>
</body>
<script>
	function showPass() {
		var pass = document.getElementById("pWord");
		var conpass = document.getElementById("ConpWord");
		if (pass.type == "password" && conpass.type == "password") {
			pass.type = "text";
			conpass.type = "text";
		} else {
			pass.type = "password";
			conpass.type = "password";
		}
	}
	document.querySelector('#error-close').addEventListener('click', function() {
		document.querySelector('#error-container').classList.add('hide');
	});
	document.querySelector('#success-close').addEventListener('click', function() {
		document.querySelector('#success-container').classList.add('hide');
	});
</script>

</html>