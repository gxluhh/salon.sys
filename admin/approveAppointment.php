<?php
session_start();
require("config.php");

if (!isset($_SESSION['admin_id']) || $_SESSION['stat'] === "inactive") {
    // Handle the case where client_id is not set in the session
    header("Location: /admin/main.php");
    die("Admin not logged in");
}

$appointment_id = $_GET["id"];

$stmt = $connection->prepare("SELECT status FROM appointments WHERE appointment_id = ?");
$stmt->bind_param("i", $appointment_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if($row['status'] === 'WAITING' || $row['status'] === 'BOOKED' || $row['status'] === 'RESCHEDULED'){
	$stmt = $connection->prepare("UPDATE appointments SET status = 'APPROVED' WHERE appointment_id = ?");
	$stmt->bind_param("i", $appointment_id);

	if ($stmt->execute()) {
	    header("location: /admin/appointment.php");
	    exit;
	} else {
	    $errorMessage = "Invalid query: " . $connection->error;
	}
}else if($row['status'] === 'APPROVED'){ 
	echo "<script>alert('This appointment is already APPROVED!'); window.location.href='/admin/appointment.php';</script>";
}
else if($row['status'] === 'COMPLETED'){ 
	echo "<script>alert('This appointment is already COMPLETED!'); window.location.href='/admin/appointment.php';</script>";
}else if($row['status'] === 'DISAPPROVED'){ 
	echo "<script>alert('This appointment is DISAPPROVED! Reschedule first.'); window.location.href='/admin/appointment.php';</script>";
}
else{
	echo "<script>alert('This appointment is CANCELLED!'); window.location.href='/admin/appointment.php';</script>";}
 	
$stmt->close();
?>