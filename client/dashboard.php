<?php
require("config.php");
session_start();
$client_id = null;
$error = null;
$success = null;

if (!isset($_SESSION['client_id'])) {
    $error = "Log in first!";
    header("location: login.php");
    exit();
} else {
    $client_id = $_SESSION['client_id'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container">
        <?php if ($error) : ?>
            <div class='error-container' id="error-container">
                <span class='close' id='error-close' onclick='successclose()'>&times;</span>
                <div id='error' class='error'>
                    <p><?= $error ?></p>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($success) : ?>
            <div class='success-container' id='success-container'>
                <span class='close' id='error-close' onclick='successclose()'>&times;</span>
                <div id='success' class='success'>
                    <p><?= $success ?></p>
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
        <div class="profile-div">
            <?php
            $stmt = $con->prepare("SELECT CONCAT(fName, ' ', lName) AS client_name FROM clients WHERE client_id =? limit 1");
            $stmt->bind_param('i', $client_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            ?>
            <p><?= $row['client_name']; ?>'s Dashboard</p>
        </div>
        <div class="dashboard">
            <div class="history">
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <td colspan="6">
                                <h1>History of Appointments</h1>
                            </td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Service</th>
                            <th>Staff</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $appointments = $con->prepare("SELECT a.appointment_id, s.category_no, a.client_id, a.appointmentDate, a.time_id, t.time, a.service_id, s.service_name, a.status, CONCAT(st.fName, ' ', st.lName) AS staff_name 
					FROM appointments a
					JOIN services s ON a.service_id = s.service_id
					JOIN timeslots t ON a.time_id = t.time_id
					JOIN staff st ON a.staff_id = st.staff_id
					WHERE client_id = ?");
                        $appointments->bind_param('i', $client_id);
                        $appointments->execute();
                        $result = $appointments->get_result();

                        if (mysqli_num_rows($result) > 0) {
                            foreach ($result as $appointment) {
                                if ($appointment['status'] == 'CANCELED') {
                                    echo "
								<tr class = 'tr'>							
									<td>$appointment[appointmentDate]</td>
									<td>$appointment[time]</td>
									<td>$appointment[service_name]</td>
									<td>$appointment[staff_name]</td>
									<td>$appointment[status]</td>
								</tr>
								";
                                } else if ($appointment['status'] == 'COMPLETED') {
                                    echo "<tr class = 'tr'>							
                                <td>$appointment[appointmentDate]</td>
                                <td>$appointment[time]</td>
                                <td>$appointment[service_name]</td>
                                <td>$appointment[staff_name]</td>
                                <td>$appointment[status]</td>
                            </tr>";
                                } else {
                                    echo "";
                                }
                            }
                        } else {
                            $error = "No appointments found!";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>