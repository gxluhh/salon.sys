<?php
session_start();
require("config.php");

if (!isset($_SESSION['admin_id']) || $_SESSION['stat'] === "inactive") {
    // Handle the case where client_id is not set in the session
    header("Location: /admin/main.php");
    die("Admin not logged in");
}

$client_type = "";
$errorMessage = "";
$successMessage = "";

if (isset($_POST['submit'])) {
    $client_type = $_POST["client_type"];

    if (!empty($client_type) && isset($_GET["client_id"])) {
        $client_id = $_GET["client_id"];
        $sql = "UPDATE clients SET clientType=? WHERE client_id=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("si", $client_type, $client_id);

        if ($stmt->execute()) {
            $successMessage = "Client updated successfully!";
            header("Location: /admin/client.php");
            exit;
        } else {
            $errorMessage = "Invalid query: " . $connection->error;
            echo "<script>alert('Data not updated');</script>";
        }
        $stmt->close();
    } else {
        $errorMessage = "Client type or client ID is missing.";
    }
}
?>
