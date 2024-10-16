<?php
session_start();
require("config.php");

if (!isset($_SESSION['admin_id']) || $_SESSION['stat'] === "inactive") {
    header("Location: /admin/adminLogin.php");
    die("Admin not logged in");
}

$admin_id = $_SESSION['admin_id'];
$dateCondition = "";

// Function to get the query based on the selected time frame
function getQuery($timeFrame) {
    global $dateCondition;

    if ($timeFrame == "daily") {
        $dateCondition = "AND DATE(t.datePaid) = CURDATE()";
    } elseif ($timeFrame == "weekly") {
        $dateCondition = "AND YEARWEEK(t.datePaid, 1) = YEARWEEK(CURDATE(), 1)";
    } elseif ($timeFrame == "monthly") {
        $dateCondition = "AND MONTH(t.datePaid) = MONTH(CURDATE()) AND YEAR(t.datePaid) = YEAR(CURDATE())";
    }

    $query = "
        SELECT s.service_name, COUNT(t.service_id) AS countNum
        FROM transactions t
        JOIN services s ON t.service_id = s.service_id
        WHERE s.category_no = ? $dateCondition
        GROUP BY t.service_id
        ORDER BY countNum DESC
        LIMIT 3
    ";

    return $query;
}

// Fetch all categories
$stmt = $connection->prepare("SELECT * FROM category");
$stmt->execute();
$categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$data = [];
$timeFrame = isset($_GET['timeFrame']) ? $_GET['timeFrame'] : 'daily'; // Default to daily

foreach ($categories as $category) {
    $category_no = $category['category_no'];
    $query = getQuery($timeFrame);
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $category_no);
    $stmt->execute();
    $result = $stmt->get_result();

    $services = [];
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }

    // Fetch top 3 availed staff for this category
    $staffQuery = "
        SELECT COUNT(t.staff_id) AS countStaff, CONCAT(s.fName, ' ', s.lName) AS staff_name
        FROM transactions t
        JOIN staff s ON t.staff_id = s.staff_id
        WHERE t.service_id IN (SELECT service_id FROM services WHERE category_no = ?) $dateCondition
        GROUP BY t.staff_id
        ORDER BY countStaff DESC
        LIMIT 3
    ";

    $stmt = $connection->prepare($staffQuery);
    $stmt->bind_param("i", $category_no);
    $stmt->execute();
    $staffResult = $stmt->get_result();

    $staff = [];
    while ($row = $staffResult->fetch_assoc()) {
        $staff[] = $row;
    }

    $data[] = [
        'category' => $category['service_category'],
        'services' => $services,
        'staff' => $staff
    ];
}

//number of staff
$stmt = $connection->prepare("SELECT COUNT(staff_id) AS numStaff FROM staff");
$stmt->execute();
$numStaff = $stmt->get_result();
$value = $numStaff->fetch_assoc();

//number of Client
$sql= $connection->prepare("SELECT COUNT(client_id) AS numClient FROM clients");
$sql->execute();
$result = $sql->get_result();
$numClient = $result->fetch_assoc();

//Number of service rendered
$sql= $connection->prepare("SELECT SUM(amount) AS cntSales FROM transactions WHERE datePaid=CURRENT_DATE()");
$sql->execute();
$result = $sql->get_result();
$row = $result->fetch_assoc();

$stmt = $connection->prepare("SELECT COUNT(service_id) AS cntServices FROM services");
$stmt->execute();
$numSrvcs = $stmt->get_result();
$services = $numSrvcs->fetch_assoc();

?>

<html>
<head>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/admin/homepage.css">
    
    <style type="text/css">
        .icon {
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
        }
        body {
            background-color: #E8D8C4;
        }
        .chart-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 20px; /* Add space between charts */
            padding: 20px;
        }
        .timeframe-select {
            display: flex;
            justify-content: center;
            background-color: #675D50;
        }
        h2 {
            font-family: Joker;
            color: white;
            font-weight: bold;
            justify-content: start;
            text-align: left;
            background-color: #4F0E0E;
            padding: 10px;
        }
        #timeFrame {
            margin-top: 10px;
            width: 100%;
            background-color: #675D50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-family: Joker;
        }		
        #timeFrame:hover {
            color: #322C2B;
            background-color: #F5EFE6;
        }
        .item-container, .btn {
            display: flex;
            gap: 20px; /* Add space between items */
            justify-content: center;
        }
        .item, .btn {
            flex: 1; /* Make items take up equal space */
            padding: 20px; /* Add padding for better appearance */
            border-radius: 5px; /* Optional: add rounded corners */
            color: white; /* Text color */
            justify-content: center;
        }
        .item.staff {
            background-color: #49243E;
            height: 250px;
            width: 50px;
            font-size: 20px;
        }
        .item.client {
            background-color: #153448; 
            height: 250px;
            width: 50px;
            font-size: 20px;
            justify-content: center;
        }
        .item.sales {
            background-color: #8B322C; 
            height: 250px;
            width: 50px;
            font-size: 20px;
            justify-content: center;
        }
        .item.services {
        	background-color: #141E46; 
            height: 250px;
            width: 50px;
            font-size: 20px;
            justify-content: center;
        }
        
       h5{
            font-family: Joker;
            font-weight: bold;
            color: white;
            padding: 5px; 
            text-align: center; /* Center align text */
            box-sizing: border-box; 
            word-wrap: break-word; /* Break long words to fit the container */
        }
        
        a { text-decoration: none; }
        #cnt { font-size: 20px; color: white; }
        .cons { font-size: 40px; color: white; }
        
        .item:hover {
            background-color: black;
            box-shadow: 2px 2px 10px #F0EBE3;
    }
    </style>
    
