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

	$stmt = $con->prepare("SELECT appointmentDate, time_id FROM appointments");
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	$aDate = json_encode($row['appointmentDate']);
	$aTimes = json_encode($row['time_id']);
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
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
	<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
	<script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
	<script>
		$(function() {
			<?php
			$stmt = $con->prepare("SELECT appointmentDate, COUNT(*) AS count FROM appointments GROUP BY appointmentDate HAVING count = 5");
			$stmt->execute();
			$result = $stmt->get_result();
			$rows = $result->fetch_all(MYSQLI_ASSOC);
			$jsdata = json_encode($rows);
			?>
			const data = <?php echo $jsdata ?>;
			var aDates = data.map(row => row.appointmentDate);

			$("#datepicker").datepicker({
				beforeShowDay: (appointmentDate) => {
					var string = jQuery.datepicker.formatDate("mm/dd/yy", appointmentDate);
					return [aDates.indexOf(string) == -1];
				},
				minDate: 1,
				maxDate: "+31D"
			});
		});
	</script>
</head>

<body>
	<div class="container">
		<?php if ($error) : ?>
			<div class='error-container' id="error-container">
				<span class='close' id='error-close' onclose='successclose()'>&times;</span>
				<div id='error' class='error'>
					<p><?= $error = $_SESSION['error']; ?></p>
				</div>
			</div>
		<?php endif; ?>
		<?php if ($success) : ?>
			<div class='success-container' id='success-container'>
				<span class='close' id='error-close' onclick='successclose()'>&times;</span>
				<div id='success' class='success'>
					<p><?= $success = $_SESSION['success']; ?></p>
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
		<div class="appointments-container">
			<table class="appointments-table">
				<thead>
					<tr>
						<td colspan="7">
							<h1>List of Appointments</h1>
							<div class="btn-field">
								<button class="btn" onClick="openPopup()">Book an Appointment</button>
							</div>
						</td>
					</tr>
					<tr>
						<th>Date</th>
						<th>Time</th>
						<th>Service</th>
						<th>Staff</th>
						<th>Price</th>
						<th>Status</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$appointments = $con->prepare(
						"SELECT a.appointment_id, s.category_no, a.client_id, a.appointmentDate, a.time_id, t.time, a.service_id, s.service_name, a.status, s.price, CONCAT(st.fName, ' ', st.lName) AS staff_name 
					FROM appointments a
					JOIN services s ON a.service_id = s.service_id
					JOIN timeslots t ON a.time_id = t.time_id
					JOIN staff st ON a.staff_id = st.staff_id
					WHERE client_id = ?
					ORDER BY a.appointmentDate ASC"
					);
					$appointments->bind_param('i', $client_id);
					$appointments->execute();
					$result = $appointments->get_result();

					$total = $con->prepare("SELECT SUM(s.price) AS total, client_id 
											FROM appointments a 
											JOIN services s ON a.service_id = s.service_id
											WHERE client_id = ? AND status = 'BOOKED'");
					$total->bind_param('i', $client_id);
					$total->execute();
					$rTotal = $total->get_result();
					$totalRow = $rTotal->fetch_assoc();

					if (mysqli_num_rows($result) > 0) {
						foreach ($result as $appointment) {
							if ($appointment['status'] == 'CANCELED') {
								echo "";
							} else if ($appointment['status'] == 'BOOKED') {
								echo "
								<tr class = 'tr'>							
									<td>$appointment[appointmentDate]</td>
									<td>$appointment[time]</td>
									<td>$appointment[service_name]</td>
									<td>$appointment[staff_name]</td>
									<td>&#8369;$appointment[price]</td>
									<td>$appointment[status]</td>
									<td>
										<div class = 'btn-field'>
											<button id='editBtn' class='btn'><a href='edit.php?appointment_id=$appointment[appointment_id]?'>Edit</a></button>
											<button id='cancel' class='btn'><a href='cancel.php?appointment_id=$appointment[appointment_id]'>Cancel</a></button>
										</div>
									</td>
								</tr>
								";
							} else if ($appointment['status'] == 'COMPLETED') {
								echo "";
							} else {
								echo "
								<tr class = 'tr'>							
									<td>$appointment[appointmentDate]</td>
									<td>$appointment[time]</td>
									<td>$appointment[service_name]</td>
									<td>$appointment[staff_name]</td>
									<td>&#8369;$appointment[price]</td>
									<td>$appointment[status]</td>
									<td>
										<div class = 'btn-field'>
											<button id='editBtn' class='btn'><a href='edit.php?appointment_id=$appointment[appointment_id]?'>Edit</a></button>
											<button id='cancel' class='btn'><a href='cancel.php?appointment_id=$appointment[appointment_id]'>Cancel</a></button>
										</div>
									</td>
								</tr>
								";
							}
						}
					} else {
						$error = "No appointments found!";
					}
					echo "					
						<tr class='tr'>
						<td>Total</td>
						<td></td>
						<td></td>
						<td></td>
						<td>&#8369; $totalRow[total] </td>
						<td></td>
						<td></td>
						</tr>";
					?>
				</tbody>
			</table>
			<div class="overlay" id="overlay"></div>
		</div>
		<div class="popup" id="bookpopup">
			<span class="close" id="close">&times;</span>
			<h1>Book an Appointment</h1>
			<div class="popup-content">
				<div class="bookDiv">
					<table>
						<input type="hidden" value="<?php echo $client_id ?>" readonly>
						<td style="text-align: right;"><label>Date</label></td>
						</td>
						<td>
							<input type="text" id="datepicker" class="btn" name="appointmentDate" value="" placeholder="Select a date" readonly>
						</td>
						</tr>
						<tr>
							<?php
							$stmt = $con->prepare("SELECT time_id, time FROM timeslots ");
							$stmt->execute();
							$time_result = $stmt->get_result();
							?>
							<td style="text-align: right;"><input type="hidden" id="time" name="time" placeholder="" value="" readonly><label>Time</label></td>
							<td>
								<select class="btn" id="select-time" name="select-time">
									<option value="" disabled selected hidden>Select a time</option>
									<?php while ($row = $time_result->fetch_assoc()) : ?>
										<option value="<?php echo $row['time_id']; ?> "> <?= $row['time'] ?> </option>
									<?php endwhile; ?>
								</select>
							</td>
						</tr>
						<tr>
							<?php
							$stmt = $con->prepare("SELECT category_no, service_category, staff_category FROM category");
							$stmt->execute();
							$category_result = $stmt->get_result();
							?>
							<td style="text-align: right;"><input type="hidden" id="category_no" name="category_no" placeholder="" readonly><label>Service Category</label>
							</td>
							<td>
								<select class="btn" id="select-category_no" name="select-category_no">
									<option value="" disabled selected hidden>Select a category</option>
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
							<td style="text-align: right;"><input type="hidden" id="service_id" name="service_id" placeholder="" readonly><label>Service Name</label>
							</td>
							<td>
								<select class="btn" id="select-service_name" name="select-service_name">
									<option value="" disabled selected hidden>Select a service</option>
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
							<td style="text-align: right;"><input type="hidden" value="" id="staff_id" name="staff_id" placeholder="Staff ID" readonly><label>Staff Name</label></td>
							<td>
								<select class="btn" id="select-name" name="select-name">
									<option value="" disabled selected hidden>Select a staff</option>
									<?php while ($row = $staff_result->fetch_assoc()) : ?>
										<option data-category="<?php echo $row['category_no']; ?>" value="<?php echo $row['staff_id'] ?>"><?= $row['staff_name'] ?></option>
									<?php endwhile; ?>
								</select>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="btn-field">
				<button class="btn" id="next" name="submit" onClick='next()'>NEXT</button>
				<button class="btn" id="done" name="submit" onClick='done()'>DONE</button>
			</div>
		</div>
	</div>
</body>
<script src="popup.js"></script>
<script>
	// Get the select element and the corresponding text field
	let appointmentData = null;
	let timeId = null;
	let serviceId = null;
	let staffId = null;

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
		staffInput.value = this.value;
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
	document.querySelector('#next').addEventListener('click', function() {
		appointmentData = document.querySelector('#datepicker').value;
		timeId = document.querySelector('#select-time').value;
		serviceId = document.querySelector('#select-service_name').value;
		staffId = document.querySelector('#select-name').value;

		if (appointmentData && timeId && serviceId && staffId) {
			// Book the appointment
			const formData = new FormData();
			formData.append('appointmentDate', appointmentData);
			formData.append('time', timeId);
			formData.append('service_id', serviceId);
			formData.append('staff_id', staffId);

			fetch('book.php', {
					method: 'POST',
					body: formData
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						document.querySelector('#success').innerText = data.success;
						document.querySelector('#success-container').classList.remove('hide');
						// Clear the form
						document.querySelector('#datepicker').value = '';
						document.querySelector('#select-time').value = '';
						document.querySelector('#select-service_name').value = '';
						document.querySelector('#select-name').value = '';
					} else {
						document.querySelector('#error').innerText = data.error;
						document.querySelector('#error-container').classList.remove('hide');
					}
				})
				.catch(error => {
					console.error('Error:', error);
				});
		} else {
			document.querySelector('#error').innerText = 'Please fill in all the fields.';
			document.querySelector('#error-container').classList.remove('hide');
		}
	});

	document.querySelector('#done').addEventListener('click', function() {
		window.location.href = "index.php";
	});
</script>

</html>