<?php
	session_start();
	
	$_SESSION['stat'] = "inactive";
	unset($_SESSION['admin_id']);
	echo "<script> window.location.href = '/admin/main.php'</script>";
?>