</head>
<body>

<h2> DASHBOARD </h2><br><br><br>

<div class="container">
    <!-- container for items -->
    <div class="item-container"> 
        <!-- container for number of staff -->
        <a class="btn" role="button" href="http://localhost/admin/staff.php" target="display">
        <div class="item staff"> 
            <h5> OVERALL NO. OF STAFFS</h5>  
            <ion-icon class="cons" name="people"></ion-icon><br><br>
            <span id="cnt"> <?php {echo $value['numStaff']; }?> <span>
        </div></a>
        
        <!-- container for number of client -->
        <a class="btn" role="button" href="http://localhost/admin/client.php" target="display">
        <div class="item client"> 
            <h5> NO. OF CLIENTS</h5> 
            <ion-icon class="cons" name="people"></ion-icon><br><br>
            <span id="cnt"> <?php {echo $numClient['numClient']; }?> <span>
        </div></a>
        
        <a class="btn" role="button" href="http://localhost/admin/transaction.php" target="display">
        <div class="item sales"> 
	      <h5> DAILY TRANSACTIONS</h5>  
	      <ion-icon class="cons" name="card"></ion-icon><br><br>
	      <span id="cnt"> <?php {echo $row['cntSales']; }?> <span> 
        </div></a>
        
        <a class="btn" role="button" href="http://localhost/admin/serviceFunc.php" target="display">
	<div class="item services"> 
		<h5> NO. OF SERVICES</h5> 
		<ion-icon class="cons" name="layers"></ion-icon><br><br>
		<span id="cnt"> <?php {echo $services['cntServices']; }?> <span>
        </div></a>
        
    </div>
</div><br><br><br><br>

<div class="timeframe-select">
    <form method="GET">
        <select id="timeFrame" name="timeFrame" onchange="this.form.submit()">
            <option value="daily" <?= $timeFrame == 'daily' ? 'selected' : '' ?>>Daily</option>
            <option value="weekly" <?= $timeFrame == 'weekly' ? 'selected' : '' ?>>Weekly</option>
            <option value="monthly" <?= $timeFrame == 'monthly' ? 'selected' : '' ?>>Monthly</option>
        </select>
    </form>
</div><br>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

<div class="chart-container">
    <?php foreach ($data as $index => $categoryData): ?>
        <div>
            <canvas id="chart<?= $index ?>" style="width:100%;max-width:600px"></canvas>
            <script>
                var ctx = document.getElementById('chart<?= $index ?>').getContext('2d');
                var chart<?= $index ?> = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: [
                            <?php foreach ($categoryData['services'] as $service): ?>
                                "<?= $service['service_name'] ?>",
                            <?php endforeach; ?>
                        ],
                        datasets: [{
                            backgroundColor: ["#F9AD8D", "#FDE3D9", "#FFCF9D"],
                            data: [
                                <?php foreach ($categoryData['services'] as $service): ?>
                                    <?= $service['countNum'] ?>,
                                <?php endforeach; ?>
                            ]
                        }]
                    },
                    options: {
                        legend: { display: false },
                        title: {
                            display: true,
                            text: "Top 3 Services in <?= $categoryData['category'] ?> (<?= ucfirst($timeFrame) ?>)"
                        }
                    }
                });
            </script>
        </div>
        
        <div>
            <canvas id="staffChart<?= $index ?>" style="width:100%;max-width:600px"></canvas>
            <script>
                var ctx = document.getElementById('staffChart<?= $index ?>').getContext('2d');
                var staffChart<?= $index ?> = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: [
                            <?php foreach ($categoryData['staff'] as $staff): ?>
                                "<?= $staff['staff_name'] ?>",
                            <?php endforeach; ?>
                        ],
                        datasets: [{
                            backgroundColor: ["#4F0E0E", "#BB8760", "#FFDADA"],
                            data: [
                                <?php foreach ($categoryData['staff'] as $staff): ?>
                                    <?= $staff['countStaff'] ?>,
                                <?php endforeach; ?>
                            ]
                        }]
                    },
                    options: {
                        legend: { display: false },
                        title: {
                            display: true,
                            text: "Top 3 Availed Staff in <?= $categoryData['category'] ?> (<?= ucfirst($timeFrame) ?>)"
                        }
                    }
                });
            </script>
        </div>
    <?php endforeach; ?>
</div>

<!-- icon -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>
