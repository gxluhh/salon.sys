<?php
session_start();
require("config.php");

if (!isset($_SESSION['admin_id']) || $_SESSION['stat'] === "inactive") {
    header("Location: /admin/main.php");
    die("Admin not logged in");
}


    $appointment_id = $_GET["id"];
  
        $stmt = $connection->prepare("SELECT status FROM appointments WHERE appointment_id = ?");
        $stmt->bind_param("i", $appointment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        

        if ($row['status'] === 'APPROVED') {
            $stmt = $connection->prepare("UPDATE appointments SET status = 'COMPLETED' WHERE appointment_id = ?");
            $stmt->bind_param("i", $appointment_id);
            if ($stmt->execute()) {
                $stmt->close();

                // Fetch appointment details for transaction
                $stmt = $connection->prepare('SELECT *
                    FROM appointments a
                    JOIN services srvcs ON a.service_id = srvcs.service_id
                    JOIN clients c ON a.client_id = c.client_id
                    JOIN staff s ON a.staff_id = s.staff_id
                    WHERE appointment_id = ? AND a.status = "COMPLETED"');
                $stmt->bind_param("i", $appointment_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $value = $result->fetch_assoc();
                $stmt->close();

                if ($value) {
                    // Insert transaction record
                    $stmt = $connection->prepare("INSERT INTO transactions(client_id, service_id, staff_id, 
                        datePaid, amount, status) VALUES (?, ?, ?, CURRENT_DATE(), ?, ?)");
                    $stmt->bind_param("iisds", $value['client_id'], $value['service_id'], $value['staff_id'], $value['price'], $value['status']);
                    if ($stmt->execute()) {
                        $stmt->close();
                        header("Location: /admin/appointment.php");
                        exit;
                    } else {
                        $errorMessage = "Invalid query: " . $connection->error;
                    }
                } else {
                    header("Location: /admin/appointment.php");
                    exit;
                }
            } else {
                $errorMessage = "Invalid query: " . $connection->error;
            }
        } else if ($row['status'] === 'WAITING') {
                echo "<script>alert('This appointment is NOT YET APPROVED!'); window.location.href='/admin/appointment.php';</script>";
         }
         else if ($row['status'] === 'COMPLETED') {
	          echo "<script>alert('This appointment is already COMPLETED!'); window.location.href='/admin/appointment.php';</script>";
            }
         else if ($row['status'] === 'DISAPPROVED') {
	 	          echo "<script>alert('This appointment is DISAPPROVED!'); window.location.href='/admin/appointment.php';</script>";
            }
         else if ($row['status'] === 'RESCHEDULED' || $row['status'] === 'BOOKED') {
	 	          echo "<script>alert('This appointment needs to be APPROVED first!'); window.location.href='/admin/appointment.php';</script>";
            }
        else {
            echo "<script>alert('This appointment is CANCELLED!'); window.location.href='/admin/appointment.php';</script>";
            exit;
        }
    

?>
