<?php
session_start();
require("config.php");

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['admin_id']) || $_SESSION['stat'] !== 'active') {
    header("Location: main.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transaction</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"> </script>
	
	<style type="text/css">
	
		.tableName{
		text-align: center;
		color: white;
		font-size:20px;
		font-family: Joker;
		font-weight: bold;
		background-color: #2F2519;}
		
		tr{
		color: white;
		background-color: #675D50;
		font-family: Joker;}
		
		a{text-decoration:none;}
		
		#icon:hover, a:hover{opacity:0.5;}
				  
		table{border-radius: 10px;}
		body{background-color:#E8D8C4;}
	</style>
  
  </head>
  
  <body><br>
  
    <center><table width="95%" border="3" cellspacing="3px" cellpadding="2px">
      <thead>
        <tr>
          <th colspan="7" align="center" class="tableName">TRANSACTIONS </th>
        </tr>
        <tr>
          <th> Transaction ID </th>
          <th> Name of Client </th>
          <th> Name of Service </th>
          <th> Name of staff</th>
          <th> Date Paid</th>
          <th> Amount </th>
          <th> Status </th>
        </tr>
      </thead>
      
      <tbody>
          <?php
          	$errMessage="No records found!";
        	
          	
          	$sql = "SELECT t.transact_id, CONCAT(c.fName, ' ', c.lName) AS client_name, srvcs.service_name, 
          			CONCAT(s.fName, ' ', s.lName) AS staff_name, t.datePaid, t.amount, t.status
		 	FROM transactions t
		 	JOIN services srvcs ON t.service_id = srvcs.service_id
			JOIN clients c ON t.client_id = c.client_id
			JOIN staff s ON t.staff_id = s.staff_id
			ORDER BY t.transact_id DESC";
          		
          	$result = $connection->query($sql);
          
          	if(!$result){
            		die("Invalid Inquiry: ".$connection->error);
         	}
          
          	if($result->num_rows > 0){
            	   while($row = $result->fetch_assoc()){
               		echo "<tr>
                    		<td> {$row['transact_id']} </td>
                    		<td> {$row['client_name']} </td>
                    		<td> {$row['service_name']} </td>
                    		<td> {$row['staff_name']} </td>
                    		<td> {$row['datePaid']} </td>
                    		<td> {$row['amount']} </td>
                    		<td> {$row['status']} </td>
                  	    </tr>";
         	   }
        	}else{ 
        		echo " <script> alert('$errMessage') </script> ";
        	}
          
         		 $connection->close();
     	   ?>
        <!-- Additional table rows to be added here from the database -->
      </tbody>
    </table> </center>
    
    
  </body>
</html>
