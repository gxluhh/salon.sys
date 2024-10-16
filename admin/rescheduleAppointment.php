<?php
    session_start();
    require("config.php");

    if (!isset($_SESSION['admin_id']) || $_SESSION['stat'] === "inactive") {
        header("Location: /admin/main.php");
        die("Admin not logged in");
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST['appointment_id']) && isset($_POST['reschedule_date'])) {
            $appointment_id = $_POST['appointment_id'];
            $reschedule_date = $_POST['reschedule_date'];
            $reschedule_time = $_POST['reschedule_time'];


            if (!empty($reschedule_date) || !empty($reschedule_time)) {
                // Check the current status of the appointment
                $stmt = $connection->prepare("SELECT status FROM appointments WHERE appointment_id = ?");
                $stmt->bind_param("i", $appointment_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 0) {
                    header("Location: /admin/appointment.php");
                    die("Appointment not found");
                }

                $row = $result->fetch_assoc();
                $current_status = $row['status'];

                // Check if the status is  'COMPLETED'
                if ($current_status === 'COMPLETED') {
                    echo "<script>alert('This appointment is already COMPLETED!'); window.location.href='/admin/appointment.php';</script>";
                } else if ($current_status === 'RESCHEDULED') {
                    echo "<script>alert('This appointment is already RESCHEDULED!'); window.location.href='/admin/appointment.php';</script>";
                } else if ($current_status === 'CANCELLED' || $current_status === 'CANCELED') {
                    echo "<script>alert('This appointment is CANCELLED!'); window.location.href='/admin/appointment.php';</script>";
                } else {
                    // Update the appointment status and reschedule date
                    $stmt = $connection->prepare("UPDATE appointments SET status = 'RESCHEDULED', appointmentDate = ?, time_id=? WHERE appointment_id = ?");
                    $stmt->bind_param("ssi", $reschedule_date, $reschedule_time, $appointment_id);

                    if ($stmt->execute()) {
                        header("Location: /admin/appointment.php");
                        exit;
                    } else {
                        die("Invalid query: " . $connection->error);
                    }
                }

                $stmt->close();
            } else {
                header("Location: /admin/appointment.php");
                die("Invalid date format");
                exit;
            }
        } else {
            die("Invalid input");
        }
    }
    ?>