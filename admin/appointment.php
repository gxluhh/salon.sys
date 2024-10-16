<?php
session_start();

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['admin_id']) || $_SESSION['stat'] === "inactive") {
    header("Location: /admin/main.php");
    die("Admin not logged in");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Appointments</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(function() {
            $("#datepicker").datepicker({
                minDate: 1,
                maxDate: "+1M +1D"
            });
        });
    </script>
    <style type="text/css">
        .tableName {
            text-align: center;
            color: white;
            font-size: 20px;
            font-family: Joker;
            font-weight: bold;
            background-color: #2F2519;
        }

        tr {
            color: white;
            background-color: #675D50;
            font-family: Joker;
        }

        a {
            text-decoration: none;
        }

        #icon:hover,
        a:hover {
            opacity: 0.5;
        }

        table {
            width: 95%;
            border-radius: 10px;
        }

        ion-icon {
            color: white;
            width: 23px;
            justify-content: space-evenly;
        }

        body {
            background-color: #E8D8C4;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: none;
        }

        .popup {
            position: fixed;
            width: 30%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #F8F4E1;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            z-index: 1001;
            display: none;
        }

        .popup.show,
        .overlay.show {
            display: block;
        }


        .popup h3 {
            margin-bottom: 20px;
            font-family: Joker;
            font-weight: bold;
            justify-content: center;
            text-align: center;
        }

        .btn-close {
            float: right;
            font-size: 12px;
            padding: 9px;
        }

        .popup-content table {
            width: 100%;
            margin-bottom: 12px;
        }

        .popup-content table td {
            padding: 5px;
            text-align: left;
            border-radius: 5px;
            width: 100%;
        }

        .popup-content input[type="text"] {
            width: 100%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        #reschedule_time {
            background-color: white;
            color: black;
        }

        .btn-field {
            text-align: center;
        }

        .btn {
            padding: 10px 20px;
            width: 100%;
            background-color: #675D50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-family: Joker;
        }

        .btn:hover {
            background-color: #574d42;
            color: white;
            opacity: 0.5;
        }
    </style>
</head>

<body>
    <br>
    <center>
        <table border="3" cellspacing="3px" cellpadding="2px">
            <thead>
                <tr id="tbl">
                    <th colspan="11" align="center" class="tableName">APPOINTMENTS</th>
                </tr>
                <tr>
                    <th>ID</th>
                    <th>Date Filed</th>
                    <th>Appointment Date</th>
                    <th>Time</th>
                    <th>Service Name</th>
                    <th>Client's Name</th>
                    <th>Chosen Staff</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

                <?php
                require("config.php");
                $errMessage = "No records found!";

                $sql = "SELECT a.appointment_id, a.dateFiled, a.appointmentDate, tm.time, srvcs.service_name, CONCAT(c.fName, ' ', c.lName) 		 AS client_name, CONCAT(s.fName, ' ', s.lName) AS staff_name, a.status
        	FROM appointments a JOIN services srvcs ON a.service_id = srvcs.service_id 
        	JOIN staff s ON a.staff_id = s.staff_id 
        	JOIN clients c ON a.client_id = c.client_id 
        	JOIN timeslots tm ON a.time_id = tm.time_id 
        	WHERE a.status IN ('BOOKED','WAITING', 'RESCHEDULED', 'APPROVED', 'DISAPPROVED', 'CANCELLED', 'CANCELED') 
       		ORDER BY FIELD(status, 'BOOKED','WAITING', 'RESCHEDULED', 'APPROVED', 'DISAPPROVED', 'CANCELLED', 'CANCELED'); ";

                $result = $connection->query($sql);

                if (!$result) {
                    die("Invalid Query: " . $connection->error);
                }

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                        <td>{$row['appointment_id']}</td>
                        <td>{$row['dateFiled']}</td>
                        <td>{$row['appointmentDate']}</td>
                        <td>{$row['time']}</td>
                        <td>{$row['service_name']}</td>
                        <td>{$row['client_name']}</td>
                        <td>{$row['staff_name']}</td>
                        <td>{$row['status']}</td>
                        <td>
                            <a href='/admin/approveAppointment.php?id={$row['appointment_id']}' role='button' name='approved'>
                                <ion-icon title='Approve' name='thumbs-up'></ion-icon>
                            </a> &nbsp;
                            <a href='/admin/disapproveAppointment.php?id={$row['appointment_id']}' role='button' name='disapproved'>
			        <ion-icon name='thumbs-down'></ion-icon>
                            </a> &nbsp;
                            <a role='button' onclick='rescheduleAppointment({$row['appointment_id']}, event)' name='reschedule'>
                                <ion-icon title='Reschedule' name='calendar-number'></ion-icon>
                            </a> &nbsp;
                            <a href='/admin/completedAppointment.php?id={$row['appointment_id']}' role='button' name='completed'>
                                <ion-icon title='Mark as Complete' name='checkmark-circle'></ion-icon>
                            </a>
                        </td>
                      </tr>";
                    }
                } else {
                    echo "<script>alert('$errMessage');</script>";
                }

                $connection->close();
                ?>
            </tbody>
        </table>
    </center>

    <!-- Popup for reschedule -->
    <div class="overlay"></div>
    <div class="popup" id="popup">
        <button type="button" id="close" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <h3>Reschedule Date</h3>

        <form method="POST" action="rescheduleAppointment.php">
            <div class="popup-content">
                <table>
                    <tr>
                        <td>
                            <input type="hidden" id="reschedAppointmentId" name="appointment_id" value="">
                            <div class="td-date">
                                <input type="text" id="datepicker" name="reschedule_date" placeholder="Select a date">
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <select class="btn" value="<?php echo $resched_time; ?>" id="reschedule_time" name="reschedule_time">
                                <option value="" disabled selected hidden>Select a time</option>
                                <option value="09:30 AM">9:30 AM</option>
                                <option value="10:30 AM">10:30 AM</option>
                                <option value="11:30 AM">11:30 AM</option>
                                <option value="01:30 PM">1:30 PM</option>
                                <option value="02:30 PM">2:30 PM</option>
                                <option value="03:30 PM">3:30 PM</option>
                                <option value="04:30 PM">4:30 PM</option>
                                <option value="05:30 PM">5:30 PM</option>
                                <option value="06:30 PM">6:30 PM</option>
                                <option value="07:30 PM">7:30 PM</option>
                                <option value="08:30 PM">8:30 PM</option>
                                <option value="09:30 PM">9:30 PM</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="btn-field">
                <button type="submit" class="btn" name="submit">DONE</button>
            </div>
        </form>
    </div>

    <!-- JavaScript scripts -->
    <script>
        function rescheduleAppointment(appointment_id, event) {
            event.preventDefault();
            var overlay = document.querySelector('.overlay');
            var popup = document.querySelector('#popup');
            overlay.style.display = 'block';
            popup.style.display = 'block';

            document.getElementById('reschedAppointmentId').value = appointment_id;

            var closeButton = document.querySelector('#close');
            closeButton.addEventListener('click', function() {
                overlay.style.display = 'none';
                popup.style.display = 'none';
            });
        }
    </script>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>

</html>