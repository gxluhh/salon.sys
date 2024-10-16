<?php
include("config.php");
session_start();
$client_id = null;
if (!isset($_SESSION['client_id'])) {
    header("location: login.php");
    $error = "Log in first!";
    exit();
} else {
    $id = $_GET["appointment_id"];
    $sql = "UPDATE appointments SET status='CANCELED' WHERE appointment_id = $id";
    $result = mysqli_query($con, $sql);
    $error = null;
    $success = null;
    if ($result) {
        $success = "Appointment Cancelled !";
        header("location: index.php");
        return $success;
    } else {
        $error = "Cancellation Failed !";
        return $error;
    }

    header("location: index.php");
    exit;
}
