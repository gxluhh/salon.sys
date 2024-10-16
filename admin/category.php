<?php

session_start();
require("config.php");

if (!isset($_SESSION['admin_id']) || $_SESSION['stat'] === "inactive") {
    // Handle the case where client_id is not set in the session
    header("Location: /admin/main.php");
    die("Admin not logged in");
}

// Fetch the categories
$categ = $connection->prepare("SELECT * FROM category ORDER BY category_no");
$categ->execute();
$category_result = $categ->get_result();

$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Add new category
        if (isset($_POST["service_category"]) && isset($_POST["staff_category"])) {
            $service_category = $_POST["service_category"];
            $staff_category = $_POST["staff_category"];
            
            $stmt = $connection->prepare("INSERT INTO category (service_category, staff_category) VALUES (?, ?)");
            $stmt->bind_param("ss", $service_category, $staff_category);

            if ($stmt->execute()) {
                $successMessage = "Category added successfully!";
                header("location: /salon/category.php");
                  exit;
            } else {
                $errorMessage = "Failed to add service: " . $stmt->error;
            }
        }
        
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

<body bgcolor="#F6F5F2"><br>
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


<!-- Category Table -->
<center><table class="serviceTable" width="98%" border="3" cellspacing=0 cellpadding="2px">
    <thead>
    	<tr>
        <th colspan="4" align="center" class="tableName">
           
        <a onclick="popup3()" role="button"><img id="add_icon" src="add.jpg" title="Add services" width="27" height="27"> </a>   
          &nbsp; &nbsp;LIST OF CATEGORIES</th>
        </tr>
        <tr>
            <th>Category Number</th>
            <th>Service Category</th>
            <th>Staff Category</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($category_result->num_rows > 0) {
            while ($row = $category_result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['category_no']}</td>
                        <td>{$row['service_category']}</td>
                        <td>{$row['staff_category']}</td>
                        <td>
                            <a href='/admin/editCategory.php?id={$row['category_no']}' role='button'><img src='edit_icon.png' title='Edit' width='27px' height='23px'></a> &nbsp;
                            <a href='/admin/deleteCategory.php?id={$row['category_no']}' role='button'><img src='delete_icon.png' title='Delete' width='23px' height='23px'></a>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>NO CATEGORIES FOUND.</td></tr>";
        }

       
        ?>
    </tbody>
</table></center><br>


<!-- POPUP -->
<div class="overlay3"></div>
<div class="popup3" id="popup3">
    <button type="button" id="close3" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <h3>ADD CATEGORY</h3>

    <form method="POST">
        <div class="popup-content">
            <table>

                <tr>
                    <td>
                        <input type="text" id="service_category" name="service_category" placeholder="Input Category of Service" required>
                    </td>
                </tr>
      		
      		<tr>
                    <td>
        		 <input type="text" id="staff_category" name="staff_category" placeholder="Input Category of Staff" required>
                    </td>
                </tr>          
            </table>
        </div>
        <div class="btn-field">
            <button type="submit" class="btn" name="add_category">ADD NOW</button>
        </div>
    </form>
</div>


<!-- JavaScript scripts -->
<script>
  
     function popup3() {
             
                var overlay3 = document.querySelector('.overlay3');
                var popup3 = document.querySelector('#popup3');
                overlay3.classList.add('show');
                popup3.classList.add('show');
            }
        
            var closeButton3 = document.querySelector('#close3');
            closeButton3.addEventListener('click', function () {
                var overlay3 = document.querySelector('.overlay3');
                var popup3 = document.querySelector('#popup3');
                overlay3.classList.remove('show');
                popup3.classList.remove('show');
    });
</script>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>
