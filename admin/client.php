<?php
session_start();
require("config.php");

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['admin_id']) || $_SESSION['stat'] === "inactive") {
    // Handle the case where client_id is not set in the session
    header("Location: /admin/main.php");
    die("Admin not logged in");
}

// Get the list of clients from the database using a prepared statement
$stmt = $connection->prepare("SELECT * FROM clients");
$stmt->execute();
$result = $stmt->get_result();
$clients = $result->fetch_all(MYSQLI_ASSOC);
// Close the database connection
$connection->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clients</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <style>
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
                border-radius: 10px;
                width: 95%;
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
                width:30%;
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
            
            .popup tr{
            	border-radius:5px;
            	text-align: center;
            	justify-content: center;
            }
    
            .popup h2 {
                margin-bottom: 20px;
                font-family: Joker;
                font-weight: bold;
                justify-content: center;
                text-align: center;
            }
            
            .btn-close{
            	float: right;
            }
    
            .popup-content {
                margin-bottom: 20px;
            }
    
            .popup-content table {
                width: 100%;
            }
    
            .popup-content table td {
                padding: 5px;
                text-align: left;
                border-radius:5px;
            }
    
            .popup-content select {
                width: 100%;
                padding: 8px;
                border-radius: 5px;
                border: 1px solid #ccc;
            }
    
            .btn-field {
                text-align: center;
            }
    
            .btn {
                padding: 10px 20px;
                width:100%;
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
        }
      
       
    </style>
</head>
<body><br>
    <!-- Table to display clients -->
  <center>  
    <table border="3" cellspacing="3px" cellpadding="2px">
        <thead>
            <tr>
                <th colspan="7" align="center" class="tableName">LIST OF CLIENTS</th>
            </tr>
            <tr>
                <th>Client ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Mobile Number</th>
                <th>Client Type</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clients as $client) {?>
                <tr>
                    <td><?= $client['client_id']?></td>
                    <td><?= $client['fName']?></td>
                    <td><?= $client['lName']?></td>
                    <td><?= $client['eMail']?></td>
                    <td><?= $client['mNum']?></td>
                    <td><?= $client['clientType']?></td>
                    <td>
                        <a href="#" class="edit-button" onclick="openPopup(<?= $client['client_id']?>, event)" role="button">
                            <img src="edit_icon.png" title="Edit" width="27px" height="23px">
                        </a>&nbsp;
                        <a href="/salon/deleteClient.php?client_id=<?= $client['client_id']?>" role="button">
                            <img src="delete_icon.png" title="Delete" width="23px" height="23px">
                        </a>
                    </td>
                </tr>
            <?php }?>
            
        </tbody>
    </table>
  </center> 
    <!-- Popup -->
    <div class="overlay"></div>
    <div class="popup" id="popup">
    	<button type="button" id="close" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <h2>Edit Client Type</h2>
        
        <form method="POST">
            <div class="popup-content">
                <table>
                   <div class="cName">
                    <tr>
                    	<td class="label">Client Name: </td>
			<td id="client_name" name="client_name">  </td>
                    </tr></div>
                  
                    <div class="cType">
                    <tr>
                        <td class="label">Client Type:</td>
                        <td>
                            <select id="client_type" name="client_type">
                                <option value="Regular">Regular</option>
                                <option value="Senior">Senior</option>
                            </select>
                        </td>
                    </tr></div>
                </table>
            </div>
            <div class="btn-field">
                <button type="submit" class="btn" name="submit">Update</button>
            </div>
        </form>
        
    </div>

   <!-- JavaScript scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
   
   <script>
        function openPopup(client_id, event) {
            event.preventDefault();
            var overlay = document.querySelector('.overlay');
            var popup = document.querySelector('#popup');
            overlay.style.display = 'block';
            popup.style.display = 'block';
            var closeButton = document.querySelector('#close');
           
           closeButton.addEventListener('click', function () {
                overlay.style.display = 'none';
                popup.style.display = 'none';
            });
            
            document.querySelector('#client_type').addEventListener('change', function () {
                document.querySelector('#popup form').action = 'editClientType.php?client_id=' + client_id;
            });
            
      	     <!-- (Asynchronous JavaScript and XML) = makes a request to the server -->
            var req = new XMLHttpRequest();
	    req.onreadystatechange = function() {
	    	  // 4 means the request is finished and 200 means the status is successful
	         if (this.readyState == 4 && this.status == 200) {
	    	   // gets the client's current info
	    	   var response = JSON.parse(this.responseText);
		   document.getElementById('client_name').innerText = response.client_name;
        	    document.getElementById('client_type').value = response.clientType;
	    	 }
	     };
	      req.open("GET", "/admin/getClientInfo.php?client_id=" + client_id, true);
	      req.send();
	      
        }
    </script>
</body>
</html>