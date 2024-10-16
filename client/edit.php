<?php
include("config.php");
session_start();

$client_id = null;
$appointment_id = null;
$error = null;
$success = null;

if (!isset($_SESSION['client_id'])) {
    $error = "Log in first!";
    echo "  <div class='error-container' id='error-container'>
    <span class='close' id='login-close'>&times;</span>
    <div id='error' class='error'>
    <p><?php echo $error ?></p>
    </div>
    </div>";
    exit();
} else {
    $client_id = $_SESSION['client_id'];
    $appointment_id = $_GET['appointment_id'];

    if (isset($_POST['submit'])) {
        $appointmentDate = $_POST['appointmentDate'];
        $time_id = $_POST['time_id'];
        $service_id = $_POST['service_id'];
        $staff_id = $_POST['staff_id'];

        $stmt = $con->prepare("UPDATE appointments SET appointmentDate = ?, time_id = ?, service_id = ?, staff_id = ? 
        WHERE appointment_id = ?");
        $stmt->bind_param("siiii", $appointmentDate, $time_id, $service_id, $staff_id, $appointment_id);
        $stmt->execute();

        if ($stmt) {
            $success = 'Appointment Edited Succesfully !';
        } else {
            $error = 'Error updating record: ' . mysqli_error($con);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
    <script>
        $(function() {
            $("#datepicker").datepicker({
                minDate: 1,
                maxDate: "+1M +1D"
            });
        });
    </script>
</head>

<body>
    <div class="container">
        <div class="navbar">
            <ul>
                <li><a href="dashboard.php">Home</a></li>
                <li><a href="index.php">Appointments</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li style="float:right"><a href="logout.php">Log Out</a></li>
            </ul>
        </div>
        <div class="popup show">
            <?php if ($error) : ?>
                <div class='error-container' id="error-container">
                    <span class='close' id='error-close' onclose='successclose()'>&times;</span>
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
            <h1>Edit Appointment</h1>
            <span class="close" id="editclose"><a href="index.php">&times;</a></span>
            <?php

            $id = $_GET["appointment_id"];
            $stmt = $con->prepare("SELECT a.appointmentDate, a.time_id, t.time, a.staff_id, s.category_no, s.service_id, st.category_no, c.service_category, s.service_name, CONCAT(st.fName, ' ', st.lName) AS staff_name 
                    FROM appointments a
                    JOIN services s ON a.service_id = s.service_id
                    JOIN timeslots t ON a.time_id = t.time_id
                    JOIN category c ON s.category_no = c.category_no
                    JOIN staff st ON a.staff_id = st.staff_id
                    WHERE appointment_id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $mainRow = $result->fetch_assoc();
            ?>
            <form action="" method="POST">
                <div class="popup-content">
                    <div class="bookDiv">
                        <table>
                            <tr>
                                <td style="text-align: right;"><label>Date</label></td>
                                </td>
                                <td>
                                    <input type="text" id="datepicker" class="btn" name="appointmentDate" value="<?php echo $mainRow['appointmentDate'] ?>" placeholder="<?php echo $mainRow['appointmentDate'] ?>" readonly>
                                </td>
                            </tr>
                            <tr>
                                <?php
                                $stmt = $con->prepare("SELECT time_id, time FROM timeslots");
                                $stmt->execute();
                                $time_result = $stmt->get_result();
                                ?>
                                <td style="text-align: right;"><input type="hidden" id="time_id" name="time_id" placeholder="" value="<?php echo $mainRow['time_id'] ?>" readonly><label>Time</label></td>
                                <td>
                                    <select class="btn" id="select-time" name="select-time">
                                        <option value="" disabled selected hidden><?php echo $mainRow['time'] ?></option>
                                        <?php while ($row = $time_result->fetch_assoc()) : ?> <option value="<?php echo $row['time_id']; ?> "> <?= $row['time'] ?> </option>
                                        <?php endwhile; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <?php
                                $stmt = $con->prepare("SELECT category_no, service_category FROM category");
                                $stmt->execute();
                                $category_result = $stmt->get_result();
                                ?>
                                <td style="text-align: right;"><input type="hidden" id="category_no" name="category_no" placeholder="" readonly><label>Service Category</label>
                                </td>
                                <td>
                                    <select class="btn" id="select-category_no" name="select-category_no">
                                        <option value="" disabled selected hidden><?= $mainRow['service_category'] ?></option>
                                        <?php while ($row = $category_result->fetch_assoc()) : ?>
                                            <option data-category="<?php echo $row['category_no']; ?>" value="<?php echo $row['category_no']; ?> "> <?= $row['service_category'] ?> </option>
                                        <?php endwhile; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <?php
                                $stmt = $con->prepare("SELECT category_no, service_id, service_name FROM services");
                                $stmt->execute();
                                $service_result = $stmt->get_result();
                                ?>
                                <td style="text-align: right;"><input type="hidden" id="service_id" name="service_id" value="<?php echo $mainRow['service_id']; ?>" readonly><label>Service Name</label></td>
                                <td>
                                    <select class="btn" id="select-service_name" name="select-service_name">
                                        <option data-category="<?php echo $mainRow['category_no']; ?>" value="<?php echo $mainRow['service_id']; ?>" disabled selected hidden><?php echo $mainRow['service_name'] ?></option>
                                        <?php while ($row = $service_result->fetch_assoc()) : ?>
                                            <option data-category="<?php echo $row['category_no']; ?>" value="<?php echo $row['service_id']; ?> "> <?= $row['service_name'] ?> </option>
                                        <?php endwhile; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <?php
                                $stmt = $con->prepare("SELECT category_no, staff_id, CONCAT(fName, ' ', lName) AS staff_name FROM staff");
                                $stmt->execute();
                                $staff_result = $stmt->get_result();
                                ?>
                                <td style="text-align: right;"><input type="hidden" id="staff_id" name="staff_id" value="<?php echo $mainRow['staff_id']; ?>" readonly><label>Staff Name</label></td>
                                <td>
                                    <select class="btn" id="select-name" name="select-name">
                                        <option data-category="<?php echo $mainRow['category_no']; ?>" value="<?php $mainRow['staff_id'] ?>" disabled selected hidden><?php echo $mainRow['staff_name'] ?></option>
                                        <?php while ($row = $staff_result->fetch_assoc()) : ?>
                                            <option data-category="<?php echo $row['category_no']; ?>" value="<?php echo $row['staff_id'] ?>"><?= $row['staff_name'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="btn-field">
                        <button class="btn" name="submit" id="submit">EDIT</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
<script src="popup.js"></script>
<script>
    // Get the select element and the corresponding text field
    const selectCategory = document.getElementById('select-category_no');
    const categoryInput = document.getElementById('category_no');
    const selectTime = document.getElementById('select-time');
    const timeInput = document.getElementById('time');
    const selectService = document.getElementById('select-service_name');
    const serviceInput = document.getElementById('service_id');
    const selectStaff = document.getElementById('select-name');
    const staffInput = document.getElementById('staff_id');

    selectTime.addEventListener('change', function() {
        timeInput.value = this.value;
    });
    selectCategory.addEventListener('change', function() {
        // Update the value of the text field with the selected value of the select element
        const selectedCategory = this.options[this.selectedIndex].dataset.category;
        const serviceOptions = selectService.options;

        for (let i = 0; i < serviceOptions.length; i++) {
            if (serviceOptions[i].dataset.category === selectedCategory) {
                serviceOptions[i].style.display = 'block';
            } else {
                serviceOptions[i].style.display = 'none';
            }
        }
    });

    selectService.addEventListener('change', function() {
        // Update the value of the text field with the selected value of the select element
        const selectedCategory = this.options[this.selectedIndex].dataset.category;
        const staffOptions = selectStaff.options;

        for (let i = 0; i < staffOptions.length; i++) {
            if (staffOptions[i].dataset.category === selectedCategory) {
                staffOptions[i].style.display = 'block';
            } else {
                staffOptions[i].style.display = 'none';
            }
        }
    });

    selectStaff.addEventListener('change', function() {
        staffInput.value = this.value;
    });
</script>

</html>