<?php

session_start();
require("config.php");

// Fetch the categories
$categ = $connection->prepare("SELECT category_no, service_category FROM category ORDER BY category_no");
$categ->execute();
$category_result = $categ->get_result();

// Handle form submission and fetch services based on selected category
$selected_category = "";
$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST["category_no"]) && !empty($_POST["category_no"])) {
        $selected_category = $_POST["category_no"];

        // Add new service if service name and price are set
        if (isset($_POST["service_name"]) && isset($_POST["price"]) && !empty($_POST["service_name"]) && !empty($_POST["price"])) {
            $service_name = $_POST["service_name"];
            $price = $_POST["price"];
            $stmt = $connection->prepare("INSERT INTO services (category_no, service_name, price) VALUES (?, ?, ?)");
            $stmt->bind_param("isd", $selected_category, $service_name, $price);

            if ($stmt->execute()) {
                $successMessage = "Service added successfully!";
            } else {
                $errorMessage = "Failed to add service: " . $stmt->error;
            }
        }
        
        //fetches all the services
        $stmt = $connection->prepare("SELECT * FROM services WHERE category_no = ?");
        $stmt->bind_param("i", $selected_category);
        $stmt->execute();
        $services_result = $stmt->get_result();
    } else {
        echo "<script>alert('Please choose a category!'); window.location.href = window.location.href;</script>";
    }
} else {
    // Fetch all services and staff initially
    $services_result = $connection->query("SELECT * FROM services");
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
                 if (!empty($successMessage)) {
                     echo " <script> alert('$successMessage') </script> ";
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
                            <option value="<?= $row['category_no'] ?>" <?= $selected_category == $row['category_no'] ? 'selected' : '' ?>>CATEGORY <?= $row['category_no'] ?>: <?= $row['service_category'] ?></option>
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

<!-- Service -->


<center><table class="serviceTable" width="98%" border="3" cellspacing=0 cellpadding="2px">
    <thead>
    	<tr>
        <th colspan="5" align="center" class="tableName">
         <a onclick="popup()" role="button"><img id="add_icon" src="add.jpg" title="Add services" width="27" height="27"> </a> 
           &nbsp; &nbsp; LIST OF SERVICES</th>
        </tr>
        <tr>
            <th>ID</th>
            <th>Category Number</th>
            <th>Service Name</th>
            <th>Price (Php)</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($services_result->num_rows > 0) {
            while ($row = $services_result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['service_id']}</td>
                        <td>{$row['category_no']}</td>
                        <td>{$row['service_name']}</td>
                        <td>{$row['price']}</td>
                        <td>
                            <a href='/admin/editService.php?id={$row['service_id']}' role='button'><img src='edit_icon.png' title='Edit' width='27px' height='23px'></a> &nbsp;
                            <a href='/admin/deleteService.php?id={$row['service_id']}' role='button'><img src='delete_icon.png' title='Delete' width='23px' height='23px'></a>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No services found for the selected category.</td></tr>";
        }

       
        ?>
    </tbody>
</table></center><br>

<!-- Popup for adding service -->
<div class="overlay"></div>
<div class="popup" id="popup">
    <button type="button" id="close" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <h3>ADD SERVICE</h3>

    <form method="POST">
        <div class="popup-content">
            <table>
                <tr>
                    <td>
                        <input type="hidden" id="category_no" name="category_no" value="<?= $selected_category ?>">
                        
                        <input type="text" id="service_name" name="service_name" placeholder="Input name of Service" required>
                      
                    </td>
                </tr>
      		
      		<tr>
                    <td>
        		 <input type="number" step="0.01" name="price" placeholder="Price (Php)" required>
                    </td>
                </tr>          
            </table>
        </div>
        <div class="btn-field">
            <button type="submit" class="btn" name="add_service">ADD NOW</button>
        </div>
    </form>
</div>

<!-- JavaScript scripts -->
<script>
    function popup() {
     
        var overlay = document.querySelector('.overlay');
        var popup = document.querySelector('#popup');
        overlay.classList.add('show');
        popup.classList.add('show');
    }

    var closeButton = document.querySelector('#close');
    closeButton.addEventListener('click', function () {
        var overlay = document.querySelector('.overlay');
        var popup = document.querySelector('#popup');
        overlay.classList.remove('show');
        popup.classList.remove('show');
    });
</script>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>
