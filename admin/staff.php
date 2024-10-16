<?php
session_start();
require("config.php");

// Fetch the categories
$categ = $connection->prepare("SELECT * FROM category ORDER BY category_no");
$categ->execute();
$category_result = $categ->get_result();

// Handle form submission and fetch services based on selected category
$selected_category = "";
$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST["category_no"]) && !empty($_POST["category_no"])) {
        $selected_category = $_POST["category_no"];

        
        // add new staff
        if (isset($_POST["Fname"]) && isset($_POST["Lname"]) && isset($_POST["main_skill"]) && isset($_POST["position"]) && isset($_POST["salary"]) && !empty($_POST["Fname"]) && !empty($_POST["Lname"]) && !empty($_POST["main_skill"]) && !empty($_POST["position"]) && !empty($_POST["salary"])) {
        
   		    $Fname = $_POST["Fname"];
		    $Lname = $_POST["Lname"];
		    $main_skill = $_POST["main_skill"];
		    $position = $_POST["position"];
    		    $salary = $_POST["salary"];

	            $stmt = $connection->prepare("INSERT INTO staff(category_no, fName, lName, mainSkill, position, salary) VALUES (?, ?, ?, ?, ?, ?)");
	            $stmt->bind_param("issssd", $selected_category, $Fname, $Lname, $main_skill, $position, $salary);
	
	            if ($stmt->execute()) {
	                $successMessage = "Service added successfully!";
	            } else {
	                $errorMessage = "Failed to add service: " . $stmt->error;
	            }
        }
        
        $stmt = $connection->prepare("SELECT * FROM staff WHERE category_no =?");
        $stmt->bind_param("i", $selected_category);
        $stmt->execute();
        $staff_result = $stmt->get_result();

    } else {
        echo "<script>alert('Please choose a category!'); window.location.href = window.location.href;</script>";
    }
} else {
    // Fetch all the staff initially
    $staff_result = $connection->query("SELECT * FROM staff");
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>salonAppointmentSys</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="category.css">
    
</head>

<body bgcolor="#F6F5F2">
 <?php
                 if (!empty($addServiceMessage)) {
                     echo " <script> alert('$addServiceMessage') </script> ";
                 }
              ?>
             
               <?php
                        if (!empty($errorMessage)) {
                            echo " <script> alert('$errorMessage') </script> ";
                        }
     ?>

<!-- Category -->
<center>
    <form method="POST">
        <table width="100%" border="3" cellspacing="3px" cellpadding="2px">
            <tr>
                <td>
                    <select class="btn" id="category_no" name="category_no" onchange="this.form.submit()">
                        <option value="" disabled selected hidden>CHOOSE A CATEGORY</option>
                        <?php while ($row = $category_result->fetch_assoc()): ?>
                            <option value="<?= $row['category_no'] ?>" <?= $selected_category == $row['category_no'] ? 'selected' : '' ?>>CATEGORY <?= $row['category_no'] ?>: <?= $row['staff_category'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </td>
                
                <td colspan="3">
		      <button type="submit" class="btn" name="submit">OK</button>
                </td>
            </tr>
        </table>
     </form>
</center><br>

<!-- Staff -->
&nbsp; &nbsp;
<a  onclick="popup2()" role="button"><img id="icon" src="add_icon.png" title="Add new staff" width="27" height="27"> </a>
    	<br>
    <center><table width="98%" border="3px" cellspacing="3px" cellpadding="2px">
      <thead>
        <tr>
          <th colspan="9" align="center" class="tableName">LIST OF EMPLOYEE </th>
        </tr>
        <tr>
          <th> Staff ID </th>
          <th> Category Number </th>
          <th> First Name </th>
          <th> Last Name</th>
          <th> Main Skill </th>
          <th> Position </th>
          <th> Salary </th>
          <th>  </th>
        </tr>
      </thead>
      
      <tbody>
          <?php
          
          	if($staff_result->num_rows > 0){
            	   while($row = $staff_result->fetch_assoc()){
               		echo "<tr>
                    		<td> {$row['staff_id']} </td>
                    		<td> {$row['category_no']} </td>
                    		<td> {$row['fName']} </td>
                    		<td> {$row['lName']} </td>
                    		<td> {$row['mainSkill']} </td>
                    		<td> {$row['position']} </td>
                    		<td> {$row['salary']} </td>
                    		<td> &nbsp;&nbsp; 
                      			<a href='/admin/editStaff.php?staff_id={$row['staff_id']}' role='button'><img src='edit_icon.png' title='Edit' width='27px' height='23px'></a> &nbsp;
                      			<a href='/admin/deleteStaff.php?staff_id={$row['staff_id']}' role='button'><img src='delete_icon.png' title='Delete' width='23px' height='23px'></a>
                    		</td>
                  	    </tr>";
         	   }
        	}else {
            		echo "<tr><td colspan='8'>No staff/s found for the selected category.</td></tr>";
        	}
          
           $connection->close();
     	   ?>
     	   
        
        <!-- Additional table rows to be added here from the database -->
      </tbody>
    </table> </center>
    
<!-- Popup for adding staff -->
<div class="overlay2"></div>
<div class="popup2" id="popup2">
    <button type="button" id="close2" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <h3>ADD EMPLOYEE</h3>
    
        <form method="POST">
            <div class="popup-content">
                <table>
                    <tr>
                        <td>
                            <input type="hidden" id="category_no" name="category_no" value="<?= $selected_category ?>">
	                <input type="text" placeholder="Enter first name" class="form-control" name="Fname" value="<?php $Fname; ?>" required>
                          
                        </td>
                    </tr>
          		
          	    <tr>
                        <td>
            		  <input type="text" placeholder="Enter last name" class="form-control" name="Lname" value="" required>
                        </td>
                    </tr>    
                    
          	    <tr>
                        <td>
            		  <input type="text" placeholder="Enter Main skill" class="form-control" name="main_skill" value="" required>
                        </td>
                    </tr>    
          		
          	    <tr>
                        <td>
            		 <input type="text" placeholder="Enter position" class="form-control" name="position" value="" required>
                        </td>
                    </tr> 
          		
          	    <tr>
                        <td>
            		 <input type="number" step="0.01" placeholder="Enter salary" class="form-control" name="salary" value="" required>
                        </td>
                    </tr>    
                </table>
            </div>
            <div class="btn-field">
                <button type="submit" class="btn" name="add_staff">ADD NOW</button>
            </div>
    </form>
</div>
</div>


<!-- JavaScript scripts -->
<script>
   
    function popup2() {
         
            var overlay2 = document.querySelector('.overlay2');
            var popup2 = document.querySelector('#popup2');
            overlay2.classList.add('show');
            popup2.classList.add('show');
        }
    
        var closeButton2 = document.querySelector('#close2');
        closeButton2.addEventListener('click', function () {
            var overlay2 = document.querySelector('.overlay2');
            var popup2 = document.querySelector('#popup2');
            overlay2.classList.remove('show');
            popup2.classList.remove('show');
    });
    
</script>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>
