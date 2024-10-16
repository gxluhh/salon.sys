<?php
require("config.php");
session_start();
$client_id = null;
$error = null;
$success = null;

if (isset($_POST['submit'])) {
	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$eMail = $_POST['eMail'];
		$pWord = $_POST['pWord'];

		$stmt = $con->prepare("SELECT client_id, eMail, pWord, CONCAT(fName, ' ', lName) AS client_name FROM clients WHERE eMail = ? AND pWord = md5(?) limit 1");
		$stmt->bind_param('ss', $eMail, $pWord);
		$stmt->execute();
		$result = $stmt->get_result();

		if (empty($eMail) || empty($pWord)) {
			$error = "Fields cannot be empty!";
		} else {
			if ($result->num_rows > 0) {
				$user_data = $result->fetch_assoc();
				$_SESSION['client_id'] = $user_data['client_id'];
				$client_id = $_SESSION['client_id'];
				$result->close();
				header("Location: dashboard.php");
			} else {
				$error = "Email or password incorrect!";
			}
		}
	} else {
		$error = "Post Error !";
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Sign In</title>
	<link rel="stylesheet" href="style.css">
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

	<div class="container">
		<img style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); opacity: 0.5;" src="JNet Logo (1).png" width="70%">
		<div class="form-box">
			<h1 id="title">Log In</h1>
			<form action="" method="post">
				<div class="input-group">
					<div class="input-field">
						<input type="text" placeholder="EMAIL" id="eMail" name="eMail">
					</div>
					<div class="input-field">
						<input type="password" placeholder="PASSWORD" id="pWord" name="pWord">
					</div>
					<div class="btn-field">
						<input type="checkbox" id="passTxt" onClick="showPass()">
						<p>Show Password</p>
					</div>
					<?php if ($error) : ?>
						<div class='error-container' id="error-container">
							<span class="close" id="error-close">&times;</span>
							<div id='error' class='error'>
								<p><?php echo $error ?></p>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<p>Don't have an Account? <a href="signup.php">Sign Up Here</a></p>
				<div class="btn-field">
					<button class="btn" id="submit" type="submit" name="submit">SIGN IN</button>
				</div>
		</div>
		</form>
		<div class="overlay"></div>
	</div>

</body>
<script>
	function showPass() {
		var pass = document.getElementById("pWord");
		if (pass.type == "password") {
			pass.type = "text";
		} else {
			pass.type = "password";
		}
	}
	document.querySelector('#error-close').addEventListener('click', function() {
		document.querySelector('#error-container').classList.add('hide');
	});
</script>
</hmtl>