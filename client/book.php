<?php
require_once("config.php");
session_start();
$client_id = null;
$error = null;
$success = null;

if (!isset($_SESSION['client_id'])) {
    $error = "Log in first!";
    exit();
} else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $client_id = $_SESSION['client_id'];
        $appointmentDate = $_POST['appointmentDate'];
        $staff_id = $_POST['staff_id'];
        $time_id = $_POST['time'];
        $service_id = $_POST['service_id'];
        $status = "BOOKED";

        if (!empty($date) || !empty($time) || !empty($service_id) || !empty($staff_id)) {

            $stmt2 = $con->prepare("SELECT * FROM appointments 
                                    WHERE (appointmentDate = ? AND time_id = ? AND service_id = ? AND (status = 'APPROVED' OR status = 'BOOKED'))");

            $stmt2->bind_param("sii", $appointmentDate, $time_id, $service_id);
            $stmt2->execute();
            $result = $stmt2->get_result();
            $row = $result->fetch_assoc();

            if ($row != 0) {
                $_SESSION['error'] = "Overlapping appointment schedule and services !";
                header("location: index.php");
            } else {
                $stmt = $con->prepare("INSERT INTO appointments(client_id, staff_id, appointmentDate, time_id, service_id, status) VALUES(?,?,?,?,?,?)");

                $stmt->bind_param("iisiis", $client_id, $staff_id, $appointmentDate, $time_id, $service_id, $status);
                $stmt->execute();

                if ($stmt) {
                    $stmt->close();
                    $_SESSION['success']  = "Booked successfully !";
                    header("location: index.php");
                } else {
                    $_SESSION['error']  = "Booking failed !";
                    header("location: index.php");
                }
            }
        } else {
            $_SESSION['error']  = "Fields cannot be empty !";
            header("location: index.php");
        }
    }
}
