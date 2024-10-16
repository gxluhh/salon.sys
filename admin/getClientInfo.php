<?php
session_start();
require("config.php");

if (isset($_GET['client_id'])) {
    $client_id = $_GET['client_id'];

    $sql = "SELECT CONCAT(fName, ' ', lName) AS client_name, clientType FROM clients WHERE client_id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $stmt->bind_result($client_name, $clientType);
    $stmt->fetch();
    $stmt->close();

    $response = array('client_name' => $client_name, 'clientType' => $clientType);
    echo json_encode($response);
} else {
    echo "Client ID not provided.";
}
?>