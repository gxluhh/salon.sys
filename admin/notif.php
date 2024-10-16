<?php
session_start();
require("config.php");

$notification = '';
$notificationType = '';

if (!isset($_SESSION['admin_id']) || $_SESSION['stat'] === "inactive") {
    $notification = "Admin not logged in";
    $notificationType = "error";
    header("Location: /admin/adminLogin.php");
    die($notification);
}

$admin_id = $_SESSION['admin_id'];
$dateCondition = '';

// Add more PHP logic here...

// Example condition to trigger a notification
if (isset($_GET['triggerNotification'])) {
    $notification = "This is a test notification!";
    $notificationType = "success";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notification System</title>
    <link rel="stylesheet" href="styles.css">
    
    <style type="text/css">
    /* styles.css */
    #notification-container {
        position: fixed;
        top: 10px;
        right: 10px;
        width: 300px;
        z-index: 1000;
    }
    
    .notification {
        background-color: #4caf50;
        color: white;
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        opacity: 0;
        transition: opacity 0.5s ease;
    }
    
    .notification.show {
        opacity: 1;
    }
    
    .notification.error {
        background-color: #f44336;
    }

    </style>
</head>
<body>
    <div id="notification-container"></div>

    <!-- Rest of your HTML content -->

    <script src="script.js"></script>
    <script>
        <?php if ($notification): ?>
            showNotification("<?php echo $notification; ?>", "<?php echo $notificationType; ?>");
        <?php endif; ?>
    </script>
    
    <script>
    /* script.js */
    function showNotification(message, type = 'success') {
        const notificationContainer = document.getElementById('notification-container');
        
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerText = message;
    
        notificationContainer.appendChild(notification);
    
        // Show the notification
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
    
        // Hide the notification after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            // Remove the notification from the DOM
            setTimeout(() => {
                notificationContainer.removeChild(notification);
            }, 500);
        }, 3000);
    }

    </script>
</body>
</html>
